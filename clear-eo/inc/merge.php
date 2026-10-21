<?php
/**
 * Tools → CLEAR-EO content → Merge duplicates.
 *
 * Running the importer on the old clear-eo.eu site added a copy of every newsletter item, event and webinar
 * that already existed there as a post. The original posts are the ones with the view counts (WP Statistics
 * records views per post ID) and the addresses people have shared, so each pair is merged into one item:
 *
 *   - event/webinar: the original post moves into Events or Webinars, keeping its ID, address, comments and
 *     views, and takes the copy's dates, location, images… where it has none; the copy goes to the bin.
 *   - newsletter item: the original is kept and takes the copy's card image and link; the copy goes to the bin.
 *   - original already in the bin: the copy is kept and gets the original's views; the old address redirects.
 *   - a post in the event or webinar category without a copy moves into Events or Webinars.
 *
 * Views recorded on a copy are added to the item that is kept. Nothing is deleted: copies go to the bin.
 *
 * @package clear-eo
 */

defined( 'ABSPATH' ) || exit;

/** Title reduced to letters and digits, so "&amp;", "&#038;" and "&" compare equal. */
function clear_eo_norm_title( $t ) {
	$t = html_entity_decode( wp_specialchars_decode( (string) $t, ENT_QUOTES ), ENT_QUOTES | ENT_HTML5, 'UTF-8' );
	return trim( preg_replace( '/[^a-z0-9]+/', ' ', strtolower( remove_accents( $t ) ) ) );
}

/** The WP Statistics table of per-page views, or '' if the plugin isn't installed. */
function clear_eo_stats_table() {
	global $wpdb;
	$t = $wpdb->prefix . 'statistics_pages';
	return $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $t ) ) === $t ? $t : '';
}

/** How WP Statistics labels views of a post type: "post", "page", or "post_type_<name>". */
function clear_eo_stats_type( $post_type ) {
	return in_array( $post_type, array( 'post', 'page' ), true ) ? $post_type : 'post_type_' . $post_type;
}

/** All views WP Statistics recorded for a post ID. */
function clear_eo_views( $id ) {
	global $wpdb;
	$t = clear_eo_stats_table();
	return $t ? (int) $wpdb->get_var( $wpdb->prepare( "SELECT SUM(`count`) FROM `$t` WHERE `id` = %d", $id ) ) : 0; // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
}

