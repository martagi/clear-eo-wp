<?php
/**
 * Reading the content back for the templates: What's new items (newsletter posts, events, webinars),
 * applications and partners, plus the cards, reader window and calendar links. The logic follows the
 * static site's script (hasPage / outLink / calendar).
 *
 * @package clear-eo
 */

defined( 'ABSPATH' ) || exit;

/* ---------- Icons and artwork ---------- */

function clear_eo_icon( $name ) {
	$paths = array(
		'arrow'     => '<path d="M5 12h14M13 6l6 6-6 6"/>',
		'out'       => '<path d="M7 17 17 7M8 7h9v9"/>',
		'pin'       => '<path d="M12 22s7-6.5 7-12a7 7 0 1 0-14 0c0 5.5 7 12 7 12z"/><circle cx="12" cy="10" r="2.5"/>',
		'clock'     => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
		'globe'     => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a14 14 0 0 1 0 18M12 3a14 14 0 0 0 0 18"/>',
		'lab'       => '<path d="M9 3h6M10 3v6L4.5 18.5A1.7 1.7 0 0 0 6 21h12a1.7 1.7 0 0 0 1.5-2.5L14 9V3"/>',
		'cloud'     => '<path d="M17.5 19a4.5 4.5 0 1 0-1.4-8.8A6 6 0 0 0 4 12a4 4 0 0 0 2 7z"/>',
		'satellite' => '<path d="M13 7 9 3 5 7l4 4M17 11l4 4-4 4-4-4M8 12l4 4M16 8l-1.5 1.5"/><circle cx="12" cy="12" r="2"/>',
		'server'    => '<rect x="3" y="4" width="18" height="6" rx="2"/><rect x="3" y="14" width="18" height="6" rx="2"/><path d="M7 7h.01M7 17h.01"/>',
	);
	$p = isset( $paths[ $name ] ) ? $paths[ $name ] : $paths['lab'];
	$w = in_array( $name, array( 'arrow', 'out' ), true ) ? '2.2' : '2';
	return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="' . $w . '" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $p . '</svg>';
}

/** Illustration for an application without a picture, keyed by its colour theme. */
function clear_eo_art( $theme ) {
	$art = array(
		'flood' => '<g fill="none" stroke="#fff" stroke-opacity=".35"><path d="M0 300 Q50 280 100 300 T200 300 T300 300 T400 300"/><path d="M0 325 Q50 305 100 325 T200 325 T300 325 T400 325"/><path d="M0 350 Q50 330 100 350 T200 350 T300 350 T400 350"/></g><g fill="#fff" fill-opacity=".18"><rect x="40" y="170" width="40" height="120"/><rect x="90" y="130" width="34" height="160"/><rect x="134" y="200" width="50" height="90"/><rect x="250" y="150" width="36" height="140"/><rect x="296" y="190" width="44" height="100"/><rect x="200" y="110" width="40" height="180"/></g><path d="M300 40 L270 100 L295 100 L265 160" fill="none" stroke="#fff" stroke-width="3" stroke-opacity=".7" stroke-linejoin="round"/>',
		'agri'  => '<g fill="none" stroke="#fff" stroke-opacity=".3"><path d="M-20 260 L420 200"/><path d="M-20 290 L420 240"/><path d="M-20 320 L420 280"/><path d="M-20 350 L420 320"/><path d="M-20 380 L420 360"/><path d="M60 400 L140 190"/><path d="M200 400 L230 180"/><path d="M340 400 L320 170"/></g><circle cx="300" cy="90" r="36" fill="#fff" fill-opacity=".2"/><g fill="#fff" fill-opacity=".55"><circle cx="110" cy="280" r="4"/><circle cx="210" cy="255" r="4"/><circle cx="290" cy="300" r="4"/><circle cx="160" cy="330" r="4"/><circle cx="330" cy="240" r="4"/></g>',
		'air'   => '<g fill="#fff" fill-opacity=".1"><circle cx="90" cy="120" r="60"/><circle cx="150" cy="100" r="70"/><circle cx="230" cy="130" r="55"/><circle cx="300" cy="90" r="45"/></g><g fill="#fff" fill-opacity=".45"><circle cx="60" cy="210" r="2.5"/><circle cx="120" cy="190" r="2"/><circle cx="180" cy="230" r="3"/><circle cx="240" cy="200" r="2"/><circle cx="310" cy="220" r="2.5"/><circle cx="350" cy="180" r="2"/><circle cx="90" cy="250" r="2"/><circle cx="270" cy="250" r="2"/></g><path d="M0 330 L60 330 L60 300 L90 300 L90 330 L150 330 L150 280 L175 280 L175 330 L240 330 L240 310 L300 310 L300 330 L400 330 L400 400 L0 400 Z" fill="#fff" fill-opacity=".16"/>',
	);
	return '<svg class="art" viewBox="0 0 400 400" preserveAspectRatio="xMidYMid slice" aria-hidden="true">' . ( isset( $art[ $theme ] ) ? $art[ $theme ] : '' ) . '</svg>';
}

