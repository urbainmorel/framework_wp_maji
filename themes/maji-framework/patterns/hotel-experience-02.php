<?php
/**
 * Title: Expérience 02 — split éditorial
 * Slug: maji/hotel-experience-02
 * Categories: maji-hotel
 * Viewport Width: 1400
 *
 * @package maji-framework
 */

$maji_img = esc_url( get_template_directory_uri() . '/assets/img/ph-hero.svg' );
?>
<!-- wp:group {"align":"full","backgroundColor":"surface","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-surface-background-color has-background" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)">
	<!-- wp:columns {"verticalAlignment":"center","align":"wide"} -->
	<div class="wp-block-columns alignwide are-vertically-aligned-center">
		<!-- wp:column {"verticalAlignment":"center","width":"52%"} -->
		<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:52%">
			<!-- wp:paragraph {"textColor":"accent","fontSize":"sm"} -->
			<p class="has-accent-color has-text-color has-sm-font-size">{{maji:content.facts.quartier}}</p>
			<!-- /wp:paragraph -->
			<!-- wp:heading {"level":2,"fontSize":"2xl"} -->
			<h2 class="wp-block-heading has-2-xl-font-size">Votre séjour, autrement</h2>
			<!-- /wp:heading -->
			<!-- wp:paragraph {"fontSize":"md"} -->
			<p class="has-md-font-size">{{maji:content.texts.experience_texte}}</p>
			<!-- /wp:paragraph -->
			<!-- wp:buttons -->
			<div class="wp-block-buttons">
				<!-- wp:button {"className":"is-style-outline"} -->
				<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="#reserver">Réserver un séjour</a></div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->
		</div>
		<!-- /wp:column -->
		<!-- wp:column {"width":"48%"} -->
		<div class="wp-block-column" style="flex-basis:48%">
			<!-- wp:image {"aspectRatio":"3/4","scale":"cover","sizeSlug":"large","style":{"border":{"radius":"var:custom|maji|radius|md"}}} -->
			<figure class="wp-block-image size-large has-custom-border"><img src="<?php echo $maji_img; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- échappé ci-dessus. ?>" alt="" style="border-radius:var(--wp--custom--maji--radius--md);aspect-ratio:3/4;object-fit:cover" data-maji-media="experience"/></figure>
			<!-- /wp:image -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->
