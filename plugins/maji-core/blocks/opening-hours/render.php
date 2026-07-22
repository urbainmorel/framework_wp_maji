<?php
/**
 * Rendu serveur du bloc maji/opening-hours.
 *
 * @package maji-core
 *
 * @var array<string, mixed> $attributes Attributs du bloc.
 */

declare(strict_types=1);

$maji_settings = \MAJI\Core\Plugin::instance()->settings();
$maji_hours    = $maji_settings->get( 'establishment.hours', [] );
if ( ! is_array( $maji_hours ) ) {
	return;
}

$maji_days = [
	'mon' => __( 'Lundi', 'maji-core' ),
	'tue' => __( 'Mardi', 'maji-core' ),
	'wed' => __( 'Mercredi', 'maji-core' ),
	'thu' => __( 'Jeudi', 'maji-core' ),
	'fri' => __( 'Vendredi', 'maji-core' ),
	'sat' => __( 'Samedi', 'maji-core' ),
	'sun' => __( 'Dimanche', 'maji-core' ),
];
?>
<div <?php echo get_block_wrapper_attributes( [ 'class' => 'maji-hours' ] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- échappé par WordPress. ?>>
	<dl class="maji-hours__list">
		<?php foreach ( $maji_days as $maji_key => $maji_label ) : ?>
			<?php
			$maji_ranges = isset( $maji_hours[ $maji_key ] ) && is_array( $maji_hours[ $maji_key ] ) ? $maji_hours[ $maji_key ] : [];
			$maji_text   = [] === $maji_ranges
				? __( 'Fermé', 'maji-core' )
				: implode(
					', ',
					array_map(
						static fn( array $maji_r ): string => $maji_r[0] . ' – ' . $maji_r[1],
						$maji_ranges
					)
				);
			?>
			<div class="maji-hours__row">
				<dt><?php echo esc_html( $maji_label ); ?></dt>
				<dd><?php echo esc_html( $maji_text ); ?></dd>
			</div>
		<?php endforeach; ?>
	</dl>
</div>
