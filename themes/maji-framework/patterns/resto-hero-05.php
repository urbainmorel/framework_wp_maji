<?php
/**
 * Title: Hero restaurant 05 — ambiance immersive
 * Slug: maji/resto-hero-05
 * Categories: maji-restaurant
 * Viewport Width: 1400
 *
 * @package maji-framework
 */

$maji_hero_img = esc_url( get_template_directory_uri() . '/assets/img/ph-hero.svg' );
?>
<!-- wp:cover {"url":"<?php echo $maji_hero_img; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- échappé ci-dessus. ?>","dimRatio":40,"overlayColor":"contrast","minHeight":100,"minHeightUnit":"vh","hasParallax":true,"align":"full","layout":{"type":"constrained"}} -->
<div class="wp-block-cover alignfull has-parallax" style="min-height:100vh">
	<span aria-hidden="true" class="wp-block-cover__background has-contrast-background-color has-background-dim-40 has-background-dim"></span>
	<div class="wp-block-cover__image-background has-parallax" style="background-position:50% 50%;background-image:url(<?php echo $maji_hero_img; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- échappé ci-dessus. ?>)" data-maji-media="hero"></div>
	<div class="wp-block-cover__inner-container">
		<!-- wp:group {"layout":{"type":"constrained","contentSize":"680px"}} -->
		<div class="wp-block-group">
			<!-- wp:paragraph {"align":"center","textColor":"base","fontSize":"sm"} -->
			<p class="has-text-align-center has-base-color has-text-color has-sm-font-size">Depuis {{maji:content.facts.annee}}</p>
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
				<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="#menu">Découvrir la carte</a></div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->
		</div>
		<!-- /wp:group -->
	</div>
</div>
<!-- /wp:cover -->
