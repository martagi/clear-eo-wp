<?php
/**
 * Content types. They mirror the static site's admin sections:
 *   Posts (labelled "Newsletter")  → content/news/
 *   Events                          → content/events/
 *   Webinars                        → content/webinars/
 *   Applications                    → applications.json
 *   Partners                        → partners.json (partners and affiliated entities)
 *
 * @package clear-eo
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'init',
	function () {
		$common = array(
			'public'       => true,
			'show_in_rest' => true,
			'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'custom-fields' ),
		);

		register_post_type(
			'clear_event',
			$common + array(
				'labels'      => clear_eo_labels( __( 'Event', 'clear-eo' ), __( 'Events', 'clear-eo' ) ),
				'menu_icon'   => 'dashicons-calendar-alt',
				'has_archive' => 'events',
				'rewrite'     => array( 'slug' => 'events', 'with_front' => false ),
				'menu_position' => 6,
			)
		);
		register_post_type(
			'clear_webinar',
			$common + array(
				'labels'      => clear_eo_labels( __( 'Webinar', 'clear-eo' ), __( 'Webinars', 'clear-eo' ) ),
				'menu_icon'   => 'dashicons-video-alt2',
				'has_archive' => 'webinars',
				'rewrite'     => array( 'slug' => 'webinars', 'with_front' => false ),
				'menu_position' => 7,
			)
		);
		register_post_type(
			'clear_application',
			array(
				'labels'        => clear_eo_labels( __( 'Application', 'clear-eo' ), __( 'Applications', 'clear-eo' ) ),
				'public'        => true,
				'show_in_rest'  => true,
				'menu_icon'     => 'dashicons-location-alt',
				'menu_position' => 21,
				'has_archive'   => false,
				'hierarchical'  => false,
				'rewrite'       => array( 'slug' => 'applications', 'with_front' => false ),
				'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes', 'revisions', 'custom-fields' ),
			)
		);
		register_post_type(
			'clear_partner',
			array(
				'labels'              => clear_eo_labels( __( 'Partner', 'clear-eo' ), __( 'Partners', 'clear-eo' ) ),
				'public'              => false,
				'show_ui'             => true,
				'show_in_rest'        => true,
				'exclude_from_search' => true,
				'menu_icon'           => 'dashicons-groups',
				'menu_position'       => 22,
				'supports'            => array( 'title', 'thumbnail', 'page-attributes', 'custom-fields' ),
			)
		);
	}
);

function clear_eo_labels( $one, $many ) {
	return array(
		'name'               => $many,
		'singular_name'      => $one,
		/* translators: %s: content type, e.g. Event */
		'add_new_item'       => sprintf( __( 'Add new %s', 'clear-eo' ), strtolower( $one ) ),
		/* translators: %s: content type, e.g. Event */
		'edit_item'          => sprintf( __( 'Edit %s', 'clear-eo' ), strtolower( $one ) ),
		/* translators: %s: content type, e.g. Event */
		'new_item'           => sprintf( __( 'New %s', 'clear-eo' ), strtolower( $one ) ),
		/* translators: %s: content type, e.g. Event */
		'view_item'          => sprintf( __( 'View %s', 'clear-eo' ), strtolower( $one ) ),
		/* translators: %s: content type, e.g. Events */
		'search_items'       => sprintf( __( 'Search %s', 'clear-eo' ), strtolower( $many ) ),
		/* translators: %s: content type, e.g. Events */
		'not_found'          => sprintf( __( 'No %s found', 'clear-eo' ), strtolower( $many ) ),
		/* translators: %s: content type, e.g. Events */
		'all_items'          => sprintf( __( 'All %s', 'clear-eo' ), strtolower( $many ) ),
		'menu_name'          => $many,
	);
}

