<?php
/**
 * Title: Hero hôtel 09 — minimal, logo centré
 * Slug: maji/hotel-hero-09
 * Categories: maji-hotel
 * Viewport Width: 1400
 *
 * @package maji-framework
 */

$maji_mark = esc_url( get_template_directory_uri() . '/assets/img/ph-square.svg' );
?>
<!-- wp:group {"align":"full","backgroundColor":"surface","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-surface-background-color has-background" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)">
	<!-- wp:group {"layout":{"type":"constrained","contentSize":"640px"}} -->
	<div class="wp-block-group">
		<!-- wp:image {"width":"96px","height":"96px","scale":"cover","sizeSlug":"thumbnail","align":"center","style":{"border":{"radius":"var:custom|maji|radius|pill"}}} -->
		<figure class="wp-block-image aligncenter size-thumbnail is-resized has-custom-border"><img src="<?php echo $maji_mark; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- échappé ci-dessus. ?>" alt="" style="border-radius:var(--wp--custom--maji--radius--pill);object-fit:cover;width:96px;height:96px" data-maji-media="hero"/></figure>
		<!-- /wp:image -->
		<!-- wp:heading {"textAlign":"center","level":1,"fontSize":"3xl"} -->
		<h1 class="wp-block-heading has-text-align-center has-3-xl-font-size">{{maji:identity.name}}</h1>
		<!-- /wp:heading -->
		<!-- wp:paragraph {"align":"center","textColor":"ink-muted","fontSize":"lg"} -->
		<p class="has-text-align-center has-ink-muted-color has-text-color has-lg-font-size">{{maji:content.texts.hero_tagline}}</p>
		<!-- /wp:paragraph -->
		<!-- wp:paragraph {"align":"center","textColor":"accent","fontSize":"sm"} -->
		<p class="has-text-align-center has-accent-color has-text-color has-sm-font-size">{{maji:content.facts.quartier}} — {{maji:identity.address.city}}</p>
		<!-- /wp:paragraph -->
		<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
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
