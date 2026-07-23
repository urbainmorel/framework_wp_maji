<?php
/**
 * Title: CTA 02 — split image
 * Slug: maji/commun-cta-02
 * Categories: maji-commun
 * Viewport Width: 1400
 *
 * @package maji-framework
 */

$maji_img = esc_url( get_template_directory_uri() . '/assets/img/ph-card.svg' );
?>
<!-- wp:group {"align":"full","backgroundColor":"primary","textColor":"primary-contrast","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-primary-contrast-color has-primary-background-color has-text-color has-background" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)">
	<!-- wp:columns {"verticalAlignment":"center","align":"wide"} -->
	<div class="wp-block-columns alignwide are-vertically-aligned-center">
		<!-- wp:column {"verticalAlignment":"center","width":"55%"} -->
		<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:55%">
			<!-- wp:heading {"level":2,"textColor":"primary-contrast","fontSize":"2xl"} -->
			<h2 class="wp-block-heading has-primary-contrast-color has-text-color has-2-xl-font-size">{{maji:content.texts.cta_titre}}</h2>
			<!-- /wp:heading -->
			<!-- wp:paragraph {"fontSize":"md"} -->
			<p class="has-md-font-size">Écrivez-nous sur WhatsApp, nous vous répondons rapidement.</p>
			<!-- /wp:paragraph -->
			<!-- wp:group {"layout":{"type":"flex","flexWrap":"wrap"}} -->
			<div class="wp-block-group">
				<!-- wp:maji/whatsapp-button {"context":"contact"} /-->
				<!-- wp:buttons -->
				<div class="wp-block-buttons">
					<!-- wp:button {"className":"is-style-outline"} -->
					<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="#reserver">Réserver</a></div>
					<!-- /wp:button -->
				</div>
				<!-- /wp:buttons -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:column -->
		<!-- wp:column {"width":"45%"} -->
		<div class="wp-block-column" style="flex-basis:45%">
			<!-- wp:image {"aspectRatio":"4/3","scale":"cover","sizeSlug":"large","style":{"border":{"radius":"var:custom|maji|radius|md"}}} -->
			<figure class="wp-block-image size-large has-custom-border"><img src="<?php echo $maji_img; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- échappé ci-dessus. ?>" alt="" style="border-radius:var(--wp--custom--maji--radius--md);aspect-ratio:4/3;object-fit:cover" data-maji-media="cta"/></figure>
			<!-- /wp:image -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->
