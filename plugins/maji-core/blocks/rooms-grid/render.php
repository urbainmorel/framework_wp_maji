<?php
/**
 * Rendu serveur du bloc maji/rooms-grid.
 *
 * @package maji-core
 *
 * @var array<string, mixed> $attributes Attributs du bloc.
 */

declare(strict_types=1);

$maji_count    = max( 1, min( 24, (int) ( $attributes['count'] ?? 6 ) ) );
$maji_columns  = max( 1, min( 4, (int) ( $attributes['columns'] ?? 3 ) ) );
$maji_amenity  = (string) ( $attributes['amenity'] ?? '' );
$maji_featured = (bool) ( $attributes['featuredOnly'] ?? false );

$maji_query_args = [
	'post_type'      => 'maji_room',
	'posts_per_page' => $maji_count,
	'orderby'        => 'menu_order title',
	'order'          => 'ASC',
];
if ( '' !== $maji_amenity ) {
	$maji_query_args['tax_query'] = [ // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- filtre volontaire, liste bornée.
		[
			'taxonomy' => 'maji_amenity',
			'field'    => 'slug',
			'terms'    => $maji_amenity,
		],
	];
}
if ( $maji_featured ) {
	$maji_query_args['meta_key']   = 'featured'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key -- liste bornée.
	$maji_query_args['meta_value'] = '1'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value -- liste bornée.
}

$maji_rooms = get_posts( $maji_query_args );
if ( [] === $maji_rooms ) {
	return;
}
?>
<div <?php echo get_block_wrapper_attributes( [ 'class' => 'maji-rooms maji-rooms--cols-' . $maji_columns ] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- échappé par WordPress. ?>>
	<?php foreach ( $maji_rooms as $maji_room ) : ?>
		<?php
		$maji_price    = (int) get_post_meta( $maji_room->ID, 'price_from', true );
		$maji_capacity = (int) get_post_meta( $maji_room->ID, 'capacity', true );
		$maji_sqm      = (int) get_post_meta( $maji_room->ID, 'size_sqm', true );
		?>
		<article class="maji-rooms__card maji-card">
			<a class="maji-rooms__media" href="<?php echo esc_url( (string) get_permalink( $maji_room ) ); ?>">
				<?php if ( has_post_thumbnail( $maji_room ) ) : ?>
					<?php echo get_the_post_thumbnail( $maji_room, 'large', [ 'loading' => 'lazy' ] ); ?>
				<?php endif; ?>
			</a>
			<div class="maji-rooms__body">
				<h3 class="maji-rooms__title">
					<a href="<?php echo esc_url( (string) get_permalink( $maji_room ) ); ?>"><?php echo esc_html( get_the_title( $maji_room ) ); ?></a>
				</h3>
				<p class="maji-rooms__meta">
					<?php if ( $maji_capacity > 0 ) : ?>
						<span>
							<?php
							/* translators: %d : nombre de personnes. */
							echo esc_html( sprintf( _n( '%d personne', '%d personnes', $maji_capacity, 'maji-core' ), $maji_capacity ) );
							?>
						</span>
					<?php endif; ?>
					<?php if ( $maji_sqm > 0 ) : ?>
						<span><?php echo esc_html( $maji_sqm . ' m²' ); ?></span>
					<?php endif; ?>
				</p>
				<?php if ( $maji_price > 0 ) : ?>
					<p class="maji-rooms__price">
						<?php
						/* translators: %s : prix formaté en FCFA. */
						echo esc_html( sprintf( __( 'À partir de %s FCFA', 'maji-core' ), number_format_i18n( $maji_price ) ) );
						?>
					</p>
				<?php endif; ?>
			</div>
		</article>
	<?php endforeach; ?>
</div>
