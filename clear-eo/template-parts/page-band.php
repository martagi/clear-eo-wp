<?php
/**
 * Dark band at the top of inner pages, behind the menu: back link, kicker and title.
 *
 * @package clear-eo
 *
 * @var array $args {
 *     @type string $back       Back link URL.
 *     @type string $back_label Back link text.
 *     @type string $kicker     Small label above the title.
 *     @type string $title      Title (omit when the content below has its own).
 *     @type string $text       Short text under the title.
 * }
 */

$args = wp_parse_args( $args, array( 'back' => '', 'back_label' => '', 'kicker' => '', 'title' => '', 'text' => '' ) );
?>
<div class="page-band<?php echo $args['title'] ? ' has-title' : ''; ?>">
	<div class="wrap">
		<?php if ( $args['back'] ) : ?>
			<a class="back" href="<?php echo esc_url( $args['back'] ); ?>">← <?php echo esc_html( $args['back_label'] ); ?></a>
		<?php endif; ?>
		<?php if ( $args['kicker'] ) : ?>
			<p class="kicker"><?php echo esc_html( $args['kicker'] ); ?></p>
		<?php endif; ?>
		<?php if ( $args['title'] ) : ?>
			<h1><?php echo esc_html( $args['title'] ); ?></h1>
		<?php endif; ?>
		<?php if ( $args['text'] ) : ?>
			<p class="band-text"><?php echo wp_kses_post( $args['text'] ); ?></p>
		<?php endif; ?>
	</div>
</div>
