<?php
/**
 * Footer with the EU funding statement, and the reader window used by What's new cards.
 *
 * @package clear-eo
 */

$clear_eo_btn = array(
	'label' => clear_eo_get( 'site:footer.button.label' ),
	'link'  => clear_eo_get( 'site:footer.button.link' ),
);
?>
<footer>
	<div class="wrap">
		<div class="foot-cta">
			<h2><?php echo clear_eo_text( 'site:footer.title' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h2>
			<?php if ( $clear_eo_btn['label'] ) : ?>
				<a class="btn btn-primary" href="<?php echo esc_url( clear_eo_href( $clear_eo_btn['link'] ) ); ?>"<?php echo clear_eo_ext( $clear_eo_btn['link'] ); // phpcs:ignore ?>><?php echo esc_html( $clear_eo_btn['label'] ); ?> <?php echo clear_eo_icon( 'out' ); // phpcs:ignore ?></a>
			<?php endif; ?>
		</div>
		<div class="foot-grid">
			<div class="eu">
				<svg viewBox="0 0 810 540" aria-label="<?php esc_attr_e( 'Flag of the European Union', 'clear-eo' ); ?>" role="img">
					<rect width="810" height="540" fill="#039"/>
					<g fill="#fc0"><?php echo clear_eo_eu_stars(); // phpcs:ignore ?></g>
				</svg>
				<div>
					<p><?php echo clear_eo_text( 'site:footer.funding' ); // phpcs:ignore ?></p>
					<?php if ( clear_eo_get( 'site:footer.support' ) ) : ?>
						<small><?php echo clear_eo_text( 'site:footer.support' ); // phpcs:ignore ?></small>
					<?php endif; ?>
				</div>
			</div>
			<div>
				<h4><?php esc_html_e( 'Explore', 'clear-eo' ); ?></h4>
				<ul><?php clear_eo_menu( 'footer' ); ?></ul>
			</div>
			<div>
				<h4><?php esc_html_e( 'Project', 'clear-eo' ); ?></h4>
				<ul>
					<?php foreach ( clear_eo_get( 'site:footer.project_links' ) as $clear_eo_l ) : ?>
						<li><a href="<?php echo esc_url( clear_eo_href( $clear_eo_l['link'] ) ); ?>"<?php echo clear_eo_ext( $clear_eo_l['link'] ); // phpcs:ignore ?>><?php echo esc_html( $clear_eo_l['label'] ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
		<div class="fine">
			<span><?php echo clear_eo_text( 'site:footer.copyright' ); // phpcs:ignore ?></span>
			<span class="draft-note"><?php echo clear_eo_text( 'site:footer.note' ); // phpcs:ignore ?></span>
		</div>
	</div>
</footer>

<dialog class="article" id="article" aria-labelledby="articleTitle">
	<div class="article-bar">
		<button class="icon-btn article-close" id="articleClose" aria-label="<?php esc_attr_e( 'Close article', 'clear-eo' ); ?>">
			<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M6 6l12 12M18 6 6 18"/></svg>
		</button>
	</div>
	<div id="articleBody"></div>
</dialog>

<?php wp_footer(); ?>
</body>
</html>
