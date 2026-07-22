<?php
/**
 * Title: CTA 01 — bandeau final WhatsApp / Réserver
 * Slug: maji/commun-cta-01
 * Categories: maji-commun
 * Viewport Width: 1400
 *
 * @package maji-framework
 */

?>
<!-- wp:group {"align":"full","backgroundColor":"accent","textColor":"accent-contrast","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-accent-contrast-color has-accent-background-color has-text-color has-background" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)">
	<!-- wp:group {"layout":{"type":"constrained","contentSize":"720px"}} -->
	<div class="wp-block-group">
		<!-- wp:heading {"textAlign":"center","level":2,"textColor":"accent-contrast","fontSize":"2xl"} -->
		<h2 class="wp-block-heading has-text-align-center has-accent-contrast-color has-text-color has-2-xl-font-size">{{maji:content.texts.cta_titre}}</h2>
		<!-- /wp:heading -->
		<!-- wp:paragraph {"align":"center","fontSize":"md"} -->
		<p class="has-text-align-center has-md-font-size">Une question, une envie ? Écrivez-nous sur WhatsApp, nous répondons vite.</p>
		<!-- /wp:paragraph -->
		<!-- wp:group {"layout":{"type":"flex","justifyContent":"center","flexWrap":"wrap"}} -->
		<div class="wp-block-group">
			<!-- wp:maji/whatsapp-button {"context":"contact"} /-->
			<!-- wp:buttons -->
			<div class="wp-block-buttons">
				<!-- wp:button {"className":"is-style-outline"} -->
				<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="#reserver">Réserver</a></div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
