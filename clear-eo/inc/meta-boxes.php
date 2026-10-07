<?php
/**
 * Extra fields for each content type, in a box under the editor. They are the static site's JSON fields:
 * the title, summary (excerpt), full text (editor) and header image (featured image) use WordPress's own.
 *
 * @package clear-eo
 */

defined( 'ABSPATH' ) || exit;

function clear_eo_meta_boxes() {
	$card = array( '_ce_card_image', __( 'Card image', 'clear-eo' ), 'media', __( "Preview picture at the top of the post's card in What's new, cropped to a wide strip. Without one, the card uses the featured image, if any.", 'clear-eo' ) );
	return array(
		'post'              => array(
			'title'  => __( "What's new card", 'clear-eo' ),
			'intro'  => __( 'The <b>excerpt</b> is the summary on the card; the <b>featured image</b> is the header across the top of the article (otherwise the Newsletter default from the Customizer).', 'clear-eo' ),
			'fields' => array(
				$card,
				array( '_ce_link', __( 'Original post', 'clear-eo' ), 'url', __( 'Optional link shown at the end of the article.', 'clear-eo' ) ),
			),
		),
		'clear_event'       => array(
			'title'  => __( 'Event details', 'clear-eo' ),
			'intro'  => __( 'The <b>excerpt</b> is the summary on the card. Upcoming events get an Upcoming badge and Add to calendar buttons (all-day, from the start to the end date).', 'clear-eo' ),
			'fields' => array(
				array( '_ce_date', __( 'Start date', 'clear-eo' ), 'date' ),
				array( '_ce_end_date', __( 'End date', 'clear-eo' ), 'date', __( 'Leave empty for a one-day event.', 'clear-eo' ) ),
				array( '_ce_location', __( 'Location', 'clear-eo' ), 'text' ),
				array( '_ce_link', __( 'Event website', 'clear-eo' ), 'url' ),
				$card,
			),
		),
		'clear_webinar'     => array(
			'title'  => __( 'Webinar details', 'clear-eo' ),
			'intro'  => __( 'The <b>excerpt</b> is the summary on the card. Upcoming webinars get a Register button and Add to calendar links.', 'clear-eo' ),
			'fields' => array(
				array( '_ce_date', __( 'Date', 'clear-eo' ), 'date' ),
				array( '_ce_time', __( 'Time', 'clear-eo' ), 'text', __( 'Write it like 14:00–15:30 CET (CET/CEST is Brussels time; UTC also works) to add it to calendars with its hours; otherwise it is added as all-day.', 'clear-eo' ) ),
				array( '_ce_registration', __( 'Registration link', 'clear-eo' ), 'url' ),
				array( '_ce_recording', __( 'Recording link', 'clear-eo' ), 'url' ),
				array( '_ce_link', __( 'More information', 'clear-eo' ), 'url' ),
				$card,
				array(
					'_ce_speakers',
					__( 'Speakers', 'clear-eo' ),
					'repeater',
					'',
					array(
						'add'    => __( 'Add speaker', 'clear-eo' ),
						'fields' => array(
							'name'         => array( __( 'Name', 'clear-eo' ), 'text' ),
							'organisation' => array( __( 'Organisation', 'clear-eo' ), 'text' ),
							'talk'         => array( __( 'Talk', 'clear-eo' ), 'text' ),
						),
					),
				),
			),
		),
		'clear_application' => array(
			'title'  => __( 'Application panel', 'clear-eo' ),
			'intro'  => __( 'The <b>excerpt</b> is the summary on the coloured side of the panel, the <b>featured image</b> its background, and the text in the editor the full application page. <b>Order</b> (in the Page attributes box) sets the tab order.', 'clear-eo' ),
			'fields' => array(
				array( '_ce_tab', __( 'Tab label', 'clear-eo' ), 'text', __( 'Short name on the tab, e.g. Floods.', 'clear-eo' ) ),
				array(
					'_ce_theme',
					__( 'Colour and illustration', 'clear-eo' ),
					'select',
					'',
					array(
						'flood' => __( 'Blue · floods', 'clear-eo' ),
						'agri'  => __( 'Green · agriculture', 'clear-eo' ),
						'air'   => __( 'Ochre · air quality', 'clear-eo' ),
					),
				),
				array( '_ce_location', __( 'Location', 'clear-eo' ), 'text' ),
				array(
					'_ce_sections',
					__( 'Panel sections', 'clear-eo' ),
					'repeater',
					__( '"Partners" sections are also shown beside the full application page. A section without a heading continues the one above it.', 'clear-eo' ),
					array(
						'add'    => __( 'Add section', 'clear-eo' ),
						'fields' => array(
							'heading' => array( __( 'Heading', 'clear-eo' ), 'text' ),
							'style'   => array(
								__( 'Shown as', 'clear-eo' ),
								'select',
								array(
									'list'     => __( 'Bulleted list', 'clear-eo' ),
									'chips'    => __( 'Tags', 'clear-eo' ),
									'partners' => __( 'Partners (highlighted tags)', 'clear-eo' ),
								),
							),
							'items'   => array( __( 'Items (one per line; **bold** and [links] work)', 'clear-eo' ), 'lines' ),
						),
					),
				),
			),
		),
		'clear_partner'     => array(
			'title'  => __( 'Partner card', 'clear-eo' ),
			'intro'  => __( 'Set the <b>logo</b> as the featured image (a PNG or SVG with a transparent background; it is shown on a white panel). <b>Order</b> (in the Page attributes box) sets the card order.', 'clear-eo' ),
			'fields' => array(
				array( '_ce_country', __( 'Country', 'clear-eo' ), 'text' ),
				array( '_ce_link', __( 'Website', 'clear-eo' ), 'url' ),
				array( '_ce_role', __( 'Role', 'clear-eo' ), 'text', __( 'e.g. Partner, Project Co-coordinator.', 'clear-eo' ) ),
				array( '_ce_description', __( 'Description', 'clear-eo' ), 'textarea', __( 'Shown when a visitor hovers over the card.', 'clear-eo' ) ),
				array( '_ce_affiliated', __( 'Affiliated entity (listed separately, dashed card)', 'clear-eo' ), 'checkbox' ),
			),
		),
	);
}