/** target="_blank" for links that leave the site. */
function clear_eo_ext( $url ) {
	return preg_match( '/^https?:/', (string) $url ) && 0 !== strpos( $url, home_url() ) ? ' target="_blank" rel="noopener"' : '';
}

/**
 * A link written as "#section" points to that section of the one-page home, from any page.
 */
function clear_eo_href( $url ) {
	return ( 0 === strpos( (string) $url, '#' ) && ! is_front_page() ) ? home_url( '/' . $url ) : $url;
}

/* ---------- What's new ---------- */

function clear_eo_types() {
	return array(
		'post'          => 'news',
		'clear_event'   => 'event',
		'clear_webinar' => 'webinar',
	);
}

function clear_eo_type_label( $type ) {
	$l = array(
		'news'    => __( 'Newsletter', 'clear-eo' ),
		'event'   => __( 'Event', 'clear-eo' ),
		'webinar' => __( 'Webinar', 'clear-eo' ),
	);
	return $l[ $type ];
}

/** Same as slug() in the static site, so its #news/<date>-<title> links keep working. */
function clear_eo_slug( $date, $title ) {
	$t = strtolower( remove_accents( wp_specialchars_decode( $title, ENT_QUOTES ) ) );
	return substr( $date, 0, 10 ) . '-' . trim( preg_replace( '/[^a-z0-9_]+/', '-', $t ), '-' );
}

/** A newsletter post, event or webinar as the array the templates use. */
function clear_eo_item( WP_Post $p ) {
	$types = clear_eo_types();
	$type  = $types[ $p->post_type ];
	$m     = function ( $k ) use ( $p ) {
		return get_post_meta( $p->ID, $k, true );
	};
	$date  = 'news' === $type ? get_the_date( 'Y-m-d', $p ) : ( $m( '_ce_date' ) ? $m( '_ce_date' ) : get_the_date( 'Y-m-d', $p ) );
	$title = get_the_title( $p );

	$defaults = array(
		'news'    => 'news:header_image',
		'event'   => 'events:header_image',
		'webinar' => 'webinars:header_image',
	);
	$featured = has_post_thumbnail( $p ) ? wp_get_attachment_image_url( get_post_thumbnail_id( $p ), '1536x1536' ) : '';
	$card     = $m( '_ce_card_image' ) ? wp_get_attachment_image_url( (int) $m( '_ce_card_image' ), 'large' ) : '';

	$summary = has_excerpt( $p ) ? $p->post_excerpt : wp_trim_words( wp_strip_all_tags( strip_shortcodes( $p->post_content ) ), 40 );

	return array(
		'post'         => $p,
		'type'         => $type,
		'title'        => $title,
		'date'         => $date,
		// Events and webinars without a start date fall back to their publish date for sorting, but are never "upcoming"
		'dated'        => 'news' === $type || (bool) $m( '_ce_date' ),
		'end_date'     => 'event' === $type ? (string) $m( '_ce_end_date' ) : '',
		'location'     => 'event' === $type ? (string) $m( '_ce_location' ) : '',
		'time'         => 'webinar' === $type ? (string) $m( '_ce_time' ) : '',
		'registration' => 'webinar' === $type ? (string) $m( '_ce_registration' ) : '',
		'recording'    => 'webinar' === $type ? (string) $m( '_ce_recording' ) : '',
		'speakers'     => 'webinar' === $type && is_array( $m( '_ce_speakers' ) ) ? $m( '_ce_speakers' ) : array(),
		'link'         => (string) $m( '_ce_link' ),
		'summary'      => $summary,
		'has_body'     => '' !== trim( $p->post_content ),
		'header'       => $featured ? $featured : clear_eo_get( $defaults[ $type ] ),
		'card_image'   => $card ? $card : ( $featured ? wp_get_attachment_image_url( get_post_thumbnail_id( $p ), 'large' ) : '' ),
		'slug'         => clear_eo_slug( $date, $title ),
		'permalink'    => get_permalink( $p ),
	);
}

