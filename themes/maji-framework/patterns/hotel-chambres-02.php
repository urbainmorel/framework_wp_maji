<?php
/**
 * Title: Chambres 02 — liste éditoriale alternée
 * Slug: maji/hotel-chambres-02
 * Categories: maji-hotel
 * Viewport Width: 1400
 *
 * @package maji-framework
 */

$maji_img_a = esc_url( get_template_directory_uri() . '/assets/img/ph-card.svg' );
$maji_img_b = esc_url( get_template_directory_uri() . '/assets/img/ph-card.svg' );
?>
<!-- wp:group {"align":"full","backgroundColor":"surface","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-surface-background-color has-background" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)">
	<!-- wp:heading {"level":2,"fontSize":"2xl"} -->
	<h2 class="wp-block-heading has-2-xl-font-size">Séjourner chez nous</h2>
	<!-- /wp:heading -->
	<!-- wp:media-text {"align":"wide","mediaType":"image","mediaWidth":45,"style":{"spacing":{"margin":{"top":"var:preset|spacing|50"}}}} -->
	<div class="wp-block-media-text alignwide is-stacked-on-mobile" style="grid-template-columns:45% auto;margin-top:var(--wp--preset--spacing--50)">
		<figure class="wp-block-media-text__media"><img src="<?php echo $maji_img_a; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- échappé ci-dessus. ?>" alt="" data-maji-media="chambre-1"/></figure>
		<div class="wp-block-media-text__content">
			<!-- wp:heading {"level":3,"fontSize":"xl"} -->
			<h3 class="wp-block-heading has-xl-font-size">{{maji:content.texts.chambre_1_nom}}</h3>
			<!-- /wp:heading -->
			<!-- wp:paragraph {"textColor":"ink-muted"} -->
			<p class="has-ink-muted-color has-text-color">{{maji:content.texts.chambre_1_description}}</p>
			<!-- /wp:paragraph -->
			<!-- wp:buttons -->
			<div class="wp-block-buttons">
				<!-- wp:button {"className":"is-style-outline"} -->
				<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="/chambres">Découvrir</a></div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->
		</div>
	</div>
	<!-- /wp:media-text -->
	<!-- wp:media-text {"align":"wide","mediaType":"image","mediaWidth":45,"mediaPosition":"right","style":{"spacing":{"margin":{"top":"var:preset|spacing|50"}}}} -->
	<div class="wp-block-media-text alignwide has-media-on-the-right is-stacked-on-mobile" style="grid-template-columns:auto 45%;margin-top:var(--wp--preset--spacing--50)">
		<div class="wp-block-media-text__content">
			<!-- wp:heading {"level":3,"fontSize":"xl"} -->
			<h3 class="wp-block-heading has-xl-font-size">{{maji:content.texts.chambre_2_nom}}</h3>
			<!-- /wp:heading -->
			<!-- wp:paragraph {"textColor":"ink-muted"} -->
			<p class="has-ink-muted-color has-text-color">{{maji:content.texts.chambre_2_description}}</p>
			<!-- /wp:paragraph -->
			<!-- wp:buttons -->
			<div class="wp-block-buttons">
				<!-- wp:button {"className":"is-style-outline"} -->
				<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="/chambres">Découvrir</a></div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->
		</div>
		<figure class="wp-block-media-text__media"><img src="<?php echo $maji_img_b; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- échappé ci-dessus. ?>" alt="" data-maji-media="chambre-2"/></figure>
	</div>
	<!-- /wp:media-text -->
</div>
<!-- /wp:group -->
