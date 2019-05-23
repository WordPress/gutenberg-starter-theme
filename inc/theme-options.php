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
	register_setting( 'gutenbergtheme-options', 'gutenbergtheme-dark-mode' );
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
							<input name="gutenbergtheme-dark-mode" type="checkbox" value="1" <?php checked( '1', get_option( 'gutenbergtheme-dark-mode' ) ); ?> />
							<?php _e( 'Enable a dark theme style for the editor.', 'gutenbergtheme' ); ?>
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
