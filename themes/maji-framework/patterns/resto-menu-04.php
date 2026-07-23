<?php
/**
 * Title: Menu 04 — carte encadrée
 * Slug: maji/resto-menu-04
 * Categories: maji-restaurant
 * Viewport Width: 1200
 *
 * @package maji-framework
 */

?>
<!-- wp:group {"align":"full","backgroundColor":"surface-alt","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-surface-alt-background-color has-background" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)">
	<!-- wp:heading {"textAlign":"center","level":2,"fontSize":"2xl"} -->
	<h2 class="wp-block-heading has-text-align-center has-2-xl-font-size">Notre carte</h2>
	<!-- /wp:heading -->
	<!-- wp:paragraph {"align":"center","textColor":"ink-muted"} -->
	<p class="has-text-align-center has-ink-muted-color has-text-color">{{maji:content.texts.menu_intro}}</p>
	<!-- /wp:paragraph -->
	<!-- wp:group {"backgroundColor":"base","style":{"spacing":{"margin":{"top":"var:preset|spacing|50"},"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"var:preset|spacing|50","right":"var:preset|spacing|50"}},"border":{"radius":"var:custom|maji|radius|md"}},"layout":{"type":"constrained","contentSize":"640px"}} -->
	<div class="wp-block-group has-base-background-color has-background" style="border-radius:var(--wp--custom--maji--radius--md);margin-top:var(--wp--preset--spacing--50);padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--50)">
		<!-- wp:maji/menu-list {"count":10} /-->
	</div>
	<!-- /wp:group -->
	<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"},"style":{"spacing":{"margin":{"top":"var:preset|spacing|40"}}}} -->
	<div class="wp-block-buttons" style="margin-top:var(--wp--preset--spacing--40)">
		<!-- wp:button -->
		<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="/commander">Commander</a></div>
		<!-- /wp:button -->
	</div>
	<!-- /wp:buttons -->
</div>
<!-- /wp:group -->
