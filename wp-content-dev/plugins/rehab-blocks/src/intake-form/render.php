<?php
/**
 * Server-side render for `rehab/intake-form`.
 *
 * Renders the multi-step intake wizard from assets/intake-spec.json (the
 * canonical field spec extracted from the legacy Forminator form 11745).
 * Field names, option values (including the legacy junk values conditions
 * depend on) and step structure are kept verbatim so submissions and
 * conditional logic behave exactly like the original form.
 *
 * Interactivity (step navigation, conditions, repeatable groups, signature
 * pad, submit to /wp-json/rehab/v1/intake) lives in view.js.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$rehab_intake_spec = null;
$rehab_intake_spec_file = REHAB_BLOCKS_PATH . 'assets/intake-spec.json';
if ( file_exists( $rehab_intake_spec_file ) ) {
	$rehab_intake_spec = json_decode( file_get_contents( $rehab_intake_spec_file ), true );
}
if ( ! is_array( $rehab_intake_spec ) || empty( $rehab_intake_spec['steps'] ) ) {
	echo '<!-- rehab/intake-form: spec missing -->';
	return;
}

$a = wp_parse_args( $attributes, [
	'anchorId' => 'intake',
	'heading'  => 'Intake form',
] );

$steps     = $rehab_intake_spec['steps'];
$countries = $rehab_intake_spec['countryList'] ?? [];
$years     = $rehab_intake_spec['dateYears'] ?? [ 'from' => 2126, 'to' => 1926 ];
$form_cfg  = $rehab_intake_spec['form'];

/** <select> for one date part. Years run from `from` to `to` in either direction
 *  (DOB descends into the past, passport expiry ascends into the future); each
 *  date element may override the global range via a `years` key (REH-129). */
$rehab_intake_date_select = static function ( array $sub, string $part, bool $required, array $range ): string {
	$opts = '<option value="">' . esc_html( 'Select ' . $part ) . '</option>';
	if ( $part === 'year' ) {
		$from = (int) $range['from'];
		$to   = (int) $range['to'];
		$step = $from <= $to ? 1 : -1;
		for ( $y = $from; $step > 0 ? $y <= $to : $y >= $to; $y += $step ) {
			$opts .= '<option value="' . $y . '">' . $y . '</option>';
		}
	} else {
		$max = $part === 'day' ? 31 : 12;
		for ( $i = 1; $i <= $max; $i++ ) {
			$opts .= '<option value="' . $i . '">' . $i . '</option>';
		}
	}
	$label = $sub['label'] ? '<span class="rehab-intake__sublabel">' . esc_html( $sub['label'] ) . '</span>' : '';
	return '<label class="rehab-intake__date-part">' . $label
		. '<select name="' . esc_attr( $sub['name'] ) . '" data-part="' . esc_attr( $part ) . '"' . ( $required ? ' data-req="1"' : '' ) . '>'
		. $opts . '</select></label>';
};