/** All published newsletter posts, events and webinars, newest first. */
function clear_eo_whatsnew_items( $limit = -1 ) {
	$posts = get_posts(
		array(
			'post_type'        => array_keys( clear_eo_types() ),
			'post_status'      => 'publish',
			'numberposts'      => -1,
			'suppress_filters' => false,
		)
	);
	$items = array_map( 'clear_eo_item', $posts );
	usort(
		$items,
		function ( $a, $b ) {
			return strcmp( $b['date'], $a['date'] );
		}
	);
	return $limit > 0 ? array_slice( $items, 0, $limit ) : $items;
}

function clear_eo_today() {
	return wp_date( 'Y-m-d' );
}

/** Events and webinars that haven't ended yet. */
function clear_eo_upcoming( $n ) {
	return 'news' !== $n['type'] && $n['dated'] && ( $n['end_date'] ? $n['end_date'] : $n['date'] ) >= clear_eo_today();
}

/** Events and webinars that have ended. */
function clear_eo_past( $n ) {
	return 'news' !== $n['type'] && $n['dated'] && ! clear_eo_upcoming( $n );
}

/**
 * Newsletter posts and upcoming events/webinars always open in the reader window (upcoming ones show the
 * calendar buttons there); past events and webinars do when they have a text or speakers.
 */
function clear_eo_has_page( $n ) {
	return 'news' === $n['type'] || clear_eo_upcoming( $n ) || $n['has_body'] || $n['speakers'];
}

/** Where a card without a page links to. */
function clear_eo_out_link( $n ) {
	if ( clear_eo_upcoming( $n ) && $n['registration'] ) {
		return $n['registration'];
	}
	foreach ( array( $n['recording'], $n['link'] ) as $u ) {
		if ( $u ) {
			return $u;
		}
	}
	return clear_eo_get( 'site:whatsnew.default_link' );
}

function clear_eo_more_text( $n ) {
	if ( clear_eo_has_page( $n ) ) {
		$t = array(
			'news'    => __( 'Read more', 'clear-eo' ),
			'event'   => __( 'Event details', 'clear-eo' ),
			'webinar' => __( 'Webinar details', 'clear-eo' ),
		);
		return $t[ $n['type'] ];
	}
	if ( clear_eo_upcoming( $n ) && $n['registration'] ) {
		return __( 'Register', 'clear-eo' );
	}
	if ( $n['recording'] ) {
		return __( 'Watch the recording', 'clear-eo' );
	}
	return 'event' === $n['type'] && $n['link'] ? __( 'Event website', 'clear-eo' ) : __( 'Read more', 'clear-eo' );
}

function clear_eo_fmt( $d, $format = 'j M Y' ) {
	return date_i18n( $format, strtotime( substr( $d, 0, 10 ) . ' 12:00:00 UTC' ), true );
}

