<?php
/**
 * Title: Hero restaurant 02 — split avec plat détouré
 * Slug: maji/resto-hero-02
 * Categories: maji-restaurant
 * Viewport Width: 1400
 *
 * @package maji-framework
 */

$maji_dish_img = esc_url( get_template_directory_uri() . '/assets/img/ph-square.svg' );
?>
<!-- wp:group {"align":"full","backgroundColor":"primary","textColor":"primary-contrast","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-primary-contrast-color has-primary-background-color has-text-color has-background" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)">
	<!-- wp:columns {"verticalAlignment":"center","align":"wide"} -->
	<div class="wp-block-columns alignwide are-vertically-aligned-center">
		<!-- wp:column {"verticalAlignment":"center","width":"55%"} -->
		<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:55%">
			<!-- wp:paragraph {"fontSize":"sm"} -->
			<p class="has-sm-font-size">{{maji:content.facts.quartier}} · {{maji:identity.address.city}}</p>
			<!-- /wp:paragraph -->
			<!-- wp:heading {"level":1,"textColor":"primary-contrast","fontSize":"3xl"} -->
			<h1 class="wp-block-heading has-primary-contrast-color has-text-color has-3-xl-font-size">{{maji:identity.name}}</h1>
			<!-- /wp:heading -->
			<!-- wp:paragraph {"fontSize":"lg"} -->
			<p class="has-lg-font-size">{{maji:content.texts.hero_tagline}}</p>
			<!-- /wp:paragraph -->
			<!-- wp:buttons -->
			<div class="wp-block-buttons">
				<!-- wp:button -->
				<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="/commander">Commander</a></div>
				<!-- /wp:button -->
				<!-- wp:button {"className":"is-style-outline"} -->
				<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="#menu">Voir la carte</a></div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->
		</div>
		<!-- /wp:column -->
		<!-- wp:column {"verticalAlignment":"center","width":"45%"} -->
		<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:45%">
			<!-- wp:image {"aspectRatio":"1","scale":"cover","sizeSlug":"large","className":"is-style-rounded"} -->
			<figure class="wp-block-image size-large is-style-rounded"><img src="<?php echo $maji_dish_img; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- échappé ci-dessus. ?>" alt="" style="aspect-ratio:1;object-fit:cover" data-maji-media="hero"/></figure>
			<!-- /wp:image -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->
