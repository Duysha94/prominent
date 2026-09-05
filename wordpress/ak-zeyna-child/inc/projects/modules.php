<?php
/**
 * The module renderer.
 *
 * A project assembles itself from the modules it actually has. Nothing is
 * forced through a fixed sequence, and a missing module is ABSENT rather than
 * empty — there is no "coming soon" state anywhere below, because a public
 * portfolio that renders its own gaps is worse than one that renders less.
 *
 * @package ak-zeyna-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render every module a project has.
 *
 * @param int    $post_id Project ID.
 * @param string $mode    Presentation mode.
 * @param array  $website Resolved website module state.
 */
function ak_render_modules( $post_id, $mode, $website ) {
	$modules = json_decode( (string) ak_project_meta( 'ak_modules', '', $post_id ), true );

	// No explicit order: fall back to the mode's natural one, filtered down to
	// the modules that have content.
	if ( ! is_array( $modules ) || ! $modules ) {
		$modules = ak_default_modules( $post_id, $mode );
	}

	foreach ( $modules as $module ) {
		if ( empty( $module['type'] ) ) {
			continue;
		}
		if ( 'website' === $module['type'] ) {
			ak_render_website_module( $post_id, $website );
			continue;
		}
		ak_render_module( $post_id, $module );
	}
}

/**
 * The modules a project has, when the owner has not ordered them by hand.
 *
 * Every entry is conditional on real content, so this returns an empty array
 * for a record that carries only a title and an address — and that record
 * renders as exactly that, without a single placeholder.
 *
 * @param int    $post_id Project ID.
 * @param string $mode    Presentation mode.
 * @return array[]
 */
function ak_default_modules( $post_id, $mode ) {
	$modules = array();

	$context = ak_project_meta( 'ak_context', '', $post_id );
	if ( $context && in_array( $mode, array( 'narrative', 'campaign', 'assembled' ), true ) ) {
		$modules[] = array( 'type' => 'statement', 'title' => __( 'Context', 'ak-zeyna-child' ), 'text' => $context );
	}

	$positioning = ak_project_meta( 'ak_positioning', '', $post_id );
	if ( $positioning ) {
		$modules[] = array( 'type' => 'statement', 'title' => __( 'Positioning', 'ak-zeyna-child' ), 'text' => $positioning );
	}

	if ( ak_project_meta( 'ak_event_date', '', $post_id ) || ak_project_meta( 'ak_event_venue', '', $post_id ) || ak_project_meta( 'ak_event_role', '', $post_id ) ) {
		$modules[] = array( 'type' => 'event' );
	}

	$film = (int) ak_project_meta( 'ak_film_id', 0, $post_id );
	if ( $film ) {
		$modules[] = array( 'type' => 'film', 'id' => $film );
	}

	$gallery = ak_sanitize_id_list( ak_project_meta( 'ak_gallery', '', $post_id ) );
	if ( $gallery ) {
		$modules[] = array( 'type' => 'gallery', 'ids' => array_map( 'absint', explode( ',', $gallery ) ) );
	}

	// The website module is placed last by default: for an owned platform its
	// site is one surface of a larger ecosystem, and leading with it would
	// reduce the platform to its web build.
	$modules[] = array( 'type' => 'website' );

	if ( ak_project_meta( 'ak_credits', '', $post_id ) ) {
		$modules[] = array( 'type' => 'credits' );
	}

	return $modules;
}

/**
 * Render one non-website module.
 *
 * @param int   $post_id Project ID.
 * @param array $module  Module definition.
 */
