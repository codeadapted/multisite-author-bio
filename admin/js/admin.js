( function ( $, window, document ) {
	'use strict';
	$( document ).ready( function () {
		var ajaxURL = mab_obj.ajax_url;

		$( '#mabForm' ).on( 'submit', function ( e ) {
			e.preventDefault();
			var clearData = $( '#admin-view .section.clear-data input' );

			if( clearData.is( ':checked' ) ) {
				clearData = 1;
			} else {
				clearData = 0;
			}

			$.ajax({
				method: 'POST',
				url: ajaxURL,
				data: {
					'action': 'mab_save_admin_page',
					'clear_data': clearData
				},
				success: function( response ) {
					console.log( response );
					setTimeout( function() {
						location.reload();
					}, 1000 );
				}
			});

		});
	});
} ( jQuery, window, document ) );
