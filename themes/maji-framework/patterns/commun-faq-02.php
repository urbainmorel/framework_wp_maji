<?php
/**
 * Title: FAQ 02 — deux colonnes
 * Slug: maji/commun-faq-02
 * Categories: maji-commun
 * Viewport Width: 1400
 *
 * @package maji-framework
 */

?>
<!-- wp:group {"align":"full","backgroundColor":"surface","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-surface-background-color has-background" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)">
	<!-- wp:columns {"align":"wide"} -->
	<div class="wp-block-columns alignwide">
		<!-- wp:column {"width":"38%"} -->
		<div class="wp-block-column" style="flex-basis:38%">
			<!-- wp:heading {"level":2,"fontSize":"2xl"} -->
			<h2 class="wp-block-heading has-2-xl-font-size">Questions fréquentes</h2>
			<!-- /wp:heading -->
			<!-- wp:paragraph {"textColor":"ink-muted","fontSize":"md"} -->
			<p class="has-ink-muted-color has-text-color has-md-font-size">Vous ne trouvez pas votre réponse ? Écrivez-nous, nous sommes là pour vous aider.</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->
		<!-- wp:column {"width":"62%"} -->
		<div class="wp-block-column" style="flex-basis:62%">
			<!-- wp:details -->
			<details class="wp-block-details"><summary>{{maji:content.texts.faq_1_question}}</summary>
				<!-- wp:paragraph -->
				<p>{{maji:content.texts.faq_1_reponse}}</p>
				<!-- /wp:paragraph -->
			</details>
			<!-- /wp:details -->
			<!-- wp:details -->
			<details class="wp-block-details"><summary>{{maji:content.texts.faq_2_question}}</summary>
				<!-- wp:paragraph -->
				<p>{{maji:content.texts.faq_2_reponse}}</p>
				<!-- /wp:paragraph -->
			</details>
			<!-- /wp:details -->
			<!-- wp:details -->
			<details class="wp-block-details"><summary>{{maji:content.texts.faq_3_question}}</summary>
				<!-- wp:paragraph -->
				<p>{{maji:content.texts.faq_3_reponse}}</p>
				<!-- /wp:paragraph -->
			</details>
			<!-- /wp:details -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->
