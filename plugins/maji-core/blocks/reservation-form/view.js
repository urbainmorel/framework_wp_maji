/**
 * Soumission AJAX des formulaires de réservation MAJI (vanilla, sans dépendance).
 */
( function() {
	'use strict';

	function submitForm( form ) {
		const feedback = form.querySelector( '.maji-form__feedback' );
		const button = form.querySelector( '.maji-form__submit' );
		const data = new FormData( form );
		const payload = {
			type: form.dataset.majiForm === 'table' ? 'table' : 'room',
			website: data.get( 'website' ) || '',
			name: data.get( 'name' ) || '',
			phone: data.get( 'phone' ) || '',
			email: data.get( 'email' ) || '',
			message: data.get( 'message' ) || '',
			guests: parseInt( data.get( 'guests' ) || '1', 10 ),
			whatsapp_consent: !! data.get( 'whatsapp_consent' ),
		};

		if ( payload.type === 'table' ) {
			payload.date_time =
				( data.get( 'date' ) || '' ) + ' ' + ( data.get( 'time' ) || '' );
		} else {
			payload.checkin = data.get( 'checkin' ) || '';
			payload.checkout = data.get( 'checkout' ) || '';
			const roomId = parseInt( data.get( 'room_id' ) || '0', 10 );
			if ( roomId > 0 ) {
				payload.room_id = roomId;
			}
		}

		button.disabled = true;
		feedback.hidden = true;

		const endpoint =
			form.dataset.majiEndpoint || '/wp-json/maji/v1/reservations';

		window
			.fetch( endpoint, {
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify( payload ),
			} )
			.then( function( response ) {
				return response.json().then( function( body ) {
					return { ok: response.ok, body };
				} );
			} )
			.then( function( result ) {
				feedback.hidden = false;
				if ( result.ok ) {
					feedback.className =
						'maji-form__feedback maji-form__feedback--success';
					feedback.textContent = result.body.message;
					form.reset();
				} else {
					feedback.className =
						'maji-form__feedback maji-form__feedback--error';
					feedback.textContent =
						result.body.message ||
						'Une erreur est survenue. Merci de réessayer.';
				}
			} )
			.catch( function() {
				feedback.hidden = false;
				feedback.className = 'maji-form__feedback maji-form__feedback--error';
				feedback.textContent =
					'Connexion impossible. Vérifiez votre réseau et réessayez.';
			} )
			.finally( function() {
				button.disabled = false;
			} );
	}

	document.addEventListener( 'submit', function( event ) {
		const form = event.target.closest( '[data-maji-form]' );
		if ( ! form ) {
			return;
		}
		event.preventDefault();
		submitForm( form );
	} );
}() );
