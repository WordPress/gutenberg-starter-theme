<?php
/**
 * Functions that make Gutenberg easier to use for full service agencies
 *
 * @package gutenberg-starter-theme
 */

/**
 * Only allow certain blocks to be used.
 *
 * @param array $allowed_blocks Blocks to allow.
 */
function gutenberg_starter_theme_allowed_block_types( $allowed_blocks ) {
	return array(
		'core/freeform',
	);
}
add_filter( 'allowed_block_types', 'gutenberg_starter_theme_allowed_block_types' );

/**
 * Disable Gutenberg's default fullscreen mode.
 */
function gutenberg_starter_theme_disable_editor_fullscreen_mode() {
	$script = "window.onload = function() { const isFullscreenMode = wp.data.select( 'core/edit-post' ).isFeatureActive( 'fullscreenMode' ); if ( isFullscreenMode ) { wp.data.dispatch( 'core/edit-post' ).toggleFeature( 'fullscreenMode' ); } }";
	wp_add_inline_script( 'wp-blocks', $script );
}
add_action( 'enqueue_block_editor_assets', 'gutenberg_starter_theme_disable_editor_fullscreen_mode' );