/** "1 Oct 2025", "11–13 Nov 2026", "30 Oct – 2 Nov 2026" or "30 Dec 2026 – 2 Jan 2027". */
function clear_eo_fmt_range( $a, $b ) {
	if ( ! $b || $b <= $a ) {
		return clear_eo_fmt( $a );
	}
	if ( substr( $a, 0, 4 ) !== substr( $b, 0, 4 ) ) {
		return clear_eo_fmt( $a ) . ' – ' . clear_eo_fmt( $b );
	}
	if ( substr( $a, 5, 2 ) !== substr( $b, 5, 2 ) ) {
		return clear_eo_fmt( $a, 'j M' ) . ' – ' . clear_eo_fmt( $b );
	}
	return clear_eo_fmt( $a, 'j' ) . '–' . clear_eo_fmt( $b );
}

/** Type and Upcoming badges with the date. */
function clear_eo_meta( $n ) {
	$up = '';
	if ( clear_eo_upcoming( $n ) ) {
		$up = '<span class="badge upcoming">' . esc_html__( 'Upcoming', 'clear-eo' ) . '</span>';
	} elseif ( clear_eo_past( $n ) ) {
		$up = '<span class="badge past">' . esc_html__( 'Past', 'clear-eo' ) . '</span>';
	}
	return sprintf(
		'<div class="card-meta"><span class="badges"><span class="badge %1$s">%2$s</span>%3$s</span><time datetime="%4$s">%5$s</time></div>',
		esc_attr( $n['type'] ),
		esc_html( clear_eo_type_label( $n['type'] ) ),
		$up,
		esc_attr( $n['date'] ),
		esc_html( clear_eo_fmt_range( $n['date'], $n['end_date'] ) )
	);
}

/** Location of an event or time of a webinar. */
function clear_eo_sub_line( $n ) {
	if ( 'event' === $n['type'] && $n['location'] ) {
		return '<span>' . clear_eo_icon( 'pin' ) . esc_html( $n['location'] ) . '</span>';
	}
	if ( 'webinar' === $n['type'] && $n['time'] ) {
		return '<span>' . clear_eo_icon( 'clock' ) . esc_html( $n['time'] ) . '</span>';
	}
	return '';
}

/* ---------- Add to calendar ---------- */

/**
 * Start and end of an event or webinar: UTC DateTimes for a webinar time like "14:00–15:30 CET"
 * (CET/CEST = Brussels time, or UTC/GMT), otherwise all-day dates with an exclusive end.
 */
function clear_eo_when( $n ) {
	$start = $n['date'];
	$end   = $n['end_date'] > $start ? $n['end_date'] : $start;
	if ( preg_match( '/(\d{1,2})[:.](\d{2})\s*[–—-]\s*(\d{1,2})[:.](\d{2})\s*(UTC|GMT)?/iu', $n['time'], $t ) ) {
		$tz  = new DateTimeZone( ! empty( $t[5] ) ? 'UTC' : 'Europe/Brussels' );
		$utc = new DateTimeZone( 'UTC' );
		$s0  = new DateTime( sprintf( '%s %02d:%02d', $start, $t[1], $t[2] ), $tz );
		$s1  = new DateTime( sprintf( '%s %02d:%02d', $start, $t[3], $t[4] ), $tz );
		return array( 'timed' => true, 'start' => $s0->setTimezone( $utc ), 'end' => $s1->setTimezone( $utc ) );
	}
	return array( 'timed' => false, 'start' => $start, 'end' => gmdate( 'Y-m-d', strtotime( $end . ' 12:00:00 UTC +1 day' ) ) );
}

function clear_eo_plain( $x ) {
	return preg_replace( '/[*_~`]/', '', preg_replace( '/!?\[([^\]]*)\]\([^)]*\)/', '$1', wp_strip_all_tags( (string) $x ) ) );
}

function clear_eo_calendar_description( $n ) {
	$parts = array( clear_eo_plain( $n['summary'] ) );
	if ( $n['registration'] ) {
		/* translators: %s: registration link */
		$parts[] = sprintf( __( 'Register: %s', 'clear-eo' ), $n['registration'] );
	}
	$parts[] = $n['permalink'];
	return implode( "\n\n", array_filter( $parts ) );
}

