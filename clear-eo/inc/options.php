<?php
/**
 * Page texts (the static site's site.json, partners.json and the What's new settings), edited in
 * Appearance → Customize → CLEAR-EO.
 *
 * Every setting is addressed as "<source>:<path>", e.g. "site:hero.title" or "partners:women.text". Its
 * default is the value at that path in import/content/<source>.json, so the theme shows the real
 * CLEAR-EO texts until an editor changes them, and that folder is the one place to update them.
 *
 * Short text fields accept **bold**, *italic* and [text](https://link), like in the static site.
 *
 * @package clear-eo
 */

defined( 'ABSPATH' ) || exit;

/** JSON file behind each source prefix. */
function clear_eo_sources() {
	return array(
		'site'     => 'site',
		'apps'     => 'applications',
		'partners' => 'partners',
		'news'     => 'news',
		'events'   => 'events',
		'webinars' => 'webinars',
	);
}

function clear_eo_json( $source ) {
	static $cache = array();
	if ( ! isset( $cache[ $source ] ) ) {
		$files            = clear_eo_sources();
		$file             = get_theme_file_path( 'import/content/' . $files[ $source ] . '.json' );
		$cache[ $source ] = is_readable( $file ) ? (array) json_decode( file_get_contents( $file ), true ) : array();
	}
	return $cache[ $source ];
}

/**
 * The editable fields, grouped into Customizer sections.
 * Types: text, textarea, url, image, checkbox, number, select (with 'choices'),
 * lines (a list of strings, one per line) and links (a list of {label, link}, one "Label | URL" per line).
 */
