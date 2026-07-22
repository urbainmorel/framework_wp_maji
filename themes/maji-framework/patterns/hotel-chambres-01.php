<?php
/**
 * Title: Chambres 01 — grille
 * Slug: maji/hotel-chambres-01
 * Categories: maji-hotel
 * Viewport Width: 1400
 *
 * @package maji-framework
 */

?>
<!-- wp:group {"align":"full","backgroundColor":"base","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-base-background-color has-background" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)">
	<!-- wp:heading {"textAlign":"center","level":2,"fontSize":"2xl"} -->
	<h2 class="wp-block-heading has-text-align-center has-2-xl-font-size">Nos chambres</h2>
	<!-- /wp:heading -->
	<!-- wp:paragraph {"align":"center","textColor":"ink-muted"} -->
	<p class="has-text-align-center has-ink-muted-color has-text-color">{{maji:content.texts.chambres_intro}}</p>
	<!-- /wp:paragraph -->
	<!-- wp:group {"align":"wide","style":{"spacing":{"margin":{"top":"var:preset|spacing|50"}}},"layout":{"type":"constrained"}} -->
	<div class="wp-block-group alignwide" style="margin-top:var(--wp--preset--spacing--50)">
		<!-- wp:maji/rooms-grid {"count":6,"columns":3} /-->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