function ak_render_module( $post_id, $module ) {
	$type = $module['type'];

	if ( 'statement' === $type && ! empty( $module['text'] ) ) {
		?>
		<div class="aks-wrap">
			<div class="aks-rail"><span class="aks-rail__mark"><?php echo esc_html( isset( $module['title'] ) ? $module['title'] : '' ); ?></span></div>
			<div class="aks-col-text aks-section">
				<?php if ( ! empty( $module['title'] ) ) : ?>
					<h2 class="aks-t-display"><?php echo esc_html( $module['title'] ); ?></h2>
				<?php endif; ?>
				<div class="aks-t-body"><?php echo wp_kses_post( wpautop( $module['text'] ) ); ?></div>
			</div>
		</div>
		<?php
		return;
	}

	if ( 'event' === $type ) {
		$rows = array_filter(
			array(
				__( 'Date', 'ak-zeyna-child' )  => ak_project_meta( 'ak_event_date', '', $post_id ),
				__( 'Venue', 'ak-zeyna-child' ) => ak_project_meta( 'ak_event_venue', '', $post_id ),
				__( 'Role', 'ak-zeyna-child' )  => ak_project_meta( 'ak_event_role', '', $post_id ),
			)
		);
		if ( ! $rows ) {
			return;
		}
		?>
		<div class="aks-wrap">
			<div class="aks-rail"><span class="aks-rail__mark"><?php esc_html_e( 'Event', 'ak-zeyna-child' ); ?></span></div>
			<div class="aks-col-full aks-section">
				<div class="aks-spec">
					<?php foreach ( $rows as $key => $value ) : ?>
						<div class="aks-spec__cell">
							<span class="aks-spec__k"><?php echo esc_html( $key ); ?></span>
							<span class="aks-spec__v"><?php echo esc_html( $value ); ?></span>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
		<?php
		return;
	}

	if ( 'film' === $type && ! empty( $module['id'] ) ) {
		$src    = wp_get_attachment_url( (int) $module['id'] );
		$poster = (int) ak_project_meta( 'ak_film_poster', 0, $post_id );
		if ( ! $src ) {
			return;
		}
		?>
		<div class="aks-wrap">
			<div class="aks-rail"><span class="aks-rail__mark"><?php esc_html_e( 'Film', 'ak-zeyna-child' ); ?></span></div>
			<div class="aks-col-full aks-section">
				<video class="aks-film-el" controls preload="metadata"
					<?php echo $poster ? 'poster="' . esc_url( wp_get_attachment_image_url( $poster, 'full' ) ) . '"' : ''; ?>>
					<source src="<?php echo esc_url( $src ); ?>" type="video/mp4">
				</video>
			</div>
		</div>
		<?php
		return;
	}

	if ( in_array( $type, array( 'gallery', 'image_pair', 'image_full' ), true ) ) {
		$ids = ! empty( $module['ids'] ) ? array_map( 'absint', (array) $module['ids'] ) : array();
		if ( ! $ids && ! empty( $module['id'] ) ) {
			$ids = array( absint( $module['id'] ) );
		}
		$ids = array_filter( $ids );
		if ( ! $ids ) {
			return;
		}
		$class = 'gallery' === $type ? 'aks-img-seq' : ( 'image_pair' === $type ? 'aks-img-pair' : 'aks-img-full' );
		?>
		<div class="aks-wrap">
			<div class="aks-rail"><span class="aks-rail__mark"><?php esc_html_e( 'Image', 'ak-zeyna-child' ); ?></span></div>
			<div class="aks-col-full aks-section">
				<div class="<?php echo esc_attr( $class ); ?>">
					<?php foreach ( $ids as $id ) : ?>
						<figure><?php echo wp_get_attachment_image( $id, 'large', false, array( 'loading' => 'lazy' ) ); ?></figure>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
		<?php
		return;
	}

	if ( 'credits' === $type ) {
		$lines = array_filter( preg_split( '/\R/', (string) ak_project_meta( 'ak_credits', '', $post_id ) ) );
		if ( ! $lines ) {
			return;
		}
		?>
		<div class="aks-wrap">
			<div class="aks-rail"><span class="aks-rail__mark"><?php esc_html_e( 'Credits', 'ak-zeyna-child' ); ?></span></div>
			<div class="aks-col-text aks-section">
				<dl class="aks-credits">
					<?php
					foreach ( $lines as $line ) :
						$parts = array_map( 'trim', explode( ':', $line, 2 ) );
						?>
						<dt><?php echo esc_html( $parts[0] ); ?></dt>
						<dd><?php echo esc_html( isset( $parts[1] ) ? $parts[1] : '' ); ?></dd>
					<?php endforeach; ?>
				</dl>
			</div>
		</div>
		<?php
	}
}

