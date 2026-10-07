<?php
/**
 * A newsletter post, event or webinar on its own page: the same article as in the home page's reader
 * window, on a card that overlaps the dark band.
 *
 * @package clear-eo
 */

get_header();
the_post();
$clear_eo_is_item = isset( clear_eo_types()[ get_post_type() ] );
?>
<main id="main">
	<?php
	get_template_part(
		'template-parts/page-band',
		null,
		array(
			'back'       => home_url( '/#news' ),
			'back_label' => clear_eo_get( 'site:whatsnew.title' ),
			'title'      => $clear_eo_is_item ? '' : get_the_title(),
		)
	);
	?>
	<div class="wrap single-wrap">
		<article <?php post_class( 'article article-page' . ( $clear_eo_is_item && clear_eo_item( get_post() )['header'] ? ' has-header' : '' ) ); ?>>
			<?php
			if ( $clear_eo_is_item ) {
				echo clear_eo_article( clear_eo_item( get_post() ), 'h1' ); // phpcs:ignore WordPress.Security.EscapeOutput
			} else {
				echo '<div class="article-content"><div class="prose">';
				the_content();
				echo '</div></div>';
			}
			?>
		</article>
	</div>
</main>
<?php
get_footer();