// Posts are the Newsletter section of What's new, as in the static site's admin
add_filter(
	'post_type_labels_post',
	function ( $l ) {
		$l->name          = __( 'Newsletter', 'clear-eo' );
		$l->menu_name     = __( 'Newsletter', 'clear-eo' );
		$l->all_items     = __( 'All newsletter items', 'clear-eo' );
		$l->add_new_item  = __( 'Add newsletter item', 'clear-eo' );
		$l->edit_item     = __( 'Edit newsletter item', 'clear-eo' );
		$l->singular_name = __( 'Newsletter item', 'clear-eo' );
		return $l;
	}
);

// Partner logos: "Logo" instead of "Featured image" in the editor
add_filter(
	'post_type_labels_clear_partner',
	function ( $l ) {
		$l->featured_image        = __( 'Logo', 'clear-eo' );
		$l->set_featured_image    = __( 'Set logo (PNG or SVG with a transparent background)', 'clear-eo' );
		$l->remove_featured_image = __( 'Remove logo', 'clear-eo' );
		$l->use_featured_image    = __( 'Use as logo', 'clear-eo' );
		return $l;
	}
);

add_filter(
	'enter_title_here',
	function ( $text, $post ) {
		return 'clear_partner' === $post->post_type ? __( 'Partner name', 'clear-eo' ) : $text;
	},
	10,
	2
);

// Admin lists: the date that matters for events and webinars, and the order of partners and applications
foreach ( array( 'clear_event', 'clear_webinar' ) as $clear_eo_pt ) {
	add_filter(
		"manage_{$clear_eo_pt}_posts_columns",
		function ( $cols ) {
			$out = array();
			foreach ( $cols as $k => $v ) {
				$out[ $k ] = $v;
				if ( 'title' === $k ) {
					$out['ce_date'] = __( 'Takes place', 'clear-eo' );
				}
			}
			unset( $out['date'] );
			return $out;
		}
	);
	add_action(
		"manage_{$clear_eo_pt}_posts_custom_column",
		function ( $col, $id ) {
			if ( 'ce_date' !== $col ) {
				return;
			}
			$n = clear_eo_item( get_post( $id ) );
			echo esc_html( clear_eo_fmt_range( $n['date'], $n['end_date'] ) );
			if ( clear_eo_upcoming( $n ) ) {
				echo ' <strong>· ' . esc_html__( 'Upcoming', 'clear-eo' ) . '</strong>';
			}
		},
		10,
		2
	);
	add_filter(
		"manage_edit-{$clear_eo_pt}_sortable_columns",
		function ( $cols ) {
			$cols['ce_date'] = 'ce_date';
			return $cols;
		}
	);
}
foreach ( array( 'clear_partner', 'clear_application' ) as $clear_eo_pt ) {
	add_filter(
		"manage_{$clear_eo_pt}_posts_columns",
		function ( $cols ) {
			unset( $cols['date'] );
			$cols['ce_order'] = __( 'Order', 'clear-eo' );
			return $cols;
		}
	);
	add_action(
		"manage_{$clear_eo_pt}_posts_custom_column",
		function ( $col, $id ) {
			if ( 'ce_order' === $col ) {
				echo (int) get_post_field( 'menu_order', $id );
				if ( get_post_meta( $id, '_ce_affiliated', true ) ) {
					echo ' · ' . esc_html__( 'Affiliated entity', 'clear-eo' );
				}
			}
		},
		10,
		2
	);
}
unset( $clear_eo_pt );

add_action(
	'pre_get_posts',
	function ( WP_Query $q ) {
		if ( ! is_admin() || ! $q->is_main_query() ) {
			return;
		}
		$type = $q->get( 'post_type' );
		if ( in_array( $type, array( 'clear_partner', 'clear_application' ), true ) && ! $q->get( 'orderby' ) ) {
			$q->set( 'orderby', 'menu_order title' );
			$q->set( 'order', 'ASC' );
		}
		if ( in_array( $type, array( 'clear_event', 'clear_webinar' ), true ) && in_array( $q->get( 'orderby' ), array( '', 'ce_date' ), true ) ) {
			$q->set( 'meta_key', '_ce_date' );
			$q->set( 'orderby', 'meta_value' );
			if ( ! $q->get( 'order' ) ) {
				$q->set( 'order', 'DESC' );
			}
		}
	}
);