/** "Add to Outlook / Teams calendar" and ".ics" links for upcoming events and webinars. */
function clear_eo_calendar( $n ) {
	if ( ! clear_eo_upcoming( $n ) ) {
		return '';
	}
	$w     = clear_eo_when( $n );
	$where = 'event' === $n['type'] ? clear_eo_plain( $n['location'] ) : __( 'Online', 'clear-eo' );
	$args  = array(
		'path'     => '/calendar/action/compose',
		'rru'      => 'addevent',
		'subject'  => wp_specialchars_decode( $n['title'], ENT_QUOTES ),
		'location' => $where,
		'body'     => clear_eo_calendar_description( $n ),
	);
	$args += $w['timed']
		? array( 'startdt' => $w['start']->format( 'Y-m-d\TH:i:s\Z' ), 'enddt' => $w['end']->format( 'Y-m-d\TH:i:s\Z' ) )
		: array( 'startdt' => $w['start'], 'enddt' => $w['end'], 'allday' => 'true' );
	$outlook = 'https://outlook.office.com/calendar/0/deeplink/compose?' . http_build_query( $args, '', '&', PHP_QUERY_RFC3986 );
	$ics     = add_query_arg( 'clear_eo_ics', $n['post']->ID, home_url( '/' ) );
	return sprintf(
		'<a class="link-btn" href="%1$s" target="_blank" rel="noopener">%2$s %3$s</a> <a class="link-btn" href="%4$s" download="%5$s.ics">%6$s</a>',
		esc_url( $outlook ),
		esc_html__( 'Add to Outlook / Teams calendar', 'clear-eo' ),
		clear_eo_icon( 'out' ),
		esc_url( $ics ),
		esc_attr( $n['slug'] ),
		esc_html__( 'Download .ics (Google, Apple, other calendars)', 'clear-eo' )
	);
}

// ?clear_eo_ics=<post id>: the event or webinar as a calendar file
add_filter(
	'query_vars',
	function ( $v ) {
		$v[] = 'clear_eo_ics';
		return $v;
	}
);
add_action(
	'template_redirect',
	function () {
		$id = absint( get_query_var( 'clear_eo_ics' ) );
		if ( ! $id ) {
			return;
		}
		$p = get_post( $id );
		if ( ! $p || 'publish' !== $p->post_status || ! in_array( $p->post_type, array( 'clear_event', 'clear_webinar' ), true ) ) {
			status_header( 404 );
			exit;
		}
		$n    = clear_eo_item( $p );
		$w    = clear_eo_when( $n );
		$text = function ( $x ) {
			return str_replace( "\n", '\\n', preg_replace( '/([\\\\;,])/', '\\\\$1', $x ) );
		};
		$utc  = function ( DateTime $d ) {
			return $d->format( 'Ymd\THis\Z' );
		};
		$host = wp_parse_url( home_url(), PHP_URL_HOST );
		$ics  = array_merge(
			array( 'BEGIN:VCALENDAR', 'VERSION:2.0', "PRODID:-//CLEAR-EO//What's new//EN", 'BEGIN:VEVENT', 'UID:' . $n['slug'] . '@' . $host, 'DTSTAMP:' . gmdate( 'Ymd\THis\Z' ) ),
			$w['timed']
				? array( 'DTSTART:' . $utc( $w['start'] ), 'DTEND:' . $utc( $w['end'] ) )
				: array( 'DTSTART;VALUE=DATE:' . str_replace( '-', '', $w['start'] ), 'DTEND;VALUE=DATE:' . str_replace( '-', '', $w['end'] ) ),
			array(
				'SUMMARY:' . $text( wp_specialchars_decode( $n['title'], ENT_QUOTES ) ),
				'LOCATION:' . $text( 'event' === $n['type'] ? clear_eo_plain( $n['location'] ) : 'Online' ),
				'DESCRIPTION:' . $text( clear_eo_calendar_description( $n ) ),
				'URL:' . $n['permalink'],
				'END:VEVENT',
				'END:VCALENDAR',
			)
		);
		nocache_headers();
		header( 'Content-Type: text/calendar; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename="' . $n['slug'] . '.ics"' );
		echo implode( "\r\n", $ics ); // phpcs:ignore WordPress.Security.EscapeOutput -- calendar file, not HTML
		exit;
	}
);

