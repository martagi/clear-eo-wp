<?php
/**
 * The small Markdown dialect of the static site, ported from index.html.
 *
 * clear_eo_md(): one line (bold, italic, links, images), used for the short text fields in the Customizer
 * and meta boxes. clear_eo_md_block(): full articles (paragraphs, headings, lists, quotes, rules), used by
 * the importer to turn the old Markdown posts into HTML. Raw HTML is always escaped.
 *
 * @package clear-eo
 */

defined( 'ABSPATH' ) || exit;

function clear_eo_safe_url( $u ) {
	return preg_match( '/^\s*(javascript|data|vbscript):/i', $u ) ? '#' : $u;
}

function clear_eo_md( $x ) {
	$keep = array();
	$s    = preg_replace_callback(
		'/\\\\([\\\\`*_{}\[\]()#+\-.!>~|])/',
		function ( $m ) use ( &$keep ) {
			$keep[] = $m[1];
			return "\0" . ( count( $keep ) - 1 ) . "\0";
		},
		(string) $x
	);
	$h = htmlspecialchars( $s, ENT_COMPAT, 'UTF-8' );
	$h = preg_replace_callback(
		'/!\[([^\]]*)\]\(([^)\s]+)(?:\s+&quot;.*?&quot;)?\)/u',
		function ( $m ) {
			return '<img src="' . clear_eo_safe_url( $m[2] ) . '" alt="' . $m[1] . '" loading="lazy">';
		},
		$h
	);
	$h = preg_replace_callback(
		'/\[([^\]]+)\]\(([^)\s]+)(?:\s+&quot;.*?&quot;)?\)/u',
		function ( $m ) {
			$ext = preg_match( '/^https?:/', $m[2] ) ? ' target="_blank" rel="noopener"' : '';
			return '<a href="' . clear_eo_safe_url( $m[2] ) . '"' . $ext . '>' . $m[1] . '</a>';
		},
		$h
	);
	$h = preg_replace_callback(
		'/\*\*(.+?)\*\*|__(.+?)__/u',
		function ( $m ) {
			return '<b>' . ( '' !== $m[1] ? $m[1] : $m[2] ) . '</b>';
		},
		$h
	);
	$h = preg_replace_callback(
		'/\*(.+?)\*|(?<![\w])_(.+?)_(?![\w])/u',
		function ( $m ) {
			return '<i>' . ( '' !== $m[1] ? $m[1] : $m[2] ) . '</i>';
		},
		$h
	);
	$h = preg_replace( '/~~(.+?)~~/u', '<s>$1</s>', $h );
	$h = preg_replace( '/`(.+?)`/u', '<code>$1</code>', $h );
	return preg_replace_callback(
		'/\x00(\d+)\x00/',
		function ( $m ) use ( $keep ) {
			return htmlspecialchars( $keep[ (int) $m[1] ], ENT_COMPAT, 'UTF-8' );
		},
		$h
	);
}

function clear_eo_md_block( $src ) {
	$lines = explode( "\n", str_replace( "\r", '', (string) $src ) );
	$out   = '';
	$para  = array();
	$list  = null;
	$quote = null;
	$brk   = '/( {2,}|\\\\)$/';

	$flush = function () use ( &$out, &$para, &$list, &$quote, $brk ) {
		if ( $para ) {
			$html = '';
			$last = count( $para ) - 1;
			foreach ( $para as $i => $l ) {
				$html .= clear_eo_md( preg_replace( $brk, '', $l ) );
				if ( $i < $last ) {
					$html .= preg_match( $brk, $l ) ? '<br>' : ' ';
				}
			}
			$out .= "<p>$html</p>\n";
		}
		if ( $list ) {
			$out .= '<' . $list['tag'] . '>' . implode( '', array_map( function ( $t ) {
				return '<li>' . clear_eo_md( $t ) . '</li>';
			}, $list['items'] ) ) . '</' . $list['tag'] . ">\n";
		}
		if ( null !== $quote ) {
			$out .= '<blockquote>' . clear_eo_md_block( implode( "\n", $quote ) ) . "</blockquote>\n";
		}
		$para  = array();
		$list  = null;
		$quote = null;
	};

	foreach ( $lines as $line ) {
		if ( '' === trim( $line ) ) {
			$flush();
			continue;
		}
		if ( preg_match( '/^\s*>\s?(.*)$/', $line, $m ) ) {
			if ( null === $quote ) {
				$flush();
				$quote = array();
			}
			$quote[] = $m[1];
			continue;
		}
		if ( null !== $quote ) {
			$flush();
		}
		if ( preg_match( '/^(#{1,6})\s+(.*?)\s*#*$/', $line, $m ) ) {
			$flush();
			$n    = min( max( strlen( $m[1] ) + 1, 3 ), 5 );
			$out .= "<h$n>" . clear_eo_md( $m[2] ) . "</h$n>\n";
			continue;
		}
		if ( preg_match( '/^\s*([-*_])(\s*\1){2,}\s*$/', $line ) ) {
			$flush();
			$out .= "<hr>\n";
			continue;
		}
		if ( preg_match( '/^\s*([-*+]|\d+[.)])\s+(.*)$/', $line, $m ) ) {
			$tag = preg_match( '/\d/', $m[1] ) ? 'ol' : 'ul';
			if ( $para || ( $list && $list['tag'] !== $tag && ! preg_match( '/^\s{2,}/', $line ) ) ) {
				$flush();
			}
			if ( ! $list ) {
				$list = array( 'tag' => $tag, 'items' => array() );
			}
			$list['items'][] = $m[2];
			continue;
		}
		if ( $list && preg_match( '/^\s{2,}\S/', $line ) ) {
			$list['items'][ count( $list['items'] ) - 1 ] .= ' ' . trim( $line );
			continue;
		}
		if ( $list ) {
			$flush();
		}
		$para[] = trim( $line ) . ( preg_match( $brk, $line ) ? '  ' : '' );
	}
	$flush();
	return $out;
}
