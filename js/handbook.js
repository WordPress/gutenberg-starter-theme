/**
 * File handbook.js.
 *
 * Handbook enhancements.
 */

( function( $ ) {
	$( document ).on( 'click', '.code-tab', function ( e ) {
		var $tab = $( e.target );
		if ( $tab.hasClass( 'is-active' ) ) {
			return;
		}

		var lang = $tab.text();

		$tab.parent().find( '.is-active, .' + lang ).toggleClass( 'is-active' );
	} );
} )( jQuery );
