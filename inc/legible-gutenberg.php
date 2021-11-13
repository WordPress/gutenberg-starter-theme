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

