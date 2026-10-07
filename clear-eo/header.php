<?php
/**
 * Top of every page: the menu bar, transparent over the dark banner and frosted once the page scrolls.
 *
 * @package clear-eo
 */

?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip" href="#main"><?php esc_html_e( 'Skip to content', 'clear-eo' ); ?></a>

<header class="nav" id="nav">
	<div class="wrap nav-inner">
		<a class="brand" href="<?php echo esc_url( is_front_page() ? '#top' : home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: site name */ __( '%s home', 'clear-eo' ), get_bloginfo( 'name' ) ) ); ?>">
			<img class="brand-mark" src="<?php echo esc_url( clear_eo_logo_url() ); ?>" alt="" width="689" height="594">
			<span><?php echo clear_eo_brand_name(); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped in the function ?></span>
		</a>
		<button class="icon-btn menu-btn" id="menuBtn" aria-expanded="false" aria-controls="navLinks" aria-label="<?php esc_attr_e( 'Open menu', 'clear-eo' ); ?>">
			<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
		</button>
		<ul class="nav-links" id="navLinks">
			<?php clear_eo_menu( 'primary' ); ?>
		</ul>
		<button class="icon-btn" id="themeBtn" aria-label="<?php esc_attr_e( 'Toggle dark mode', 'clear-eo' ); ?>">
			<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z"/></svg>
		</button>
	</div>
</header>
