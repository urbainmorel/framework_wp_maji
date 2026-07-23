<?php
/**
 * Title: Chiffres 01 — repères de l'hôtel
 * Slug: maji/hotel-chiffres-01
 * Categories: maji-hotel
 * Viewport Width: 1400
 *
 * @package maji-framework
 */

?>
<!-- wp:group {"align":"full","backgroundColor":"primary","textColor":"primary-contrast","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-primary-contrast-color has-primary-background-color has-text-color has-background" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)">
	<!-- wp:columns {"align":"wide"} -->
	<div class="wp-block-columns alignwide">
		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:heading {"textAlign":"center","level":2,"textColor":"primary-contrast","fontSize":"3xl"} -->
			<h2 class="wp-block-heading has-text-align-center has-primary-contrast-color has-text-color has-3-xl-font-size">{{maji:content.facts.annee}}</h2>
			<!-- /wp:heading -->
			<!-- wp:paragraph {"align":"center","fontSize":"sm"} -->
			<p class="has-text-align-center has-sm-font-size">Année d'ouverture</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->
		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:heading {"textAlign":"center","level":2,"textColor":"primary-contrast","fontSize":"3xl"} -->
			<h2 class="wp-block-heading has-text-align-center has-primary-contrast-color has-text-color has-3-xl-font-size">{{maji:content.facts.quartier}}</h2>
			<!-- /wp:heading -->
			<!-- wp:paragraph {"align":"center","fontSize":"sm"} -->
			<p class="has-text-align-center has-sm-font-size">Notre quartier</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->
		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:heading {"textAlign":"center","level":2,"textColor":"primary-contrast","fontSize":"3xl"} -->
			<h2 class="wp-block-heading has-text-align-center has-primary-contrast-color has-text-color has-3-xl-font-size">{{maji:identity.address.city}}</h2>
			<!-- /wp:heading -->
			<!-- wp:paragraph {"align":"center","fontSize":"sm"} -->
			<p class="has-text-align-center has-sm-font-size">Où nous trouver</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->
