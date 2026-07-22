<?php
/**
 * Title: Services hôtel 01 — cartes
 * Slug: maji/hotel-services-01
 * Categories: maji-hotel
 * Viewport Width: 1400
 *
 * @package maji-framework
 */

$maji_img = esc_url( get_template_directory_uri() . '/assets/img/ph-card.svg' );
?>
<!-- wp:group {"align":"full","backgroundColor":"base","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-base-background-color has-background" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)">
	<!-- wp:heading {"textAlign":"center","level":2,"fontSize":"2xl"} -->
	<h2 class="wp-block-heading has-text-align-center has-2-xl-font-size">Vivre {{maji:identity.name}}</h2>
	<!-- /wp:heading -->
	<!-- wp:columns {"align":"wide","style":{"spacing":{"margin":{"top":"var:preset|spacing|50"}}}} -->
	<div class="wp-block-columns alignwide" style="margin-top:var(--wp--preset--spacing--50)">
		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:group {"backgroundColor":"surface","className":"maji-card","style":{"border":{"radius":"var:custom|maji|radius|md"},"spacing":{"padding":{"top":"0","bottom":"var:preset|spacing|40","left":"0","right":"0"},"blockGap":"var:preset|spacing|30"}},"layout":{"type":"constrained"}} -->
			<div class="wp-block-group maji-card has-surface-background-color has-background" style="border-radius:var(--wp--custom--maji--radius--md);padding-top:0;padding-right:0;padding-bottom:var(--wp--preset--spacing--40);padding-left:0">
				<!-- wp:image {"aspectRatio":"16/9","scale":"cover","sizeSlug":"medium_large"} -->
				<figure class="wp-block-image size-medium_large"><img src="<?php echo $maji_img; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- échappé ci-dessus. ?>" alt="" style="aspect-ratio:16/9;object-fit:cover" data-maji-media="service-1"/></figure>
				<!-- /wp:image -->
				<!-- wp:heading {"textAlign":"center","level":3,"fontSize":"lg"} -->
				<h3 class="wp-block-heading has-text-align-center has-lg-font-size">Restaurant</h3>
				<!-- /wp:heading -->
				<!-- wp:paragraph {"align":"center","textColor":"ink-muted","fontSize":"sm"} -->
				<p class="has-text-align-center has-ink-muted-color has-text-color has-sm-font-size">{{maji:content.texts.service_1_description}}</p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:column -->
		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:group {"backgroundColor":"surface","className":"maji-card","style":{"border":{"radius":"var:custom|maji|radius|md"},"spacing":{"padding":{"top":"0","bottom":"var:preset|spacing|40","left":"0","right":"0"},"blockGap":"var:preset|spacing|30"}},"layout":{"type":"constrained"}} -->
			<div class="wp-block-group maji-card has-surface-background-color has-background" style="border-radius:var(--wp--custom--maji--radius--md);padding-top:0;padding-right:0;padding-bottom:var(--wp--preset--spacing--40);padding-left:0">
				<!-- wp:image {"aspectRatio":"16/9","scale":"cover","sizeSlug":"medium_large"} -->
				<figure class="wp-block-image size-medium_large"><img src="<?php echo $maji_img; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- échappé ci-dessus. ?>" alt="" style="aspect-ratio:16/9;object-fit:cover" data-maji-media="service-2"/></figure>
				<!-- /wp:image -->
				<!-- wp:heading {"textAlign":"center","level":3,"fontSize":"lg"} -->
				<h3 class="wp-block-heading has-text-align-center has-lg-font-size">Salle de réunion</h3>
				<!-- /wp:heading -->
				<!-- wp:paragraph {"align":"center","textColor":"ink-muted","fontSize":"sm"} -->
				<p class="has-text-align-center has-ink-muted-color has-text-color has-sm-font-size">{{maji:content.texts.service_2_description}}</p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:column -->
		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:group {"backgroundColor":"surface","className":"maji-card","style":{"border":{"radius":"var:custom|maji|radius|md"},"spacing":{"padding":{"top":"0","bottom":"var:preset|spacing|40","left":"0","right":"0"},"blockGap":"var:preset|spacing|30"}},"layout":{"type":"constrained"}} -->
			<div class="wp-block-group maji-card has-surface-background-color has-background" style="border-radius:var(--wp--custom--maji--radius--md);padding-top:0;padding-right:0;padding-bottom:var(--wp--preset--spacing--40);padding-left:0">
				<!-- wp:image {"aspectRatio":"16/9","scale":"cover","sizeSlug":"medium_large"} -->
				<figure class="wp-block-image size-medium_large"><img src="<?php echo $maji_img; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- échappé ci-dessus. ?>" alt="" style="aspect-ratio:16/9;object-fit:cover" data-maji-media="service-3"/></figure>
				<!-- /wp:image -->
				<!-- wp:heading {"textAlign":"center","level":3,"fontSize":"lg"} -->
				<h3 class="wp-block-heading has-text-align-center has-lg-font-size">Événements</h3>
				<!-- /wp:heading -->
				<!-- wp:paragraph {"align":"center","textColor":"ink-muted","fontSize":"sm"} -->
				<p class="has-text-align-center has-ink-muted-color has-text-color has-sm-font-size">{{maji:content.texts.service_3_description}}</p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->
