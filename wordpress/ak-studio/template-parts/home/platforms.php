<?php
/**
 * Platforms: the runways, brand and magazine the founders built — the proof.
 *
 * @package AK_Studio
 */

defined( 'ABSPATH' ) || exit;

$platforms = ak_studio_platforms();
if ( ! $platforms ) {
	return;
}
$big   = array_slice( $platforms, 0, 2 );
$small = array_slice( $platforms, 2 );
?>
<section class="platforms section" id="platforms">
	<div class="wrap">
		<?php ak_studio_section_head( __( 'Our own runways', 'ak-studio' ), __( 'We don’t rent the stage. *We built it.*', 'ak-studio' ) ); ?>
		<div class="platforms__big">
			<?php foreach ( $big as $i => $pl ) : ?>
				<?php
				$project = $pl['project'] ? get_page_by_path( $pl['project'], OBJECT, 'ak_project' ) : null;
				$href    = $project ? get_permalink( $project ) : $pl['url'];
				$video   = $pl['video'] ? get_theme_file_uri( 'assets/media/' . $pl['video'] ) : '';
				?>
				<article class="platform" data-rise>
					<a class="platform__link" href="<?php echo esc_url( $href ); ?>" data-cursor="<?php esc_attr_e( 'Open', 'ak-studio' ); ?>">
						<div class="platform__media" aria-hidden="true">
							<?php if ( $video ) : ?>
								<video muted loop playsinline preload="none" poster="<?php echo esc_url( $video . '.jpg' ); ?>" data-autoplay data-src="<?php echo esc_url( $video . '-sm.mp4' ); ?>"></video>
							<?php endif; ?>
						</div>
						<div class="platform__body">
							<p class="platform__kind"><?php echo esc_html( $pl['kind'] ); ?></p>
							<h3 class="platform__name"><?php echo esc_html( $pl['name'] ); ?></h3>
							<p class="platform__note"><?php echo esc_html( $pl['note'] ); ?></p>
							<?php if ( $pl['facts'] ) : ?>
								<dl class="platform__facts">
									<?php foreach ( $pl['facts'] as $k => $v ) : ?>
										<div><dt><?php echo esc_html( $k ); ?></dt><dd><?php echo esc_html( $v ); ?></dd></div>
									<?php endforeach; ?>
								</dl>
							<?php endif; ?>
							<span class="platform__go"><?php esc_html_e( 'See the platform', 'ak-studio' ); ?><?php echo ak_studio_arrow( 'link-arrow__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
						</div>
					</a>
				</article>
			<?php endforeach; ?>
		</div>
		<div class="platforms__small">
			<?php foreach ( $small as $pl ) : ?>
				<?php
				$project = $pl['project'] ? get_page_by_path( $pl['project'], OBJECT, 'ak_project' ) : null;
				$href    = $project ? get_permalink( $project ) : ( $pl['url'] ?: $pl['social'] );
				$ext     = ! $project;
				?>
				<article class="platform-sm" data-rise>
					<a href="<?php echo esc_url( $href ); ?>"<?php echo $ext ? ' target="_blank" rel="noopener"' : ''; ?>>
						<p class="platform__kind"><?php echo esc_html( $pl['kind'] ); ?></p>
						<h3 class="platform-sm__name"><?php echo esc_html( $pl['name'] ); ?></h3>
						<p><?php echo esc_html( $pl['note'] ); ?></p>
						<p class="platform-sm__founder"><?php echo esc_html( $pl['founder'] ); ?></p>
						<?php echo ak_studio_arrow( 'platform-sm__arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</a>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
