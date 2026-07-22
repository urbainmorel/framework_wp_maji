<?php
/**
 * Title: Galerie 01 — mosaïque
 * Slug: maji/commun-galerie-01
 * Categories: maji-commun
 * Viewport Width: 1400
 *
 * @package maji-framework
 */

$maji_img_a = esc_url( get_template_directory_uri() . '/assets/img/ph-card.svg' );
$maji_img_b = esc_url( get_template_directory_uri() . '/assets/img/ph-square.svg' );
$maji_img_c = esc_url( get_template_directory_uri() . '/assets/img/ph-portrait.svg' );
$maji_img_d = esc_url( get_template_directory_uri() . '/assets/img/ph-card.svg' );
?>
<!-- wp:group {"align":"full","backgroundColor":"base","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-base-background-color has-background" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)">
	<!-- wp:heading {"textAlign":"center","level":2,"fontSize":"2xl"} -->
	<h2 class="wp-block-heading has-text-align-center has-2-xl-font-size">En images</h2>
	<!-- /wp:heading -->
	<!-- wp:gallery {"columns":4,"linkTo":"none","align":"wide","style":{"spacing":{"margin":{"top":"var:preset|spacing|50"}}}} -->
	<figure class="wp-block-gallery alignwide has-nested-images columns-4 is-cropped" style="margin-top:var(--wp--preset--spacing--50)">
		<!-- wp:image {"sizeSlug":"large","linkDestination":"none","lightbox":{"enabled":true}} -->
		<figure class="wp-block-image size-large"><img src="<?php echo $maji_img_a; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- échappé ci-dessus. ?>" alt="" data-maji-media="gallery-1"/></figure>
		<!-- /wp:image -->
		<!-- wp:image {"sizeSlug":"large","linkDestination":"none","lightbox":{"enabled":true}} -->
		<figure class="wp-block-image size-large"><img src="<?php echo $maji_img_b; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- échappé ci-dessus. ?>" alt="" data-maji-media="gallery-2"/></figure>
		<!-- /wp:image -->
		<!-- wp:image {"sizeSlug":"large","linkDestination":"none","lightbox":{"enabled":true}} -->
		<figure class="wp-block-image size-large"><img src="<?php echo $maji_img_c; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- échappé ci-dessus. ?>" alt="" data-maji-media="gallery-3"/></figure>
		<!-- /wp:image -->
		<!-- wp:image {"sizeSlug":"large","linkDestination":"none","lightbox":{"enabled":true}} -->
		<figure class="wp-block-image size-large"><img src="<?php echo $maji_img_d; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- échappé ci-dessus. ?>" alt="" data-maji-media="gallery-4"/></figure>
		<!-- /wp:image -->
	</figure>
	<!-- /wp:gallery -->
</div>
<!-- /wp:group -->
