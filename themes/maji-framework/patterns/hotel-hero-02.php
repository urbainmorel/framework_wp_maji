<?php
/**
 * Title: Hero hôtel 02 — split texte gauche / image droite
 * Slug: maji/hotel-hero-02
 * Categories: maji-hotel
 * Viewport Width: 1400
 *
 * @package maji-framework
 */

$maji_hero_img = esc_url( get_template_directory_uri() . '/assets/img/ph-portrait.svg' );
?>
<!-- wp:group {"align":"full","backgroundColor":"base","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-base-background-color has-background" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)">
	<!-- wp:columns {"verticalAlignment":"center","align":"wide","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|60"}}}} -->
	<div class="wp-block-columns alignwide are-vertically-aligned-center">
		<!-- wp:column {"verticalAlignment":"center","width":"55%"} -->
		<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:55%">
			<!-- wp:paragraph {"textColor":"accent","fontSize":"sm"} -->
			<p class="has-accent-color has-text-color has-sm-font-size">Depuis {{maji:content.facts.annee}} à {{maji:identity.address.city}}</p>
			<!-- /wp:paragraph -->
			<!-- wp:heading {"level":1,"fontSize":"3xl"} -->
			<h1 class="wp-block-heading has-3-xl-font-size">{{maji:identity.name}}</h1>
			<!-- /wp:heading -->
			<!-- wp:paragraph {"fontSize":"lg","textColor":"ink-muted"} -->
			<p class="has-ink-muted-color has-text-color has-lg-font-size">{{maji:content.texts.hero_tagline}}</p>
			<!-- /wp:paragraph -->
			<!-- wp:buttons -->
			<div class="wp-block-buttons">
				<!-- wp:button -->
				<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="#reserver">Réserver un séjour</a></div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->
		</div>
		<!-- /wp:column -->
		<!-- wp:column {"verticalAlignment":"center","width":"45%"} -->
		<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:45%">
			<!-- wp:image {"aspectRatio":"3/4","scale":"cover","sizeSlug":"large","style":{"border":{"radius":"var:custom|maji|radius|lg"}}} -->
			<figure class="wp-block-image size-large has-custom-border"><img src="<?php echo $maji_hero_img; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- échappé ci-dessus. ?>" alt="" style="border-radius:var(--wp--custom--maji--radius--lg);aspect-ratio:3/4;object-fit:cover" data-maji-media="hero"/></figure>
			<!-- /wp:image -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->
