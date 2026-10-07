<?php
/**
 * CLEAR-EO theme.
 *
 * The one-page home (front-page.php) is built from:
 *   - page texts in Appearance → Customize → CLEAR-EO page sections (inc/options.php)
 *   - Applications and Partners, two content types of their own (inc/post-types.php)
 *   - What's new: Posts (labelled Newsletter), Events and Webinars, merged and filterable
 * Tools → CLEAR-EO content imports the CLEAR-EO website's content (inc/importer.php).
 *
 * @package clear-eo
 */

defined( 'ABSPATH' ) || exit;

foreach ( array( 'markdown', 'options', 'setup', 'post-types', 'meta-boxes', 'content', 'importer' ) as $clear_eo_file ) {
	require_once get_theme_file_path( "inc/$clear_eo_file.php" );
}
unset( $clear_eo_file );
