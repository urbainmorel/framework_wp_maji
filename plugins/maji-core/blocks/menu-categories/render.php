<?php
/**
 * Rendu serveur du bloc maji/menu-categories.
 *
 * @package maji-core
 *
 * @var array<string, mixed> $attributes Attributs du bloc.
 */

declare(strict_types=1);

use MAJI\Core\Restaurant\Menu;

$maji_groups = Menu::by_categories( (int) ( $attributes['perCategory'] ?? 12 ) );
if ( [] === $maji_groups ) {
	return;
}
$maji_badge_labels = Menu::badge_labels();
?>
<div <?php echo get_block_wrapper_attributes( [ 'class' => 'maji-menu-cats' ] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- échappé par WordPress. ?>>
	<?php foreach ( $maji_groups as $maji_group ) : ?>
		<section class="maji-menu-cats__section">
			<h3 class="maji-menu-cats__title"><?php echo esc_html( $maji_group['term']->name ); ?></h3>
			<?php if ( '' !== $maji_group['term']->description ) : ?>
				<p class="maji-menu-cats__description"><?php echo esc_html( $maji_group['term']->description ); ?></p>
			<?php endif; ?>
			<ul class="maji-menu-cats__items">
				<?php foreach ( $maji_group['dishes'] as $maji_dish ) : ?>
					<li class="maji-menu-cats__item">
						<div class="maji-menu-cats__heading">
							<span class="maji-menu-cats__name">
								<?php echo esc_html( (string) $maji_dish['name'] ); ?>
								<?php foreach ( $maji_dish['badges'] as $maji_badge ) : ?>
									<span class="maji-badge maji-badge--<?php echo esc_attr( (string) $maji_badge ); ?>"><?php echo esc_html( $maji_badge_labels[ $maji_badge ] ?? (string) $maji_badge ); ?></span>
								<?php endforeach; ?>
							</span>
							<span class="maji-menu-cats__price"><?php echo wp_kses_post( (string) $maji_dish['price_html'] ); ?></span>
						</div>
						<?php if ( '' !== (string) $maji_dish['description'] ) : ?>
							<p class="maji-menu-cats__dish-description"><?php echo esc_html( wp_strip_all_tags( (string) $maji_dish['description'] ) ); ?></p>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</section>
	<?php endforeach; ?>
</div>
