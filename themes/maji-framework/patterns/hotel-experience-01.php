<?php
/**
 * Title: Expérience 01 — activités à proximité
 * Slug: maji/hotel-experience-01
 * Categories: maji-hotel
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
	<!-- wp:group {"layout":{"type":"constrained","contentSize":"720px"}} -->
	<div class="wp-block-group">
		<!-- wp:heading {"textAlign":"center","level":2,"fontSize":"2xl"} -->
		<h2 class="wp-block-heading has-text-align-center has-2-xl-font-size">Vivre {{maji:identity.address.city}}</h2>
		<!-- /wp:heading -->
		<!-- wp:paragraph {"align":"center","textColor":"ink-muted"} -->
		<p class="has-text-align-center has-ink-muted-color has-text-color">À deux pas de chez nous, de quoi remplir votre séjour.</p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->
	<!-- wp:columns {"align":"wide","style":{"spacing":{"margin":{"top":"var:preset|spacing|50"}}}} -->
	<div class="wp-block-columns alignwide" style="margin-top:var(--wp--preset--spacing--50)">
		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:image {"aspectRatio":"4/3","scale":"cover","sizeSlug":"medium_large","style":{"border":{"radius":"var:custom|maji|radius|md"}}} -->
			<figure class="wp-block-image size-medium_large has-custom-border"><img src="<?php echo $maji_img_a; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- échappé ci-dessus. ?>" alt="" style="border-radius:var(--wp--custom--maji--radius--md);aspect-ratio:4/3;object-fit:cover" data-maji-media="experience-1"/></figure>
			<!-- /wp:image -->
			<!-- wp:heading {"level":3,"fontSize":"lg"} -->
			<h3 class="wp-block-heading has-lg-font-size">Explorer</h3>
			<!-- /wp:heading -->
			<!-- wp:paragraph {"textColor":"ink-muted","fontSize":"sm"} -->
			<p class="has-ink-muted-color has-text-color has-sm-font-size">Marchés, monuments et vie locale à découvrir.</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->
		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:image {"aspectRatio":"4/3","scale":"cover","sizeSlug":"medium_large","style":{"border":{"radius":"var:custom|maji|radius|md"}}} -->
			<figure class="wp-block-image size-medium_large has-custom-border"><img src="<?php echo $maji_img_b; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- échappé ci-dessus. ?>" alt="" style="border-radius:var(--wp--custom--maji--radius--md);aspect-ratio:4/3;object-fit:cover" data-maji-media="experience-2"/></figure>
			<!-- /wp:image -->
			<!-- wp:heading {"level":3,"fontSize":"lg"} -->
			<h3 class="wp-block-heading has-lg-font-size">Se détendre</h3>
			<!-- /wp:heading -->
			<!-- wp:paragraph {"textColor":"ink-muted","fontSize":"sm"} -->
			<p class="has-ink-muted-color has-text-color has-sm-font-size">Des espaces calmes pour souffler après la journée.</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->
		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:image {"aspectRatio":"4/3","scale":"cover","sizeSlug":"medium_large","style":{"border":{"radius":"var:custom|maji|radius|md"}}} -->
			<figure class="wp-block-image size-medium_large has-custom-border"><img src="<?php echo $maji_img_c; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- échappé ci-dessus. ?>" alt="" style="border-radius:var(--wp--custom--maji--radius--md);aspect-ratio:4/3;object-fit:cover" data-maji-media="experience-3"/></figure>
			<!-- /wp:image -->
			<!-- wp:heading {"level":3,"fontSize":"lg"} -->
			<h3 class="wp-block-heading has-lg-font-size">Savourer</h3>
			<!-- /wp:heading -->
			<!-- wp:paragraph {"textColor":"ink-muted","fontSize":"sm"} -->
			<p class="has-ink-muted-color has-text-color has-sm-font-size">Les meilleures tables et saveurs de la région.</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->
