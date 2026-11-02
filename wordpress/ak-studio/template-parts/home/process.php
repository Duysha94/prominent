<?php
/**
 * How a project runs: four steps from brief to presence.
 *
 * @package AK_Studio
 */

defined( 'ABSPATH' ) || exit;

$steps = array(
	array( __( 'Brief', 'ak-studio' ), __( 'Tell us about the brand, the date you are working towards and what you need. We read every brief ourselves.', 'ak-studio' ) ),
	array( __( 'Proposal', 'ak-studio' ), __( 'A written proposal: scope, team, timeline and the fee — agreed before any work begins.', 'ak-studio' ) ),
	array( __( 'Production', 'ak-studio' ), __( 'Strategy, design, shoots, shows and builds in one plan, with one team accountable for all of it.', 'ak-studio' ) ),
	array( __( 'Presence', 'ak-studio' ), __( 'Launch, press and promotion — then the numbers, so you know what worked and what to do next season.', 'ak-studio' ) ),
);
?>
<section class="process section" id="process">
	<div class="wrap">
		<?php ak_studio_section_head( __( 'How we work', 'ak-studio' ), __( 'From brief *to presence.*', 'ak-studio' ) ); ?>
		<ol class="process__list">
			<?php foreach ( $steps as $i => $step ) : ?>
				<li class="process__step" data-rise>
					<span class="process__num"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
					<h3 class="process__title"><?php echo esc_html( $step[0] ); ?></h3>
					<p><?php echo esc_html( $step[1] ); ?></p>
				</li>
			<?php endforeach; ?>
		</ol>
	</div>
</section>
