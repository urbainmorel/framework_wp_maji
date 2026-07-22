<?php
/**
 * Rendu serveur du bloc maji/whatsapp-button.
 *
 * @package maji-core
 *
 * @var array<string, mixed> $attributes Attributs du bloc.
 */

declare(strict_types=1);

$maji_settings = \MAJI\Core\Plugin::instance()->settings();
$maji_whatsapp = new \MAJI\Core\WhatsApp\WhatsApp( $maji_settings );

$maji_context = (string) ( $attributes['context'] ?? 'contact' );
$maji_item    = (string) ( $attributes['item'] ?? '' );
$maji_variant = (string) ( $attributes['variant'] ?? 'button' );
$maji_label   = (string) ( $attributes['label'] ?? '' );
if ( '' === $maji_label ) {
	$maji_label = 'header' === $maji_context
		? __( 'WhatsApp', 'maji-core' )
		: __( 'Discuter sur WhatsApp', 'maji-core' );
}
if ( 'header' === $maji_context ) {
	$maji_context = 'contact';
}

$maji_url = $maji_whatsapp->url( $maji_context, $maji_item );
if ( '' === $maji_url ) {
	return;
}

$maji_classes = 'maji-whatsapp maji-whatsapp--' . $maji_variant;
?>
<div <?php echo get_block_wrapper_attributes( [ 'class' => $maji_classes ] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- échappé par WordPress. ?>>
	<a class="maji-whatsapp__link wp-element-button" href="<?php echo esc_url( $maji_url ); ?>" target="_blank" rel="noopener noreferrer">
		<svg class="maji-whatsapp__icon" aria-hidden="true" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38a9.87 9.87 0 0 0 4.74 1.21c5.46 0 9.9-4.45 9.9-9.91A9.87 9.87 0 0 0 12.04 2m0 18.03a8.2 8.2 0 0 1-4.18-1.15l-.3-.17-3.12.82.83-3.04-.2-.31a8.16 8.16 0 0 1-1.26-4.38c0-4.54 3.7-8.24 8.24-8.24 2.2 0 4.27.86 5.82 2.42a8.18 8.18 0 0 1 2.41 5.83c0 4.54-3.7 8.22-8.24 8.22m4.52-6.16c-.25-.12-1.47-.72-1.69-.81-.23-.08-.39-.12-.56.12-.16.25-.64.81-.78.97-.14.17-.29.19-.54.06-.25-.12-1.05-.39-1.99-1.23-.74-.66-1.23-1.47-1.38-1.72-.14-.25-.02-.38.11-.51.11-.11.25-.29.37-.43.12-.14.17-.25.25-.41.08-.17.04-.31-.02-.43-.06-.12-.56-1.34-.76-1.84-.2-.48-.41-.42-.56-.43h-.48c-.17 0-.43.06-.66.31-.22.25-.86.85-.86 2.07 0 1.22.89 2.4 1.01 2.56.12.17 1.75 2.67 4.23 3.74.59.26 1.05.41 1.41.52.59.19 1.13.16 1.56.1.48-.07 1.47-.6 1.67-1.18.21-.58.21-1.07.14-1.18-.06-.1-.22-.16-.47-.28"/></svg>
		<span><?php echo esc_html( $maji_label ); ?></span>
	</a>
</div>
