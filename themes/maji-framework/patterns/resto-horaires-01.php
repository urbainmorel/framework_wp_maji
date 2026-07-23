<?php
/**
 * Title: Horaires 01 — horaires et services
 * Slug: maji/resto-horaires-01
 * Categories: maji-restaurant
 * Viewport Width: 1200
 *
 * @package maji-framework
 */

?>
<!-- wp:group {"align":"full","backgroundColor":"base","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-base-background-color has-background" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)">
	<!-- wp:columns {"align":"wide"} -->
	<div class="wp-block-columns alignwide">
		<!-- wp:column {"width":"50%"} -->
		<div class="wp-block-column" style="flex-basis:50%">
			<!-- wp:heading {"level":2,"fontSize":"xl"} -->
			<h2 class="wp-block-heading has-xl-font-size">Horaires d'ouverture</h2>
			<!-- /wp:heading -->
			<!-- wp:maji/opening-hours /-->
		</div>
		<!-- /wp:column -->
		<!-- wp:column {"width":"50%"} -->
		<div class="wp-block-column" style="flex-basis:50%">
			<!-- wp:heading {"level":2,"fontSize":"xl"} -->
			<h2 class="wp-block-heading has-xl-font-size">Nos services</h2>
			<!-- /wp:heading -->
			<!-- wp:list {"className":"maji-services-list"} -->
			<ul class="wp-block-list maji-services-list"><!-- wp:list-item -->
			<li>Sur place</li>
			<!-- /wp:list-item -->
			<!-- wp:list-item -->
			<li>À emporter</li>
			<!-- /wp:list-item -->
			<!-- wp:list-item -->
			<li>Livraison à {{maji:identity.address.city}}</li>
			<!-- /wp:list-item --></ul>
			<!-- /wp:list -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->
