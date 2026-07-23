<?php
/**
 * Title: Populaires 02 — plats signature en cartes
 * Slug: maji/resto-populaires-02
 * Categories: maji-restaurant
 * Viewport Width: 1400
 *
 * @package maji-framework
 */

?>
<!-- wp:group {"align":"full","backgroundColor":"base","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-base-background-color has-background" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)">
	<!-- wp:group {"layout":{"type":"constrained","contentSize":"720px"}} -->
	<div class="wp-block-group">
		<!-- wp:heading {"textAlign":"center","level":2,"fontSize":"2xl"} -->
		<h2 class="wp-block-heading has-text-align-center has-2-xl-font-size">Nos plats signature</h2>
		<!-- /wp:heading -->
		<!-- wp:paragraph {"align":"center","textColor":"ink-muted"} -->
		<p class="has-text-align-center has-ink-muted-color has-text-color">Le meilleur de notre cuisine {{maji:content.facts.specialites}}, préparé chaque jour.</p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->
	<!-- wp:group {"align":"wide","style":{"spacing":{"margin":{"top":"var:preset|spacing|50"}}},"layout":{"type":"constrained"}} -->
	<div class="wp-block-group alignwide" style="margin-top:var(--wp--preset--spacing--50)">
		<!-- wp:maji/menu-grid {"count":4,"columns":2,"badge":"populaire"} /-->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
