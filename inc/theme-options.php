<?php 

/**
 * Set up a WP-Admin page for managing turning on and off theme features.
 */
function gutenberg_starter_theme_add_options_page() {
	add_theme_page(
		'Theme Options',
		'Theme Options',
		'manage_options',
		'gutenberg-starter-theme-options',
		'gutenberg_starter_theme_options_page'
	);

	// Call register settings function
	add_action( 'admin_init', 'gutenberg_starter_theme_options' );
}
add_action( 'admin_menu', 'gutenberg_starter_theme_add_options_page' );


/**
 * Register settings for the WP-Admin page.
 */
function gutenberg_starter_theme_options() {
	register_setting( 'gutenberg-starter-theme-options', 'gutenberg-starter-theme-dark-mode' );
}


/**
 * Build the WP-Admin settings page.
 */
function gutenberg_starter_theme_options_page() { ?>
	<div class="wrap">
	<h1><?php _e('Gutenberg Starter Theme Options', 'gutenberg-starter-theme'); ?></h1>

	<form method="post" action="options.php">
		<?php settings_fields( 'gutenberg-starter-theme-options' ); ?>
		<?php do_settings_sections( 'gutenberg-starter-theme-options' ); ?>

			<table class="form-table">
				<tr valign="top">
					<td>
						<label>
							<input name="gutenberg-starter-theme-dark-mode" type="checkbox" value="1" <?php checked( '1', get_option( 'gutenberg-starter-theme-dark-mode' ) ); ?> />
							<?php _e( 'Enable a dark theme style.', 'gutenberg-starter-theme' ); ?>
							(<a href="https://developer.wordpress.org/block-editor/developers/themes/theme-support/#dark-backgrounds"><code>dark-editor-style</code></a>)
						</label>
					</td>
				</tr>
			</table>

		<?php submit_button(); ?>
	</form>
	</div>
<?php }


/**
 * Enable dark mode if gutenberg-starter-theme-dark-mode setting is active.
 */
function gutenberg_starter_theme_enable_dark_mode() {
	if ( get_option( 'gutenberg-starter-theme-dark-mode' ) == 1 ) {
		
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
	if ( get_option( 'gutenberg-starter-theme-dark-mode' ) == 1 ) {
		wp_enqueue_style( 'gutenberg-starter-themedark-style', get_template_directory_uri() . '/css/dark-mode.css' );
	}
}
add_action( 'wp_enqueue_scripts', 'gutenberg_starter_theme_enable_dark_mode_frontend_styles' );