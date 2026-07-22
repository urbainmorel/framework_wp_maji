<?php
/**
 * Title: Hero hôtel 03 — éditorial asymétrique, titre débordant
 * Slug: maji/hotel-hero-03
 * Categories: maji-hotel
 * Viewport Width: 1400
 *
 * @package maji-framework
 */

$maji_hero_img = esc_url( get_template_directory_uri() . '/assets/img/ph-hero.svg' );
?>
<!-- wp:group {"align":"full","backgroundColor":"base","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|60"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-base-background-color has-background" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--60)">
	<!-- wp:group {"align":"wide","layout":{"type":"constrained","justifyContent":"left","contentSize":"900px"}} -->
	<div class="wp-block-group alignwide">
		<!-- wp:paragraph {"textColor":"accent","fontSize":"sm"} -->
		<p class="has-accent-color has-text-color has-sm-font-size">{{maji:content.facts.quartier}} — {{maji:identity.address.city}}</p>
		<!-- /wp:paragraph -->
		<!-- wp:heading {"level":1,"fontSize":"3xl"} -->
		<h1 class="wp-block-heading has-3-xl-font-size">{{maji:identity.name}}</h1>
		<!-- /wp:heading -->
	</div>
	<!-- /wp:group -->
	<!-- wp:group {"align":"wide","style":{"spacing":{"margin":{"top":"calc(-1 * var(--wp--preset--spacing--50))"}}},"layout":{"type":"flex","flexWrap":"wrap","justifyContent":"right","verticalAlignment":"bottom"}} -->
	<div class="wp-block-group alignwide" style="margin-top:calc(-1 * var(--wp--preset--spacing--50))">
		<!-- wp:image {"width":"64%","aspectRatio":"16/9","scale":"cover","sizeSlug":"large","style":{"border":{"radius":"var:custom|maji|radius|md"}}} -->
		<figure class="wp-block-image size-large has-custom-border" style="width:64%"><img src="<?php echo $maji_hero_img; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- échappé ci-dessus. ?>" alt="" style="border-radius:var(--wp--custom--maji--radius--md);aspect-ratio:16/9;object-fit:cover" data-maji-media="hero"/></figure>
		<!-- /wp:image -->
		<!-- wp:group {"backgroundColor":"surface","className":"maji-card","style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}},"border":{"radius":"var:custom|maji|radius|md"}},"layout":{"type":"constrained","contentSize":"320px"}} -->
		<div class="wp-block-group maji-card has-surface-background-color has-background" style="border-radius:var(--wp--custom--maji--radius--md);padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)">
			<!-- wp:paragraph {"fontSize":"md"} -->
			<p class="has-md-font-size">{{maji:content.texts.hero_tagline}}</p>
			<!-- /wp:paragraph -->
			<!-- wp:buttons -->
			<div class="wp-block-buttons">
				<!-- wp:button {"width":100} -->
				<div class="wp-block-button has-custom-width wp-block-button__width-100"><a class="wp-block-button__link wp-element-button" href="#reserver">Réserver un séjour</a></div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