add_action(
	'add_meta_boxes',
	function ( $post_type ) {
		$boxes = clear_eo_meta_boxes();
		if ( ! isset( $boxes[ $post_type ] ) ) {
			return;
		}
		// Classic editor only: the block editor shows the same fields in its sidebar (assets/js/editor-fields.js),
		// which saves them together with the post instead of in a second request
		add_meta_box( 'clear_eo_fields', $boxes[ $post_type ]['title'], 'clear_eo_render_box', $post_type, 'normal', 'high', array( '__back_compat_meta_box' => true ) );
	}
);

function clear_eo_render_box( WP_Post $post ) {
	$box = clear_eo_meta_boxes()[ $post->post_type ];
	wp_nonce_field( 'clear_eo_save', 'clear_eo_nonce' );
	echo '<div class="ce-box">';
	if ( ! empty( $box['intro'] ) ) {
		echo '<p class="ce-intro">' . wp_kses( $box['intro'], array( 'b' => array() ) ) . '</p>';
	}
	foreach ( $box['fields'] as $f ) {
		list( $key, $label, $type ) = $f;
		$help                       = isset( $f[3] ) ? $f[3] : '';
		$extra                      = isset( $f[4] ) ? $f[4] : array();
		$value                      = get_post_meta( $post->ID, $key, true );
		echo '<div class="ce-field ce-' . esc_attr( $type ) . '">';
		if ( 'checkbox' === $type ) {
			printf( '<label><input type="checkbox" name="%1$s" value="1"%2$s> %3$s</label>', esc_attr( $key ), checked( (bool) $value, true, false ), esc_html( $label ) );
		} else {
			printf( '<label class="ce-label" for="%1$s">%2$s</label>', esc_attr( $key ), esc_html( $label ) );
			if ( 'repeater' === $type ) {
				clear_eo_render_repeater( $key, is_array( $value ) ? $value : array(), $extra );
			} else {
				clear_eo_render_input( $key, $key, $type, $value, $extra );
			}
		}
		if ( $help ) {
			echo '<p class="description">' . esc_html( $help ) . '</p>';
		}
		echo '</div>';
	}
	echo '</div>';
}

