<?php
/**
 * What's new filter buttons (main.js shows or hides the cards by their data-t).
 *
 * @package clear-eo
 */

?>
<div class="filters" role="group" aria-label="<?php esc_attr_e( 'Filter news', 'clear-eo' ); ?>">
	<button class="filter" data-f="all" aria-pressed="true"><?php esc_html_e( 'All', 'clear-eo' ); ?></button>
	<button class="filter" data-f="webinar" aria-pressed="false"><?php esc_html_e( 'Webinars', 'clear-eo' ); ?></button>
	<button class="filter" data-f="event" aria-pressed="false"><?php esc_html_e( 'Events', 'clear-eo' ); ?></button>
	<button class="filter" data-f="news" aria-pressed="false"><?php esc_html_e( 'Newsletters', 'clear-eo' ); ?></button>
</div>
