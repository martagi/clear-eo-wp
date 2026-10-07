<?php
/**
 * Theme supports, menus and assets.
 *
 * @package clear-eo
 */

defined( 'ABSPATH' ) || exit;

const CLEAR_EO_FONTS = 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Space+Grotesk:wght@400;500;600;700&display=swap';

add_action(
	'after_setup_theme',
	function () {
		load_theme_textdomain( 'clear-eo', get_theme_file_path( 'languages' ) );
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'align-wide' );
		add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
		add_theme_support( 'custom-logo', array( 'height' => 120, 'width' => 140, 'flex-height' => true, 'flex-width' => true ) );
		add_theme_support( 'editor-styles' );
		add_editor_style( array( CLEAR_EO_FONTS, 'assets/css/editor.css' ) );
		// The CLEAR-EO palette, for text and backgrounds in the block editor
		add_theme_support(
			'editor-color-palette',
			array(
				array( 'name' => __( 'Navy', 'clear-eo' ), 'slug' => 'navy', 'color' => '#205076' ),
				array( 'name' => __( 'Deep navy', 'clear-eo' ), 'slug' => 'deep-navy', 'color' => '#132435' ),
				array( 'name' => __( 'Sky blue', 'clear-eo' ), 'slug' => 'sky', 'color' => '#79c0db' ),
				array( 'name' => __( 'Teal green', 'clear-eo' ), 'slug' => 'teal', 'color' => '#48756a' ),
				array( 'name' => __( 'Sage', 'clear-eo' ), 'slug' => 'sage', 'color' => '#789686' ),
				array( 'name' => __( 'Cream', 'clear-eo' ), 'slug' => 'cream', 'color' => '#f3f2ea' ),
			)
		);
		register_nav_menus(
			array(
				'primary' => __( 'Top menu', 'clear-eo' ),
				'footer'  => __( 'Footer: Explore', 'clear-eo' ),
			)
		);
	}
);

// Event and webinar addresses (/events/…, /webinars/…, /applications/…) need fresh rewrite rules
add_action( 'after_switch_theme', 'flush_rewrite_rules' );

add_action(
	'wp_enqueue_scripts',
	function () {
		$ver = wp_get_theme()->get( 'Version' );
		wp_enqueue_style( 'clear-eo-fonts', CLEAR_EO_FONTS, array(), null ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
		wp_enqueue_style( 'clear-eo', get_stylesheet_uri(), array( 'clear-eo-fonts' ), $ver );
		wp_enqueue_script( 'clear-eo', get_theme_file_uri( 'assets/js/main.js' ), array(), $ver, array( 'in_footer' => true, 'strategy' => 'defer' ) );
	}
);

add_filter(
	'wp_resource_hints',
	function ( $urls, $type ) {
		if ( 'preconnect' === $type ) {
			$urls[] = 'https://fonts.googleapis.com';
			$urls[] = array( 'href' => 'https://fonts.gstatic.com', 'crossorigin' );
		}
		return $urls;
	},
	10,
	2
);

// Apply the visitor's light/dark choice before the page paints
add_action(
	'wp_head',
	function () {
		echo "<script>document.documentElement.classList.add('js');try{var t=localStorage.getItem('clear-eo-theme');if(t)document.documentElement.dataset.theme=t}catch(e){}</script>\n";
	},
	1
);

add_filter(
	'body_class',
	function ( $c ) {
		// Pages that open on a dark picture: the menu starts transparent with light text
		if ( is_front_page() || is_singular( 'clear_application' ) ) {
			$c[] = 'has-hero';
		}
		return $c;
	}
);

// Menu items written as "#about" point to that section of the home page from every page
add_filter(
	'nav_menu_link_attributes',
	function ( $atts ) {
		if ( ! empty( $atts['href'] ) ) {
			$atts['href'] = clear_eo_href( $atts['href'] );
		}
		return $atts;
	}
);

/** The home page sections, used by the menus until the editor builds their own. */
function clear_eo_default_menu( $footer = false ) {
	$items = array(
		'#about'        => __( 'About', 'clear-eo' ),
		'#applications' => __( 'Applications', 'clear-eo' ),
		'#observatory'  => __( 'Virtual Observatory', 'clear-eo' ),
		'#consortium'   => $footer ? __( 'Partners', 'clear-eo' ) : __( 'Consortium', 'clear-eo' ),
	);
	if ( $footer ) {
		$items['#collaborations'] = __( 'Collaborations', 'clear-eo' );
	}
	$items['#news'] = __( "What's new", 'clear-eo' );
	$out            = '';
	foreach ( $items as $href => $label ) {
		$out .= '<li><a href="' . esc_url( clear_eo_href( $href ) ) . '">' . esc_html( $label ) . '</a></li>';
	}
	return $out;
}

function clear_eo_menu( $location ) {
	if ( has_nav_menu( $location ) ) {
		wp_nav_menu(
			array(
				'theme_location' => $location,
				'container'      => false,
				'items_wrap'     => '%3$s',
				'depth'          => 1,
			)
		);
		return;
	}
	echo clear_eo_default_menu( 'footer' === $location ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in clear_eo_default_menu()
}

/** The site name with the part after the first hyphen highlighted, as in CLEAR<b>-EO</b>. */
function clear_eo_brand_name() {
	$name = get_bloginfo( 'name' );
	$at   = strpos( $name, '-' );
	return false === $at ? esc_html( $name ) : esc_html( substr( $name, 0, $at ) ) . '<b>' . esc_html( substr( $name, $at ) ) . '</b>';
}

function clear_eo_logo_url() {
	$id = get_theme_mod( 'custom_logo' );
	return $id ? wp_get_attachment_image_url( $id, 'medium' ) : get_theme_file_uri( 'assets/img/logo.png' );
}

/** The 12 stars of the EU flag. */
function clear_eo_eu_stars() {
	$out = '';
	for ( $i = 0; $i < 12; $i++ ) {
		$a  = $i * M_PI / 6;
		$cx = 405 + 180 * sin( $a );
		$cy = 270 - 180 * cos( $a );
		$p  = '';
		for ( $k = 0; $k < 10; $k++ ) {
			$b  = -M_PI / 2 + $k * M_PI / 5;
			$r  = $k % 2 ? 30 * 0.382 : 30;
			$p .= sprintf( '%.1f,%.1f ', $cx + $r * cos( $b ), $cy + $r * sin( $b ) );
		}
		$out .= '<polygon points="' . trim( $p ) . '"/>';
	}
	return $out;
}
