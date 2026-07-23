<?php
/**
 * Title: Chambres 03 — chambre vedette
 * Slug: maji/hotel-chambres-03
 * Categories: maji-hotel
 * Viewport Width: 1400
 *
 * @package maji-framework
 */

$maji_img = esc_url( get_template_directory_uri() . '/assets/img/ph-hero.svg' );
?>
<!-- wp:group {"align":"full","backgroundColor":"base","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-base-background-color has-background" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)">
	<!-- wp:media-text {"align":"wide","mediaType":"image","mediaWidth":55,"verticalAlignment":"center"} -->
	<div class="wp-block-media-text alignwide is-vertically-aligned-center is-stacked-on-mobile" style="grid-template-columns:55% auto">
		<figure class="wp-block-media-text__media"><img src="<?php echo $maji_img; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- échappé ci-dessus. ?>" alt="" data-maji-media="chambre-1"/></figure>
		<div class="wp-block-media-text__content">
			<!-- wp:paragraph {"textColor":"accent","fontSize":"sm"} -->
			<p class="has-accent-color has-text-color has-sm-font-size">Notre chambre vedette</p>
			<!-- /wp:paragraph -->
			<!-- wp:heading {"level":2,"fontSize":"2xl"} -->
			<h2 class="wp-block-heading has-2-xl-font-size">{{maji:content.texts.chambre_1_nom}}</h2>
			<!-- /wp:heading -->
			<!-- wp:paragraph {"textColor":"ink-muted","fontSize":"md"} -->
			<p class="has-ink-muted-color has-text-color has-md-font-size">{{maji:content.texts.chambre_1_description}}</p>
			<!-- /wp:paragraph -->
			<!-- wp:buttons -->
			<div class="wp-block-buttons">
				<!-- wp:button -->
				<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="#reserver">Réserver cette chambre</a></div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->
		</div>
	</div>
	<!-- /wp:media-text -->
</div>
<!-- /wp:group -->
