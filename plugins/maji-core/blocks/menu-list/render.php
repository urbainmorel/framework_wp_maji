<?php
/**
 * Rendu serveur du bloc maji/menu-list.
 *
 * @package maji-core
 *
 * @var array<string, mixed> $attributes Attributs du bloc.
 */

declare(strict_types=1);

use MAJI\Core\Restaurant\Menu;

$maji_dishes = Menu::dishes(
	(int) ( $attributes['count'] ?? 12 ),
	(string) ( $attributes['category'] ?? '' )
);
if ( [] === $maji_dishes ) {
	return;
}
$maji_badge_labels = Menu::badge_labels();
?>
<div <?php echo get_block_wrapper_attributes( [ 'class' => 'maji-menu-list' ] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- échappé par WordPress. ?>>
	<ul class="maji-menu-list__items">
		<?php foreach ( $maji_dishes as $maji_dish ) : ?>
			<li class="maji-menu-list__item">
				<div class="maji-menu-list__heading">
					<span class="maji-menu-list__name">
						<?php echo esc_html( (string) $maji_dish['name'] ); ?>
						<?php foreach ( $maji_dish['badges'] as $maji_badge ) : ?>
							<span class="maji-badge maji-badge--<?php echo esc_attr( (string) $maji_badge ); ?>"><?php echo esc_html( $maji_badge_labels[ $maji_badge ] ?? (string) $maji_badge ); ?></span>
						<?php endforeach; ?>
					</span>
					<span class="maji-menu-list__dots" aria-hidden="true"></span>
					<span class="maji-menu-list__price"><?php echo wp_kses_post( (string) $maji_dish['price_html'] ); ?></span>
				</div>
				<?php if ( '' !== (string) $maji_dish['description'] ) : ?>
					<p class="maji-menu-list__description"><?php echo esc_html( wp_strip_all_tags( (string) $maji_dish['description'] ) ); ?></p>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
	</ul>
</div>