/* ---------- Cards and reader ---------- */

/** A What's new card. Items with a page open in the reader window (data-dialog), the rest link out. */
function clear_eo_card( $n, $feature = false ) {
	$page = clear_eo_has_page( $n );
	$href = $page ? $n['permalink'] : clear_eo_out_link( $n );
	$sub  = clear_eo_sub_line( $n );
	$img  = $n['card_image'] ? '<img class="card-img" src="' . esc_url( $n['card_image'] ) . '" alt="" loading="lazy">' : '';
	return sprintf(
		'<a class="card reveal%1$s" data-t="%2$s" href="%3$s"%4$s>%5$s%6$s<h3>%7$s</h3>%8$s<p>%9$s</p><span class="more">%10$s →</span></a>',
		$feature ? ' feature' : '',
		esc_attr( $n['type'] ),
		esc_url( $href ),
		$page ? ' data-dialog="' . esc_attr( $n['slug'] ) . '"' : clear_eo_ext( $href ),
		$img,
		clear_eo_meta( $n ),
		esc_html( $n['title'] ),
		$sub ? '<p class="card-sub">' . $sub . '</p>' : '',
		clear_eo_md( $n['summary'] ),
		esc_html( clear_eo_more_text( $n ) )
	);
}

/**
 * The article: header image, badges, title, buttons, text and speakers. Shown in the reader window on the
 * home page, and on the post's own page.
 *
 * @param array  $n     Item from clear_eo_item().
 * @param string $title Tag for the title: h2 in the reader window, h1 on the post's page.
 */
function clear_eo_article( $n, $title = 'h2' ) {
	$up      = clear_eo_upcoming( $n );
	$actions = '';
	if ( $up && $n['registration'] ) {
		$actions .= '<a class="btn btn-primary" href="' . esc_url( $n['registration'] ) . '"' . clear_eo_ext( $n['registration'] ) . '>' . esc_html__( 'Register', 'clear-eo' ) . ' ' . clear_eo_icon( 'arrow' ) . '</a> ';
	}
	if ( $n['recording'] ) {
		$actions .= '<a class="link-btn" href="' . esc_url( $n['recording'] ) . '"' . clear_eo_ext( $n['recording'] ) . '>' . esc_html__( 'Watch the recording', 'clear-eo' ) . ' ' . clear_eo_icon( 'out' ) . '</a> ';
	}
	// An event's website is shown next to its location instead (see below)
	if ( $n['link'] && 'event' !== $n['type'] ) {
		$labels   = array(
			'event'   => __( 'Event website', 'clear-eo' ),
			'webinar' => __( 'More information', 'clear-eo' ),
			'news'    => __( 'Original post', 'clear-eo' ),
		);
		$actions .= '<a class="link-btn" href="' . esc_url( $n['link'] ) . '"' . clear_eo_ext( $n['link'] ) . '>' . esc_html( $labels[ $n['type'] ] ) . ' ' . clear_eo_icon( 'out' ) . '</a> ';
	}
	// A newsletter item with only a summary points to the full story elsewhere
	$default = clear_eo_get( 'site:whatsnew.default_link' );
	if ( 'news' === $n['type'] && ! $n['link'] && ! $n['has_body'] && $default ) {
		$actions .= '<a class="link-btn" href="' . esc_url( $default ) . '"' . clear_eo_ext( $default ) . '>' . esc_html__( 'Read more', 'clear-eo' ) . ' ' . clear_eo_icon( 'out' ) . '</a> ';
	}
	$actions .= clear_eo_calendar( $n );
	$actions  = $actions ? '<p class="article-actions">' . $actions . '</p>' : '';

	if ( $n['has_body'] ) {
		$GLOBALS['post'] = $n['post']; // phpcs:ignore WordPress.WP.GlobalVariablesOverride -- the_content filters read it
		setup_postdata( $n['post'] );
		$body = '<div class="prose">' . apply_filters( 'the_content', get_the_content( null, false, $n['post'] ) ) . '</div>'; // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals
		wp_reset_postdata();
	} else {
		$body = '<p class="prose">' . clear_eo_md( $n['summary'] ) . '</p>';
	}

	$speakers = '';
	if ( $n['speakers'] ) {
		$speakers = '<h3 class="speakers-h">' . esc_html__( 'Speakers', 'clear-eo' ) . '</h3><ul class="speakers">';
		foreach ( $n['speakers'] as $sp ) {
			$speakers .= '<li><b>' . esc_html( $sp['name'] ) . '</b>'
				. ( ! empty( $sp['organisation'] ) ? '<span>' . esc_html( $sp['organisation'] ) . '</span>' : '' )
				. ( ! empty( $sp['talk'] ) ? '<i>' . esc_html( $sp['talk'] ) . '</i>' : '' ) . '</li>';
		}
		$speakers .= '</ul>';
	}
	$sub = clear_eo_sub_line( $n );
	if ( 'event' === $n['type'] && $n['link'] ) {
		$sub .= '<a class="sub-link" href="' . esc_url( $n['link'] ) . '"' . clear_eo_ext( $n['link'] ) . '>' . clear_eo_icon( 'globe' ) . esc_html__( 'Event website', 'clear-eo' ) . ' ' . clear_eo_icon( 'out' ) . '</a>';
	}

	return ( $n['header'] ? '<img class="article-header" src="' . esc_url( $n['header'] ) . '" alt="">' : '' )
		. '<div class="article-content">'
		. clear_eo_meta( $n )
		. sprintf( '<%1$s class="article-title" id="articleTitle">%2$s</%1$s>', $title, esc_html( $n['title'] ) )
		. ( $sub ? '<p class="card-sub article-sub">' . $sub . '</p>' : '' )
		. ( $up ? $actions : '' )
		. $body
		. $speakers
		. ( $up ? '' : $actions )
		. '</div>';
}

