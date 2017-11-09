<?php
/**
 * gutenbergtheme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Gutenbergtheme
 */

if ( ! defined( 'WPORGPATH' ) ) {
	define( 'WPORGPATH', get_theme_file_path( '/inc/' ) );
}

if ( ! function_exists( 'gutenbergtheme_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function gutenbergtheme_setup() {
		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on gutenbergtheme, use a find and replace
		 * to change 'gutenbergtheme' to the name of your theme in all the template files.
		 */
		load_theme_textdomain( 'gutenbergtheme', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

		// This theme uses wp_nav_menu() in one location.
		register_nav_menus( array(
			'menu-1' => esc_html__( 'Primary', 'theme' ),
		) );

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support( 'html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		) );

		add_theme_support( 'gutenberg', array(
			'wide-images' => true,
   		'colors' => array(
				'#0073aa',
				'#229fd8',
				'#eee',
				'#444',
			),
		) );
	}
endif;
add_action( 'after_setup_theme', 'gutenbergtheme_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function gutenbergtheme_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'gutenbergtheme_content_width', 640 );
}
add_action( 'after_setup_theme', 'gutenbergtheme_content_width', 0 );

/**
 * Register Google Fonts
 */
function gutenbergtheme_fonts_url() {
    $fonts_url = '';

    /* Translators: If there are characters in your language that are not
	 * supported by Karla, translate this to 'off'. Do not translate
	 * into your own language.
	 */
	$notoserif = esc_html_x( 'on', 'Noto Serif font: on or off', 'karuna' );

	if ( 'off' !== $notoserif ) {
		$font_families = array();
		$font_families[] = 'Noto Serif:400,400italic,700,700italic';

		$query_args = array(
			'family' => urlencode( implode( '|', $font_families ) ),
			'subset' => urlencode( 'latin,latin-ext' ),
		);

		$fonts_url = add_query_arg( $query_args, 'https://fonts.googleapis.com/css' );
	}

	return $fonts_url;

}

/**
 * Enqueue scripts and styles.
 */
function gutenbergtheme_scripts() {
	wp_enqueue_style( 'gutenbergtheme-style', get_stylesheet_uri() );

	wp_enqueue_style( 'gutenbergthemeblocks-style', get_template_directory_uri() . '/blocks.css');

	wp_enqueue_style( 'gutenbergtheme-prism', gutenbergtheme_fonts_url(), array(), null );

	wp_enqueue_script( 'gutenbergtheme-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20151215', true );

	wp_enqueue_script( 'gutenbergtheme-prism', get_template_directory_uri() . '/js/prism.js', array(), null );

	wp_enqueue_script( 'gutenbergtheme-handbook', get_template_directory_uri() . '/js/handbook.js', array( 'jquery' ), null );

	wp_enqueue_script( 'gutenbergtheme-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20151215', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'gutenbergtheme_scripts' );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}

/**
 * Gutenberg documentation
 */
require __DIR__ . '/inc/docs-importer.php';

add_action( 'load-post.php', array( 'Import_Gutendocs', 'action_load_post_php' ) );
add_action( 'edit_form_after_title', array( 'Import_Gutendocs', 'action_edit_form_after_title' ) );
add_action( 'save_post', array( 'Import_Gutendocs', 'action_save_post' ) );
add_filter( 'cron_schedules', array( 'Import_Gutendocs', 'filter_cron_schedules' ) );
add_action( 'init', array( 'Import_Gutendocs', 'action_init' ) );
add_action( 'wporg_gutenberg_manifest_import', array( 'Import_Gutendocs', 'action_wporg_gutenberg_manifest_import' ) );
add_action( 'wporg_gutenberg_markdown_import', array( 'Import_Gutendocs', 'action_wporg_gutenberg_markdown_import' ) );