/**
 * Render the website module, in whichever state it resolved to.
 *
 * UNAVAILABLE returns immediately and prints nothing. That is the whole
 * contract: capture failure is administrative information, never public
 * design. There is no branch below that emits a frame, a plate, or the words
 * "pending", "failed" or "unavailable" to a visitor.
 *
 * @param int   $post_id Project ID.
 * @param array $module  Resolved state from ak_website_module().
 */
function ak_render_website_module( $post_id, $module ) {
	if ( 'unavailable' === $module['state'] ) {
		return;
	}

	$host = $module['url'] ? wp_parse_url( $module['url'], PHP_URL_HOST ) : '';
	?>
	<div class="aks-wrap">
		<div class="aks-rail"><span class="aks-rail__mark"><?php esc_html_e( 'Module', 'ak-zeyna-child' ); ?></span></div>
		<div class="aks-col-full aks-section">
			<h2 class="aks-t-display"><?php esc_html_e( 'Website', 'ak-zeyna-child' ); ?></h2>
			<?php if ( $module['narrative'] ) : ?>
				<div class="aks-t-body aks-wp__intro"><?php echo wp_kses_post( wpautop( $module['narrative'] ) ); ?></div>
			<?php endif; ?>

			<div class="aks-wp">
				<div class="aks-wp__bar">
					<span class="aks-wp__dom"><?php echo esc_html( $host ); ?></span>
					<?php if ( $module['url'] ) : ?>
						<a class="aks-wp__open" href="<?php echo esc_url( $module['url'] ); ?>" rel="noopener noreferrer" target="_blank">
							<?php esc_html_e( 'Open the live site', 'ak-zeyna-child' ); ?>
						</a>
					<?php endif; ?>
				</div>

				<?php if ( 'live' === $module['state'] ) : ?>
					<div class="aks-wp__frame aks-wp__frame--live">
						<iframe src="<?php echo esc_url( $module['url'] ); ?>" loading="lazy"
							title="<?php echo esc_attr( sprintf( /* translators: %s: host name */ __( 'Live site: %s', 'ak-zeyna-child' ), $host ) ); ?>"
							referrerpolicy="no-referrer" sandbox="allow-scripts allow-same-origin"></iframe>
					</div>

				<?php elseif ( 'manual' === $module['state'] && $module['video'] ) : ?>
					<div class="aks-wp__frame">
						<video class="aks-wp__video" controls preload="metadata"
							<?php echo $module['desktop'] ? 'poster="' . esc_url( wp_get_attachment_image_url( $module['desktop'], 'large' ) ) . '"' : ''; ?>>
							<source src="<?php echo esc_url( wp_get_attachment_url( $module['video'] ) ); ?>" type="video/mp4">
						</video>
					</div>

				<?php elseif ( 'manual' === $module['state'] && $module['desktop'] ) : ?>
					<div class="aks-wp__frame">
						<?php echo wp_get_attachment_image( $module['desktop'], 'full', false, array( 'class' => 'aks-wp__shot', 'loading' => 'lazy' ) ); ?>
					</div>

				<?php elseif ( 'auto' === $module['state'] ) : ?>
					<div class="aks-wp__frame">
						<img class="aks-wp__shot" src="<?php echo esc_url( $module['capture'] ); ?>"
							loading="lazy" decoding="async" referrerpolicy="no-referrer" width="1200" height="900"
							alt="<?php echo esc_attr( sprintf( /* translators: %s: host name */ __( '%s — current front page', 'ak-zeyna-child' ), $host ) ); ?>">
					</div>
				<?php endif; ?>

				<?php if ( 'manual' === $module['state'] && $module['mobile'] ) : ?>
					<div class="aks-wp__mobile">
						<?php echo wp_get_attachment_image( $module['mobile'], 'large', false, array( 'loading' => 'lazy' ) ); ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<?php
}