/* ---------- Applications and partners ---------- */

function clear_eo_applications() {
	$posts = get_posts(
		array(
			'post_type'   => 'clear_application',
			'post_status' => 'publish',
			'numberposts' => -1,
			'orderby'     => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
		)
	);
	return array_map(
		function ( WP_Post $p ) {
			$theme = get_post_meta( $p->ID, '_ce_theme', true );
			$tab   = get_post_meta( $p->ID, '_ce_tab', true );
			return array(
				'post'      => $p,
				'theme'     => $theme ? $theme : 'flood',
				'tab'       => $tab ? $tab : get_the_title( $p ),
				'title'     => get_the_title( $p ),
				'summary'   => $p->post_excerpt,
				'location'  => (string) get_post_meta( $p->ID, '_ce_location', true ),
				'sections'  => (array) get_post_meta( $p->ID, '_ce_sections', true ),
				'image'     => has_post_thumbnail( $p ) ? wp_get_attachment_image_url( get_post_thumbnail_id( $p ), '1536x1536' ) : '',
				'has_page'  => '' !== trim( $p->post_content ),
				'permalink' => get_permalink( $p ),
			);
		},
		$posts
	);
}

/** One section of an application panel: a bulleted list or tags. */
function clear_eo_app_section( $sec ) {
	$style = ! empty( $sec['style'] ) ? $sec['style'] : 'list';
	$items = isset( $sec['items'] ) ? (array) $sec['items'] : array();
	if ( 'list' === $style ) {
		$body = '<ul class="list">' . implode( '', array_map( function ( $x ) {
			return '<li><span>' . clear_eo_md( $x ) . '</span></li>';
		}, $items ) ) . '</ul>';
	} else {
		$body = '<div class="chips">' . implode( '', array_map( function ( $x ) use ( $style ) {
			return '<span class="chip' . ( 'partners' === $style ? ' strong' : '' ) . '">' . clear_eo_md( $x ) . '</span>';
		}, $items ) ) . '</div>';
	}
	return ! empty( $sec['heading'] ) ? '<div><h4>' . esc_html( $sec['heading'] ) . '</h4>' . $body . '</div>' : $body;
}

