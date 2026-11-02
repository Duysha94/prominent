<?php
/**
 * Hero: a full-bleed reel of the studio's own runways behind a three-line
 * kinetic headline — the founders' sentence, then what sets the studio apart.
 *
 * @package AK_Studio
 */

defined( 'ABSPATH' ) || exit;

$reel  = ak_studio_reel();
$total = count( $reel );
$event = ak_studio_event();
$front = (int) get_option( 'page_on_front' );
$lede  = $front ? (string) get_post_field( 'post_excerpt', $front ) : '';
$lede  = $lede ?: __( 'An independent London studio for fashion and lifestyle brands. We build the brand, shoot the campaign, produce the show and get it seen.', 'ak-studio' );
$work  = get_post_type_archive_link( 'ak_project' ) ?: home_url( '/work/' );

/**
 * One kinetic line: the phrase repeated so the track can loop seamlessly.
 *
 * @param string $phrase Escaped HTML for one repetition.
 * @param int    $times  Repetitions per half of the track.
 */
$line = static function ( string $phrase, int $times = 4 ): string {
	$one  = '<span class="kinetic__phrase">' . $phrase . '</span><i class="kinetic__slash">/</i>';
	$half = str_repeat( $one, $times );
	return '<span class="kinetic__track">' . $half . $half . '</span>';
};
?>
<section class="hero" id="top" data-hero>
	<div class="hero__media" aria-hidden="true">
		<?php foreach ( $reel as $i => $clip ) : ?>
			<video class="hero__clip<?php echo 0 === $i ? ' is-on' : ''; ?>" muted loop playsinline preload="<?php echo 0 === $i ? 'auto' : 'none'; ?>"
				poster="<?php echo esc_url( $clip['poster'] ); ?>" data-src="<?php echo esc_url( $clip['video'] ); ?>" data-src-sm="<?php echo esc_url( $clip['video_sm'] ); ?>"
				data-label="<?php echo esc_attr( $clip['label'] ); ?>" data-kind="<?php echo esc_attr( $clip['kind'] ); ?>"></video>
		<?php endforeach; ?>
		<div class="hero__scrim"></div>
	</div>

	<svg class="shape shape--gyro" viewBox="0 0 120 120" aria-hidden="true">
		<g fill="none" stroke="currentColor" stroke-width=".8">
			<ellipse cx="60" cy="60" rx="54" ry="18"/>
			<ellipse cx="60" cy="60" rx="54" ry="18" transform="rotate(60 60 60)"/>
			<ellipse cx="60" cy="60" rx="54" ry="18" transform="rotate(120 60 60)"/>
			<circle cx="60" cy="60" r="3" fill="currentColor"/>
		</g>
	</svg>
	<svg class="shape shape--cube" viewBox="0 0 100 100" aria-hidden="true">
		<g fill="none" stroke="currentColor" stroke-width=".8">
			<path d="M50 8 88 30v40L50 92 12 70V30Z"/>
			<path d="M50 8v42m0 0 38-20M50 50 12 30m38 20v42"/>
		</g>
	</svg>

	<div class="hero__inner">
		<div class="hero__meta wrap" data-rise>
			<p class="slate"><span class="rec" aria-hidden="true"></span>REC <span class="slate__sep">/</span> <?php echo esc_html( ak_studio_get( 'city' ) ); ?> — <?php echo esc_html( ak_studio_get( 'discipline' ) ?: __( 'Brand & fashion development studio', 'ak-studio' ) ); ?></p>
			<?php if ( $total ) : ?>
				<div class="hero__reel" aria-hidden="true">
					<span class="hero__num" data-reel-num>01</span>
					<span class="hero__of">/ <?php echo esc_html( str_pad( (string) $total, 2, '0', STR_PAD_LEFT ) ); ?></span>
					<span class="hero__label" data-reel-label><?php echo esc_html( $reel[0]['label'] ); ?></span>
					<span class="hero__kind" data-reel-kind><?php echo esc_html( $reel[0]['kind'] ); ?></span>
					<span class="hero__bar"><span data-reel-bar></span></span>
				</div>
			<?php endif; ?>
		</div>

		<h1 class="kinetic">
			<span class="sr-only"><?php echo esc_html( ak_studio_get( 'name' ) ); ?>. <?php esc_html_e( 'From the initial idea to international presence.', 'ak-studio' ); ?></span>
			<span class="kinetic__row kinetic__row--outline" data-dir="-1" data-speed="0.6" aria-hidden="true"><?php echo $line( esc_html__( 'From the initial idea', 'ak-studio' ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- built from escaped parts. ?></span>
			<span class="kinetic__row" data-dir="1" data-speed="0.42" aria-hidden="true"><?php echo $line( esc_html__( 'To international', 'ak-studio' ) . ' <em>' . esc_html__( 'presence', 'ak-studio' ) . '</em>' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
			<span class="kinetic__row kinetic__row--outline" data-dir="-1" data-speed="0.78" aria-hidden="true"><?php echo $line( esc_html__( 'We produce the show', 'ak-studio' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
		</h1>

		<div class="hero__intro wrap">
			<div class="hero__lede" data-rise>
				<?php if ( ! empty( $event['upcoming'] ) ) : ?>
					<a class="pill" href="#event"><span class="pill__dot" aria-hidden="true"></span><?php echo esc_html( sprintf( '%s · %s · %s', $event['label'], wp_date( 'j M', strtotime( $event['date'] ) ), strtok( $event['venue'], ',' ) ) ); ?></a>
				<?php elseif ( ak_studio_get( 'booking' ) ) : ?>
					<p class="pill"><span class="pill__dot" aria-hidden="true"></span><?php echo esc_html( ak_studio_get( 'booking' ) ); ?></p>
				<?php endif; ?>
				<p class="hero__text"><?php echo esc_html( $lede ); ?></p>
				<div class="hero__ctas">
					<a class="btn btn--accent" href="<?php echo esc_url( $work ); ?>" data-magnetic><?php esc_html_e( 'See the work', 'ak-studio' ); ?><?php echo ak_studio_arrow(); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
					<a class="btn btn--ghost" href="#brief" data-magnetic><?php esc_html_e( 'Start a project', 'ak-studio' ); ?></a>
				</div>
			</div>
			<dl class="stats" data-rise>
				<?php foreach ( ak_studio_stats() as $stat ) : ?>
					<div class="stat">
						<dt><?php echo esc_html( $stat['label'] ); ?></dt>
						<?php if ( is_numeric( $stat['value'] ) ) : ?>
							<dd><span data-count="<?php echo esc_attr( $stat['value'] ); ?>"><?php echo esc_html( $stat['value'] ); ?></span><?php echo esc_html( $stat['suffix'] ); ?></dd>
						<?php else : ?>
							<dd><?php echo str_replace( '+', '<em>+</em>', esc_html( $stat['value'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></dd>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</dl>
		</div>
	</div>

	<a class="hero__scroll" href="#services" data-rise><span><?php esc_html_e( 'Scroll', 'ak-studio' ); ?></span><i aria-hidden="true"></i></a>
</section>
