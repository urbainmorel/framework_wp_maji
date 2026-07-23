<?php
/**
 * Title: Hero restaurant 10 — bandeau ambiance sombre
 * Slug: maji/resto-hero-10
 * Categories: maji-restaurant
 * Viewport Width: 1400
 *
 * @package maji-framework
 */

$maji_hero_img = esc_url( get_template_directory_uri() . '/assets/img/ph-hero.svg' );
?>
<!-- wp:cover {"url":"<?php echo $maji_hero_img; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- échappé ci-dessus. ?>","dimRatio":70,"overlayColor":"contrast","minHeight":76,"minHeightUnit":"vh","contentPosition":"bottom left","align":"full","layout":{"type":"constrained"}} -->
<div class="wp-block-cover alignfull has-custom-content-position is-position-bottom-left" style="min-height:76vh">
	<span aria-hidden="true" class="wp-block-cover__background has-contrast-background-color has-background-dim-70 has-background-dim"></span>
	<img class="wp-block-cover__image-background" alt="" src="<?php echo $maji_hero_img; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- échappé ci-dessus. ?>" data-maji-media="hero" data-object-fit="cover"/>
	<div class="wp-block-cover__inner-container">
		<!-- wp:group {"align":"wide","layout":{"type":"constrained","justifyContent":"left","contentSize":"680px"}} -->
		<div class="wp-block-group alignwide">
			<!-- wp:paragraph {"textColor":"base","fontSize":"sm"} -->
			<p class="has-base-color has-text-color has-sm-font-size">Cuisine {{maji:content.facts.specialites}}</p>
			<!-- /wp:paragraph -->
			<!-- wp:heading {"level":1,"textColor":"base","fontSize":"3xl"} -->
			<h1 class="wp-block-heading has-base-color has-text-color has-3-xl-font-size">{{maji:identity.name}}</h1>
			<!-- /wp:heading -->
			<!-- wp:paragraph {"textColor":"base","fontSize":"lg"} -->
			<p class="has-base-color has-text-color has-lg-font-size">{{maji:content.texts.hero_tagline}}</p>
			<!-- /wp:paragraph -->
			<!-- wp:buttons -->
			<div class="wp-block-buttons">
				<!-- wp:button -->
				<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="/commander">Commander</a></div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->
		</div>
		<!-- /wp:group -->
	</div>
</div>
<!-- /wp:cover -->
