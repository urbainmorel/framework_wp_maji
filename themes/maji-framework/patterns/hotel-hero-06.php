<?php
/**
 * Title: Hero hôtel 06 — bandeau bas et carte flottante
 * Slug: maji/hotel-hero-06
 * Categories: maji-hotel
 * Viewport Width: 1400
 *
 * @package maji-framework
 */

$maji_hero_img = esc_url( get_template_directory_uri() . '/assets/img/ph-hero.svg' );
?>
<!-- wp:cover {"url":"<?php echo $maji_hero_img; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- échappé ci-dessus. ?>","dimRatio":30,"overlayColor":"contrast","minHeight":70,"minHeightUnit":"vh","contentPosition":"bottom left","align":"full","layout":{"type":"constrained"}} -->
<div class="wp-block-cover alignfull has-custom-content-position is-position-bottom-left" style="min-height:70vh">
	<span aria-hidden="true" class="wp-block-cover__background has-contrast-background-color has-background-dim-30 has-background-dim"></span>
	<img class="wp-block-cover__image-background" alt="" src="<?php echo $maji_hero_img; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- échappé ci-dessus. ?>" data-maji-media="hero" data-object-fit="cover"/>
	<div class="wp-block-cover__inner-container">
		<!-- wp:group {"align":"wide","style":{"spacing":{"padding":{"bottom":"var:preset|spacing|50"}}},"layout":{"type":"constrained","justifyContent":"left","contentSize":"680px"}} -->
		<div class="wp-block-group alignwide" style="padding-bottom:var(--wp--preset--spacing--50)">
			<!-- wp:heading {"level":1,"textColor":"base","fontSize":"3xl"} -->
			<h1 class="wp-block-heading has-base-color has-text-color has-3-xl-font-size">{{maji:identity.name}}</h1>
			<!-- /wp:heading -->
		</div>
		<!-- /wp:group -->
	</div>
</div>
<!-- /wp:cover -->

<!-- wp:group {"align":"full","backgroundColor":"base","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-base-background-color has-background">
	<!-- wp:group {"align":"wide","backgroundColor":"surface","className":"maji-card--float","style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|50","right":"var:preset|spacing|50"},"margin":{"top":"calc(-1 * var(--wp--preset--spacing--60))"}},"border":{"radius":"var:custom|maji|radius|md"}},"layout":{"type":"flex","justifyContent":"space-between","flexWrap":"wrap"}} -->
	<div class="wp-block-group alignwide maji-card--float has-surface-background-color has-background" style="border-radius:var(--wp--custom--maji--radius--md);margin-top:calc(-1 * var(--wp--preset--spacing--60));padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--50)">
		<!-- wp:paragraph {"fontSize":"md"} -->
		<p class="has-md-font-size">{{maji:content.texts.hero_tagline}}</p>
		<!-- /wp:paragraph -->
		<!-- wp:buttons -->
		<div class="wp-block-buttons">
			<!-- wp:button -->
			<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="#reserver">Réserver un séjour</a></div>
			<!-- /wp:button -->
		</div>
		<!-- /wp:buttons -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
