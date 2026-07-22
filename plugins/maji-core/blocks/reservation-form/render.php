<?php
/**
 * Rendu serveur du bloc maji/reservation-form.
 *
 * @package maji-core
 *
 * @var array<string, mixed> $attributes Attributs du bloc.
 */

declare(strict_types=1);

$maji_show_rooms = (bool) ( $attributes['showRoomPicker'] ?? true );
$maji_rooms      = [];
if ( $maji_show_rooms ) {
	$maji_rooms = get_posts(
		[
			'post_type'      => 'maji_room',
			'posts_per_page' => 30,
			'orderby'        => 'title',
			'order'          => 'ASC',
		]
	);
}
$maji_uid = wp_unique_id( 'maji-resa-' );
?>
<div <?php echo get_block_wrapper_attributes( [ 'class' => 'maji-form maji-reservation-form' ] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- échappé par WordPress. ?>>
	<form class="maji-form__inner" data-maji-form="reservation" data-maji-endpoint="<?php echo esc_url( rest_url( 'maji/v1/reservations' ) ); ?>" novalidate>
		<p class="maji-form__row maji-form__row--website" aria-hidden="true">
			<label for="<?php echo esc_attr( $maji_uid ); ?>-website"><?php esc_html_e( 'Ne pas remplir ce champ', 'maji-core' ); ?></label>
			<input type="text" id="<?php echo esc_attr( $maji_uid ); ?>-website" name="website" tabindex="-1" autocomplete="off">
		</p>
		<div class="maji-form__grid">
			<p class="maji-form__row">
				<label for="<?php echo esc_attr( $maji_uid ); ?>-checkin"><?php esc_html_e( 'Arrivée', 'maji-core' ); ?> <span class="maji-form__required">*</span></label>
				<input type="date" id="<?php echo esc_attr( $maji_uid ); ?>-checkin" name="checkin" required>
			</p>
			<p class="maji-form__row">
				<label for="<?php echo esc_attr( $maji_uid ); ?>-checkout"><?php esc_html_e( 'Départ', 'maji-core' ); ?> <span class="maji-form__required">*</span></label>
				<input type="date" id="<?php echo esc_attr( $maji_uid ); ?>-checkout" name="checkout" required>
			</p>
			<p class="maji-form__row">
				<label for="<?php echo esc_attr( $maji_uid ); ?>-guests"><?php esc_html_e( 'Personnes', 'maji-core' ); ?></label>
				<input type="number" id="<?php echo esc_attr( $maji_uid ); ?>-guests" name="guests" min="1" max="20" value="2">
			</p>
			<?php if ( $maji_show_rooms && count( $maji_rooms ) > 0 ) : ?>
				<p class="maji-form__row">
					<label for="<?php echo esc_attr( $maji_uid ); ?>-room"><?php esc_html_e( 'Chambre souhaitée', 'maji-core' ); ?></label>
					<select id="<?php echo esc_attr( $maji_uid ); ?>-room" name="room_id">
						<option value=""><?php esc_html_e( 'Peu importe', 'maji-core' ); ?></option>
						<?php foreach ( $maji_rooms as $maji_room ) : ?>
							<option value="<?php echo esc_attr( (string) $maji_room->ID ); ?>"><?php echo esc_html( get_the_title( $maji_room ) ); ?></option>
						<?php endforeach; ?>
					</select>
				</p>
			<?php endif; ?>
			<p class="maji-form__row">
				<label for="<?php echo esc_attr( $maji_uid ); ?>-name"><?php esc_html_e( 'Nom complet', 'maji-core' ); ?> <span class="maji-form__required">*</span></label>
				<input type="text" id="<?php echo esc_attr( $maji_uid ); ?>-name" name="name" autocomplete="name" required>
			</p>
			<p class="maji-form__row">
				<label for="<?php echo esc_attr( $maji_uid ); ?>-phone"><?php esc_html_e( 'Téléphone (WhatsApp)', 'maji-core' ); ?> <span class="maji-form__required">*</span></label>
				<input type="tel" id="<?php echo esc_attr( $maji_uid ); ?>-phone" name="phone" placeholder="+22901020304" autocomplete="tel" required>
			</p>
			<p class="maji-form__row">
				<label for="<?php echo esc_attr( $maji_uid ); ?>-email"><?php esc_html_e( 'E-mail (facultatif)', 'maji-core' ); ?></label>
				<input type="email" id="<?php echo esc_attr( $maji_uid ); ?>-email" name="email" autocomplete="email">
			</p>
		</div>
		<p class="maji-form__row">
			<label for="<?php echo esc_attr( $maji_uid ); ?>-message"><?php esc_html_e( 'Message (facultatif)', 'maji-core' ); ?></label>
			<textarea id="<?php echo esc_attr( $maji_uid ); ?>-message" name="message" rows="3"></textarea>
		</p>
		<p class="maji-form__row maji-form__row--consent">
			<label>
				<input type="checkbox" name="whatsapp_consent" value="1" required>
				<?php esc_html_e( 'J\'accepte d\'être contacté·e sur WhatsApp pour cette demande.', 'maji-core' ); ?>
			</label>
		</p>
		<p class="maji-form__row">
			<button type="submit" class="wp-element-button maji-form__submit"><?php esc_html_e( 'Envoyer la demande', 'maji-core' ); ?></button>
		</p>
		<p class="maji-form__feedback" role="status" aria-live="polite" hidden></p>
	</form>
</div>
