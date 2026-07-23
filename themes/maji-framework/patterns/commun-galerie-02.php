<?php
/**
 * Title: Galerie 02 — bandeau pleine largeur
 * Slug: maji/commun-galerie-02
 * Categories: maji-commun
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
	<h2 class="wp-block-heading has-text-align-center has-2-xl-font-size">Un aperçu</h2>
	<!-- /wp:heading -->
	<!-- wp:columns {"align":"full","style":{"spacing":{"margin":{"top":"var:preset|spacing|50"},"blockGap":{"left":"var:preset|spacing|20"}}}} -->
	<div class="wp-block-columns alignfull" style="margin-top:var(--wp--preset--spacing--50)">
		<!-- wp:column {"width":"40%"} -->
		<div class="wp-block-column" style="flex-basis:40%">
			<!-- wp:image {"aspectRatio":"3/4","scale":"cover","sizeSlug":"large"} -->
			<figure class="wp-block-image size-large"><img src="<?php echo $maji_img_a; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- échappé ci-dessus. ?>" alt="" style="aspect-ratio:3/4;object-fit:cover" data-maji-media="gallery-1"/></figure>
			<!-- /wp:image -->
		</div>
		<!-- /wp:column -->
		<!-- wp:column {"width":"30%"} -->
		<div class="wp-block-column" style="flex-basis:30%">
			<!-- wp:image {"aspectRatio":"3/4","scale":"cover","sizeSlug":"large"} -->
			<figure class="wp-block-image size-large"><img src="<?php echo $maji_img_b; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- échappé ci-dessus. ?>" alt="" style="aspect-ratio:3/4;object-fit:cover" data-maji-media="gallery-2"/></figure>
			<!-- /wp:image -->
		</div>
		<!-- /wp:column -->
		<!-- wp:column {"width":"30%"} -->
		<div class="wp-block-column" style="flex-basis:30%">
			<!-- wp:image {"aspectRatio":"3/4","scale":"cover","sizeSlug":"large"} -->
			<figure class="wp-block-image size-large"><img src="<?php echo $maji_img_c; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- échappé ci-dessus. ?>" alt="" style="aspect-ratio:3/4;object-fit:cover" data-maji-media="gallery-3"/></figure>
			<!-- /wp:image -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->