/** All panel sections; a section without a heading continues the one above it. */
function clear_eo_app_sections( $sections ) {
	$out = array();
	foreach ( $sections as $sec ) {
		if ( ! is_array( $sec ) ) {
			continue;
		}
		$html = clear_eo_app_section( $sec );
		if ( empty( $sec['heading'] ) && $out ) {
			$out[ count( $out ) - 1 ] = preg_replace( '#</div>$#', $html . '</div>', $out[ count( $out ) - 1 ] );
		} else {
			$out[] = $html;
		}
	}
	return implode( '', $out );
}

function clear_eo_partners( $affiliated ) {
	$posts = get_posts(
		array(
			'post_type'   => 'clear_partner',
			'post_status' => 'publish',
			'numberposts' => -1,
			'orderby'     => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
		)
	);
	return array_values(
		array_filter(
			$posts,
			function ( $p ) use ( $affiliated ) {
				return (bool) get_post_meta( $p->ID, '_ce_affiliated', true ) === $affiliated;
			}
		)
	);
}

/**
 * A partner card. The whole card links to the partner's website; hovering or focusing it slides up its
 * role and description (on touch screens the description is shown in the card).
 */
function clear_eo_partner_card( WP_Post $p, $affiliated ) {
	$name = get_the_title( $p );
	$link = get_post_meta( $p->ID, '_ce_link', true );
	$role = get_post_meta( $p->ID, '_ce_role', true );
	$desc = get_post_meta( $p->ID, '_ce_description', true );
	$tag  = $link ? 'a' : 'div';
	if ( has_post_thumbnail( $p ) ) {
		$logo_id = get_post_thumbnail_id( $p );
		$alt     = get_post_meta( $logo_id, '_wp_attachment_image_alt', true );
		/* translators: %s: partner name */
		$mark = '<span class="logo"><img src="' . esc_url( wp_get_attachment_image_url( $logo_id, 'medium' ) ) . '" alt="' . esc_attr( $alt ? $alt : sprintf( __( '%s logo', 'clear-eo' ), $name ) ) . '" loading="lazy"></span>';
	} else {
		$mark = '<span class="wm">' . esc_html( $name ) . '</span>';
	}
	$info = '';
	if ( $role || $desc || $link ) {
		$info = '<span class="partner-info">'
			. ( $role ? '<span class="role">' . esc_html( $role ) . '</span>' : '' )
			. '<span class="pname">' . esc_html( $name ) . '</span>'
			. ( $desc ? '<span class="desc">' . esc_html( $desc ) . '</span>' : '' )
			. ( $link ? '<span class="go">' . esc_html__( 'Visit website', 'clear-eo' ) . ' ' . clear_eo_icon( 'out' ) . '</span>' : '' )
			. '</span>';
	}
	return sprintf(
		'<%1$s class="partner%2$s%3$s"%4$s>%5$s<span class="full">%6$s</span><span class="cc">%7$s</span>%8$s</%1$s>',
		$tag,
		$affiliated ? ' aff' : '',
		$info ? ' has-info' : '',
		$link ? ' href="' . esc_url( $link ) . '"' . clear_eo_ext( $link ) : '',
		$mark,
		esc_html( $name ),
		esc_html( get_post_meta( $p->ID, '_ce_country', true ) ),
		$info
	);
}

/** For logged-in editors: a hint in an empty section, pointing to where its content is added. */
function clear_eo_empty_hint( $what, $admin_url ) {
	if ( ! current_user_can( 'edit_posts' ) ) {
		return '';
	}
	return '<p class="empty-hint">' . sprintf(
		/* translators: 1: content type, 2: link to add it, 3: link to the importer */
		esc_html__( 'No %1$s yet. %2$s, or %3$s.', 'clear-eo' ),
		esc_html( $what ),
		'<a href="' . esc_url( admin_url( $admin_url ) ) . '">' . esc_html__( 'Add one', 'clear-eo' ) . '</a>',
		'<a href="' . esc_url( admin_url( 'tools.php?page=clear-eo-import' ) ) . '">' . esc_html__( 'import the CLEAR-EO content', 'clear-eo' ) . '</a>'
	) . '</p>';
}
