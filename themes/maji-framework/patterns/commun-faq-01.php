<?php
/**
 * Title: FAQ 01 — accordéon
 * Slug: maji/commun-faq-01
 * Categories: maji-commun
 * Viewport Width: 1200
 *
 * @package maji-framework
 */

?>
<!-- wp:group {"align":"full","backgroundColor":"base","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull has-base-background-color has-background" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)">
	<!-- wp:group {"layout":{"type":"constrained","contentSize":"720px"}} -->
	<div class="wp-block-group">
		<!-- wp:heading {"textAlign":"center","level":2,"fontSize":"2xl"} -->
		<h2 class="wp-block-heading has-text-align-center has-2-xl-font-size">Questions fréquentes</h2>
		<!-- /wp:heading -->
		<!-- wp:group {"style":{"spacing":{"margin":{"top":"var:preset|spacing|50"}}},"layout":{"type":"constrained"}} -->
		<div class="wp-block-group" style="margin-top:var(--wp--preset--spacing--50)">
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
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
