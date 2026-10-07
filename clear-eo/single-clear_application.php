<?php
/**
 * An application's own page: its picture as the banner, the full text, and beside it the partners
 * involved and links to the other applications.
 *
 * @package clear-eo
 */

// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- helpers return escaped HTML

get_header();
the_post();

$clear_eo_apps = clear_eo_applications();
$clear_eo_i    = 0;
foreach ( $clear_eo_apps as $clear_eo_k => $clear_eo_x ) {
	if ( $clear_eo_x['post']->ID === get_the_ID() ) {
		$clear_eo_i = $clear_eo_k;
	}
}
$clear_eo_a = isset( $clear_eo_apps[ $clear_eo_i ] ) && $clear_eo_apps[ $clear_eo_i ]['post']->ID === get_the_ID() ? $clear_eo_apps[ $clear_eo_i ] : null;
if ( ! $clear_eo_a ) {
	// A draft being previewed is not in the published list
	$clear_eo_a = array(
		'theme'    => get_post_meta( get_the_ID(), '_ce_theme', true ) ? get_post_meta( get_the_ID(), '_ce_theme', true ) : 'flood',
		'title'    => get_the_title(),
		'summary'  => get_the_excerpt(),
		'location' => get_post_meta( get_the_ID(), '_ce_location', true ),
		'sections' => (array) get_post_meta( get_the_ID(), '_ce_sections', true ),
		'image'    => get_the_post_thumbnail_url( null, '1536x1536' ),
	);
	$clear_eo_i = count( $clear_eo_apps );
}
$clear_eo_others = array_filter(
	$clear_eo_apps,
	function ( $x ) {
		return $x['post']->ID !== get_the_ID() && $x['has_page'];
	}
);
$clear_eo_partner_secs = array_filter(
	$clear_eo_a['sections'],
	function ( $s ) {
		return is_array( $s ) && isset( $s['style'] ) && 'partners' === $s['style'];
	}
);
?>
<main id="main">
	<div class="app-hero p-<?php echo esc_attr( $clear_eo_a['theme'] ); ?><?php echo $clear_eo_a['image'] ? ' has-photo' : ''; ?>">
		<?php echo $clear_eo_a['image'] ? '<img class="photo" src="' . esc_url( $clear_eo_a['image'] ) . '" alt="">' : clear_eo_art( $clear_eo_a['theme'] ); ?>
		<div class="wrap">
			<a class="back" href="<?php echo esc_url( home_url( '/#applications' ) ); ?>">← <?php esc_html_e( 'All applications', 'clear-eo' ); ?></a>
			<?php if ( $clear_eo_a['location'] ) : ?>
				<span class="pin"><?php echo clear_eo_icon( 'pin' ) . esc_html( $clear_eo_a['location'] ); ?></span>
			<?php endif; ?>
			<span class="num"><?php echo esc_html( sprintf( /* translators: %s: number, e.g. 01 */ __( 'APPLICATION %s', 'clear-eo' ), str_pad( $clear_eo_i + 1, 2, '0', STR_PAD_LEFT ) ) ); ?></span>
			<h1><?php echo esc_html( $clear_eo_a['title'] ); ?></h1>
			<p><?php echo clear_eo_md( $clear_eo_a['summary'] ); ?></p>
		</div>
	</div>
	<div class="wrap app-body p-<?php echo esc_attr( $clear_eo_a['theme'] ); ?>">
		<article class="prose"><?php the_content(); ?></article>
		<?php if ( $clear_eo_partner_secs || $clear_eo_others ) : ?>
			<aside class="app-aside">
				<?php echo implode( '', array_map( 'clear_eo_app_section', $clear_eo_partner_secs ) ); ?>
				<?php if ( $clear_eo_others ) : ?>
					<div>
						<h4><?php esc_html_e( 'Other applications', 'clear-eo' ); ?></h4>
						<ul class="app-others">
							<?php foreach ( $clear_eo_others as $clear_eo_x ) : ?>
								<li><a class="t-<?php echo esc_attr( $clear_eo_x['theme'] ); ?>" href="<?php echo esc_url( $clear_eo_x['permalink'] ); ?>"><span class="sw"></span><?php echo esc_html( $clear_eo_x['title'] ); ?> →</a></li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>
			</aside>
		<?php endif; ?>
	</div>
</main>
<?php
get_footer();
