<?php
/**
 * Not found.
 *
 * @package AK_Studio
 */

defined( 'ABSPATH' ) || exit;

get_header();
$work = get_page_by_path( 'work' );
?>
<main id="main" class="lost">
	<div class="wrap lost__inner">
		<p class="eyebrow"><?php esc_html_e( 'Error 404', 'ak-studio' ); ?></p>
		<h1 class="display lost__title"><?php echo ak_studio_heading( __( 'Lost *backstage.*', 'ak-studio' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></h1>
		<p class="lede"><?php esc_html_e( 'This page has moved or never existed. The show goes on here:', 'ak-studio' ); ?></p>
		<div class="lost__links">
			<a class="btn btn--accent" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'ak-studio' ); ?></a>
			<?php if ( $work ) : ?><a class="btn btn--ghost" href="<?php echo esc_url( get_permalink( $work ) ); ?>"><?php esc_html_e( 'Work', 'ak-studio' ); ?></a><?php endif; ?>
			<a class="btn btn--ghost" href="<?php echo esc_url( ak_studio_contact_url() ); ?>"><?php esc_html_e( 'Start a project', 'ak-studio' ); ?></a>
		</div>
		<?php get_search_form(); ?>
	</div>
	<div class="lost__word" aria-hidden="true">404</div>
</main>
<?php
get_footer();
