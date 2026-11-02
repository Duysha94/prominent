<?php
/**
 * Search form.
 *
 * @package AK_Studio
 */

defined( 'ABSPATH' ) || exit;
?>
<form role="search" method="get" class="search" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="sr-only" for="s"><?php esc_html_e( 'Search the site', 'ak-studio' ); ?></label>
	<input type="search" id="s" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Search the site', 'ak-studio' ); ?>">
	<button type="submit" class="btn btn--ghost btn--sm"><?php esc_html_e( 'Search', 'ak-studio' ); ?></button>
</form>
