<?php 

/**
 * Set up a WP-Admin page for managing turning on and off theme features.
 */
function gutenbergtheme_add_options_page() {
	add_menu_page(
		'Theme Options',
		'Theme Options',
		'manage_options',
		'gutenbergtheme-options',
		'gutenbergtheme_options_page'
	);

	// Call register settings function
	add_action( 'admin_init', 'gutenbergtheme_options' );
}
add_action( 'admin_menu', 'gutenbergtheme_add_options_page' );


/**
 * Register settings for the WP-Admin page.
 */
function gutenbergtheme_options() {
	register_setting( 'gutenbergtheme-options', 'gutenbergtheme-align-wide', array( 'default' => 1 ) );
	register_setting( 'gutenbergtheme-options', 'gutenbergtheme-wp-block-styles', array( 'default' => 1 ) );
	register_setting( 'gutenbergtheme-options', 'gutenbergtheme-editor-color-palette', array( 'default' => 1 ) );
	register_setting( 'gutenbergtheme-options', 'gutenbergtheme-dark-mode' );
	register_setting( 'gutenbergtheme-options', 'gutenbergtheme-responsive-embeds', array( 'default' => 1 ) );
}


/**
 * Build the WP-Admin settings page.
 */
function gutenbergtheme_options_page() { ?>
	<div class="wrap">
	<h1><?php _e('Gutenberg Starter Theme Options', 'gutenbergtheme'); ?></h1>
	<form method="post" action="options.php">
		<?php settings_fields( 'gutenbergtheme-options' ); ?>
		<?php do_settings_sections( 'gutenbergtheme-options' ); ?>

			<table class="form-table">
				<tr valign="top">
					<td>
						<label>
							<input name="gutenbergtheme-align-wide" type="checkbox" value="1" <?php checked( '1', get_option( 'gutenbergtheme-align-wide' ) ); ?> />
							<?php _e( 'Enable wide and full alignments.', 'gutenbergtheme' ); ?>
							(<a href="https://developer.wordpress.org/block-editor/developers/themes/theme-support/#wide-alignment"><code>align-wide</code></a>)
						</label>
					</td>
				</tr>
				<tr valign="top">
					<td>
						<label>
							<input name="gutenbergtheme-editor-color-palette" type="checkbox" value="1" <?php checked( '1', get_option( 'gutenbergtheme-editor-color-palette' ) ); ?> />
							<?php _e( 'Enable a custom theme color palette.', 'gutenbergtheme' ); ?>
							(<a href="https://developer.wordpress.org/block-editor/developers/themes/theme-support/#block-color-palettes"><code>editor-color-palette</code></a>)
						</label>
					</td>
				</tr>
				<tr valign="top">
					<td>
						<label>
							<input name="gutenbergtheme-dark-mode" type="checkbox" value="1" <?php checked( '1', get_option( 'gutenbergtheme-dark-mode' ) ); ?> />
							<?php _e( 'Enable a dark theme style for the editor.', 'gutenbergtheme' ); ?>
							(<a href="https://developer.wordpress.org/block-editor/developers/themes/theme-support/#dark-backgrounds"><code>dark-editor-style</code></a>)
						</label>
					</td>
				</tr>
				<tr valign="top">
					<td>
						<label>
							<input name="gutenbergtheme-wp-block-styles" type="checkbox" value="1" <?php checked( '1', get_option( 'gutenbergtheme-wp-block-styles' ) ); ?> />
							<?php _e( 'Enable core block styles on the front end.', 'gutenbergtheme' ); ?>
							(<a href="https://developer.wordpress.org/block-editor/developers/themes/theme-support/#default-block-styles"><code>wp-block-styles</code></a>)
						</label>
					</td>
				</tr>
				<tr valign="top">
					<td>
						<label>
							<input name="gutenbergtheme-responsive-embeds" type="checkbox" value="1" <?php checked( '1', get_option( 'gutenbergtheme-responsive-embeds' ) ); ?> />
							<?php _e( 'Enable responsive embedded content.', 'gutenbergtheme' ); ?>
							(<a href="https://developer.wordpress.org/block-editor/developers/themes/theme-support/#responsive-embedded-content"><code>responsive-embeds</code></a>)
						</label>
					</td>
				</tr>
			</table>

		<?php submit_button(); ?>
	</form>
	</div>
<?php }


/**
 * Enable alignwide and alignfull support if the gutenbergtheme-align-wide setting is active.
 */
function gutenbergtheme_enable_align_wide() {

	if ( get_option( 'gutenbergtheme-align-wide', 1 ) == 1 ) {
		
		// Add support for full and wide align images.
		add_theme_support( 'align-wide' );
	}
}
add_action( 'after_setup_theme', 'gutenbergtheme_enable_align_wide' );


/**
 * Enable custom theme colors if the gutenbergtheme-editor-color-palette setting is active.
 */
function gutenbergtheme_enable_editor_color_palette() {
	if ( get_option( 'gutenbergtheme-editor-color-palette', 1 ) == 1 ) {
		
		// Add support for a custom color scheme.
		add_theme_support( 'editor-color-palette', array(
			array(
				'name'  => __( 'Strong Blue', 'gutenbergtheme' ),
				'slug'  => 'strong-blue',
				'color' => '#0073aa',
			),
			array(
				'name'  => __( 'Lighter Blue', 'gutenbergtheme' ),
				'slug'  => 'lighter-blue',
				'color' => '#229fd8',
			),
			array(
				'name'  => __( 'Very Light Gray', 'gutenbergtheme' ),
				'slug'  => 'very-light-gray',
				'color' => '#eee',
			),
			array(
				'name'  => __( 'Very Dark Gray', 'gutenbergtheme' ),
				'slug'  => 'very-dark-gray',
				'color' => '#444',
			),
		) );
	}
}
add_action( 'after_setup_theme', 'gutenbergtheme_enable_editor_color_palette' );


/**
 * Enable dark mode if gutenbergtheme-dark-mode setting is active.
 */
function gutenbergtheme_enable_dark_mode() {
	if ( get_option( 'gutenbergtheme-dark-mode' ) == 1 ) {
		
		// Add support for editor styles.
		add_theme_support( 'editor-styles' );
		add_editor_style( 'style-editor-dark.css' );
		
		// Add support for dark styles.
		add_theme_support( 'dark-editor-style' );
	}
}
add_action( 'after_setup_theme', 'gutenbergtheme_enable_dark_mode' );


/**
 * Enable core block styles support if the gutenbergtheme-wp-block-styles setting is active.
 */
function gutenbergtheme_enable_wp_block_styles() {

	if ( get_option( 'gutenbergtheme-wp-block-styles', 1 ) == 1 ) {
		
		// Adding support for core block visual styles.
		add_theme_support( 'wp-block-styles' );
	}
}
add_action( 'after_setup_theme', 'gutenbergtheme_enable_wp_block_styles' );


/**
 * Enable responsive embedded content if the gutenbergtheme-responsive-embeds setting is active.
 */
function gutenbergtheme_enable_responsive_embeds() {

	if ( get_option( 'gutenbergtheme-responsive-embeds', 1 ) == 1 ) {
		
		// Adding support for responsive embedded content.
		add_theme_support( 'responsive-embeds' );
	}
}
add_action( 'after_setup_theme', 'gutenbergtheme_enable_responsive_embeds' );