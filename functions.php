<?php
/**
 * Lumina theme functions and setup.
 *
 * @package Lumina
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'LUMINA_VERSION', '1.0.0' );

/**
 * Theme setup: supports, menus, widget areas.
 */
function lumina_setup() {
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'html5', array( 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'search-form' ) );
	add_theme_support( 'custom-logo', array(
		'height'      => 64,
		'width'       => 220,
		'flex-height' => true,
		'flex-width'  => true,
	) );
	add_theme_support( 'automatic-feed-links' );

	register_nav_menus( array(
		'primary' => __( 'Primary menu', 'lumina' ),
		'footer'  => __( 'Footer menu', 'lumina' ),
	) );

	register_sidebar( array(
		'name'          => __( 'Footer widgets', 'lumina' ),
		'id'            => 'footer-widgets',
		'description'   => __( 'Widgets shown above the footer on every page.', 'lumina' ),
		'before_widget' => '<div class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
}
add_action( 'after_setup_theme', 'lumina_setup' );

/**
 * Enqueue front-end assets: Google Fonts, theme stylesheet, interactions.
 */
function lumina_assets() {
	wp_enqueue_style(
		'lumina-fonts',
		'https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600;9..144,700&family=Manrope:wght@400;500;600;700;800&display=swap',
		array(),
		null
	);
	wp_enqueue_style( 'lumina-style', get_stylesheet_uri(), array( 'lumina-fonts' ), LUMINA_VERSION );
	wp_enqueue_script( 'lumina-theme', get_template_directory_uri() . '/assets/js/theme.js', array(), LUMINA_VERSION, true );
}
add_action( 'wp_enqueue_scripts', 'lumina_assets' );

/**
 * Enqueue editor stylesheet.
 */
function lumina_editor_assets() {
	add_editor_style( 'assets/css/editor.css' );
}
add_action( 'after_setup_theme', 'lumina_editor_assets' );

/**
 * Register the Lumina block pattern category.
 */
function lumina_pattern_category() {
	register_block_pattern_category( 'lumina', array(
		'label' => __( 'Lumina', 'lumina' ),
	) );
}
add_action( 'init', 'lumina_pattern_category' );

/**
 * Custom block styles: soft card, glow button, rounded image.
 */
function lumina_block_styles() {
	register_block_style( 'core/group', array(
		'name'  => 'lumina-card',
		'label' => __( 'Lumina card', 'lumina' ),
	) );
	register_block_style( 'core/button', array(
		'name'  => 'lumina-glow',
		'label' => __( 'Lumina glow', 'lumina' ),
	) );
	register_block_style( 'core/image', array(
		'name'  => 'lumina-round',
		'label' => __( 'Lumina rounded', 'lumina' ),
	) );
	register_block_style( 'core/quote', array(
		'name'  => 'lumina-pull',
		'label' => __( 'Lumina pull quote', 'lumina' ),
	) );
}
add_action( 'init', 'lumina_block_styles' );

/**
 * Custom excerpt length for cards and archives.
 */
function lumina_excerpt_length( $length ) {
	return 24;
}
add_filter( 'excerpt_length', 'lumina_excerpt_length' );

/**
 * Inline SVG icon helper.
 *
 * @param string $name Icon slug: check, star, phone, pin, clock, arrow.
 * @return string SVG markup.
 */
function lumina_icon( $name ) {
	$icons = array(
		'check' => '<path d="M20 6 9 17l-5-5"/>',
		'star'  => '<path d="M12 2l2.9 6.3 6.9.8-5.1 4.7 1.4 6.8L12 17.2 5.9 20.6l1.4-6.8L2.2 9.1l6.9-.8z"/>',
		'phone' => '<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.13.96.36 1.9.7 2.8a2 2 0 0 1-.45 2.1L8.1 9.9a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.45c.9.34 1.84.57 2.8.7A2 2 0 0 1 22 16.9z"/>',
		'pin'   => '<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0z"/><circle cx="12" cy="10" r="3"/>',
		'clock' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
		'arrow' => '<path d="M5 12h14M13 6l6 6-6 6"/>',
	);
	$path = isset( $icons[ $name ] ) ? $icons[ $name ] : $icons['check'];
	return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $path . '</svg>';
}

/**
 * Estimated reading time for a post.
 *
 * @param int $post_id Post ID.
 * @return string Localised reading-time label.
 */
function lumina_reading_time( $post_id = 0 ) {
	$post_id  = $post_id ? $post_id : get_the_ID();
	$content  = get_post_field( 'post_content', $post_id );
	$words    = str_word_count( wp_strip_all_tags( $content ) );
	$minutes  = max( 1, (int) ceil( $words / 200 ) );
	return sprintf(
		/* translators: %d: number of minutes */
		_n( '%d min read', '%d min read', $minutes, 'lumina' ),
		$minutes
	);
}
