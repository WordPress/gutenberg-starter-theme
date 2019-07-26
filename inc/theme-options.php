<?php 

/**
 * Enable alignwide and alignfull support if the gutenberg-starter-theme-align-wide setting is active.
 */
function gutenberg_starter_theme_enable_align_wide() {
	if ( true === get_theme_mod( 'gutenberg_starter_theme_align_wide' ) ) {
		
		// Add support for full and wide align images.
		add_theme_support( 'align-wide' );
	}
}
add_action( 'after_setup_theme', 'gutenberg_starter_theme_enable_align_wide' );

/**
 * Enable custom theme colors if the gutenberg-starter-theme-editor-color-palette setting is active.
 */
function gutenberg_starter_theme_enable_editor_color_palette() {
	if ( true === get_theme_mod( 'gutenberg_starter_theme_color_palette' ) ) {
		
		// Add support for a custom color scheme.
		add_theme_support( 'editor-color-palette', array(
			array(
				'name'  => __( 'Strong Blue', 'gutenberg-starter-theme' ),
				'slug'  => 'strong-blue',
				'color' => '#0073aa',
			),
			array(
				'name'  => __( 'Lighter Blue', 'gutenberg-starter-theme' ),
				'slug'  => 'lighter-blue',
				'color' => '#229fd8',
			),
			array(
				'name'  => __( 'Very Light Gray', 'gutenberg-starter-theme' ),
				'slug'  => 'very-light-gray',
				'color' => '#eee',
			),
			array(
				'name'  => __( 'Very Dark Gray', 'gutenberg-starter-theme' ),
				'slug'  => 'very-dark-gray',
				'color' => '#444',
			),
		) );
	}
}
add_action( 'after_setup_theme', 'gutenberg_starter_theme_enable_editor_color_palette' );

/**
 * Enable dark mode if gutenberg-starter-theme-dark-mode setting is active.
 */
function gutenberg_starter_theme_enable_dark_mode() {
	if ( true === get_theme_mod( 'gutenberg_starter_theme_dark_mode' ) ) {
		
		// Add support for editor styles.
		add_theme_support( 'editor-styles' );
		add_editor_style( 'style-editor-dark.css' );
		
		// Add support for dark styles.
		add_theme_support( 'dark-editor-style' );
	}
}
add_action( 'after_setup_theme', 'gutenberg_starter_theme_enable_dark_mode' );

/**
 * Enable dark mode on the front end if gutenberg-starter-theme-dark-mode setting is active.
 */
function gutenberg_starter_theme_enable_dark_mode_frontend_styles() {
	if ( true === get_theme_mod( 'gutenberg_starter_theme_dark_mode' ) ) {
		wp_enqueue_style( 'gutenberg-starter-themedark-style', get_template_directory_uri() . '/css/dark-mode.css' );
	}
}
add_action( 'wp_enqueue_scripts', 'gutenberg_starter_theme_enable_dark_mode_frontend_styles' );

/**
 * Enable core block styles support if the gutenberg-starter-theme-wp-block-styles setting is active.
 */
function gutenberg_starter_theme_enable_wp_block_styles() {
	if ( true === get_theme_mod( 'gutenberg_starter_theme_wp_block_styles' ) ) {
		
		// Adding support for core block visual styles.
		add_theme_support( 'wp-block-styles' );
	}
}
add_action( 'after_setup_theme', 'gutenberg_starter_theme_enable_wp_block_styles' );

/**
 * Enable responsive embedded content if the gutenberg-starter-theme-responsive-embeds setting is active.
 */
function gutenberg_starter_theme_enable_responsive_embeds() {
	if ( true === get_theme_mod( 'gutenberg_starter_theme_responsive_embeds' ) ) {
		
		// Adding support for responsive embedded content.
		add_theme_support( 'responsive-embeds' );
	}
}
add_action( 'after_setup_theme', 'gutenberg_starter_theme_enable_responsive_embeds' );