/** One field (recursion-free — groups call it per inner field). */
$rehab_intake_field = static function ( array $el ) use ( $countries, $rehab_intake_date_select ): string {
	$id       = $el['element_id'];
	$required = ! empty( $el['required'] );
	$label    = $el['label'] ?? null;
	$out      = '';

	$label_html = '';
	if ( $label !== null && $label !== '' ) {
		$label_html = '<label class="rehab-intake__label" for="rehab-intake-' . esc_attr( $id ) . '">'
			. esc_html( $label ) . ( $required ? ' <span class="rehab-intake__req" aria-hidden="true">*</span>' : '' )
			. '</label>';
	}
	$desc_html = ! empty( $el['description'] )
		? '<p class="rehab-intake__desc">' . esc_html( $el['description'] ) . '</p>'
		: '';

	switch ( $el['type'] ) {
		case 'name':
		case 'text':
		case 'phone':
			$input_type = $el['type'] === 'phone' ? 'tel' : 'text';
			$out = $label_html . $desc_html . '<input type="' . $input_type . '" id="rehab-intake-' . esc_attr( $id ) . '" name="' . esc_attr( $id ) . '"'
				. ' placeholder="' . esc_attr( $el['placeholder'] ?? '' ) . '"'
				. ( $required ? ' data-req="1" aria-required="true"' : '' )
				. ( $el['type'] === 'name' ? ' autocomplete="name"' : '' ) . '>';
			break;

		case 'email':
			$out = $label_html . $desc_html . '<input type="email" id="rehab-intake-' . esc_attr( $id ) . '" name="' . esc_attr( $id ) . '"'
				. ' placeholder="' . esc_attr( $el['placeholder'] ?? '' ) . '" autocomplete="email"'
				. ( $required ? ' data-req="1" aria-required="true"' : '' ) . '>';
			break;

		case 'number':
			$out = $label_html . $desc_html . '<input type="number" id="rehab-intake-' . esc_attr( $id ) . '" name="' . esc_attr( $id ) . '"'
				. ' placeholder="' . esc_attr( $el['placeholder'] ?? '' ) . '"'
				. ( isset( $el['min'] ) ? ' min="' . (int) $el['min'] . '"' : '' )
				. ( isset( $el['max'] ) ? ' max="' . (int) $el['max'] . '"' : '' )
				. ( $required ? ' data-req="1" aria-required="true"' : '' ) . '>';
			break;

		case 'textarea':
			$out = $label_html . $desc_html . '<textarea id="rehab-intake-' . esc_attr( $id ) . '" name="' . esc_attr( $id ) . '"'
				. ' placeholder="' . esc_attr( $el['placeholder'] ?? '' ) . '" rows="5"'
				. ( $required ? ' data-req="1" aria-required="true"' : '' ) . '></textarea>';
			break;

		case 'radio':
			$items = '';
			foreach ( $el['options'] as $i => $opt ) {
				$items .= '<label class="rehab-intake__radio"><input type="radio" name="' . esc_attr( $id ) . '"'
					. ' value="' . esc_attr( $opt['value'] ) . '"' . ( $required ? ' data-req="1"' : '' ) . '>'
					. '<span>' . esc_html( $opt['label'] ) . '</span></label>';
			}
			$out = ( $label !== null && $label !== ''
					? '<span class="rehab-intake__label" id="rehab-intake-' . esc_attr( $id ) . '-label">' . esc_html( $label )
						. ( $required ? ' <span class="rehab-intake__req" aria-hidden="true">*</span>' : '' ) . '</span>'
					: '' )
				. $desc_html
				. '<div class="rehab-intake__radios" role="radiogroup"'
				. ( $label ? ' aria-labelledby="rehab-intake-' . esc_attr( $id ) . '-label"' : '' ) . '>' . $items . '</div>';
			break;

		case 'select':
			$opts = '';
			foreach ( $el['options'] as $opt ) {
				$opts .= '<option value="' . esc_attr( $opt['value'] ) . '"' . ( ! empty( $opt['default'] ) ? ' selected' : '' ) . '>'
					. esc_html( $opt['label'] ) . '</option>';
			}
			$out = $label_html . $desc_html . '<select id="rehab-intake-' . esc_attr( $id ) . '" name="' . esc_attr( $id ) . '"'
				. ( $required ? ' data-req="1"' : '' ) . '>' . $opts . '</select>';
			break;

		case 'date':
			$parts = '';
			foreach ( $el['subfields'] as $sub ) {
				$parts .= $rehab_intake_date_select( $sub, $sub['key'], $required, $el['years'] ?? $years );
			}
			$out = $label_html . $desc_html
				. '<div class="rehab-intake__date" data-date-field="' . esc_attr( $id ) . '"'
				. ( ( $el['defaultDate'] ?? '' ) === 'today' ? ' data-default="today"' : '' ) . '>' . $parts . '</div>';
			break;

		case 'address':
			$rows = '';
			foreach ( $el['subfields'] as $sub ) {
				$sub_req   = ! empty( $sub['required'] );
				$sub_label = '<label class="rehab-intake__label" for="rehab-intake-' . esc_attr( $sub['name'] ) . '">' . esc_html( $sub['label'] )
					. ( $sub_req ? ' <span class="rehab-intake__req" aria-hidden="true">*</span>' : '' ) . '</label>';
				if ( ( $sub['optionsSource'] ?? '' ) === 'country-list' ) {
					$copts = '<option value="">' . esc_html( $sub['placeholder'] ?: 'Select country' ) . '</option>';
					foreach ( $countries as $country ) {
						$copts .= '<option value="' . esc_attr( $country ) . '"'
							. ( ( $sub['defaultValue'] ?? '' ) === $country ? ' selected' : '' ) . '>' . esc_html( $country ) . '</option>';
					}
					$control = '<select id="rehab-intake-' . esc_attr( $sub['name'] ) . '" name="' . esc_attr( $sub['name'] ) . '"'
						. ( $sub_req ? ' data-req="1"' : '' ) . '>' . $copts . '</select>';
				} else {
					$control = '<input type="text" id="rehab-intake-' . esc_attr( $sub['name'] ) . '" name="' . esc_attr( $sub['name'] ) . '"'
						. ' placeholder="' . esc_attr( $sub['placeholder'] ?? '' ) . '"' . ( $sub_req ? ' data-req="1"' : '' ) . '>';
				}
				$span = in_array( $sub['key'], [ 'street_address', 'address_line' ], true ) ? 12 : 6;
				$rows .= '<div class="rehab-intake__field is-cols-' . $span . '">' . $sub_label . $control . '</div>';
			}
			$out = $label_html . $desc_html . '<div class="rehab-intake__address rehab-intake__row">' . $rows . '</div>';
			break;

		case 'signature':
			$sig    = $el['signature'] ?? [];
			$height = (int) ( $sig['height'] ?? 180 );
			$out = $label_html . $desc_html
				. '<div class="rehab-intake__signature" data-signature="' . esc_attr( $id ) . '" data-thickness="' . (int) ( $sig['thickness'] ?? 2 ) . '">'
				. '<canvas height="' . $height . '" aria-label="' . esc_attr( $el['label'] ?? 'Signature' ) . ' drawing area"></canvas>'
				. '<input type="hidden" name="' . esc_attr( $id ) . '"' . ( $required ? ' data-req="1"' : '' ) . '>'
				. '<button type="button" class="rehab-intake__sig-clear">Clear signature</button>'
				. '</div>';
			break;

		default:
			$out = '<!-- rehab/intake-form: unhandled type ' . esc_html( $el['type'] ) . ' -->';
	}

	return $out . '<p class="rehab-intake__error" aria-live="polite"></p>';
};

