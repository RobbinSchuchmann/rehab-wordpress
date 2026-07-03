<?php
/**
 * Intake-form handler for rehab/intake-form (REH-65).
 *
 * Registers POST /wp-json/rehab/v1/intake — accepts the multi-step intake
 * wizard submission, re-validates against the same spec the block renders
 * from (plugins/rehab-blocks/assets/intake-spec.json), honeypot-checks,
 * stores the entry as a `rehab_submission` post (CPT registered by
 * zz-contact-form.php) and emails the admissions recipients configured in
 * the spec's notification block (ported from the legacy Forminator form).
 *
 * The legacy PDF attachment (Forminator PDF addon, template 11790) is NOT
 * reproduced; the email body carries every answered field instead, plus the
 * signature PNG as an attachment.
 *
 * @package RehabParent
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/** Load + cache the shared intake spec. */
function rehab_intake_spec(): ?array {
	static $spec = null;
	if ( $spec === null ) {
		$file = WP_PLUGIN_DIR . '/rehab-blocks/assets/intake-spec.json';
		$spec = file_exists( $file ) ? json_decode( file_get_contents( $file ), true ) : false;
	}
	return $spec ?: null;
}

add_action( 'rest_api_init', function () {
	register_rest_route( 'rehab/v1', '/intake', [
		'methods'             => 'POST',
		'callback'            => 'rehab_intake_form_submit',
		'permission_callback' => '__return_true', // public; protected by honeypot + rate limit
	] );
} );

/**
 * Evaluate a spec condition block against submitted values —
 * mirrors view.js so "required" only applies to fields the user could see.
 */
function rehab_intake_condition_met( ?array $cond, array $values ): bool {
	if ( empty( $cond ) ) return true;
	$results = array_map(
		static function ( $rule ) use ( $values ) {
			$v = (string) ( $values[ $rule['field'] ] ?? '' );
			return $rule['operator'] === 'is_not' ? $v !== $rule['value'] : $v === $rule['value'];
		},
		$cond['rules']
	);
	$met = ( $cond['rule'] ?? 'all' ) === 'any' ? in_array( true, $results, true ) : ! in_array( false, $results, true );
	return ( $cond['action'] ?? 'show' ) === 'hide' ? ! $met : $met;
}

/** Flatten the spec's steps into field elements (groups unwrapped, kinds section/html skipped). */
function rehab_intake_walk_fields( array $spec ): array {
	$fields = [];
	foreach ( $spec['steps'] as $step ) {
		foreach ( $step['elements'] as $el ) {
			if ( $el['kind'] === 'field' ) {
				$fields[] = $el;
			} elseif ( $el['kind'] === 'group' ) {
				foreach ( $el['fields'] as $gf ) {
					$gf['inGroup']    = $el['element_id'];
					$gf['repeatable'] = ! empty( $el['repeatable'] );
					$gf['conditions'] = $gf['conditions'] ?? ( $el['conditions'] ?? null );
					$fields[]         = $gf;
				}
			}
		}
	}
	return $fields;
}

