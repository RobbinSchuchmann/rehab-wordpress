<?php
/**
 * Replace the contact form shortcode with a placeholder + email mailto fallback,
 * since Forminator may not be installed in the dev stack.
 */

$post = get_post( 1189 );
if ( ! $post ) {
	exit( 'no contact page' );
}

$contact_form_block = <<<'BLOCKS'
<!-- wp:rehab/contact-form {"heading":"Contact our admissions team","subheading":"Free, confidential, no obligation. We respond within an hour, 24/7.","shortcode":"<div style="text-align:center;padding:2rem 1rem;background:#fff;border:1px solid rgba(186,195,161,0.3);"><p style="margin:0 0 1rem;font-family:var(--rehab-font-display);font-size:1.5rem;">Email <a href="mailto:info@diamondrehabthailand.com">info@diamondrehabthailand.com</a></p><p style="margin:0 0 1.5rem;font-size:0.9375rem;">Or call our admissions team 24/7</p><a class="rehab-btn rehab-btn--luxury" href="tel:+66965823832">+66 96 582 3832</a></div>"} -->
<section class="wp-block-rehab-contact-form rehab-contact-form rehab-bg-cream"><div class="rehab-container rehab-container--narrow"><header class="rehab-contact-form__header"><h2 class="rehab-heading rehab-heading--lg">Contact our admissions team</h2><p class="rehab-contact-form__subheading">Free, confidential, no obligation. We respond within an hour, 24/7.</p></header><div class="rehab-contact-form__embed"><div style="text-align:center;padding:2rem 1rem;background:#fff;border:1px solid rgba(186,195,161,0.3);"><p style="margin:0 0 1rem;font-family:var(--rehab-font-display);font-size:1.5rem;">Email <a href="mailto:info@diamondrehabthailand.com">info@diamondrehabthailand.com</a></p><p style="margin:0 0 1.5rem;font-size:0.9375rem;">Or call our admissions team 24/7</p><a class="rehab-btn rehab-btn--luxury" href="tel:+66965823832">+66 96 582 3832</a></div></div></div></section>
<!-- /wp:rehab/contact-form -->
BLOCKS;

$updated = preg_replace(
	'/<!--\s*wp:rehab\/contact-form[^>]*?(?:\/-->|-->.*?<!--\s*\/wp:rehab\/contact-form\s*-->)/is',
	$contact_form_block,
	$post->post_content,
	1
);

wp_update_post( [ 'ID' => 1189, 'post_content' => $updated ] );
echo "OK contact form updated";