function clear_eo_fields() {
	$pillar_icons = array(
		'lab'       => __( 'Flask', 'clear-eo' ),
		'satellite' => __( 'Satellite', 'clear-eo' ),
		'server'    => __( 'Server', 'clear-eo' ),
		'cloud'     => __( 'Cloud', 'clear-eo' ),
	);

	$facts = array();
	for ( $i = 0; $i < 5; $i++ ) {
		/* translators: %d: fact number */
		$facts[ "site:facts.$i.value" ] = array( sprintf( __( 'Fact %d: value', 'clear-eo' ), $i + 1 ), 'text' );
		/* translators: %d: fact number */
		$facts[ "site:facts.$i.label" ] = array( sprintf( __( 'Fact %d: label', 'clear-eo' ), $i + 1 ), 'text' );
	}

	$about = array(
		'site:about.kicker' => array( __( 'Kicker', 'clear-eo' ), 'text' ),
		'site:about.title'  => array( __( 'Title', 'clear-eo' ), 'text' ),
		'site:about.text'   => array( __( 'Text', 'clear-eo' ), 'textarea' ),
		'site:about.quote'  => array( __( 'Quote', 'clear-eo' ), 'textarea' ),
	);
	for ( $i = 0; $i < 4; $i++ ) {
		$n = $i + 1;
		/* translators: %d: box number */
		$about[ "site:about.pillars.$i.icon" ] = array( sprintf( __( 'Box %d: icon', 'clear-eo' ), $n ), 'select', $pillar_icons );
		/* translators: %d: box number */
		$about[ "site:about.pillars.$i.title" ] = array( sprintf( __( 'Box %d: title', 'clear-eo' ), $n ), 'text' );
		/* translators: %d: box number */
		$about[ "site:about.pillars.$i.text" ] = array( sprintf( __( 'Box %d: text', 'clear-eo' ), $n ), 'textarea' );
	}

	$vo = array(
		'site:observatory.kicker' => array( __( 'Kicker', 'clear-eo' ), 'text' ),
		'site:observatory.title'  => array( __( 'Title', 'clear-eo' ), 'textarea' ),
		'site:observatory.text'   => array( __( 'Text', 'clear-eo' ), 'textarea' ),
	);
	for ( $i = 0; $i < 4; $i++ ) {
		$n = $i + 1;
		/* translators: %d: step number */
		$vo[ "site:observatory.steps.$i.title" ] = array( sprintf( __( 'Step %d: title', 'clear-eo' ), $n ), 'text' );
		/* translators: %d: step number */
		$vo[ "site:observatory.steps.$i.text" ] = array( sprintf( __( 'Step %d: text', 'clear-eo' ), $n ), 'textarea' );
		/* translators: %d: step number */
		$vo[ "site:observatory.steps.$i.chips" ] = array( sprintf( __( 'Step %d: tags (one per line)', 'clear-eo' ), $n ), 'lines' );
		/* translators: %d: step number */
		$vo[ "site:observatory.steps.$i.pulse" ] = array( sprintf( __( 'Step %d: show the live pulse', 'clear-eo' ), $n ), 'checkbox' );
	}
	$vo['site:observatory.note_title'] = array( __( 'Highlight box: title', 'clear-eo' ), 'text' );
	$vo['site:observatory.note_text']  = array( __( 'Highlight box: text', 'clear-eo' ), 'textarea' );

	$consortium = array(
		'partners:kicker'           => array( __( 'Kicker', 'clear-eo' ), 'text' ),
		'partners:title'            => array( __( 'Title', 'clear-eo' ), 'text' ),
		'partners:text'             => array( __( 'Text', 'clear-eo' ), 'textarea' ),
		'partners:map'              => array( __( 'Partner-country map', 'clear-eo' ), 'image' ),
		'partners:map_alt'          => array( __( 'Map description (for screen readers)', 'clear-eo' ), 'textarea' ),
		'partners:affiliated_title' => array( __( 'Heading above the affiliated entities', 'clear-eo' ), 'text' ),
		'partners:women.percent'    => array( __( 'Women box: percentage for the ring (0–100)', 'clear-eo' ), 'number' ),
		'partners:women.label'      => array( __( 'Women box: label in the ring', 'clear-eo' ), 'text' ),
		'partners:women.text'       => array( __( 'Women box: text (leave empty to hide the box)', 'clear-eo' ), 'textarea' ),
		'partners:women.link_label' => array( __( 'Women box: link text', 'clear-eo' ), 'text' ),
		'partners:women.link'       => array( __( 'Women box: link', 'clear-eo' ), 'url' ),
	);
	for ( $i = 0; $i < 2; $i++ ) {
		$n = $i + 1;
		/* translators: %d: collaboration number */
		$consortium[ "partners:collaborations.$i.name" ] = array( sprintf( __( 'Collaboration %d: name (leave empty to hide)', 'clear-eo' ), $n ), 'text' );
		/* translators: %d: collaboration number */
		$consortium[ "partners:collaborations.$i.text" ] = array( sprintf( __( 'Collaboration %d: text', 'clear-eo' ), $n ), 'textarea' );
		/* translators: %d: collaboration number */
		$consortium[ "partners:collaborations.$i.link_label" ] = array( sprintf( __( 'Collaboration %d: link text', 'clear-eo' ), $n ), 'text' );
		/* translators: %d: collaboration number */
		$consortium[ "partners:collaborations.$i.link" ] = array( sprintf( __( 'Collaboration %d: link', 'clear-eo' ), $n ), 'url' );
	}

	return array(
		'clear_eo_hero'        => array(
			'title'  => __( 'Top banner', 'clear-eo' ),
			'fields' => array(
				'site:hero.background'             => array( __( 'Background picture', 'clear-eo' ), 'image' ),
				'site:hero.eyebrow'                => array( __( 'Small label above the title', 'clear-eo' ), 'text' ),
				'site:hero.title'                  => array( __( 'Title', 'clear-eo' ), 'text' ),
				'site:hero.title_highlight'        => array( __( 'Title, highlighted part', 'clear-eo' ), 'text' ),
				'site:hero.acronym'                => array( __( 'Acronym line', 'clear-eo' ), 'textarea' ),
				'site:hero.lede'                   => array( __( 'Introduction', 'clear-eo' ), 'textarea' ),
				'site:hero.primary_button.label'   => array( __( 'Main button: text', 'clear-eo' ), 'text' ),
				'site:hero.primary_button.link'    => array( __( 'Main button: link', 'clear-eo' ), 'text' ),
				'site:hero.secondary_button.label' => array( __( 'Second button: text', 'clear-eo' ), 'text' ),
				'site:hero.secondary_button.link'  => array( __( 'Second button: link', 'clear-eo' ), 'text' ),
				'site:hero.tags'                   => array( __( 'Satellite missions and infrastructures (one per line)', 'clear-eo' ), 'lines' ),
			),
		),
		'clear_eo_facts'       => array(
			'title'  => __( 'Key facts', 'clear-eo' ),
			'fields' => $facts,
		),
		'clear_eo_about'       => array(
			'title'  => __( 'About', 'clear-eo' ),
			'fields' => $about,
		),
		'clear_eo_apps'        => array(
			'title'       => __( 'Applications', 'clear-eo' ),
			'description' => __( 'The applications themselves are under Applications in the admin menu.', 'clear-eo' ),
			'fields'      => array(
				'apps:kicker' => array( __( 'Kicker', 'clear-eo' ), 'text' ),
				'apps:title'  => array( __( 'Title', 'clear-eo' ), 'textarea' ),
			),
		),
		'clear_eo_observatory' => array(
			'title'  => __( 'Virtual Observatory', 'clear-eo' ),
			'fields' => $vo,
		),
		'clear_eo_consortium'  => array(
			'title'       => __( 'Consortium & partners', 'clear-eo' ),
			'description' => __( 'The partner cards themselves are under Partners in the admin menu.', 'clear-eo' ),
			'fields'      => $consortium,
		),
		'clear_eo_whatsnew'    => array(
			'title'       => __( "What's new", 'clear-eo' ),
			'description' => __( 'Header images are shown across the full width of every post of that type that has no featured image of its own, cropped to a wide strip: a landscape picture of at least 1800 × 600 px works best.', 'clear-eo' ),
			'fields'      => array(
				'site:whatsnew.kicker'       => array( __( 'Kicker', 'clear-eo' ), 'text' ),
				'site:whatsnew.title'        => array( __( 'Title', 'clear-eo' ), 'text' ),
				'site:whatsnew.default_link' => array( __( 'Default link (for items with no page and no link of their own)', 'clear-eo' ), 'url' ),
				'news:header_image'          => array( __( 'Newsletter: default header image', 'clear-eo' ), 'image' ),
				'events:header_image'        => array( __( 'Events: default header image', 'clear-eo' ), 'image' ),
				'webinars:header_image'      => array( __( 'Webinars: default header image', 'clear-eo' ), 'image' ),
			),
		),
		'clear_eo_footer'      => array(
			'title'  => __( 'Footer', 'clear-eo' ),
			'fields' => array(
				'site:footer.title'         => array( __( 'Title', 'clear-eo' ), 'text' ),
				'site:footer.button.label'  => array( __( 'Button: text', 'clear-eo' ), 'text' ),
				'site:footer.button.link'   => array( __( 'Button: link', 'clear-eo' ), 'url' ),
				'site:footer.funding'       => array( __( 'EU funding statement', 'clear-eo' ), 'textarea' ),
				'site:footer.support'       => array( __( 'Small print under the funding statement', 'clear-eo' ), 'textarea' ),
				'site:footer.project_links' => array( __( 'Project links (one "Label | https://link" per line)', 'clear-eo' ), 'links' ),
				'site:footer.copyright'     => array( __( 'Copyright', 'clear-eo' ), 'text' ),
				'site:footer.note'          => array( __( 'Note on the right', 'clear-eo' ), 'text' ),
			),
		),
	);
}

