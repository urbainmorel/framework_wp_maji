<?php
/**
 * Rendu serveur du bloc maji/menu-grid.
 *
 * @package maji-core
 *
 * @var array<string, mixed> $attributes Attributs du bloc.
 */

declare(strict_types=1);

use MAJI\Core\Restaurant\Menu;

$maji_dishes = Menu::dishes(
	(int) ( $attributes['count'] ?? 8 ),
	(string) ( $attributes['category'] ?? '' ),
	(string) ( $attributes['badge'] ?? '' )
);
if ( [] === $maji_dishes ) {
	return;
}
$maji_columns      = max( 2, min( 4, (int) ( $attributes['columns'] ?? 4 ) ) );
$maji_badge_labels = Menu::badge_labels();
?>
<div <?php echo get_block_wrapper_attributes( [ 'class' => 'maji-menu-grid maji-menu-grid--cols-' . $maji_columns ] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- échappé par WordPress. ?>>
	<?php foreach ( $maji_dishes as $maji_dish ) : ?>
		<article class="maji-menu-grid__card maji-card">
			<a class="maji-menu-grid__media" href="<?php echo esc_url( (string) $maji_dish['permalink'] ); ?>">
				<?php if ( $maji_dish['image_id'] > 0 ) : ?>
					<?php echo wp_get_attachment_image( (int) $maji_dish['image_id'], 'medium_large', false, [ 'loading' => 'lazy' ] ); ?>
				<?php endif; ?>
				<?php if ( [] !== $maji_dish['badges'] ) : ?>
					<span class="maji-menu-grid__badges">
						<?php foreach ( $maji_dish['badges'] as $maji_badge ) : ?>
							<span class="maji-badge maji-badge--<?php echo esc_attr( (string) $maji_badge ); ?>"><?php echo esc_html( $maji_badge_labels[ $maji_badge ] ?? (string) $maji_badge ); ?></span>
						<?php endforeach; ?>
					</span>
				<?php endif; ?>
			</a>
			<div class="maji-menu-grid__body">
				<h3 class="maji-menu-grid__name"><a href="<?php echo esc_url( (string) $maji_dish['permalink'] ); ?>"><?php echo esc_html( (string) $maji_dish['name'] ); ?></a></h3>
				<p class="maji-menu-grid__price"><?php echo wp_kses_post( (string) $maji_dish['price_html'] ); ?></p>
			</div>
		</article>
	<?php endforeach; ?>
</div>
