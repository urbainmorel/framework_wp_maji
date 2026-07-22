<?php
/**
 * Title: Livraison 01 — zones et frais
 * Slug: maji/resto-livraison-01
 * Categories: maji-restaurant
 * Viewport Width: 1200
 *
 * @package maji-framework
 */

?>
<!-- wp:group {"align":"full","backgroundColor":"primary","textColor":"primary-contrast","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-primary-contrast-color has-primary-background-color has-text-color has-background" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)">
	<!-- wp:columns {"align":"wide","verticalAlignment":"center"} -->
	<div class="wp-block-columns alignwide are-vertically-aligned-center">
		<!-- wp:column {"verticalAlignment":"center","width":"40%"} -->
		<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:40%">
			<!-- wp:heading {"level":2,"textColor":"primary-contrast","fontSize":"2xl"} -->
			<h2 class="wp-block-heading has-primary-contrast-color has-text-color has-2-xl-font-size">Livraison à {{maji:identity.address.city}}</h2>
			<!-- /wp:heading -->
			<!-- wp:paragraph {"fontSize":"md"} -->
			<p class="has-md-font-size">Commandez sur le site, payez à la livraison. Nous vous confirmons le délai sur WhatsApp.</p>
			<!-- /wp:paragraph -->
			<!-- wp:buttons -->
			<div class="wp-block-buttons">
				<!-- wp:button -->
				<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="/commander">Commander</a></div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->
		</div>
		<!-- /wp:column -->
		<!-- wp:column {"verticalAlignment":"center","width":"60%"} -->
		<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:60%">
			<!-- wp:table {"className":"maji-zones-table"} -->
			<figure class="wp-block-table maji-zones-table"><table><thead><tr><th>Zone</th><th>Frais</th></tr></thead><tbody><tr><td>{{maji:content.texts.zone_1_nom}}</td><td>{{maji:content.texts.zone_1_frais}} FCFA</td></tr><tr><td>{{maji:content.texts.zone_2_nom}}</td><td>{{maji:content.texts.zone_2_frais}} FCFA</td></tr><tr><td>{{maji:content.texts.zone_3_nom}}</td><td>{{maji:content.texts.zone_3_frais}} FCFA</td></tr></tbody></table></figure>
			<!-- /wp:table -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->
