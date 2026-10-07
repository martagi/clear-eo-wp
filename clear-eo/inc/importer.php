<?php
/**
 * Tools → CLEAR-EO content: imports the static site's content (bundled in import/) as WordPress content.
 *
 *   content/news/*.json      → Posts (Newsletter)
 *   content/events/*.json    → Events
 *   content/webinars/*.json  → Webinars
 *   applications.json        → Applications
 *   partners.json            → Partners (the section texts stay in the Customizer)
 *
 * Pictures are added to the Media Library; Markdown texts are converted to HTML. Items imported before
 * (matched by their source id) are skipped, so running it twice adds nothing.
 *
 * @package clear-eo
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'admin_menu',
	function () {
		add_management_page( __( 'CLEAR-EO content', 'clear-eo' ), __( 'CLEAR-EO content', 'clear-eo' ), 'manage_options', 'clear-eo-import', 'clear_eo_import_page' );
	}
);

// After the theme is switched on: point to the importer while the site has no applications yet
add_action(
	'admin_notices',
	function () {
		if ( ! current_user_can( 'manage_options' ) || get_option( 'clear_eo_imported' ) || ( isset( $_GET['page'] ) && 'clear-eo-import' === $_GET['page'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			return;
		}
		if ( wp_count_posts( 'clear_application' )->publish > 0 ) {
			return;
		}
		printf(
			'<div class="notice notice-info"><p>%1$s <a class="button button-primary" href="%2$s">%3$s</a></p></div>',
			esc_html__( 'The CLEAR-EO theme is active. Import the project\'s applications, partners, newsletters, events and webinars to fill the home page.', 'clear-eo' ),
			esc_url( admin_url( 'tools.php?page=clear-eo-import' ) ),
			esc_html__( 'Import CLEAR-EO content', 'clear-eo' )
		);
	}
);

function clear_eo_import_page() {
	$log = null;
	if ( isset( $_POST['clear_eo_import'] ) && check_admin_referer( 'clear_eo_import' ) ) {
		$log = clear_eo_import();
	}
	echo '<div class="wrap"><h1>' . esc_html__( 'CLEAR-EO content', 'clear-eo' ) . '</h1>';
	if ( $log ) {
		echo '<div class="notice notice-success"><p>' . esc_html__( 'Import finished.', 'clear-eo' ) . '</p><ul style="list-style:disc;padding-left:20px">';
		foreach ( $log as $line ) {
			echo '<li>' . esc_html( $line ) . '</li>';
		}
		echo '</ul><p><a class="button" href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'View the site', 'clear-eo' ) . '</a></p></div>';
	}
	echo '<p>' . esc_html__( 'Adds the content of the CLEAR-EO website that comes with the theme: applications, partners and affiliated entities, newsletter items, events and webinars, with their pictures. Items that were imported before are skipped, so nothing is duplicated.', 'clear-eo' ) . '</p>';
	echo '<p>' . esc_html__( 'The texts of the page sections (top banner, key facts, About, Virtual Observatory, footer…) are already in place: change them under Appearance → Customize → CLEAR-EO page sections.', 'clear-eo' ) . '</p>';
	echo '<form method="post">';
	wp_nonce_field( 'clear_eo_import' );
	echo '<p><button class="button button-primary" name="clear_eo_import" value="1">' . esc_html__( 'Import CLEAR-EO content', 'clear-eo' ) . '</button></p></form></div>';
}

/** Runs the import and returns a log, one line per step. */
function clear_eo_import() {
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';
	if ( function_exists( 'set_time_limit' ) ) {
		set_time_limit( 300 );
	}
	$dir = get_theme_file_path( 'import/content/' );
	$log = array();

	// Partners and affiliated entities
	$partners = json_decode( file_get_contents( $dir . 'partners.json' ), true );
	$count    = 0;
	$order    = 0;
	foreach ( array( 'partners' => false, 'affiliated' => true ) as $list => $aff ) {
		foreach ( (array) $partners[ $list ] as $p ) {
			$order += 10;
			$id     = clear_eo_import_post(
				'partner:' . $p['name'],
				array(
					'post_type'  => 'clear_partner',
					'post_title' => $p['name'],
					'menu_order' => $order,
				),
				array(
					'_ce_country'     => isset( $p['country'] ) ? $p['country'] : '',
					'_ce_link'        => isset( $p['link'] ) ? $p['link'] : '',
					'_ce_role'        => isset( $p['role'] ) ? $p['role'] : '',
					'_ce_description' => isset( $p['description'] ) ? $p['description'] : '',
					'_ce_affiliated'  => $aff ? 1 : '',
				)
			);
			if ( $id && ! empty( $p['logo'] ) ) {
				$logo = clear_eo_import_image( $p['logo'], $id );
				if ( $logo ) {
					set_post_thumbnail( $id, $logo );
					update_post_meta( $logo, '_wp_attachment_image_alt', isset( $p['logo_alt'] ) ? $p['logo_alt'] : $p['name'] . ' logo' );
				}
			}
			$count += $id ? 1 : 0;
		}
	}
	/* translators: %d: number of items */
	$log[] = sprintf( __( 'Partners and affiliated entities: %d added.', 'clear-eo' ), $count );

	// Applications
	$apps  = json_decode( file_get_contents( $dir . 'applications.json' ), true );
	$count = 0;
	foreach ( (array) $apps['items'] as $i => $a ) {
		$id = clear_eo_import_post(
			'application:' . $a['theme'],
			array(
				'post_type'    => 'clear_application',
				'post_title'   => $a['title'],
				'post_excerpt' => $a['summary'],
				'post_content' => clear_eo_import_html( isset( $a['page'] ) ? $a['page'] : '' ),
				'menu_order'   => ( $i + 1 ) * 10,
			),
			array(
				'_ce_theme'    => $a['theme'],
				'_ce_tab'      => $a['tab'],
				'_ce_location' => isset( $a['location'] ) ? $a['location'] : '',
				'_ce_sections' => array_map(
					function ( $s ) {
						return array(
							'heading' => isset( $s['heading'] ) ? $s['heading'] : '',
							'style'   => isset( $s['style'] ) ? $s['style'] : 'list',
							'items'   => isset( $s['items'] ) ? array_values( (array) $s['items'] ) : array(),
						);
					},
					(array) $a['sections']
				),
			)
		);
		if ( $id && ! empty( $a['image'] ) ) {
			$img = clear_eo_import_image( $a['image'], $id );
			if ( $img ) {
				set_post_thumbnail( $id, $img );
			}
		}
		$count += $id ? 1 : 0;
	}
	/* translators: %d: number of items */
	$log[] = sprintf( __( 'Applications: %d added.', 'clear-eo' ), $count );

	// What's new: newsletter items, events, webinars
	$sections = array(
		'news'     => array( 'post', __( 'Newsletter items', 'clear-eo' ) ),
		'events'   => array( 'clear_event', __( 'Events', 'clear-eo' ) ),
		'webinars' => array( 'clear_webinar', __( 'Webinars', 'clear-eo' ) ),
	);
	foreach ( $sections as $folder => $s ) {
		$count = 0;
		foreach ( glob( $dir . $folder . '/*.json' ) as $file ) {
			$n    = json_decode( file_get_contents( $file ), true );
			$date = substr( $n['date'], 0, 10 );
			// Posts dated in the future would be "scheduled": events and webinars keep their date in _ce_date
			$post_date = min( $date . ' 09:00:00', current_time( 'mysql' ) );
			$meta      = array( '_ce_link' => isset( $n['link'] ) ? $n['link'] : '' );
			if ( 'news' !== $folder ) {
				$meta['_ce_date'] = $date;
			}
			if ( 'events' === $folder ) {
				$meta['_ce_end_date'] = isset( $n['end_date'] ) ? substr( $n['end_date'], 0, 10 ) : '';
				$meta['_ce_location'] = isset( $n['location'] ) ? $n['location'] : '';
			}
			if ( 'webinars' === $folder ) {
				foreach ( array( 'time', 'registration', 'recording' ) as $k ) {
					$meta[ '_ce_' . $k ] = isset( $n[ $k ] ) ? $n[ $k ] : '';
				}
				$meta['_ce_speakers'] = isset( $n['speakers'] ) ? (array) $n['speakers'] : array();
			}
			$id = clear_eo_import_post(
				$folder . ':' . basename( $file, '.json' ),
				array(
					'post_type'     => $s[0],
					'post_title'    => $n['title'],
					'post_excerpt'  => isset( $n['summary'] ) ? $n['summary'] : '',
					'post_content'  => clear_eo_import_html( isset( $n['body'] ) ? $n['body'] : '' ),
					'post_date'     => $post_date,
					'post_date_gmt' => get_gmt_from_date( $post_date ),
				),
				$meta
			);
			if ( ! $id ) {
				continue;
			}
			if ( ! empty( $n['image'] ) ) {
				$img = clear_eo_import_image( $n['image'], $id );
				if ( $img ) {
					set_post_thumbnail( $id, $img );
				}
			}
			if ( ! empty( $n['card_image'] ) ) {
				$img = clear_eo_import_image( $n['card_image'], $id );
				if ( $img ) {
					update_post_meta( $id, '_ce_card_image', $img );
				}
			}
			++$count;
		}
		/* translators: 1: content type, 2: number of items */
		$log[] = sprintf( __( '%1$s: %2$d added.', 'clear-eo' ), $s[1], $count );
	}

	// WordPress's sample post would otherwise show up in What's new
	$hello = get_page_by_path( 'hello-world', OBJECT, 'post' );
	if ( $hello && 'publish' === $hello->post_status ) {
		wp_trash_post( $hello->ID );
		$log[] = __( 'The sample post "Hello world!" was moved to the bin.', 'clear-eo' );
	}

	update_option( 'clear_eo_imported', time() );
	flush_rewrite_rules();
	return $log;
}

