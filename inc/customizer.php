<?php
/**
 * gutenberg-starter-theme Theme Customizer
 *
 * @package gutenberg-starter-theme
 */

/**
 * Add Theme Options for the Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function gutenberg_starter_theme_customize_register( $wp_customize ) {
	
	/**
	 * Add postMessage support for site title and description for the Theme Customizer.
	 */
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';

	if ( isset( $wp_customize->selective_refresh ) ) {
		$wp_customize->selective_refresh->add_partial( 'blogname', array(
			'selector'        => '.site-title a',
			'render_callback' => 'gutenberg_starter_theme_customize_partial_blogname',
		) );
		$wp_customize->selective_refresh->add_partial( 'blogdescription', array(
			'selector'        => '.site-description',
			'render_callback' => 'gutenberg_starter_theme_customize_partial_blogdescription',
		) );
	}

	/**
	 * Create Theme Options panel
	 */
	$wp_customize->add_section( 'gutenberg_starter_theme_options', array(
		'capability' => 'edit_theme_options',
		'title'      => esc_html__( 'Theme Options', 'gutenberg-starter-theme' ),
	) );

	/**
	 * Add setting: Align Wide
	 */
	$wp_customize->add_setting( 'gutenberg_starter_theme_align_wide', array(
		'default'        => true
	 ) );
	$wp_customize->add_control( 'gutenberg_starter_theme_align_wide', array(
		'section'   => 'gutenberg_starter_theme_options',
		'label'     => __( 'Enable wide and full alignments.', 'gutenberg-starter-theme' ),
		'type'      => 'checkbox'
	) );

	/**
	 * Add setting: Color Palette
	 */
	$wp_customize->add_setting( 'gutenberg_starter_theme_color_palette', array(
		'default'        => true
	 ) );
	$wp_customize->add_control( 'gutenberg_starter_theme_color_palette', array(
		'section'   => 'gutenberg_starter_theme_options',
		'label'     => __( 'Enable custom color palette in Gutenberg.', 'gutenberg-starter-theme' ),
		'type'      => 'checkbox'
	) );

	/**
	 * Add setting: Dark Mode
	 */
	$wp_customize->add_setting( 'gutenberg_starter_theme_dark_mode', array (
		'default'        => false
	) );
	$wp_customize->add_control( 'gutenberg_starter_theme_dark_mode', array(
		'section'   => 'gutenberg_starter_theme_options',
		'label'     => __( 'Enable a dark theme style.', 'gutenberg-starter-theme' ),
		'type'      => 'checkbox'
	) );

	/**
	 * Add setting: Default Block Styles
	 */
	$wp_customize->add_setting( 'gutenberg_starter_theme_wp_block_styles', array(
		'default'        => true
	 ) );
	$wp_customize->add_control( 'gutenberg_starter_theme_wp_block_styles', array(
		'section'   => 'gutenberg_starter_theme_options',
		'label'     => __( 'Enable default core block styles on the front end.', 'gutenberg-starter-theme' ),
		'type'      => 'checkbox'
	) );

	/**
	 * Add setting: Responsive Embeds
	 */
	$wp_customize->add_setting( 'gutenberg_starter_theme_responsive_embeds', array(
		'default'        => true
	 ) );
	$wp_customize->add_control( 'gutenberg_starter_theme_responsive_embeds', array(
		'section'   => 'gutenberg_starter_theme_options',
		'label'     => __( 'Enable Enable responsive embedded content.', 'gutenberg-starter-theme' ),
		'type'      => 'checkbox'
	) );

}
add_action( 'customize_register', 'gutenberg_starter_theme_customize_register' );

/**
 * Render the site title for the selective refresh partial.
 *
 * @return void
 */
function gutenberg_starter_theme_customize_partial_blogname() {
	bloginfo( 'name' );
}

/**
 * Render the site tagline for the selective refresh partial.
 *
 * @return void
 */
function gutenberg_starter_theme_customize_partial_blogdescription() {
	bloginfo( 'description' );
}

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function gutenberg_starter_theme_customize_preview_js() {
	wp_enqueue_script( 'gutenberg-starter-theme-customizer', get_template_directory_uri() . '/js/customizer.js', array( 'customize-preview' ), '20151215', true );
}
add_action( 'customize_preview_init', 'gutenberg_starter_theme_customize_preview_js' );
