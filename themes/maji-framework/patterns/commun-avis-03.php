<?php
/**
 * Title: Avis 03 — note et avis en vedette
 * Slug: maji/commun-avis-03
 * Categories: maji-commun
 * Viewport Width: 1400
 *
 * @package maji-framework
 */

?>
<!-- wp:group {"align":"full","backgroundColor":"surface-alt","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-surface-alt-background-color has-background" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)">
	<!-- wp:columns {"verticalAlignment":"center","align":"wide"} -->
	<div class="wp-block-columns alignwide are-vertically-aligned-center">
		<!-- wp:column {"verticalAlignment":"center","width":"38%"} -->
		<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:38%">
			<!-- wp:paragraph {"textColor":"accent","fontSize":"2xl"} -->
			<p class="has-accent-color has-text-color has-2-xl-font-size">★★★★★</p>
			<!-- /wp:paragraph -->
			<!-- wp:heading {"level":2,"fontSize":"2xl"} -->
			<h2 class="wp-block-heading has-2-xl-font-size">Vos avis comptent</h2>
			<!-- /wp:heading -->
			<!-- wp:paragraph {"textColor":"ink-muted","fontSize":"md"} -->
			<p class="has-ink-muted-color has-text-color has-md-font-size">Nos clients nous recommandent, et nous les en remercions.</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->
		<!-- wp:column {"verticalAlignment":"center","width":"62%"} -->
		<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:62%">
			<!-- wp:group {"backgroundColor":"base","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"var:preset|spacing|50","right":"var:preset|spacing|50"}},"border":{"radius":"var:custom|maji|radius|md"}},"layout":{"type":"constrained"}} -->
			<div class="wp-block-group has-base-background-color has-background" style="border-radius:var(--wp--custom--maji--radius--md);padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--50)">
				<!-- wp:paragraph {"fontSize":"lg"} -->
				<p class="has-lg-font-size">« {{maji:content.texts.avis_1_texte}} »</p>
				<!-- /wp:paragraph -->
				<!-- wp:paragraph {"textColor":"ink-muted","fontSize":"sm"} -->
				<p class="has-ink-muted-color has-text-color has-sm-font-size">— {{maji:content.texts.avis_1_auteur}}</p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->
