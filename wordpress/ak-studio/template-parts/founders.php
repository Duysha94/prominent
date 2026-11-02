<?php
/**
 * A and K: the two founders, as letter plates with their names and work.
 *
 * @package AK_Studio
 */

defined( 'ABSPATH' ) || exit;

$short = ! empty( $args['short'] );
?>
<div class="founders<?php echo $short ? ' founders--short' : ''; ?>">
	<?php foreach ( ak_studio_founders() as $f ) : ?>
		<article class="founder" data-rise>
			<div class="founder__plate" aria-hidden="true"><span><?php echo esc_html( $f['letter'] ); ?></span></div>
			<div class="founder__body">
				<h3 class="founder__name"><?php echo esc_html( $f['name'] ); ?></h3>
				<p class="founder__role"><?php echo esc_html( $f['role'] ); ?></p>
				<?php if ( $short ) : ?>
					<p><?php echo esc_html( $f['short'] ); ?></p>
				<?php else : ?>
					<?php foreach ( $f['bio'] as $para ) : ?>
						<p><?php echo esc_html( $para ); ?></p>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>
		</article>
	<?php endforeach; ?>
</div>
