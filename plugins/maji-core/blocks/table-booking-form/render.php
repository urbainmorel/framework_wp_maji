<?php
/**
 * Rendu serveur du bloc maji/table-booking-form.
 *
 * @package maji-core
 */

declare(strict_types=1);

$maji_uid = wp_unique_id( 'maji-table-' );
?>
<div <?php echo get_block_wrapper_attributes( [ 'class' => 'maji-form maji-table-booking-form' ] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- échappé par WordPress. ?>>
	<form class="maji-form__inner" data-maji-form="table" novalidate>
		<p class="maji-form__row maji-form__row--website" aria-hidden="true">
			<label for="<?php echo esc_attr( $maji_uid ); ?>-website"><?php esc_html_e( 'Ne pas remplir ce champ', 'maji-core' ); ?></label>
			<input type="text" id="<?php echo esc_attr( $maji_uid ); ?>-website" name="website" tabindex="-1" autocomplete="off">
		</p>
		<div class="maji-form__grid">
			<p class="maji-form__row">
				<label for="<?php echo esc_attr( $maji_uid ); ?>-date"><?php esc_html_e( 'Date', 'maji-core' ); ?> <span class="maji-form__required">*</span></label>
				<input type="date" id="<?php echo esc_attr( $maji_uid ); ?>-date" name="date" required>
			</p>
			<p class="maji-form__row">
				<label for="<?php echo esc_attr( $maji_uid ); ?>-time"><?php esc_html_e( 'Heure', 'maji-core' ); ?> <span class="maji-form__required">*</span></label>
				<input type="time" id="<?php echo esc_attr( $maji_uid ); ?>-time" name="time" required>
			</p>
			<p class="maji-form__row">
				<label for="<?php echo esc_attr( $maji_uid ); ?>-guests"><?php esc_html_e( 'Couverts', 'maji-core' ); ?></label>
				<input type="number" id="<?php echo esc_attr( $maji_uid ); ?>-guests" name="guests" min="1" max="30" value="2">
			</p>
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
			<button type="submit" class="wp-element-button maji-form__submit"><?php esc_html_e( 'Réserver ma table', 'maji-core' ); ?></button>
		</p>
		<p class="maji-form__feedback" role="status" aria-live="polite" hidden></p>
	</form>
</div>