function clear_eo_render_input( $id, $name, $type, $value, $choices = array() ) {
	switch ( $type ) {
		case 'textarea':
		case 'lines':
			$v = is_array( $value ) ? implode( "\n", $value ) : (string) $value;
			printf( '<textarea id="%1$s" name="%2$s" rows="%3$d" class="widefat">%4$s</textarea>', esc_attr( $id ), esc_attr( $name ), 'lines' === $type ? 5 : 3, esc_textarea( $v ) );
			break;
		case 'select':
			printf( '<select id="%1$s" name="%2$s">', esc_attr( $id ), esc_attr( $name ) );
			foreach ( $choices as $k => $l ) {
				printf( '<option value="%1$s"%2$s>%3$s</option>', esc_attr( $k ), selected( $value, $k, false ), esc_html( $l ) );
			}
			echo '</select>';
			break;
		case 'media':
			$url = $value ? wp_get_attachment_image_url( (int) $value, 'medium' ) : '';
			printf(
				'<div class="ce-media" data-media><input type="hidden" id="%1$s" name="%2$s" value="%3$s"><img src="%4$s" alt=""%5$s><button type="button" class="button" data-pick>%6$s</button> <button type="button" class="button-link button-link-delete" data-clear%7$s>%8$s</button></div>',
				esc_attr( $id ),
				esc_attr( $name ),
				esc_attr( $value ),
				esc_url( $url ? $url : '' ),
				$url ? '' : ' hidden',
				esc_html__( 'Choose image', 'clear-eo' ),
				$url ? '' : ' hidden',
				esc_html__( 'Remove', 'clear-eo' )
			);
			break;
		default:
			printf( '<input type="%1$s" id="%2$s" name="%3$s" value="%4$s" class="%5$s">', esc_attr( in_array( $type, array( 'url', 'date' ), true ) ? $type : 'text' ), esc_attr( $id ), esc_attr( $name ), esc_attr( $value ), 'date' === $type ? '' : 'widefat' );
	}
}

