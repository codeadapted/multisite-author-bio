( function ( $, window, document ) {
	'use strict';

	function getTranslatedBio( ajaxUrl, userId ) {

		var val = $( '.mab-select-bio-variation' ).val();
		if( val ) {
			$.ajax( {
				method: 'GET',
				url: ajaxUrl,
				data: {
					action: 'mab_get_bio_variation',
					site_name: val,
					user_id: userId
				},
				success: function( response ) {
					$( '.mab-bio-variation-text' ).val( response.data ).change();
					$( '.mab-bio-variation-text' ).removeClass( 'hidden' );
					$( '.mab-bio-variation-label' ).removeClass( 'hidden' );
				}
			} );
		} else {
			$( '.mab-bio-variation-text' ).addClass( 'hidden' );
			$( '.mab-bio-variation-label' ).addClass( 'hidden' );
		}

	}

	$( document ).ready( function () {

		var ajaxUrl = mab_user_obj.ajax_url;
		var userId = $( '.mab-form-container' ).data( 'user' );

		getTranslatedBio( ajaxUrl, userId );

		$( '.mab-select-bio-variation' ).change( function() {
			getTranslatedBio( ajaxUrl, userId );
		} );

	});

} ( jQuery, window, document ) );