function rehab_intake_form_submit( WP_REST_Request $req ) {
	$spec = rehab_intake_spec();
	if ( ! $spec ) {
		return new WP_REST_Response( [ 'ok' => false, 'error' => 'Form unavailable.' ], 500 );
	}

	$p = $req->get_json_params();
	if ( ! is_array( $p ) ) {
		return new WP_REST_Response( [ 'ok' => false, 'error' => 'Invalid request.' ], 400 );
	}

	// Honeypot trap (same control name as the legacy form).
	$hp = $spec['form']['honeypotControl'] ?? 'input_59';
	if ( ! empty( $p[ $hp ] ) ) {
		return new WP_REST_Response( [ 'ok' => true ], 200 ); // pretend success; don't notify
	}

	// Rate limit: max 3 submissions / 10 min per IP (long form — repeats are suspect).
	$ip    = isset( $_SERVER['REMOTE_ADDR'] ) ? preg_replace( '/[^0-9a-f:.]/', '', $_SERVER['REMOTE_ADDR'] ) : 'unknown';
	$key   = 'rehab_intake_' . md5( $ip );
	$count = (int) get_transient( $key );
	if ( $count >= 3 ) {
		return new WP_REST_Response( [ 'ok' => false, 'error' => 'Too many submissions. Please try again later.' ], 429 );
	}
	set_transient( $key, $count + 1, 10 * MINUTE_IN_SECONDS );

	// ---- sanitize + validate against the spec --------------------------------
	$fields = rehab_intake_walk_fields( $spec );
	$scalar = static fn( $v ) => sanitize_textarea_field( is_scalar( $v ) ? (string) $v : '' );
	$errors = [];
	$clean  = [];

	// First pass: sanitize scalars so condition evaluation sees clean values.
	foreach ( $p as $k => $v ) {
		if ( is_array( $v ) ) {
			$clean[ $k ] = array_map( $scalar, $v );
		} elseif ( strpos( (string) $v, 'data:image/png;base64,' ) === 0 ) {
			$clean[ $k ] = (string) $v; // signature — handled below
		} else {
			$clean[ $k ] = $scalar( $v );
		}
	}

	foreach ( $fields as $f ) {
		$id = $f['element_id'];

		if ( ! rehab_intake_condition_met( $f['conditions'] ?? null, $clean ) ) {
			// Field was hidden — discard any value so hidden answers can't sneak in.
			unset( $clean[ $id ] );
			continue;
		}

		$required = ! empty( $f['required'] );
		if ( $f['type'] === 'date' ) {
			foreach ( $f['subfields'] as $sub ) {
				if ( $required && ( $clean[ $sub['name'] ] ?? '' ) === '' ) {
					$errors[] = $id;
					break;
				}
			}
			continue;
		}
		if ( $f['type'] === 'address' ) {
			foreach ( $f['subfields'] as $sub ) {
				if ( ! empty( $sub['required'] ) && ( $clean[ $sub['name'] ] ?? '' ) === '' ) {
					$errors[] = $sub['name'];
				}
			}
			continue;
		}

		$value = $clean[ $id ] ?? '';
		if ( $required && ( is_array( $value ) ? ! array_filter( $value ) : $value === '' ) ) {
			$errors[] = $id;
		}
		if ( $f['type'] === 'email' && ! is_array( $value ) && $value !== '' && ! is_email( $value ) ) {
			$errors[] = $id;
		}
	}

	if ( $errors ) {
		return new WP_REST_Response( [
			'ok'     => false,
			'error'  => 'Please complete all required fields.',
			'fields' => array_values( array_unique( $errors ) ),
		], 400 );
	}

	// ---- signature -------------------------------------------------------------
	$signature_path = '';
	$signature_url  = '';
	if ( ! empty( $clean['signature-1'] ) ) {
		$b64 = substr( $clean['signature-1'], strlen( 'data:image/png;base64,' ) );
		$png = base64_decode( $b64, true );
		if ( $png === false || strlen( $png ) > 512000 || substr( $png, 0, 8 ) !== "\x89PNG\r\n\x1a\n" ) {
			return new WP_REST_Response( [ 'ok' => false, 'error' => 'Signature image invalid.', 'fields' => [ 'signature-1' ] ], 400 );
		}
		$upload = wp_upload_bits( 'intake-signature-' . gmdate( 'Ymd-His' ) . '-' . wp_generate_password( 6, false ) . '.png', null, $png );
		if ( empty( $upload['error'] ) ) {
			$signature_path = $upload['file'];
			$signature_url  = $upload['url'];
		}
		unset( $clean['signature-1'] );
	}

	// ---- format the all-fields body (labels, option labels, sections) -----------
	$label_for = static function ( array $f, $value ) {
		if ( isset( $f['options'] ) ) {
			foreach ( $f['options'] as $opt ) {
				if ( (string) $opt['value'] === (string) $value ) {
					return $opt['label'];
				}
			}
		}
		return $value;
	};

	$lines = [];
	foreach ( $spec['steps'] as $step ) {
		foreach ( $step['elements'] as $el ) {
			if ( $el['kind'] === 'section' ) {
				$lines[] = '';
				$lines[] = '== ' . $el['title'] . ' ==';
				continue;
			}
			if ( $el['kind'] === 'group' ) {
				$has_values = false;
				$group_lines = [ $el['label'] . ':' ];
				if ( ! empty( $el['repeatable'] ) ) {
					$counts = max( array_map( static fn( $gf ) => count( (array) ( $clean[ $gf['element_id'] ] ?? [] ) ), $el['fields'] ) );
					for ( $i = 0; $i < $counts; $i++ ) {
						$parts = [];
						foreach ( $el['fields'] as $gf ) {
							$v = ( $clean[ $gf['element_id'] ] ?? [] )[ $i ] ?? '';
							if ( $v !== '' ) $parts[] = $gf['label'] . ': ' . $v;
						}
						if ( $parts ) {
							$group_lines[] = '  ' . ( $i + 1 ) . '. ' . implode( ' — ', $parts );
							$has_values = true;
						}
					}
				} else {
					foreach ( $el['fields'] as $gf ) {
						$v = $clean[ $gf['element_id'] ] ?? '';
						if ( $v !== '' ) {
							$group_lines[] = '  ' . ( $gf['label'] ?: $gf['element_id'] ) . ': ' . $v;
							$has_values = true;
						}
					}
				}
				if ( $has_values ) $lines = array_merge( $lines, $group_lines );
				continue;
			}
			if ( $el['kind'] !== 'field' ) continue;

			if ( $el['type'] === 'date' ) {
				$parts = array_map( static fn( $sub ) => $clean[ $sub['name'] ] ?? '', $el['subfields'] );
				if ( array_filter( $parts ) ) {
					$lines[] = ( $el['label'] ?: $el['element_id'] ) . ': ' . implode( '/', array_map( static fn( $x ) => str_pad( $x, 2, '0', STR_PAD_LEFT ), $parts ) );
				}
				continue;
			}
			if ( $el['type'] === 'address' ) {
				$parts = [];
				foreach ( $el['subfields'] as $sub ) {
					$v = $clean[ $sub['name'] ] ?? '';
					if ( $v !== '' ) $parts[] = $v;
				}
				if ( $parts ) $lines[] = 'Address: ' . implode( ', ', $parts );
				continue;
			}
			if ( $el['type'] === 'signature' ) {
				$lines[] = 'Applicant signature: ' . ( $signature_url ? 'signed (attached)' : 'not provided' );
				continue;
			}
			$v = $clean[ $el['element_id'] ] ?? '';
			if ( $v === '' || is_array( $v ) ) continue;
			$label = $el['label'] ?: ( $el['placeholder'] ?: $el['element_id'] );
			$lines[] = $label . ': ' . $label_for( $el, $v );
		}
	}
	$body_fields = trim( implode( "\n", $lines ) );

	// ---- store the entry ------------------------------------------------------
	$name  = $clean['name-1'] ?? '';
	$email = $clean['email-1'] ?? '';
	$post_id = wp_insert_post( [
		'post_type'    => 'rehab_submission',
		'post_status'  => 'publish',
		'post_title'   => 'Intake — ' . $name . ' — ' . $email,
		'post_content' => $body_fields,
		'meta_input'   => [
			'_form'      => 'intake',
			'_email'     => $email,
			'_phone'     => $clean['phone-1'] ?? '',
			'_signature' => $signature_url,
			'_payload'   => wp_json_encode( $clean, JSON_UNESCAPED_UNICODE ),
			'_source'    => $clean['source'] ?? '',
			'_ip'        => $ip,
			'_ua'        => isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( $_SERVER['HTTP_USER_AGENT'] ) : '',
		],
	], true );
	if ( is_wp_error( $post_id ) ) {
		$post_id = 0; // don't fail the applicant — still try email
	}

	// ---- notify (recipients ported from the legacy Forminator notification) -----
	$notif      = $spec['notification'];
	$recipients = implode( ',', $notif['recipients'] );
	$subject    = 'Intake Form ' . $name . ' (#' . ( $post_id ?: gmdate( 'YmdHis' ) ) . ')';

	$body  = "You have a new intake form (Diamond):\n\n";
	$body .= $body_fields . "\n\n----\n";
	if ( $signature_url ) $body .= "Signature: $signature_url\n";
	if ( $post_id ) $body .= 'Admin: ' . admin_url( 'post.php?action=edit&post=' . $post_id ) . "\n";
	$body .= 'This message was sent from ' . home_url() . "\n";

	$headers = [ 'Content-Type: text/plain; charset=UTF-8' ];
	foreach ( $notif['bcc'] ?? [] as $bcc ) {
		$headers[] = 'Bcc: ' . $bcc;
	}
	if ( $email && is_email( $email ) ) {
		$headers[] = 'Reply-To: ' . $name . ' <' . $email . '>';
	}
	$mail_ok = wp_mail( $recipients, $subject, $body, $headers, $signature_path ? [ $signature_path ] : [] );

	return new WP_REST_Response( [
		'ok'      => true,
		'stored'  => (bool) $post_id,
		'emailed' => (bool) $mail_ok,
	], 200 );
}
