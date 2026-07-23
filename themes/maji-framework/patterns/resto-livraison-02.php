<?php
/**
 * Title: Livraison 02 — commander en trois étapes
 * Slug: maji/resto-livraison-02
 * Categories: maji-restaurant
 * Viewport Width: 1400
 *
 * @package maji-framework
 */

?>
<!-- wp:group {"align":"full","backgroundColor":"surface","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-surface-background-color has-background" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)">
	<!-- wp:heading {"textAlign":"center","level":2,"fontSize":"2xl"} -->
	<h2 class="wp-block-heading has-text-align-center has-2-xl-font-size">Commander en trois étapes</h2>
	<!-- /wp:heading -->
	<!-- wp:columns {"align":"wide","style":{"spacing":{"margin":{"top":"var:preset|spacing|50"}}}} -->
	<div class="wp-block-columns alignwide" style="margin-top:var(--wp--preset--spacing--50)">
		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:group {"backgroundColor":"base","style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}},"border":{"radius":"var:custom|maji|radius|md"}},"layout":{"type":"constrained"}} -->
			<div class="wp-block-group has-base-background-color has-background" style="border-radius:var(--wp--custom--maji--radius--md);padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)">
				<!-- wp:paragraph {"textColor":"accent","fontSize":"2xl"} -->
				<p class="has-accent-color has-text-color has-2-xl-font-size">1</p>
				<!-- /wp:paragraph -->
				<!-- wp:heading {"level":3,"fontSize":"lg"} -->
				<h3 class="wp-block-heading has-lg-font-size">Choisissez</h3>
				<!-- /wp:heading -->
				<!-- wp:paragraph {"textColor":"ink-muted","fontSize":"sm"} -->
				<p class="has-ink-muted-color has-text-color has-sm-font-size">Parcourez la carte et ajoutez vos plats au panier.</p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:column -->
		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:group {"backgroundColor":"base","style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}},"border":{"radius":"var:custom|maji|radius|md"}},"layout":{"type":"constrained"}} -->
			<div class="wp-block-group has-base-background-color has-background" style="border-radius:var(--wp--custom--maji--radius--md);padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)">
				<!-- wp:paragraph {"textColor":"accent","fontSize":"2xl"} -->
				<p class="has-accent-color has-text-color has-2-xl-font-size">2</p>
				<!-- /wp:paragraph -->
				<!-- wp:heading {"level":3,"fontSize":"lg"} -->
				<h3 class="wp-block-heading has-lg-font-size">Commandez</h3>
				<!-- /wp:heading -->
				<!-- wp:paragraph {"textColor":"ink-muted","fontSize":"sm"} -->
				<p class="has-ink-muted-color has-text-color has-sm-font-size">Livraison ou retrait, paiement à la réception.</p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:column -->
		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:group {"backgroundColor":"base","style":{"spacing":{"padding":{"top":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}},"border":{"radius":"var:custom|maji|radius|md"}},"layout":{"type":"constrained"}} -->
			<div class="wp-block-group has-base-background-color has-background" style="border-radius:var(--wp--custom--maji--radius--md);padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)">
				<!-- wp:paragraph {"textColor":"accent","fontSize":"2xl"} -->
				<p class="has-accent-color has-text-color has-2-xl-font-size">3</p>
				<!-- /wp:paragraph -->
				<!-- wp:heading {"level":3,"fontSize":"lg"} -->
				<h3 class="wp-block-heading has-lg-font-size">Dégustez</h3>
				<!-- /wp:heading -->
				<!-- wp:paragraph {"textColor":"ink-muted","fontSize":"sm"} -->
				<p class="has-ink-muted-color has-text-color has-sm-font-size">Nous vous livrons à {{maji:identity.address.city}} ou vous accueillons à table.</p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
	<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"},"style":{"spacing":{"margin":{"top":"var:preset|spacing|40"}}}} -->
	<div class="wp-block-buttons" style="margin-top:var(--wp--preset--spacing--40)">
		<!-- wp:button -->
		<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="/commander">Commander</a></div>
		<!-- /wp:button -->
	</div>
	<!-- /wp:buttons -->
</div>
<!-- /wp:group -->
