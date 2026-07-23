<?php
/**
 * Title: Localisation 02 — contact compact
 * Slug: maji/commun-localisation-02
 * Categories: maji-commun
 * Viewport Width: 1200
 *
 * @package maji-framework
 */

?>
<!-- wp:group {"align":"full","backgroundColor":"base","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-base-background-color has-background" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)">
	<!-- wp:columns {"align":"wide"} -->
	<div class="wp-block-columns alignwide">
		<!-- wp:column {"width":"34%"} -->
		<div class="wp-block-column" style="flex-basis:34%">
			<!-- wp:heading {"level":3,"fontSize":"lg"} -->
			<h3 class="wp-block-heading has-lg-font-size">Nous contacter</h3>
			<!-- /wp:heading -->
			<!-- wp:maji/establishment-info {"variant":"coordonnees"} /-->
			<!-- wp:group {"style":{"spacing":{"margin":{"top":"var:preset|spacing|30"}}},"layout":{"type":"flex"}} -->
			<div class="wp-block-group" style="margin-top:var(--wp--preset--spacing--30)">
				<!-- wp:maji/whatsapp-button {"context":"contact"} /-->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:column -->
		<!-- wp:column {"width":"33%"} -->
		<div class="wp-block-column" style="flex-basis:33%">
			<!-- wp:heading {"level":3,"fontSize":"lg"} -->
			<h3 class="wp-block-heading has-lg-font-size">Horaires</h3>
			<!-- /wp:heading -->
			<!-- wp:maji/opening-hours /-->
		</div>
		<!-- /wp:column -->
		<!-- wp:column {"width":"33%"} -->
		<div class="wp-block-column" style="flex-basis:33%">
			<!-- wp:heading {"level":3,"fontSize":"lg"} -->
			<h3 class="wp-block-heading has-lg-font-size">Où nous trouver</h3>
			<!-- /wp:heading -->
			<!-- wp:maji/establishment-info {"variant":"adresse"} /-->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->
