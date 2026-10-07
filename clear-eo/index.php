<?php
/**
 * Lists: the posts page, Events and Webinars archives, categories, search. What's new items are shown
 * as the home page's cards; anything else as a plain card.
 *
 * @package clear-eo
 */

get_header();

if ( is_search() ) {
	/* translators: %s: search terms */
	$clear_eo_title = sprintf( __( 'Search results for “%s”', 'clear-eo' ), get_search_query() );
} elseif ( is_home() ) {
	$clear_eo_title = get_option( 'page_for_posts' ) ? get_the_title( get_option( 'page_for_posts' ) ) : __( 'Newsletter', 'clear-eo' );
} else {
	$clear_eo_title = wp_strip_all_tags( get_the_archive_title() );
}
$clear_eo_types = clear_eo_types();
?>
<main id="main">
	<?php
	get_template_part(
		'template-parts/page-band',
		null,
		array(
			'back'       => home_url( '/#news' ),
			'back_label' => clear_eo_get( 'site:whatsnew.title' ),
			'kicker'     => clear_eo_get( 'site:whatsnew.kicker' ),
			'title'      => $clear_eo_title,
			'text'       => get_the_archive_description(),
		)
	);
	?>
	<section class="block news list-page">
		<div class="wrap">
			<?php if ( have_posts() ) : ?>
				<div class="news-grid">
					<?php
					while ( have_posts() ) :
						the_post();
						if ( isset( $clear_eo_types[ get_post_type() ] ) ) {
							echo clear_eo_card( clear_eo_item( get_post() ) ); // phpcs:ignore WordPress.Security.EscapeOutput
							continue;
						}
						?>
						<a class="card reveal" href="<?php the_permalink(); ?>">
							<h3><?php the_title(); ?></h3>
							<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 30 ) ); ?></p>
							<span class="more"><?php esc_html_e( 'Read more', 'clear-eo' ); ?> →</span>
						</a>
					<?php endwhile; ?>
				</div>
				<div class="pager">
					<?php
					the_posts_pagination(
						array(
							'prev_text' => '← ' . __( 'Newer', 'clear-eo' ),
							'next_text' => __( 'Older', 'clear-eo' ) . ' →',
						)
					);
					?>
				</div>
			<?php else : ?>
				<p class="sub"><?php esc_html_e( 'Nothing here yet.', 'clear-eo' ); ?></p>
				<?php get_search_form(); ?>
			<?php endif; ?>
		</div>
	</section>
</main>
<?php
get_footer();
