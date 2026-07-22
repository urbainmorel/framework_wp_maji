<?php
/**
 * Title: Hero restaurant 03 — typographique, menu du jour en vedette
 * Slug: maji/resto-hero-03
 * Categories: maji-restaurant
 * Viewport Width: 1400
 *
 * @package maji-framework
 */

?>
<!-- wp:group {"align":"full","backgroundColor":"base","style":{"spacing":{"padding":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-base-background-color has-background" style="padding-top:var(--wp--preset--spacing--70);padding-bottom:var(--wp--preset--spacing--70)">
	<!-- wp:group {"layout":{"type":"constrained","contentSize":"860px"}} -->
	<div class="wp-block-group">
		<!-- wp:paragraph {"align":"center","textColor":"accent","fontSize":"sm"} -->
		<p class="has-text-align-center has-accent-color has-text-color has-sm-font-size">Cuisine {{maji:content.facts.specialites}}</p>
		<!-- /wp:paragraph -->
		<!-- wp:heading {"textAlign":"center","level":1,"fontSize":"3xl"} -->
		<h1 class="wp-block-heading has-text-align-center has-3-xl-font-size">{{maji:identity.name}}</h1>
		<!-- /wp:heading -->
		<!-- wp:separator {"backgroundColor":"accent","className":"is-style-wide"} -->
		<hr class="wp-block-separator has-text-color has-accent-color has-alpha-channel-opacity has-accent-background-color has-background is-style-wide"/>
		<!-- /wp:separator -->
		<!-- wp:paragraph {"align":"center","fontSize":"xl"} -->
		<p class="has-text-align-center has-xl-font-size">{{maji:content.texts.menu_du_jour}}</p>
		<!-- /wp:paragraph -->
		<!-- wp:paragraph {"align":"center","textColor":"ink-muted","fontSize":"md"} -->
		<p class="has-text-align-center has-ink-muted-color has-text-color has-md-font-size">{{maji:content.texts.hero_tagline}}</p>
		<!-- /wp:paragraph -->
		<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
		<div class="wp-block-buttons">
			<!-- wp:button -->
			<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="/commander">Commander</a></div>
			<!-- /wp:button -->
		</div>
		<!-- /wp:buttons -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