/** Files all of $from's views under $to, labelled as $post_type. */
function clear_eo_move_views( $from, $to, $post_type ) {
	global $wpdb;
	$t = clear_eo_stats_table();
	if ( $t ) {
		$wpdb->query( $wpdb->prepare( "UPDATE `$t` SET `id` = %d, `type` = %s WHERE `id` = %d", $to, clear_eo_stats_type( $post_type ), $from ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}
}

/**
 * What the merge would do, as a list of steps:
 * array( 'action' => merge|adopt|move, 'keep' => WP_Post, 'drop' => WP_Post|null, 'to' => post type ).
 */
function clear_eo_merge_plan() {
	$live   = array( 'publish', 'future', 'draft', 'pending', 'private' );
	$copies = get_posts(
		array(
			'post_type'   => array( 'post', 'clear_event', 'clear_webinar' ),
			'post_status' => $live,
			'numberposts' => -1,
			'meta_key'    => '_ce_import_id', // phpcs:ignore WordPress.DB.SlowDBQuery
		)
	);
	$originals = get_posts(
		array(
			'post_type'   => 'post',
			'post_status' => array_merge( $live, array( 'trash' ) ),
			'numberposts' => -1,
			'orderby'     => 'ID',
			'order'       => 'ASC',
			'meta_query'  => array( array( 'key' => '_ce_import_id', 'compare' => 'NOT EXISTS' ) ), // phpcs:ignore WordPress.DB.SlowDBQuery
		)
	);
	// Originals already merged into another item (their views moved there) are left alone
	$merged = array_map( 'intval', get_posts( array( 'post_type' => 'any', 'post_status' => 'any', 'numberposts' => -1, 'fields' => 'ids', 'meta_key' => '_ce_old_id' ) ) ); // phpcs:ignore WordPress.DB.SlowDBQuery
	$done   = array();
	foreach ( $merged as $m ) {
		foreach ( get_post_meta( $m, '_ce_old_id' ) as $old ) {
			$done[ (int) $old ] = true;
		}
	}
	$by_title = array();
	foreach ( $originals as $o ) {
		if ( isset( $done[ $o->ID ] ) ) {
			continue;
		}
		$by_title[ clear_eo_norm_title( $o->post_title ) ][] = $o;
	}

	$plan = array();
	$used = array();
	foreach ( $copies as $c ) {
		$found = null;
		foreach ( array( false, true ) as $in_bin ) {
			foreach ( isset( $by_title[ clear_eo_norm_title( $c->post_title ) ] ) ? $by_title[ clear_eo_norm_title( $c->post_title ) ] : array() as $o ) {
				if ( ! $found && empty( $used[ $o->ID ] ) && ( 'trash' === $o->post_status ) === $in_bin ) {
					$found = $o;
				}
			}
		}
		if ( ! $found ) {
			continue;
		}
		$used[ $found->ID ] = true;
		$plan[]             = 'trash' === $found->post_status
			? array( 'action' => 'adopt', 'keep' => $c, 'drop' => $found, 'to' => $c->post_type )
			: array( 'action' => 'merge', 'keep' => $found, 'drop' => $c, 'to' => $c->post_type );
	}
	foreach ( $originals as $o ) {
		if ( empty( $used[ $o->ID ] ) && 'trash' !== $o->post_status ) {
			$kind = clear_eo_post_kind( $o );
			if ( 'news' !== $kind ) {
				$plan[] = array( 'action' => 'move', 'keep' => $o, 'drop' => null, 'to' => 'event' === $kind ? 'clear_event' : 'clear_webinar' );
			}
		}
	}
	return $plan;
}

/** One line describing a step, for the preview and the log. */
function clear_eo_merge_describe( $s ) {
	$sections = array(
		'post'          => __( 'Newsletter', 'clear-eo' ),
		'clear_event'   => __( 'Events', 'clear-eo' ),
		'clear_webinar' => __( 'Webinars', 'clear-eo' ),
	);
	$k = $s['keep'];
	$d = $s['drop'];
	switch ( $s['action'] ) {
		case 'merge':
			return 'post' === $s['to']
				/* translators: 1: title, 2: kept ID, 3: views, 4: copy ID */
				? sprintf( __( '“%1$s”: keep the original (#%2$d, %3$d views); the copy #%4$d goes to the bin.', 'clear-eo' ), get_the_title( $k ), $k->ID, clear_eo_views( $k->ID ), $d->ID )
				/* translators: 1: title, 2: kept ID, 3: section, 4: views, 5: copy ID */
				: sprintf( __( '“%1$s”: the original post #%2$d moves into %3$s with its %4$d views; the copy #%5$d goes to the bin.', 'clear-eo' ), get_the_title( $k ), $k->ID, $sections[ $s['to'] ], clear_eo_views( $k->ID ), $d->ID );
		case 'adopt':
			/* translators: 1: title, 2: original ID, 3: views, 4: kept ID */
			return sprintf( __( '“%1$s”: the original #%2$d is already in the bin; its %3$d views go to the copy #%4$d, and its old address redirects there.', 'clear-eo' ), get_the_title( $k ), $d->ID, clear_eo_views( $d->ID ), $k->ID );
		default:
			/* translators: 1: title, 2: ID, 3: section, 4: views */
			return sprintf( __( '“%1$s”: post #%2$d moves into %3$s with its %4$d views.', 'clear-eo' ), get_the_title( $k ), $k->ID, $sections[ $s['to'] ], clear_eo_views( $k->ID ) );
	}
}

/** Carries out the plan and returns a log, one line per step. */
function clear_eo_merge_run( array $plan ) {
	$log = array();
	foreach ( $plan as $s ) {
		$line = clear_eo_merge_describe( $s );
		$k    = $s['keep'];
		$d    = $s['drop'];
		if ( 'adopt' === $s['action'] ) {
			clear_eo_move_views( $d->ID, $k->ID, $k->post_type );
			add_post_meta( $k->ID, '_ce_old_id', $d->ID );
			$log[] = $line;
			continue;
		}
		if ( 'merge' === $s['action'] ) {
			// The original takes whatever the copy has and it lacks: dates, location, links, speakers, images, summary
			$boxes = clear_eo_meta_boxes();
			foreach ( $boxes[ $s['to'] ]['fields'] as $f ) {
				$mine   = get_post_meta( $k->ID, $f[0], true );
				$theirs = get_post_meta( $d->ID, $f[0], true );
				if ( empty( $mine ) && ! empty( $theirs ) ) {
					update_post_meta( $k->ID, $f[0], wp_slash( $theirs ) );
				}
			}
			if ( ! has_post_thumbnail( $k ) && has_post_thumbnail( $d ) ) {
				set_post_thumbnail( $k->ID, get_post_thumbnail_id( $d ) );
			}
			if ( '' === trim( $k->post_excerpt ) && '' !== trim( $d->post_excerpt ) ) {
				wp_update_post( array( 'ID' => $k->ID, 'post_excerpt' => wp_slash( $d->post_excerpt ) ) );
			}
			clear_eo_move_views( $d->ID, $k->ID, $s['to'] );
			add_post_meta( $k->ID, '_ce_old_id', $d->ID );
			// Bin the copy first: its address (slug) is then free for the original to take over
			wp_trash_post( $d->ID );
		}
		if ( 'post' !== $s['to'] ) {
			$slug = $k->post_name;
			set_post_type( $k->ID, $s['to'] );
			$unique = wp_unique_post_slug( $slug, $k->ID, $k->post_status, $s['to'], 0 );
			if ( $unique !== $slug ) {
				wp_update_post( array( 'ID' => $k->ID, 'post_name' => $unique ) );
			}
			global $wpdb;
			$t = clear_eo_stats_table();
			if ( $t ) {
				$wpdb->query( $wpdb->prepare( "UPDATE `$t` SET `type` = %s WHERE `id` = %d AND `type` = 'post'", clear_eo_stats_type( $s['to'] ), $k->ID ) ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
			}
		}
		clean_post_cache( $k->ID );
		clear_eo_file_under_type( $k->ID );
		$log[] = $line;
	}
	return $log;
}

// The old address of an item merged into another (e.g. ?p=1 of a post now in the bin) redirects to it
add_action(
	'template_redirect',
	function () {
		$old = absint( get_query_var( 'p' ) );
		if ( ! is_404() || ! $old ) {
			return;
		}
		$to = get_posts(
			array(
				'post_type'   => array( 'post', 'clear_event', 'clear_webinar' ),
				'post_status' => 'publish',
				'meta_key'    => '_ce_old_id', // phpcs:ignore WordPress.DB.SlowDBQuery
				'meta_value'  => $old, // phpcs:ignore WordPress.DB.SlowDBQuery
				'numberposts' => 1,
				'fields'      => 'ids',
			)
		);
		if ( $to ) {
			wp_safe_redirect( get_permalink( $to[0] ), 301 );
			exit;
		}
	}
);

/** The Merge duplicates part of Tools → CLEAR-EO content. */
function clear_eo_merge_section() {
	$plan = clear_eo_merge_plan();
	echo '<hr><h2 id="merge">' . esc_html__( 'Merge duplicates', 'clear-eo' ) . '</h2>';
	if ( isset( $_POST['clear_eo_merge'] ) && check_admin_referer( 'clear_eo_merge' ) ) {
		$log = clear_eo_merge_run( $plan );
		echo '<div class="notice notice-success"><p>' . esc_html__( 'Merge finished. The copies are in the bin, if you need one back.', 'clear-eo' ) . '</p><ul style="list-style:disc;padding-left:20px">';
		foreach ( $log as $line ) {
			echo '<li>' . esc_html( $line ) . '</li>';
		}
		echo '</ul></div>';
		$plan = clear_eo_merge_plan();
	}
	echo '<p>' . esc_html__( 'If this site already had its newsletter items, events and webinars as posts when the content was imported, each of them now exists twice. The original posts hold the view counts and the addresses people have shared, so they are kept: events and webinars move into the Events and Webinars sections with their views, and the imported copies go to the bin. Posts filed under event or webinar without a copy move there too.', 'clear-eo' ) . '</p>';
	if ( ! clear_eo_stats_table() ) {
		echo '<p><em>' . esc_html__( 'WP Statistics is not installed here, so there are no view counts to carry over.', 'clear-eo' ) . '</em></p>';
	}
	if ( ! $plan ) {
		echo '<p><strong>' . esc_html__( 'Nothing to merge: there are no duplicates, and no event or webinar posts outside their sections.', 'clear-eo' ) . '</strong></p>';
		return;
	}
	echo '<p>' . esc_html__( 'This is what will happen:', 'clear-eo' ) . '</p><ul style="list-style:disc;padding-left:20px">';
	foreach ( $plan as $s ) {
		echo '<li>' . esc_html( clear_eo_merge_describe( $s ) ) . '</li>';
	}
	echo '</ul><form method="post">';
	wp_nonce_field( 'clear_eo_merge' );
	echo '<p><button class="button button-primary" name="clear_eo_merge" value="1">' . esc_html__( 'Merge duplicates', 'clear-eo' ) . '</button></p></form>';
}

// On the dashboard and the Newsletter, Events and Webinars lists: say so while there are duplicates to merge
add_action(
	'admin_notices',
	function () {
		$screen = get_current_screen();
		if ( ! current_user_can( 'manage_options' ) || ! $screen || ! ( 'dashboard' === $screen->id || ( 'edit' === $screen->base && in_array( $screen->post_type, array( 'post', 'clear_event', 'clear_webinar' ), true ) ) ) ) {
			return;
		}
		$plan = array_filter(
			clear_eo_merge_plan(),
			function ( $s ) {
				return 'move' !== $s['action'];
			}
		);
		if ( ! $plan ) {
			return;
		}
		printf(
			'<div class="notice notice-warning"><p>%1$s <a class="button" href="%2$s">%3$s</a></p></div>',
			esc_html(
				sprintf(
					/* translators: %d: number of items */
					_n( '%d newsletter item, event or webinar exists twice (the original post and an imported copy), so it also shows twice on the site.', '%d newsletter items, events and webinars exist twice (the original post and an imported copy), so they also show twice on the site.', count( $plan ), 'clear-eo' ),
					count( $plan )
				)
			),
			esc_url( admin_url( 'tools.php?page=clear-eo-import#merge' ) ),
			esc_html__( 'Review and merge', 'clear-eo' )
		);
	}
);
