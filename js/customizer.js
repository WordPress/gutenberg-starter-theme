/**
 * File customizer.js.
 *
 * Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Theme Customizer preview reload changes asynchronously.
 */

( function( $ ) {

	// Site title and description.
	wp.customize( 'blogname', function( value ) {
		value.bind( function( to ) {
			document.querySelector( '.site-title a' ).textContent( to );
		});
	});
	wp.customize( 'blogdescription', function( value ) {
		value.bind( function( to ) {
			document.querySelector( '.site-description' ).textContent( to );
		});
	});

	// Header text color.
	wp.customize( 'header_textcolor', function( value ) {
		value.bind( function( to ) {
			if ( 'blank' === to ) {
				document.querySelector( '.site-title, .site-description' ).style.clip = 'auto';
        document.querySelector( '.site-title, .site-description' ).style.position = 'absolute'
			} else {
        document.querySelector( '.site-title, .site-description' ).style.clip = 'auto';
        document.querySelector( '.site-title, .site-description' ).style.position = 'relative';
				document.querySelector( '.site-title a, .site-description' ).style.color = to ;
			}
		});
	});
})( jQuery );