/** Rows of sub-fields; admin.js adds, removes and moves rows using the <template>. */
function clear_eo_render_repeater( $key, array $rows, array $spec ) {
	$row = function ( $i, $values ) use ( $key, $spec ) {
		ob_start();
		echo '<fieldset class="ce-row"><div class="ce-row-tools"><button type="button" class="button-link" data-up aria-label="' . esc_attr__( 'Move up', 'clear-eo' ) . '">↑</button> <button type="button" class="button-link" data-down aria-label="' . esc_attr__( 'Move down', 'clear-eo' ) . '">↓</button> <button type="button" class="button-link button-link-delete" data-remove>' . esc_html__( 'Remove', 'clear-eo' ) . '</button></div>';
		foreach ( $spec['fields'] as $sub => $f ) {
			$id = "{$key}_{$i}_{$sub}";
			echo '<div class="ce-sub ce-' . esc_attr( $f[1] ) . '"><label for="' . esc_attr( $id ) . '">' . esc_html( $f[0] ) . '</label>';
			clear_eo_render_input( $id, "{$key}[{$i}][{$sub}]", $f[1], isset( $values[ $sub ] ) ? $values[ $sub ] : '', isset( $f[2] ) ? $f[2] : array() );
			echo '</div>';
		}
		echo '</fieldset>';
		return ob_get_clean();
	};
	echo '<div class="ce-repeater" data-repeater><div class="ce-rows">';
	foreach ( array_values( $rows ) as $i => $values ) {
		echo $row( $i, $values ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above
	}
	echo '</div><template>' . $row( '__i__', array() ) . '</template>'; // phpcs:ignore WordPress.Security.EscapeOutput
	echo '<button type="button" class="button" data-add>' . esc_html( $spec['add'] ) . '</button></div>';
}

// Classic editor: the fields arrive with the form
add_action(
	'save_post',
	function ( $post_id, $post ) {
		if ( ! isset( $_POST['clear_eo_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['clear_eo_nonce'] ), 'clear_eo_save' ) ) {
			return;
		}
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		$boxes = clear_eo_meta_boxes();
		if ( ! isset( $boxes[ $post->post_type ] ) ) {
			return;
		}
		foreach ( $boxes[ $post->post_type ]['fields'] as $f ) {
			$value = clear_eo_clean_field( isset( $_POST[ $f[0] ] ) ? wp_unslash( $_POST[ $f[0] ] ) : '', $f ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- sanitized there
			if ( '' === $value || array() === $value || false === $value ) {
				delete_post_meta( $post_id, $f[0] );
			} else {
				update_post_meta( $post_id, $f[0], wp_slash( $value ) );
			}
		}
	},
	10,
	2
);

// Block editor: the fields are saved through the REST API with the post, so register them there
add_action(
	'init',
	function () {
		foreach ( clear_eo_meta_boxes() as $post_type => $box ) {
			foreach ( $box['fields'] as $f ) {
				register_post_meta(
					$post_type,
					$f[0],
					array(
						'single'            => true,
						'type'              => clear_eo_rest_type( $f[2] ),
						'default'           => clear_eo_rest_default( $f[2] ),
						'show_in_rest'      => array( 'schema' => clear_eo_rest_schema( $f ) ),
						'sanitize_callback' => function ( $v ) use ( $f ) {
							return clear_eo_clean_field( $v, $f );
						},
						'auth_callback'     => function ( $allowed, $key, $post_id ) {
							return current_user_can( 'edit_post', $post_id );
						},
					)
				);
			}
		}
	}
);

function clear_eo_rest_type( $type ) {
	$t = array(
		'repeater' => 'array',
		'lines'    => 'array',
		'media'    => 'integer',
		'checkbox' => 'boolean',
	);
	return isset( $t[ $type ] ) ? $t[ $type ] : 'string';
}

function clear_eo_rest_default( $type ) {
	$d = array(
		'array'   => array(),
		'integer' => 0,
		'boolean' => false,
	);
	$t = clear_eo_rest_type( $type );
	return isset( $d[ $t ] ) ? $d[ $t ] : '';
}

function clear_eo_rest_schema( $f ) {
	$schema = array( 'type' => clear_eo_rest_type( $f[2] ) );
	if ( 'lines' === $f[2] ) {
		$schema['items'] = array( 'type' => 'string' );
	}
	if ( 'repeater' === $f[2] ) {
		$props = array();
		foreach ( $f[4]['fields'] as $sub => $sf ) {
			$props[ $sub ] = 'lines' === $sf[1] ? array( 'type' => 'array', 'items' => array( 'type' => 'string' ) ) : array( 'type' => 'string' );
		}
		$schema['items'] = array(
			'type'       => 'object',
			'properties' => $props,
		);
	}
	return $schema;
}

/** A field's value, cleaned. Repeaters drop the rows nothing was typed in. */
function clear_eo_clean_field( $raw, $f ) {
	if ( 'repeater' !== $f[2] ) {
		return clear_eo_clean( $raw, $f[2], isset( $f[4] ) ? $f[4] : array() );
	}
	$rows = array();
	foreach ( is_array( $raw ) ? $raw : array() as $r ) {
		$clean  = array();
		$filled = false;
		foreach ( $f[4]['fields'] as $sub => $sf ) {
			$clean[ $sub ] = clear_eo_clean( is_array( $r ) && isset( $r[ $sub ] ) ? $r[ $sub ] : '', $sf[1], isset( $sf[2] ) ? $sf[2] : array() );
			// A drop-down always has a value, so only typed-in fields make a row worth keeping
			$filled = $filled || ( 'select' !== $sf[1] && $clean[ $sub ] );
		}
		if ( $filled ) {
			$rows[] = $clean;
		}
	}
	return $rows;
}

// Events and webinars are dated by their start date; one saved without a date gets today's.
// wp_after_insert_post runs after the fields are saved, whichever editor saved them.
add_action(
	'wp_after_insert_post',
	function ( $post_id, $post ) {
		if ( in_array( $post->post_type, array( 'clear_event', 'clear_webinar' ), true ) && 'auto-draft' !== $post->post_status && ! get_post_meta( $post_id, '_ce_date', true ) ) {
			update_post_meta( $post_id, '_ce_date', wp_date( 'Y-m-d' ) );
		}
	},
	10,
	2
);

function clear_eo_clean( $v, $type, $choices = array() ) {
	if ( 'lines' === $type && is_array( $v ) ) {
		$v = implode( "\n", array_filter( $v, 'is_scalar' ) );
	}
	if ( 'checkbox' === $type ) {
		return (bool) $v && 'false' !== $v;
	}
	if ( is_array( $v ) ) {
		return '';
	}
	switch ( $type ) {
		case 'url':
			return esc_url_raw( trim( $v ) );
		case 'date':
			return preg_match( '/^\d{4}-\d{2}-\d{2}$/', $v ) ? $v : '';
		case 'media':
			return absint( $v );
		case 'select':
			return array_key_exists( $v, $choices ) ? $v : '';
		case 'lines':
			return array_values( array_filter( array_map( 'sanitize_text_field', explode( "\n", $v ) ), 'strlen' ) );
		case 'textarea':
			return sanitize_textarea_field( $v );
		default:
			return sanitize_text_field( $v );
	}
}

add_action(
	'admin_enqueue_scripts',
	function ( $hook ) {
		if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) || ! isset( clear_eo_meta_boxes()[ get_post_type() ] ) ) {
			return;
		}
		wp_enqueue_media();
		$ver = wp_get_theme()->get( 'Version' );
		wp_enqueue_style( 'clear-eo-admin', get_theme_file_uri( 'assets/css/admin.css' ), array(), $ver );
		wp_enqueue_script( 'clear-eo-admin', get_theme_file_uri( 'assets/js/admin.js' ), array(), $ver, true );
	}
);

// Block editor: the fields as a panel in the sidebar's post settings
add_action(
	'enqueue_block_editor_assets',
	function () {
		$type  = get_post_type();
		$boxes = clear_eo_meta_boxes();
		if ( ! $type || ! isset( $boxes[ $type ] ) ) {
			return;
		}
		$fields = array();
		foreach ( $boxes[ $type ]['fields'] as $f ) {
			$field = array(
				'key'   => $f[0],
				'label' => $f[1],
				'type'  => $f[2],
				'help'  => isset( $f[3] ) ? $f[3] : '',
			);
			if ( 'select' === $f[2] ) {
				$field['choices'] = $f[4];
			}
			if ( 'repeater' === $f[2] ) {
				$field['add'] = $f[4]['add'];
				$field['sub'] = array();
				foreach ( $f[4]['fields'] as $sub => $sf ) {
					$field['sub'][] = array(
						'key'     => $sub,
						'label'   => $sf[0],
						'type'    => $sf[1],
						'choices' => isset( $sf[2] ) ? $sf[2] : null,
					);
				}
			}
			$fields[] = $field;
		}
		$ver = wp_get_theme()->get( 'Version' );
		wp_enqueue_style( 'clear-eo-admin', get_theme_file_uri( 'assets/css/admin.css' ), array(), $ver );
		wp_enqueue_script( 'clear-eo-editor-fields', get_theme_file_uri( 'assets/js/editor-fields.js' ), array( 'wp-plugins', 'wp-editor', 'wp-element', 'wp-components', 'wp-data', 'wp-core-data', 'wp-block-editor' ), $ver, true );
		wp_add_inline_script(
			'clear-eo-editor-fields',
			'window.clearEoFields = ' . wp_json_encode(
				array(
					'title'  => $boxes[ $type ]['title'],
					'intro'  => wp_kses( $boxes[ $type ]['intro'], array( 'b' => array() ) ),
					'fields' => $fields,
					'i18n'   => array(
						'choose'  => __( 'Choose image', 'clear-eo' ),
						'replace' => __( 'Replace image', 'clear-eo' ),
						'remove'  => __( 'Remove', 'clear-eo' ),
						'up'      => __( 'Move up', 'clear-eo' ),
						'down'    => __( 'Move down', 'clear-eo' ),
					),
				)
			) . ';',
			'before'
		);
	}
);