/** Field definition by key. */
function clear_eo_field( $key ) {
	static $flat = null;
	if ( null === $flat ) {
		$flat = array();
		foreach ( clear_eo_fields() as $section ) {
			$flat += $section['fields'];
		}
	}
	return isset( $flat[ $key ] ) ? $flat[ $key ] : array( '', 'text' );
}

function clear_eo_mod_name( $key ) {
	return 'ce_' . trim( preg_replace( '/[^a-z0-9]+/', '_', strtolower( $key ) ), '_' );
}

/** Value at "<source>:<path>" in the bundled JSON, in the form the Customizer stores it. */
function clear_eo_default( $key ) {
	list( $source, $path ) = explode( ':', $key, 2 );
	$v                     = clear_eo_json( $source );
	foreach ( explode( '.', $path ) as $k ) {
		$v = is_array( $v ) && array_key_exists( $k, $v ) ? $v[ $k ] : null;
	}
	$type = clear_eo_field( $key )[1];
	if ( 'lines' === $type ) {
		return implode( "\n", (array) $v );
	}
	if ( 'links' === $type ) {
		return implode( "\n", array_map( function ( $l ) {
			return $l['label'] . ' | ' . $l['link'];
		}, (array) $v ) );
	}
	if ( 'checkbox' === $type ) {
		return ! empty( $v );
	}
	return null === $v ? '' : (string) $v;
}

