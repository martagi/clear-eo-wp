<?php
/**
 * A page (e.g. Privacy Policy).
 *
 * @package clear-eo
 */

get_header();
the_post();
?>
<main id="main">
	<?php get_template_part( 'template-parts/page-band', null, array( 'title' => get_the_title() ) ); ?>
	<div class="wrap single-wrap">
		<article <?php post_class( 'article article-page' ); ?>>
			<?php if ( has_post_thumbnail() ) : ?>
				<?php the_post_thumbnail( '1536x1536', array( 'class' => 'article-header', 'alt' => '' ) ); ?>
			<?php endif; ?>
			<div class="article-content"><div class="prose"><?php the_content(); ?></div>
				<?php wp_link_pages(); ?>
			</div>
		</article>
	</div>
</main>
<?php
get_footer();
