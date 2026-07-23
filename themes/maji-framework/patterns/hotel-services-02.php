<?php
/**
 * Title: Services hôtel 02 — liste média alternée
 * Slug: maji/hotel-services-02
 * Categories: maji-hotel
 * Viewport Width: 1400
 *
 * @package maji-framework
 */

$maji_img_a = esc_url( get_template_directory_uri() . '/assets/img/ph-card.svg' );
$maji_img_b = esc_url( get_template_directory_uri() . '/assets/img/ph-card.svg' );
$maji_img_c = esc_url( get_template_directory_uri() . '/assets/img/ph-card.svg' );
?>
<!-- wp:group {"align":"full","backgroundColor":"base","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-base-background-color has-background" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)">
	<!-- wp:heading {"textAlign":"center","level":2,"fontSize":"2xl"} -->
	<h2 class="wp-block-heading has-text-align-center has-2-xl-font-size">Nos services</h2>
	<!-- /wp:heading -->
	<!-- wp:media-text {"align":"wide","mediaType":"image","mediaWidth":40,"verticalAlignment":"center","style":{"spacing":{"margin":{"top":"var:preset|spacing|50"}}}} -->
	<div class="wp-block-media-text alignwide is-vertically-aligned-center is-stacked-on-mobile" style="grid-template-columns:40% auto;margin-top:var(--wp--preset--spacing--50)">
		<figure class="wp-block-media-text__media"><img src="<?php echo $maji_img_a; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- échappé ci-dessus. ?>" alt="" data-maji-media="service-1"/></figure>
		<div class="wp-block-media-text__content">
			<!-- wp:heading {"level":3,"fontSize":"xl"} -->
			<h3 class="wp-block-heading has-xl-font-size">Restaurant</h3>
			<!-- /wp:heading -->
			<!-- wp:paragraph {"textColor":"ink-muted"} -->
			<p class="has-ink-muted-color has-text-color">{{maji:content.texts.service_1_description}}</p>
			<!-- /wp:paragraph -->
		</div>
	</div>
	<!-- /wp:media-text -->
	<!-- wp:media-text {"align":"wide","mediaType":"image","mediaWidth":40,"mediaPosition":"right","verticalAlignment":"center","style":{"spacing":{"margin":{"top":"var:preset|spacing|40"}}}} -->
	<div class="wp-block-media-text alignwide has-media-on-the-right is-vertically-aligned-center is-stacked-on-mobile" style="grid-template-columns:auto 40%;margin-top:var(--wp--preset--spacing--40)">
		<div class="wp-block-media-text__content">
			<!-- wp:heading {"level":3,"fontSize":"xl"} -->
			<h3 class="wp-block-heading has-xl-font-size">Salle de réunion</h3>
			<!-- /wp:heading -->
			<!-- wp:paragraph {"textColor":"ink-muted"} -->
			<p class="has-ink-muted-color has-text-color">{{maji:content.texts.service_2_description}}</p>
			<!-- /wp:paragraph -->
		</div>
		<figure class="wp-block-media-text__media"><img src="<?php echo $maji_img_b; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- échappé ci-dessus. ?>" alt="" data-maji-media="service-2"/></figure>
	</div>
	<!-- /wp:media-text -->
	<!-- wp:media-text {"align":"wide","mediaType":"image","mediaWidth":40,"verticalAlignment":"center","style":{"spacing":{"margin":{"top":"var:preset|spacing|40"}}}} -->
	<div class="wp-block-media-text alignwide is-vertically-aligned-center is-stacked-on-mobile" style="grid-template-columns:40% auto;margin-top:var(--wp--preset--spacing--40)">
		<figure class="wp-block-media-text__media"><img src="<?php echo $maji_img_c; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- échappé ci-dessus. ?>" alt="" data-maji-media="service-3"/></figure>
		<div class="wp-block-media-text__content">
			<!-- wp:heading {"level":3,"fontSize":"xl"} -->
			<h3 class="wp-block-heading has-xl-font-size">Événements</h3>
			<!-- /wp:heading -->
			<!-- wp:paragraph {"textColor":"ink-muted"} -->
			<p class="has-ink-muted-color has-text-color">{{maji:content.texts.service_3_description}}</p>
			<!-- /wp:paragraph -->
		</div>
	</div>
	<!-- /wp:media-text -->
</div>
<!-- /wp:group -->