/**
 * Creates a published post unless one with this source id exists. Returns the new post's id, or 0 when it
 * was imported before.
 */
function clear_eo_import_post( $source, array $post, array $meta ) {
	$found = get_posts(
		array(
			'post_type'   => $post['post_type'],
			'post_status' => 'any',
			'meta_key'    => '_ce_import_id', // phpcs:ignore WordPress.DB.SlowDBQuery
			'meta_value'  => $source, // phpcs:ignore WordPress.DB.SlowDBQuery
			'fields'      => 'ids',
			'numberposts' => 1,
		)
	);
	if ( $found ) {
		return 0;
	}
	$id = wp_insert_post( wp_slash( $post + array( 'post_status' => 'publish' ) ), true );
	if ( is_wp_error( $id ) ) {
		return 0;
	}
	update_post_meta( $id, '_ce_import_id', $source );
	foreach ( $meta as $k => $v ) {
		if ( '' !== $v && array() !== $v ) {
			update_post_meta( $id, $k, wp_slash( $v ) );
		}
	}
	return $id;
}

/** Adds a bundled picture ("_assets/…") to the Media Library once, and returns its attachment id. */
function clear_eo_import_image( $path, $parent = 0 ) {
	$path  = rawurldecode( $path );
	$found = get_posts(
		array(
			'post_type'   => 'attachment',
			'post_status' => 'inherit',
			'meta_key'    => '_ce_import_id', // phpcs:ignore WordPress.DB.SlowDBQuery
			'meta_value'  => $path, // phpcs:ignore WordPress.DB.SlowDBQuery
			'fields'      => 'ids',
			'numberposts' => 1,
		)
	);
	if ( $found ) {
		return (int) $found[0];
	}
	$file = get_theme_file_path( 'import/' . $path );
	if ( ! file_exists( $file ) ) {
		$file = get_theme_file_path( 'assets/img/' . substr( $path, 8 ) );
	}
	if ( ! file_exists( $file ) ) {
		return 0;
	}
	$tmp = wp_tempnam( basename( $file ) );
	copy( $file, $tmp );
	$id = media_handle_sideload(
		array(
			'name'     => sanitize_file_name( remove_accents( basename( $file ) ) ),
			'tmp_name' => $tmp,
		),
		$parent
	);
	if ( is_wp_error( $id ) ) {
		wp_delete_file( $tmp );
		return 0;
	}
	update_post_meta( $id, '_ce_import_id', $path );
	return (int) $id;
}

/** Markdown from the static site → HTML, with its pictures moved to the Media Library. */
function clear_eo_import_html( $markdown ) {
	if ( '' === trim( (string) $markdown ) ) {
		return '';
	}
	return preg_replace_callback(
		'/src="(_assets\/[^"]+)"/',
		function ( $m ) {
			$id = clear_eo_import_image( html_entity_decode( $m[1] ) );
			return $id ? 'src="' . esc_url( wp_get_attachment_url( $id ) ) . '"' : $m[0];
		},
		clear_eo_md_block( $markdown )
	);
}