$rehab_intake_cond_attr = static function ( ?array $conditions ): string {
	if ( empty( $conditions ) ) {
		return '';
	}
	return " data-cond='" . esc_attr( wp_json_encode( $conditions ) ) . "'";
};

// ---- markup ----

$wrapper = get_block_wrapper_attributes( [
	'class' => 'rehab-intake rehab-bg-cream',
	'id'    => sanitize_html_class( $a['anchorId'] ),
] );

ob_start();
?>
<section <?php echo $wrapper; // phpcs:ignore WordPress.Security.EscapeOutput ?>>
	<div class="rehab-intake__inner">
		<header class="rehab-intake__header">
			<h1 class="rehab-intake__heading"><?php echo esc_html( $a['heading'] ); ?></h1>
		</header>

		<form class="rehab-intake__form" data-rehab-intake novalidate
			data-thankyou="<?php echo esc_attr( wp_strip_all_tags( $form_cfg['thankYouMessage'] ?? 'Thank you!' ) ); ?>"
			data-invalid="<?php echo esc_attr( $form_cfg['invalidFormMessage'] ?? 'Please fix the errors above.' ); ?>">

			<div class="rehab-intake__progress-row">
				<span class="rehab-intake__progress-pct" data-progress-pct><?php echo (int) round( 100 / count( $steps ) ); ?>%</span>
				<div class="rehab-intake__progress" role="progressbar" aria-valuemin="1" aria-valuemax="<?php echo count( $steps ); ?>" aria-valuenow="1" aria-label="Form progress">
					<div class="rehab-intake__progress-fill" style="width:<?php echo esc_attr( round( 100 / count( $steps ), 2 ) ); ?>%"></div>
				</div>
			</div>

			<?php foreach ( $steps as $s_i => $step ) : ?>
			<fieldset class="rehab-intake__step" data-step="<?php echo (int) $s_i; ?>" <?php echo $s_i > 0 ? 'hidden' : ''; ?>>
				<?php
				// Group elements into rendered rows, preserving order.
				$rows = [];
				foreach ( $step['elements'] as $el ) {
					$rows[ $el['row'] ][] = $el;
				}
				foreach ( $rows as $row_els ) {
					$row_html = '';
					foreach ( $row_els as $el ) {
						switch ( $el['kind'] ) {
							case 'section':
								$row_html .= '<div class="rehab-intake__section is-cols-12' . ( ! empty( $el['customClass'] ) ? ' ' . esc_attr( $el['customClass'] ) : '' ) . '">'
									. '<h2>' . esc_html( $el['title'] ) . '</h2>'
									. ( ! empty( $el['subtitle'] ) ? '<p>' . esc_html( $el['subtitle'] ) . '</p>' : '' )
									. '</div>';
								break;

							case 'html':
								if ( ! empty( $el['collapseToggle'] ) ) {
									break; // legacy Forminator collapse decorations — intentionally not reproduced
								}
								$row_html .= '<div class="rehab-intake__content is-cols-12">'
									. ( ! empty( $el['label'] ) ? '<h3>' . esc_html( $el['label'] ) . '</h3>' : '' )
									. wp_kses_post( $el['html'] ?? '' )
									. '</div>';
								break;

							case 'group':
								$inner = '';
								$g_rows = [];
								foreach ( $el['fields'] as $gf ) {
									$g_rows[ $gf['row'] ][] = $gf;
								}
								foreach ( $g_rows as $g_row ) {
									$inner .= '<div class="rehab-intake__row">';
									foreach ( $g_row as $gf ) {
										// Repeatable-group inputs post as arrays.
										if ( ! empty( $el['repeatable'] ) ) {
											$gf['element_id'] .= '[]';
										}
										$inner .= '<div class="rehab-intake__field is-cols-' . (int) ( $gf['cols'] ?: 12 ) . '">'
											. $rehab_intake_field( $gf ) . '</div>';
									}
									$inner .= '</div>';
								}
								$head = '<h3 class="rehab-intake__group-title">' . esc_html( $el['label'] ) . '</h3>'
									. ( ! empty( $el['description'] ) ? '<p class="rehab-intake__desc">' . esc_html( $el['description'] ) . '</p>' : '' );
								if ( ! empty( $el['repeatable'] ) ) {
									$row_html .= '<div class="rehab-intake__group is-cols-12" data-repeat-group="' . esc_attr( $el['element_id'] ) . '"' . $rehab_intake_cond_attr( $el['conditions'] ?? null ) . '>'
										. $head
										. '<div class="rehab-intake__group-items">'
										. '<div class="rehab-intake__group-item">' . $inner
										. '<button type="button" class="rehab-intake__group-remove" hidden>' . esc_html( $el['removeButtonText'] ?? 'Remove' ) . '</button>'
										. '</div></div>'
										. '<template>' . '<div class="rehab-intake__group-item">' . $inner
										. '<button type="button" class="rehab-intake__group-remove">' . esc_html( $el['removeButtonText'] ?? 'Remove' ) . '</button>'
										. '</div>' . '</template>'
										. '<button type="button" class="rehab-intake__group-add">' . esc_html( $el['addButtonText'] ?? 'Add' ) . '</button>'
										. '</div>';
								} else {
									$row_html .= '<div class="rehab-intake__group is-cols-12"' . $rehab_intake_cond_attr( $el['conditions'] ?? null ) . '>'
										. $head . $inner . '</div>';
								}
								break;

							case 'field':
								$row_html .= '<div class="rehab-intake__field is-cols-' . (int) ( $el['cols'] ?: 12 ) . '"'
									. ' data-field="' . esc_attr( $el['element_id'] ) . '"'
									. $rehab_intake_cond_attr( $el['conditions'] ?? null ) . '>'
									. $rehab_intake_field( $el )
									. '</div>';
								break;
						}
					}
					if ( $row_html !== '' ) {
						echo '<div class="rehab-intake__row">' . $row_html . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput
					}
				}
				?>
				<div class="rehab-intake__nav">
					<?php if ( $s_i > 0 ) : ?>
						<button type="button" class="rehab-intake__prev">Previous</button>
					<?php endif; ?>
					<?php if ( $s_i < count( $steps ) - 1 ) : ?>
						<button type="button" class="rehab-intake__next">Next</button>
					<?php else : ?>
						<button type="submit" class="rehab-intake__submit"><?php echo esc_html( $form_cfg['submitText'] ?? 'Submit' ); ?></button>
					<?php endif; ?>
				</div>
			</fieldset>
			<?php endforeach; ?>

			<?php // Honeypot — same control name as the legacy form; hidden from real users. ?>
			<input class="rehab-intake__hp" type="text" name="<?php echo esc_attr( $form_cfg['honeypotControl'] ?? 'input_59' ); ?>" value="" autocomplete="off" tabindex="-1" aria-hidden="true">

			<p class="rehab-intake__status" role="status" aria-live="polite"></p>
		</form>

		<div class="rehab-intake__contact">
			<a class="rehab-intake__contact-link" href="https://wa.me/66965823832">
				<svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor" aria-hidden="true"><path d="M12.04 2c-5.5 0-9.96 4.46-9.96 9.96 0 1.76.46 3.47 1.34 4.98L2 22l5.19-1.36a9.9 9.9 0 0 0 4.85 1.24h.01c5.5 0 9.96-4.46 9.96-9.96S17.54 2 12.04 2zm0 18.2h-.01a8.2 8.2 0 0 1-4.18-1.15l-.3-.18-3.08.81.82-3-.19-.31a8.24 8.24 0 0 1-1.26-4.39c0-4.55 3.7-8.25 8.25-8.25 2.2 0 4.28.86 5.83 2.42a8.19 8.19 0 0 1 2.41 5.84c0 4.55-3.7 8.24-8.24 8.24zm4.52-6.17c-.25-.12-1.47-.72-1.69-.81-.23-.08-.39-.12-.56.13-.16.24-.64.8-.78.97-.14.16-.29.18-.54.06-.25-.12-1.05-.39-1.99-1.23-.74-.66-1.23-1.47-1.38-1.72-.14-.25-.02-.38.11-.5.11-.11.25-.29.37-.43.13-.14.16-.25.25-.41.08-.16.04-.31-.02-.43-.06-.12-.56-1.34-.76-1.84-.2-.48-.4-.42-.56-.42l-.48-.01c-.16 0-.43.06-.66.31-.23.24-.87.85-.87 2.07 0 1.22.89 2.4 1.01 2.56.12.16 1.75 2.67 4.25 3.74.59.26 1.06.41 1.42.52.6.19 1.14.16 1.57.1.48-.07 1.47-.6 1.68-1.18.21-.58.21-1.07.14-1.18-.06-.11-.22-.17-.47-.29z"/></svg>
				<span>WhatsApp</span>
			</a>
			<a class="rehab-intake__contact-link" href="mailto:info@diamondrehabthailand.com">
				<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><polyline points="3 7 12 13 21 7"/></svg>
				<span>Email</span>
			</a>
		</div>
	</div>
</section>
<?php
echo ob_get_clean(); // phpcs:ignore WordPress.Security.EscapeOutput
