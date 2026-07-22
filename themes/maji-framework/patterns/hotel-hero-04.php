<?php
/**
 * Title: Hero hôtel 04 — formulaire de réservation visible
 * Slug: maji/hotel-hero-04
 * Categories: maji-hotel
 * Viewport Width: 1400
 *
 * @package maji-framework
 */

$maji_hero_img = esc_url( get_template_directory_uri() . '/assets/img/ph-hero.svg' );
?>
<!-- wp:cover {"url":"<?php echo $maji_hero_img; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- échappé ci-dessus. ?>","dimRatio":60,"overlayColor":"contrast","minHeight":88,"minHeightUnit":"vh","align":"full","layout":{"type":"constrained"}} -->
<div class="wp-block-cover alignfull" style="min-height:88vh">
	<span aria-hidden="true" class="wp-block-cover__background has-contrast-background-color has-background-dim-60 has-background-dim"></span>
	<img class="wp-block-cover__image-background" alt="" src="<?php echo $maji_hero_img; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- échappé ci-dessus. ?>" data-maji-media="hero" data-object-fit="cover"/>
	<div class="wp-block-cover__inner-container">
		<!-- wp:columns {"verticalAlignment":"center","align":"wide"} -->
		<div class="wp-block-columns alignwide are-vertically-aligned-center">
			<!-- wp:column {"verticalAlignment":"center","width":"50%"} -->
			<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:50%">
				<!-- wp:heading {"level":1,"textColor":"base","fontSize":"3xl"} -->
				<h1 class="wp-block-heading has-base-color has-text-color has-3-xl-font-size">{{maji:identity.name}}</h1>
				<!-- /wp:heading -->
				<!-- wp:paragraph {"textColor":"base","fontSize":"lg"} -->
				<p class="has-base-color has-text-color has-lg-font-size">{{maji:content.texts.hero_tagline}}</p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:column -->
			<!-- wp:column {"verticalAlignment":"center","width":"50%"} -->
			<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:50%">
				<!-- wp:maji/reservation-form {"showRoomPicker":false} /-->
			</div>
			<!-- /wp:column -->
		</div>
		<!-- /wp:columns -->
	</div>
</div>
<!-- /wp:cover -->
