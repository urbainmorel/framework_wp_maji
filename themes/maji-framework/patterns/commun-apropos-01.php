<?php
/**
 * Title: À propos 01 — présentation
 * Slug: maji/commun-apropos-01
 * Categories: maji-commun
 * Viewport Width: 1200
 *
 * @package maji-framework
 */

?>
<!-- wp:group {"align":"full","backgroundColor":"base","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-base-background-color has-background" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)">
	<!-- wp:group {"layout":{"type":"constrained","contentSize":"720px"}} -->
	<div class="wp-block-group">
		<!-- wp:heading {"textAlign":"center","level":2,"fontSize":"2xl"} -->
		<h2 class="wp-block-heading has-text-align-center has-2-xl-font-size">À propos</h2>
		<!-- /wp:heading -->
		<!-- wp:separator {"backgroundColor":"accent","className":"is-style-wide"} -->
		<hr class="wp-block-separator has-text-color has-accent-color has-alpha-channel-opacity has-accent-background-color has-background is-style-wide"/>
		<!-- /wp:separator -->
		<!-- wp:paragraph {"align":"center","fontSize":"md"} -->
		<p class="has-text-align-center has-md-font-size">{{maji:content.texts.apropos_texte}}</p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
