<?php
/**
 * Latest from the journal.
 *
 * @package AK_Studio
 */

defined( 'ABSPATH' ) || exit;

$posts = get_posts( array( 'posts_per_page' => 3, 'ignore_sticky_posts' => true ) );
if ( ! $posts ) {
	return;
}
$journal = (int) get_option( 'page_for_posts' );
?>
<section class="journal section" id="journal">
	<div class="wrap">
		<?php ak_studio_section_head( __( 'Journal', 'ak-studio' ), __( 'Notes from the studio *and the runway.*', 'ak-studio' ), $journal ? array( 'url' => get_permalink( $journal ), 'label' => __( 'All entries', 'ak-studio' ) ) : array() ); ?>
		<div class="post-grid">
			<?php foreach ( $posts as $p ) : ?>
				<?php ak_studio_post_card( $p ); ?>
			<?php endforeach; ?>
		</div>
	</div>
</section>
