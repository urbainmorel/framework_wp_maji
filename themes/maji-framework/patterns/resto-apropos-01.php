<?php
/**
 * Title: À propos 01 — notre histoire
 * Slug: maji/resto-apropos-01
 * Categories: maji-restaurant
 * Viewport Width: 1400
 *
 * @package maji-framework
 */

$maji_img = esc_url( get_template_directory_uri() . '/assets/img/ph-portrait.svg' );
?>
<!-- wp:group {"align":"full","backgroundColor":"base","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-base-background-color has-background" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)">
	<!-- wp:columns {"verticalAlignment":"center","align":"wide"} -->
	<div class="wp-block-columns alignwide are-vertically-aligned-center">
		<!-- wp:column {"width":"45%"} -->
		<div class="wp-block-column" style="flex-basis:45%">
			<!-- wp:image {"aspectRatio":"3/4","scale":"cover","sizeSlug":"large","style":{"border":{"radius":"var:custom|maji|radius|md"}}} -->
			<figure class="wp-block-image size-large has-custom-border"><img src="<?php echo $maji_img; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- échappé ci-dessus. ?>" alt="" style="border-radius:var(--wp--custom--maji--radius--md);aspect-ratio:3/4;object-fit:cover" data-maji-media="apropos"/></figure>
			<!-- /wp:image -->
		</div>
		<!-- /wp:column -->
		<!-- wp:column {"verticalAlignment":"center","width":"55%"} -->
		<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:55%">
			<!-- wp:paragraph {"textColor":"accent","fontSize":"sm"} -->
			<p class="has-accent-color has-text-color has-sm-font-size">Cuisine {{maji:content.facts.specialites}}</p>
			<!-- /wp:paragraph -->
			<!-- wp:heading {"level":2,"fontSize":"2xl"} -->
			<h2 class="wp-block-heading has-2-xl-font-size">Notre histoire</h2>
			<!-- /wp:heading -->
			<!-- wp:paragraph {"fontSize":"md"} -->
			<p class="has-md-font-size">{{maji:content.texts.apropos_texte}}</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->
