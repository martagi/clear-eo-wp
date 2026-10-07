<?php
/**
 * The one-page home: hero → key facts → About → Applications → Virtual Observatory → Consortium →
 * What's new. Used whatever "Your homepage displays" is set to.
 *
 * @package clear-eo
 */

// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- clear_eo_text(), clear_eo_md() and the card/icon helpers return escaped HTML

get_header();

$clear_eo_bg       = clear_eo_get( 'site:hero.background' );
$clear_eo_default  = clear_eo_asset( '_assets/hero.jpg' );
$clear_eo_buttons  = array(
	array( 'btn-primary', 'site:hero.primary_button', true ),
	array( 'btn-ghost', 'site:hero.secondary_button', false ),
);
$clear_eo_apps     = clear_eo_applications();
$clear_eo_items    = clear_eo_whatsnew_items();
$clear_eo_partners = clear_eo_partners( false );
$clear_eo_aff      = clear_eo_partners( true );
?>
<main id="main">
	<!-- ================= HERO ================= -->
	<section class="hero" id="top">
		<?php if ( $clear_eo_bg === $clear_eo_default ) : ?>
			<img class="hero-bg" src="<?php echo esc_url( $clear_eo_default ); ?>" srcset="<?php echo esc_url( clear_eo_asset( '_assets/hero-small.jpg' ) ); ?> 1200w, <?php echo esc_url( $clear_eo_default ); ?> 2400w" sizes="100vw" alt="" fetchpriority="high">
		<?php elseif ( $clear_eo_bg ) : ?>
			<img class="hero-bg" src="<?php echo esc_url( $clear_eo_bg ); ?>" alt="" fetchpriority="high">
		<?php endif; ?>

		<div class="wrap">
			<div class="hero-content">
				<span class="eyebrow"><span class="dot"></span><span><?php echo esc_html( clear_eo_get( 'site:hero.eyebrow' ) ); ?></span></span>
				<h1><?php echo esc_html( clear_eo_get( 'site:hero.title' ) ); ?> <span class="grad"><?php echo esc_html( clear_eo_get( 'site:hero.title_highlight' ) ); ?></span></h1>
				<p class="acronym"><?php echo clear_eo_text( 'site:hero.acronym' ); ?></p>
				<p class="lede"><?php echo esc_html( clear_eo_get( 'site:hero.lede' ) ); ?></p>
				<div class="cta">
					<?php
					foreach ( $clear_eo_buttons as $clear_eo_b ) :
						$clear_eo_label = clear_eo_get( $clear_eo_b[1] . '.label' );
						$clear_eo_link  = clear_eo_get( $clear_eo_b[1] . '.link' );
						if ( ! $clear_eo_label ) {
							continue;
						}
						?>
						<a class="btn <?php echo esc_attr( $clear_eo_b[0] ); ?>" href="<?php echo esc_url( $clear_eo_link ); ?>"<?php echo clear_eo_ext( $clear_eo_link ); ?>><?php echo esc_html( $clear_eo_label ); ?><?php echo $clear_eo_b[2] ? ' ' . clear_eo_icon( 'arrow' ) : ''; ?></a>
					<?php endforeach; ?>
				</div>
				<div class="sat-tags" aria-label="<?php esc_attr_e( 'Satellite missions and infrastructures', 'clear-eo' ); ?>">
					<?php foreach ( clear_eo_get( 'site:hero.tags' ) as $clear_eo_t ) : ?>
						<span><?php echo esc_html( $clear_eo_t ); ?></span>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</section>

	<!-- ================= FACTS ================= -->
	<div class="facts">
		<div class="wrap">
			<div class="facts-grid reveal">
				<?php
				for ( $clear_eo_i = 0; $clear_eo_i < 5; $clear_eo_i++ ) :
					$clear_eo_v = clear_eo_get( "site:facts.$clear_eo_i.value" );
					if ( '' === $clear_eo_v ) {
						continue;
					}
					?>
					<div class="fact"><b><?php echo esc_html( $clear_eo_v ); ?></b><span><?php echo esc_html( clear_eo_get( "site:facts.$clear_eo_i.label" ) ); ?></span></div>
				<?php endfor; ?>
			</div>
		</div>
	</div>

	<!-- ================= ABOUT ================= -->
	<section class="block" id="about">
		<div class="wrap about-grid">
			<div class="reveal">
				<p class="kicker"><?php echo esc_html( clear_eo_get( 'site:about.kicker' ) ); ?></p>
				<h2 class="h2"><?php echo esc_html( clear_eo_get( 'site:about.title' ) ); ?></h2>
				<p class="sub"><?php echo clear_eo_text( 'site:about.text' ); ?></p>
				<?php if ( clear_eo_get( 'site:about.quote' ) ) : ?>
					<p class="quote"><?php echo clear_eo_text( 'site:about.quote' ); ?></p>
				<?php endif; ?>
			</div>
			<div class="pillars reveal">
				<?php
				for ( $clear_eo_i = 0; $clear_eo_i < 4; $clear_eo_i++ ) :
					$clear_eo_title = clear_eo_get( "site:about.pillars.$clear_eo_i.title" );
					if ( '' === $clear_eo_title ) {
						continue;
					}
					?>
					<div class="pillar">
						<div class="ic"><?php echo clear_eo_icon( clear_eo_get( "site:about.pillars.$clear_eo_i.icon" ) ); ?></div>
						<h3><?php echo esc_html( $clear_eo_title ); ?></h3>
						<p><?php echo clear_eo_text( "site:about.pillars.$clear_eo_i.text" ); ?></p>
					</div>
				<?php endfor; ?>
			</div>
		</div>
	</section>

	<!-- ================= APPLICATIONS ================= -->
	<section class="block apps" id="applications">
		<div class="wrap">
			<div class="reveal">
				<p class="kicker"><?php echo esc_html( clear_eo_get( 'apps:kicker' ) ); ?></p>
				<h2 class="h2"><?php echo esc_html( clear_eo_get( 'apps:title' ) ); ?></h2>
			</div>
			<?php echo $clear_eo_apps ? '' : clear_eo_empty_hint( __( 'applications', 'clear-eo' ), 'post-new.php?post_type=clear_application' ); ?>
			<div class="tablist" role="tablist" aria-label="<?php esc_attr_e( 'Applications', 'clear-eo' ); ?>">
				<?php foreach ( $clear_eo_apps as $clear_eo_i => $clear_eo_a ) : ?>
					<button class="tab t-<?php echo esc_attr( $clear_eo_a['theme'] ); ?>" role="tab" id="tab-<?php echo (int) $clear_eo_i; ?>" aria-controls="panel-<?php echo (int) $clear_eo_i; ?>" aria-selected="<?php echo $clear_eo_i ? 'false' : 'true'; ?>"<?php echo $clear_eo_i ? ' tabindex="-1"' : ''; ?>><span class="sw"></span><?php echo esc_html( $clear_eo_a['tab'] ); ?></button>
				<?php endforeach; ?>
			</div>
			<div>
				<?php
				foreach ( $clear_eo_apps as $clear_eo_i => $clear_eo_a ) :
					// The coloured side links to the application's own page, so its summary can't contain links
					$clear_eo_tag = $clear_eo_a['has_page'] ? 'a' : 'div';
					?>
					<div class="panel p-<?php echo esc_attr( $clear_eo_a['theme'] ); ?>" role="tabpanel" id="panel-<?php echo (int) $clear_eo_i; ?>" aria-labelledby="tab-<?php echo (int) $clear_eo_i; ?>" tabindex="0"<?php echo $clear_eo_i ? ' hidden' : ''; ?>>
						<<?php echo $clear_eo_tag; ?> class="panel-visual<?php echo $clear_eo_a['image'] ? ' has-photo' : ''; ?>"<?php echo $clear_eo_a['has_page'] ? ' href="' . esc_url( $clear_eo_a['permalink'] ) . '"' : ''; ?>>
							<?php echo $clear_eo_a['image'] ? '<img class="photo" src="' . esc_url( $clear_eo_a['image'] ) . '" alt="" loading="lazy">' : clear_eo_art( $clear_eo_a['theme'] ); ?>
							<?php if ( $clear_eo_a['location'] ) : ?>
								<span class="pin"><?php echo clear_eo_icon( 'pin' ) . esc_html( $clear_eo_a['location'] ); ?></span>
							<?php endif; ?>
							<span class="num"><?php echo esc_html( sprintf( /* translators: %s: number, e.g. 01 */ __( 'APPLICATION %s', 'clear-eo' ), str_pad( $clear_eo_i + 1, 2, '0', STR_PAD_LEFT ) ) ); ?></span>
							<h3><?php echo esc_html( $clear_eo_a['title'] ); ?></h3>
							<p><?php echo esc_html( clear_eo_plain( $clear_eo_a['summary'] ) ); ?></p>
							<?php if ( $clear_eo_a['has_page'] ) : ?>
								<span class="learn"><?php esc_html_e( 'Learn more →', 'clear-eo' ); ?></span>
							<?php endif; ?>
						</<?php echo $clear_eo_tag; ?>>
						<div class="panel-body"><?php echo clear_eo_app_sections( $clear_eo_a['sections'] ); ?></div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<!-- ================= VIRTUAL OBSERVATORY ================= -->
	<section class="block vo" id="observatory">
		<div class="wrap">
			<div class="reveal">
				<p class="kicker"><?php echo esc_html( clear_eo_get( 'site:observatory.kicker' ) ); ?></p>
				<h2 class="h2"><?php echo esc_html( clear_eo_get( 'site:observatory.title' ) ); ?></h2>
				<p class="sub"><?php echo clear_eo_text( 'site:observatory.text' ); ?></p>
			</div>
			<div class="flow reveal">
				<?php
				$clear_eo_steps = array();
				for ( $clear_eo_i = 0; $clear_eo_i < 4; $clear_eo_i++ ) {
					if ( '' !== clear_eo_get( "site:observatory.steps.$clear_eo_i.title" ) ) {
						$clear_eo_steps[] = $clear_eo_i;
					}
				}
				foreach ( $clear_eo_steps as $clear_eo_k => $clear_eo_i ) :
					$clear_eo_chips = clear_eo_get( "site:observatory.steps.$clear_eo_i.chips" );
					?>
					<div class="node<?php echo count( $clear_eo_steps ) - 1 === $clear_eo_k ? ' out' : ''; ?>">
						<?php echo clear_eo_get( "site:observatory.steps.$clear_eo_i.pulse" ) ? '<span class="pulse" aria-hidden="true"></span>' : ''; ?>
						<h3><?php echo esc_html( clear_eo_get( "site:observatory.steps.$clear_eo_i.title" ) ); ?></h3>
						<p><?php echo clear_eo_text( "site:observatory.steps.$clear_eo_i.text" ); ?></p>
						<?php if ( $clear_eo_chips ) : ?>
							<div class="chips">
								<?php foreach ( $clear_eo_chips as $clear_eo_c ) : ?>
									<span class="chip"><?php echo esc_html( $clear_eo_c ); ?></span>
								<?php endforeach; ?>
							</div>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
			<?php if ( clear_eo_get( 'site:observatory.note_title' ) || clear_eo_get( 'site:observatory.note_text' ) ) : ?>
				<div class="aviso reveal">
					<b><?php echo esc_html( clear_eo_get( 'site:observatory.note_title' ) ); ?></b>
					<p><?php echo clear_eo_text( 'site:observatory.note_text' ); ?></p>
				</div>
			<?php endif; ?>
		</div>
	</section>

	<!-- ================= CONSORTIUM ================= -->
	<section class="block" id="consortium">
		<div class="wrap">
			<div class="consortium-head">
				<div class="reveal">
					<p class="kicker"><?php echo esc_html( clear_eo_get( 'partners:kicker' ) ); ?></p>
					<h2 class="h2"><?php echo esc_html( clear_eo_get( 'partners:title' ) ); ?></h2>
					<p class="sub"><?php echo clear_eo_text( 'partners:text' ); ?></p>
				</div>
				<?php if ( clear_eo_get( 'partners:map' ) ) : ?>
					<figure class="partner-map reveal"><img src="<?php echo esc_url( clear_eo_get( 'partners:map' ) ); ?>" alt="<?php echo esc_attr( clear_eo_get( 'partners:map_alt' ) ); ?>" loading="lazy"></figure>
				<?php endif; ?>
			</div>
			<?php echo $clear_eo_partners ? '' : clear_eo_empty_hint( __( 'partners', 'clear-eo' ), 'post-new.php?post_type=clear_partner' ); ?>
			<div class="partners reveal">
				<?php
				foreach ( $clear_eo_partners as $clear_eo_p ) {
					echo clear_eo_partner_card( $clear_eo_p, false );
				}
				?>
			</div>
			<?php if ( $clear_eo_aff ) : ?>
				<p class="sub-h"><?php echo esc_html( clear_eo_get( 'partners:affiliated_title' ) ); ?></p>
				<div class="partners reveal" style="margin-top:14px">
					<?php
					foreach ( $clear_eo_aff as $clear_eo_p ) {
						echo clear_eo_partner_card( $clear_eo_p, true );
					}
					?>
				</div>
			<?php endif; ?>
			<?php
			if ( clear_eo_get( 'partners:women.text' ) ) :
				$clear_eo_wl = clear_eo_get( 'partners:women.link' );
				?>
				<div class="women reveal">
					<div class="ring" style="--p:<?php echo (int) clear_eo_get( 'partners:women.percent' ); ?>"><span><?php echo esc_html( clear_eo_get( 'partners:women.label' ) ); ?></span></div>
					<p><?php echo clear_eo_text( 'partners:women.text' ); ?>
						<?php if ( $clear_eo_wl ) : ?>
							<a href="<?php echo esc_url( $clear_eo_wl ); ?>"<?php echo clear_eo_ext( $clear_eo_wl ); ?>><?php echo esc_html( clear_eo_get( 'partners:women.link_label' ) ? clear_eo_get( 'partners:women.link_label' ) : __( 'Read more', 'clear-eo' ) ); ?></a>
						<?php endif; ?>
					</p>
				</div>
			<?php endif; ?>
			<div id="collaborations">
				<?php
				for ( $clear_eo_i = 0; $clear_eo_i < 2; $clear_eo_i++ ) :
					$clear_eo_name = clear_eo_get( "partners:collaborations.$clear_eo_i.name" );
					if ( '' === $clear_eo_name ) {
						continue;
					}
					$clear_eo_cl = clear_eo_get( "partners:collaborations.$clear_eo_i.link" );
					?>
					<div class="collab reveal">
						<div>
							<span class="tag"><?php esc_html_e( 'Collaborations', 'clear-eo' ); ?></span>
							<h3><?php echo esc_html( $clear_eo_name ); ?></h3>
							<p><?php echo clear_eo_text( "partners:collaborations.$clear_eo_i.text" ); ?></p>
						</div>
						<?php if ( $clear_eo_cl ) : ?>
							<a class="link-btn" href="<?php echo esc_url( $clear_eo_cl ); ?>"<?php echo clear_eo_ext( $clear_eo_cl ); ?>><?php echo esc_html( clear_eo_get( "partners:collaborations.$clear_eo_i.link_label" ) ? clear_eo_get( "partners:collaborations.$clear_eo_i.link_label" ) : $clear_eo_cl ); ?> <?php echo clear_eo_icon( 'out' ); ?></a>
						<?php endif; ?>
					</div>
				<?php endfor; ?>
			</div>
		</div>
	</section>

	<!-- ================= WHAT'S NEW ================= -->
	<section class="block news" id="news">
		<div class="wrap">
			<div class="news-head reveal">
				<div>
					<p class="kicker"><?php echo esc_html( clear_eo_get( 'site:whatsnew.kicker' ) ); ?></p>
					<h2 class="h2"><?php echo esc_html( clear_eo_get( 'site:whatsnew.title' ) ); ?></h2>
				</div>
				<?php get_template_part( 'template-parts/filters' ); ?>
			</div>
			<?php echo $clear_eo_items ? '' : clear_eo_empty_hint( __( 'news, events or webinars', 'clear-eo' ), 'post-new.php' ); ?>
			<div class="news-grid" id="newsGrid">
				<?php
				foreach ( $clear_eo_items as $clear_eo_i => $clear_eo_n ) {
					echo clear_eo_card( $clear_eo_n, 0 === $clear_eo_i );
				}
				?>
			</div>
			<?php
			// The reader window's content for every card that opens one (see main.js)
			foreach ( $clear_eo_items as $clear_eo_n ) {
				if ( clear_eo_has_page( $clear_eo_n ) ) {
					echo '<template data-article="' . esc_attr( $clear_eo_n['slug'] ) . '">' . clear_eo_article( $clear_eo_n ) . '</template>';
				}
			}
			?>
		</div>
	</section>
</main>
<?php
get_footer();
