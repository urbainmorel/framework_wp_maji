<?php
/**
 * Title: Hero hôtel 01 — image plein écran, titre centré
 * Slug: maji/hotel-hero-01
 * Categories: maji-hotel
 * Viewport Width: 1400
 *
 * @package maji-framework
 */

$maji_hero_img = esc_url( get_template_directory_uri() . '/assets/img/ph-hero.svg' );
?>
<!-- wp:cover {"url":"<?php echo $maji_hero_img; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- échappé ci-dessus. ?>","dimRatio":50,"overlayColor":"contrast","minHeight":92,"minHeightUnit":"vh","align":"full","layout":{"type":"constrained"}} -->
<div class="wp-block-cover alignfull" style="min-height:92vh">
	<span aria-hidden="true" class="wp-block-cover__background has-contrast-background-color has-background-dim"></span>
	<img class="wp-block-cover__image-background" alt="" src="<?php echo $maji_hero_img; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- échappé ci-dessus. ?>" data-maji-media="hero" data-object-fit="cover"/>
	<div class="wp-block-cover__inner-container">
		<!-- wp:group {"layout":{"type":"constrained","contentSize":"820px"}} -->
		<div class="wp-block-group">
			<!-- wp:paragraph {"align":"center","textColor":"base","fontSize":"sm"} -->
			<p class="has-text-align-center has-base-color has-text-color has-sm-font-size">{{maji:content.facts.quartier}} · {{maji:identity.address.city}}</p>
			<!-- /wp:paragraph -->
			<!-- wp:heading {"textAlign":"center","level":1,"textColor":"base","fontSize":"3xl"} -->
			<h1 class="wp-block-heading has-text-align-center has-base-color has-text-color has-3-xl-font-size">{{maji:identity.name}}</h1>
			<!-- /wp:heading -->
			<!-- wp:paragraph {"align":"center","textColor":"base","fontSize":"lg"} -->
			<p class="has-text-align-center has-base-color has-text-color has-lg-font-size">{{maji:content.texts.hero_tagline}}</p>
			<!-- /wp:paragraph -->
			<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
			<div class="wp-block-buttons">
				<!-- wp:button -->
				<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="#reserver">Réserver un séjour</a></div>
				<!-- /wp:button -->
				<!-- wp:button {"className":"is-style-outline"} -->
				<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="#chambres">Découvrir les chambres</a></div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->
		</div>
		<!-- /wp:group -->
	</div>
</div>
<!-- /wp:cover -->
