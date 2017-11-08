/**
 * File handbook.js.
 *
 * Handbook enhancements.
 */

jQuery( document ).ready( function() {
	jQuery( document ).on( 'click', '.code-tab', function ( e ) {
		var $tab = jQuery( e.target );
		if ( $tab.hasClass( 'is-active' ) ) {
			return;
		}

		var lang = $tab.text();

		$tab.parent().find( '.is-active, .' + lang ).toggleClass( 'is-active' );
	} );


	// Add our expandable button
	jQuery( '.menu-table-of-contents-container > ul > .menu-item-has-children > a' )
		.wrap( '<div class="expandable"></div>' )
		.after( '<button class="dashicons dashicons-arrow-down-alt2" aria-expanded="false"></button>' );

	// Invisibly open all of the submenus
	jQuery( '.menu-item-has-children > ul ul' ).addClass( 'default-open' );

	// Open the current menu
	jQuery( '.menu-table-of-contents-container .current-menu-item a' ).first()
		.addClass( 'active' )
		.parents( '.menu-item-has-children' )
			.toggleClass( 'open' )
		.find( '> div > .dashicons' )
			.attr( 'aria-expanded', true );

	// Or if wrapped in a div.expandable
	jQuery( '.menu-item-has-children > div > .dashicons' ).click( function() {
		var menuToggle = jQuery( this ).closest( '.menu-item-has-children' );

		jQuery( this ).parent().siblings( '.children' ).slideToggle();

		menuToggle.toggleClass( 'open' );
		jQuery( this ).attr( 'aria-expanded', menuToggle.hasClass( 'open' ) );
	} );
} );
