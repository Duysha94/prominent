<?php
/**
 * Site footer.
 *
 * @package AK_Studio
 */

defined( 'ABSPATH' ) || exit;
?>
<footer class="site-foot">
	<div class="wrap site-foot__row">
		<p>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( ak_studio_get( 'name' ) ); ?></p>
		<p><?php echo esc_html( ak_studio_get( 'city' ) ); ?></p>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
