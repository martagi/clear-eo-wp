<?php
/**
 * Page not found.
 *
 * @package clear-eo
 */

get_header();
?>
<main id="main">
	<?php
	get_template_part(
		'template-parts/page-band',
		null,
		array(
			'kicker' => '404',
			'title'  => __( 'This page could not be found', 'clear-eo' ),
		)
	);
	?>
	<section class="block">
		<div class="wrap">
			<p class="sub"><?php esc_html_e( 'It may have moved, or the address may be mistyped.', 'clear-eo' ); ?></p>
			<p class="cta"><a class="btn btn-primary" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Back to the home page', 'clear-eo' ); ?> <?php echo clear_eo_icon( 'arrow' ); // phpcs:ignore ?></a></p>
		</div>
	</section>
</main>
<?php
get_footer();