/**
 * A page text. Lines and links fields come back as arrays, images as URLs.
 *
 * @param string $key "<source>:<path>".
 */
function clear_eo_get( $key ) {
	$v    = get_theme_mod( clear_eo_mod_name( $key ), clear_eo_default( $key ) );
	$type = clear_eo_field( $key )[1];
	if ( 'lines' === $type ) {
		return array_values( array_filter( array_map( 'trim', explode( "\n", (string) $v ) ), 'strlen' ) );
	}
	if ( 'links' === $type ) {
		$out = array();
		foreach ( array_filter( array_map( 'trim', explode( "\n", (string) $v ) ) ) as $line ) {
			$parts = array_map( 'trim', explode( '|', $line, 2 ) );
			$out[] = array(
				'label' => $parts[0],
				'link'  => isset( $parts[1] ) ? $parts[1] : $parts[0],
			);
		}
		return $out;
	}
	if ( 'image' === $type ) {
		return clear_eo_asset( $v );
	}
	return $v;
}

/** Short text with **bold**, *italic* and [links] rendered, for printing. */
function clear_eo_text( $key ) {
	return clear_eo_md( clear_eo_get( $key ) );
}

/**
 * URL of a bundled picture. The JSON files name them as in the static site ("_assets/…"): the theme's own
 * pictures are in assets/img/, the ones the importer uses in import/_assets/. Any other value is returned as is.
 */
function clear_eo_asset( $path ) {
	if ( ! is_string( $path ) || 0 !== strpos( $path, '_assets/' ) ) {
		return $path;
	}
	$rest = substr( $path, 8 );
	if ( file_exists( get_theme_file_path( 'assets/img/' . $rest ) ) ) {
		return get_theme_file_uri( 'assets/img/' . $rest );
	}
	return get_theme_file_uri( 'import/' . $path );
}

add_action(
	'customize_register',
	function ( WP_Customize_Manager $wp ) {
		$wp->add_panel(
			'clear_eo',
			array(
				'title'       => __( 'CLEAR-EO page sections', 'clear-eo' ),
				'description' => __( 'Texts of the one-page home. In short fields, put **double asterisks** around text to make it bold, and use [text](https://link) for a link.', 'clear-eo' ),
				'priority'    => 25,
			)
		);
		foreach ( clear_eo_fields() as $section_id => $section ) {
			$wp->add_section(
				$section_id,
				array(
					'title'       => $section['title'],
					'description' => isset( $section['description'] ) ? $section['description'] : '',
					'panel'       => 'clear_eo',
				)
			);
			foreach ( $section['fields'] as $key => $f ) {
				$id   = clear_eo_mod_name( $key );
				$type = $f[1];
				$wp->add_setting(
					$id,
					array(
						'default'           => clear_eo_default( $key ),
						'sanitize_callback' => clear_eo_sanitizer( $type, isset( $f[2] ) ? $f[2] : array() ),
					)
				);
				if ( 'image' === $type ) {
					$wp->add_control( new WP_Customize_Image_Control( $wp, $id, array( 'label' => $f[0], 'section' => $section_id ) ) );
					continue;
				}
				$wp->add_control(
					$id,
					array(
						'label'   => $f[0],
						'section' => $section_id,
						'type'    => in_array( $type, array( 'lines', 'links' ), true ) ? 'textarea' : $type,
						'choices' => isset( $f[2] ) ? $f[2] : array(),
					)
				);
			}
		}
	}
);

function clear_eo_sanitizer( $type, $choices ) {
	switch ( $type ) {
		case 'checkbox':
			return function ( $v ) {
				return (bool) $v;
			};
		case 'number':
			return 'absint';
		case 'select':
			return function ( $v ) use ( $choices ) {
				return array_key_exists( $v, $choices ) ? $v : key( $choices );
			};
		case 'image':
			// Default pictures are stored as "_assets/…" theme paths, uploads as URLs
			return function ( $v ) {
				return 0 === strpos( (string) $v, '_assets/' ) ? sanitize_text_field( $v ) : esc_url_raw( $v );
			};
		case 'url':
			return 'esc_url_raw';
		case 'text':
			return 'sanitize_text_field';
		default:
			return 'sanitize_textarea_field';
	}
}
