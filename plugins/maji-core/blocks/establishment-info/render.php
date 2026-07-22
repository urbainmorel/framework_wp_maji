<?php
/**
 * Rendu serveur du bloc maji/establishment-info.
 *
 * Variantes : coordonnees (téléphone + e-mail + adresse), phone, adresse, reseaux.
 *
 * @package maji-core
 *
 * @var array<string, mixed> $attributes Attributs du bloc.
 */

declare(strict_types=1);

$maji_settings = \MAJI\Core\Plugin::instance()->settings();
$maji_variant  = (string) ( $attributes['variant'] ?? 'coordonnees' );

$maji_phone   = (string) $maji_settings->get( 'establishment.phone', '' );
$maji_email   = (string) $maji_settings->get( 'establishment.email', '' );
$maji_address = $maji_settings->get( 'establishment.address', [] );
$maji_socials = $maji_settings->get( 'establishment.socials', [] );
if ( ! is_array( $maji_address ) ) {
	$maji_address = [];
}
if ( ! is_array( $maji_socials ) ) {
	$maji_socials = [];
}

$maji_address_line = implode(
	', ',
	array_filter(
		[
			(string) ( $maji_address['street'] ?? '' ),
			(string) ( $maji_address['district'] ?? '' ),
			(string) ( $maji_address['city'] ?? '' ),
		],
		static fn( string $maji_part ): bool => '' !== $maji_part
	)
);
?>
<div <?php echo get_block_wrapper_attributes( [ 'class' => 'maji-info maji-info--' . $maji_variant ] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- échappé par WordPress. ?>>
	<?php if ( 'phone' === $maji_variant ) : ?>
		<?php if ( '' !== $maji_phone ) : ?>
			<a class="maji-info__phone" href="tel:<?php echo esc_attr( $maji_phone ); ?>"><?php echo esc_html( $maji_phone ); ?></a>
		<?php endif; ?>
	<?php elseif ( 'adresse' === $maji_variant ) : ?>
		<?php if ( '' !== $maji_address_line ) : ?>
			<address class="maji-info__address"><?php echo esc_html( $maji_address_line ); ?></address>
		<?php endif; ?>
	<?php elseif ( 'reseaux' === $maji_variant ) : ?>
		<ul class="maji-info__socials">
			<?php
			foreach ( [
				'facebook'  => 'Facebook',
				'instagram' => 'Instagram',
				'tiktok'    => 'TikTok',
			] as $maji_network => $maji_network_label ) :
				?>
				<?php $maji_network_url = (string) ( $maji_socials[ $maji_network ] ?? '' ); ?>
				<?php if ( '' !== $maji_network_url ) : ?>
					<li><a href="<?php echo esc_url( $maji_network_url ); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html( $maji_network_label ); ?></a></li>
				<?php endif; ?>
			<?php endforeach; ?>
		</ul>
	<?php else : ?>
		<ul class="maji-info__contact">
			<?php if ( '' !== $maji_phone ) : ?>
				<li><a href="tel:<?php echo esc_attr( $maji_phone ); ?>"><?php echo esc_html( $maji_phone ); ?></a></li>
			<?php endif; ?>
			<?php if ( '' !== $maji_email ) : ?>
				<li><a href="mailto:<?php echo esc_attr( $maji_email ); ?>"><?php echo esc_html( $maji_email ); ?></a></li>
			<?php endif; ?>
			<?php if ( '' !== $maji_address_line ) : ?>
				<li><address><?php echo esc_html( $maji_address_line ); ?></address></li>
			<?php endif; ?>
		</ul>
	<?php endif; ?>
</div>
