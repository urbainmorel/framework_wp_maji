<?php
/**
 * Title: Localisation 01 — adresse, carte et horaires
 * Slug: maji/commun-localisation-01
 * Categories: maji-commun
 * Viewport Width: 1400
 *
 * @package maji-framework
 */

$maji_map_img = esc_url( get_template_directory_uri() . '/assets/img/ph-card.svg' );
?>
<!-- wp:group {"align":"full","backgroundColor":"surface","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-surface-background-color has-background" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)">
	<!-- wp:heading {"level":2,"fontSize":"2xl"} -->
	<h2 class="wp-block-heading has-2-xl-font-size">Nous trouver</h2>
	<!-- /wp:heading -->
	<!-- wp:columns {"align":"wide","style":{"spacing":{"margin":{"top":"var:preset|spacing|50"}}}} -->
	<div class="wp-block-columns alignwide" style="margin-top:var(--wp--preset--spacing--50)">
		<!-- wp:column {"width":"55%"} -->
		<div class="wp-block-column" style="flex-basis:55%">
			<!-- wp:image {"aspectRatio":"4/3","scale":"cover","sizeSlug":"large","style":{"border":{"radius":"var:custom|maji|radius|md"}}} -->
			<figure class="wp-block-image size-large has-custom-border"><img src="<?php echo $maji_map_img; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- échappé ci-dessus. ?>" alt="Plan d'accès" style="border-radius:var(--wp--custom--maji--radius--md);aspect-ratio:4/3;object-fit:cover" data-maji-media="map"/></figure>
			<!-- /wp:image -->
		</div>
		<!-- /wp:column -->
		<!-- wp:column {"width":"45%"} -->
		<div class="wp-block-column" style="flex-basis:45%">
			<!-- wp:heading {"level":3,"fontSize":"lg"} -->
			<h3 class="wp-block-heading has-lg-font-size">Adresse</h3>
			<!-- /wp:heading -->
			<!-- wp:maji/establishment-info {"variant":"coordonnees"} /-->
			<!-- wp:heading {"level":3,"fontSize":"lg","style":{"spacing":{"margin":{"top":"var:preset|spacing|40"}}}} -->
			<h3 class="wp-block-heading has-lg-font-size" style="margin-top:var(--wp--preset--spacing--40)">Horaires</h3>
			<!-- /wp:heading -->
			<!-- wp:maji/opening-hours /-->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->
