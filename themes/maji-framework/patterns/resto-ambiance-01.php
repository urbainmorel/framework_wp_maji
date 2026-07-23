<?php
/**
 * Title: Ambiance 01 — dans notre salle
 * Slug: maji/resto-ambiance-01
 * Categories: maji-restaurant
 * Viewport Width: 1400
 *
 * @package maji-framework
 */

$maji_img_a = esc_url( get_template_directory_uri() . '/assets/img/ph-card.svg' );
$maji_img_b = esc_url( get_template_directory_uri() . '/assets/img/ph-square.svg' );
?>
<!-- wp:group {"align":"full","backgroundColor":"surface","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-surface-background-color has-background" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)">
	<!-- wp:group {"layout":{"type":"constrained","contentSize":"640px"}} -->
	<div class="wp-block-group">
		<!-- wp:heading {"textAlign":"center","level":2,"fontSize":"2xl"} -->
		<h2 class="wp-block-heading has-text-align-center has-2-xl-font-size">Dans notre salle</h2>
		<!-- /wp:heading -->
		<!-- wp:paragraph {"align":"center","textColor":"ink-muted"} -->
		<p class="has-text-align-center has-ink-muted-color has-text-color">Une cuisine {{maji:content.facts.specialites}} à partager, dans un cadre chaleureux à {{maji:identity.address.city}}.</p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->
	<!-- wp:columns {"align":"wide","style":{"spacing":{"margin":{"top":"var:preset|spacing|50"}}}} -->
	<div class="wp-block-columns alignwide" style="margin-top:var(--wp--preset--spacing--50)">
		<!-- wp:column {"width":"58%"} -->
		<div class="wp-block-column" style="flex-basis:58%">
			<!-- wp:image {"aspectRatio":"16/9","scale":"cover","sizeSlug":"large","style":{"border":{"radius":"var:custom|maji|radius|md"}}} -->
			<figure class="wp-block-image size-large has-custom-border"><img src="<?php echo $maji_img_a; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- échappé ci-dessus. ?>" alt="" style="border-radius:var(--wp--custom--maji--radius--md);aspect-ratio:16/9;object-fit:cover" data-maji-media="gallery-1"/></figure>
			<!-- /wp:image -->
		</div>
		<!-- /wp:column -->
		<!-- wp:column {"width":"42%"} -->
		<div class="wp-block-column" style="flex-basis:42%">
			<!-- wp:image {"aspectRatio":"1","scale":"cover","sizeSlug":"large","style":{"border":{"radius":"var:custom|maji|radius|md"}}} -->
			<figure class="wp-block-image size-large has-custom-border"><img src="<?php echo $maji_img_b; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- échappé ci-dessus. ?>" alt="" style="border-radius:var(--wp--custom--maji--radius--md);aspect-ratio:1;object-fit:cover" data-maji-media="gallery-2"/></figure>
			<!-- /wp:image -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->
