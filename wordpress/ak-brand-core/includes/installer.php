<?php
/**
 * Launch content installer: Tools → AK launch content (or `wp ak-brand install`).
 *
 * Runs in small steps so a shared host never times out: media in batches,
 * then pages, projects, journal, menus and site settings. Safe to run again —
 * everything it creates is tagged, nothing that already exists is touched
 * unless you tick "replace", and anything you edited after installing is kept.
 *
 * @package AK_Brand_Core
 */

defined( 'ABSPATH' ) || exit;

const AK_SEED_VERSION    = '1.0.0';
const AK_SEED_MEDIA_STEP = 5;

/**
 * @return string[] Ordered step ids.
 */
function ak_brand_install_steps(): array {
	$steps  = array( 'terms' );
	$chunks = (int) ceil( count( ak_brand_seed_media() ) / AK_SEED_MEDIA_STEP );
	for ( $i = 0; $i < $chunks; $i++ ) {
		$steps[] = 'media:' . $i;
	}
	return array_merge( $steps, array( 'pages', 'projects', 'posts', 'menus', 'settings' ) );
}

/**
 * Pages that already exist at an address the installer wants.
 *
 * @return array<int,WP_Post>
 */
function ak_brand_install_conflicts(): array {
	$out = array();
	foreach ( ak_brand_seed_pages() as $p ) {
		$page = get_page_by_path( ( empty( $p['parent'] ) ? '' : $p['parent'] . '/' ) . $p['slug'] );
		if ( $page && ! get_post_meta( $page->ID, '_ak_seed_key', true ) && ! in_array( $page->post_status, array( 'draft', 'auto-draft' ), true ) ) {
			$out[] = $page;
		}
	}
	return $out;
}

/**
 * Attachment id for a seed file (0 when not imported yet).
 */
function ak_brand_seed_attachment( string $file ): int {
	$map = get_option( 'akbrand_seed_media', array() );
	$id  = is_array( $map ) && isset( $map[ $file ] ) ? (int) $map[ $file ] : 0;
	return $id && get_post( $id ) ? $id : 0;
}

/**
 * Run one step.
 *
 * @param string              $step Step id.
 * @param array<string,mixed> $opts replace (bool).
 * @return array{ok:bool,log:string[]}
 */
function ak_brand_install_run_step( string $step, array $opts = array() ): array {
	$log     = array();
	$replace = ! empty( $opts['replace'] );
	kses_remove_filters(); // Trusted launch copy; WP-CLI runs without a user who could post unfiltered HTML.
	if ( function_exists( 'set_time_limit' ) ) {
		@set_time_limit( 120 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors -- not allowed on some hosts.
	}

	try {
		if ( 'terms' === $step ) {
			ak_brand_register_content_model();
			ak_brand_seed_capabilities();
			foreach ( array( 'news' => 'News', 'notes' => 'Notes' ) as $slug => $name ) {
				if ( ! term_exists( $slug, 'category' ) ) {
					wp_insert_term( $name, 'category', array( 'slug' => $slug ) );
				}
			}
			$log[] = 'Disciplines, project types and journal categories are ready.';
		} elseif ( 0 === strpos( $step, 'media:' ) ) {
			$log = array_merge( $log, ak_brand_install_media( (int) substr( $step, 6 ) ) );
		} elseif ( 'pages' === $step ) {
			$log = ak_brand_install_pages( $replace );
		} elseif ( 'projects' === $step ) {
			$log = ak_brand_install_projects();
		} elseif ( 'posts' === $step ) {
			$log = ak_brand_install_posts();
		} elseif ( 'menus' === $step ) {
			$log = ak_brand_install_menus();
		} elseif ( 'settings' === $step ) {
			$log = ak_brand_install_settings();
		} else {
			return array( 'ok' => false, 'log' => array( 'Unknown step: ' . $step ) );
		}
	} catch ( Throwable $e ) {
		return array( 'ok' => false, 'log' => array( 'Error in ' . $step . ': ' . $e->getMessage() ) );
	}
	return array( 'ok' => true, 'log' => $log );
}

/**
 * @return string[]
 */
function ak_brand_install_media( int $chunk ): array {
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';

	$all   = ak_brand_seed_media();
	$files = array_slice( array_keys( $all ), $chunk * AK_SEED_MEDIA_STEP, AK_SEED_MEDIA_STEP );
	$map   = get_option( 'akbrand_seed_media', array() );
	$map   = is_array( $map ) ? $map : array();
	$log   = array();

	foreach ( $files as $file ) {
		if ( ak_brand_seed_attachment( $file ) ) {
			continue;
		}
		$src = AK_BRAND_CORE_DIR . 'seed/media/' . $file;
		if ( ! is_readable( $src ) ) {
			$log[] = 'Missing file: ' . $file;
			continue;
		}
		$tmp = wp_tempnam( $file );
		copy( $src, $tmp );
		$title = ucfirst( str_replace( array( '-', '.jpg', '.mp4' ), array( ' ', '', '' ), $file ) );
		$id    = media_handle_sideload(
			array(
				'name'     => $file,
				'tmp_name' => $tmp,
			),
			0,
			$title,
			array( 'post_excerpt' => $all[ $file ]['caption'] )
		);
		if ( is_wp_error( $id ) ) {
			wp_delete_file( $tmp );
			$log[] = 'Could not import ' . $file . ': ' . $id->get_error_message();
			continue;
		}
		if ( $all[ $file ]['alt'] ) {
			update_post_meta( $id, '_wp_attachment_image_alt', $all[ $file ]['alt'] );
		}
		update_post_meta( $id, '_ak_seed_key', $file );
		$map[ $file ] = $id;
		update_option( 'akbrand_seed_media', $map, false );
		$log[] = 'Imported ' . $file;
	}
	if ( ! $log ) {
		$log[] = 'Media batch ' . ( $chunk + 1 ) . ' already in the library.';
	}
	return $log;
}

/**
 * Create or update one seeded post. Returns [id, verb].
 *
 * @param array<string,mixed> $data     wp_insert_post data (without ID).
 * @param string              $key      Seed key.
 * @param WP_Post|null        $existing Existing post at the same address.
 * @param bool                $replace  Replace an existing post not created by the installer.
 * @return array{0:int,1:string}
 */
function ak_brand_seed_upsert( array $data, string $key, ?WP_Post $existing, bool $replace = false ): array {
	$src     = md5( (string) ( $data['post_content'] ?? '' ) . '|' . ( $data['post_excerpt'] ?? '' ) . '|' . ( $data['post_title'] ?? '' ) );
	$as_saved = static function ( int $id ): string {
		$p = get_post( $id );
		return $p ? md5( $p->post_content . '|' . $p->post_excerpt . '|' . $p->post_title ) : '';
	};
	$stamp   = static function ( int $id ) use ( $key, $src, $as_saved ) {
		update_post_meta( $id, '_ak_seed_key', $key );
		update_post_meta( $id, '_ak_seed_src', $src );
		update_post_meta( $id, '_ak_seed_hash', $as_saved( $id ) );
	};

	if ( ! $existing ) {
		$id = wp_insert_post( wp_slash( $data ), true );
		if ( is_wp_error( $id ) ) {
			throw new RuntimeException( $id->get_error_message() ); // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
		}
		$stamp( (int) $id );
		return array( (int) $id, 'created' );
	}

	$owned = (bool) get_post_meta( $existing->ID, '_ak_seed_key', true );
	if ( $owned ) {
		if ( (string) get_post_meta( $existing->ID, '_ak_seed_src', true ) === $src ) {
			return array( $existing->ID, 'up to date' );
		}
		$installed = (string) get_post_meta( $existing->ID, '_ak_seed_hash', true );
		if ( $installed && $installed !== $as_saved( $existing->ID ) ) {
			return array( $existing->ID, 'kept (edited by you)' );
		}
	} elseif ( ! $replace && ! in_array( $existing->post_status, array( 'draft', 'auto-draft' ), true ) ) {
		// Published content that predates the installer is never overwritten silently.
		// Unpublished drafts (such as WordPress's sample privacy page) are fair game.
		return array( $existing->ID, 'kept (already existed)' );
	}

	$data['ID'] = $existing->ID;
	$id         = wp_update_post( wp_slash( $data ), true );
	if ( is_wp_error( $id ) ) {
		throw new RuntimeException( $id->get_error_message() ); // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
	}
	if ( ! $owned ) {
		// A page built with Elementor would otherwise keep showing the builder's layout.
		delete_post_meta( $existing->ID, '_elementor_edit_mode' );
		delete_post_meta( $existing->ID, '_wp_page_template' );
	}
	$stamp( $existing->ID );
	return array( $existing->ID, $owned ? 'updated' : 'replaced' );
}

/**
 * @return string[]
 */
function ak_brand_install_pages( bool $replace ): array {
	$log = array();
	foreach ( ak_brand_seed_pages() as $p ) {
		$parent   = empty( $p['parent'] ) ? 0 : (int) ( get_page_by_path( $p['parent'] )->ID ?? 0 );
		$existing = get_page_by_path( ( $parent ? $p['parent'] . '/' : '' ) . $p['slug'] );
		list( $id, $verb ) = ak_brand_seed_upsert(
			array(
				'post_type'      => 'page',
				'post_status'    => 'publish',
				'post_title'     => $p['title'],
				'post_name'      => $p['slug'],
				'post_parent'    => $parent,
				'menu_order'     => (int) ( $p['order'] ?? 0 ),
				'post_excerpt'   => $p['excerpt'] ?? '',
				'post_content'   => $p['content'] ?? '',
				'comment_status' => 'closed',
				'ping_status'    => 'closed',
			),
			'page:' . $p['slug'],
			$existing instanceof WP_Post ? $existing : null,
			$replace
		);
		if ( in_array( $verb, array( 'created', 'updated', 'replaced' ), true ) ) {
			update_post_meta( $id, '_ak_eyebrow', $p['eyebrow'] ?? '' );
			update_post_meta( $id, '_ak_heading', $p['heading'] ?? '' );
			update_post_meta( $id, '_ak_movement', $p['movement'] ?? '' );
		}
		$log[] = 'Page “' . $p['title'] . '”: ' . $verb;
	}
	return $log;
}

/**
 * @return string[]
 */
function ak_brand_install_projects(): array {
	ak_brand_register_content_model();
	$log = array();
	foreach ( ak_brand_seed_projects() as $p ) {
		$found    = get_posts( array( 'post_type' => 'ak_project', 'name' => $p['slug'], 'post_status' => 'any', 'numberposts' => 1 ) );
		$existing = $found ? $found[0] : null;
		list( $id, $verb ) = ak_brand_seed_upsert(
			array(
				'post_type'    => 'ak_project',
				'post_status'  => 'publish',
				'post_title'   => $p['title'],
				'post_name'    => $p['slug'],
				'menu_order'   => (int) $p['order'],
				'post_excerpt' => $p['excerpt'],
				'post_content' => $p['content'],
			),
			'project:' . $p['slug'],
			$existing
		);
		if ( in_array( $verb, array( 'created', 'updated' ), true ) ) {
			foreach ( $p['meta'] as $k => $v ) {
				update_post_meta( $id, $k, $v );
			}
			update_post_meta( $id, 'ak_featured', ! empty( $p['featured'] ) );
			update_post_meta( $id, 'ak_short_title', $p['short'] );
			$gallery = array_filter( array_map( 'ak_brand_seed_attachment', $p['gallery'] ?? array() ) );
			update_post_meta( $id, 'ak_gallery', implode( ',', $gallery ) );
			update_post_meta( $id, 'ak_film', ! empty( $p['film'] ) ? ak_brand_seed_attachment( $p['film'] ) : 0 );
			update_post_meta( $id, 'ak_loop', ! empty( $p['loop'] ) ? ak_brand_seed_attachment( $p['loop'] ) : 0 );
			$cover = ak_brand_seed_attachment( $p['cover'] );
			if ( $cover ) {
				set_post_thumbnail( $id, $cover );
			}
			if ( ! empty( $p['film'] ) && ! empty( $p['film_poster'] ) ) {
				$film   = ak_brand_seed_attachment( $p['film'] );
				$poster = ak_brand_seed_attachment( $p['film_poster'] );
				if ( $film && $poster ) {
					set_post_thumbnail( $film, $poster ); // Core uses a video's featured image as its poster.
				}
			}
			wp_set_object_terms( $id, $p['disciplines'], 'ak_capability' );
			wp_set_object_terms( $id, array( $p['relationship'] ), 'ak_relationship' );
			wp_set_object_terms( $id, array( $p['type'] ), 'ak_project_type' );
		}
		$log[] = 'Project “' . $p['short'] . '”: ' . $verb;
	}
	return $log;
}

/**
 * @return string[]
 */
function ak_brand_install_posts(): array {
	$log = array();
	foreach ( ak_brand_seed_posts() as $p ) {
		$found    = get_posts( array( 'post_type' => 'post', 'name' => $p['slug'], 'post_status' => 'any', 'numberposts' => 1 ) );
		$existing = $found ? $found[0] : null;
		$cat      = get_term_by( 'slug', sanitize_title( $p['category'] ), 'category' );
		list( $id, $verb ) = ak_brand_seed_upsert(
			array(
				'post_type'      => 'post',
				'post_status'    => 'publish',
				'post_title'     => $p['title'],
				'post_name'      => $p['slug'],
				'post_date'      => $p['date'],
				'post_excerpt'   => $p['excerpt'],
				'post_content'   => $p['content'],
				'post_category'  => $cat ? array( (int) $cat->term_id ) : array(),
				'comment_status' => 'closed',
				'ping_status'    => 'closed',
			),
			'post:' . $p['slug'],
			$existing
		);
		$image = ak_brand_seed_attachment( $p['image'] );
		if ( $image && ( 'created' === $verb || 'updated' === $verb || ! has_post_thumbnail( $id ) ) ) {
			set_post_thumbnail( $id, $image );
		}
		$log[] = 'Journal “' . $p['title'] . '”: ' . $verb;
	}
	return $log;
}

/**
 * @return string[]
 */
function ak_brand_install_menus(): array {
	$page = static fn( string $path ) => get_page_by_path( $path );
	$menus = array(
		'AK Primary'  => array(
			array( 'Work', home_url( '/work/' ) ),
			array( 'Services', $page( 'services' ) ),
			array( 'Show in London', $page( 'show-in-london' ) ),
			array( 'Studio', $page( 'about' ) ),
			array( 'Journal', $page( 'journal' ) ),
			array( 'Contact', $page( 'contact' ) ),
		),
		'AK Services' => array_map( static fn( $m ) => array( $m['name'], $page( 'services/' . $m['page'] ) ), ak_brand_movements() ),
		'AK Footer'   => array(
			array( 'Privacy Policy', $page( 'privacy-policy' ) ),
			array( 'Cookie Policy', $page( 'cookie-policy' ) ),
		),
	);
	$log = array();
	$ids = array();
	foreach ( $menus as $name => $items ) {
		$menu = wp_get_nav_menu_object( $name );
		if ( $menu ) {
			$ids[ $name ] = (int) $menu->term_id;
			$log[]        = 'Menu “' . $name . '”: kept (already exists)';
			continue;
		}
		$menu_id = wp_create_nav_menu( $name );
		if ( is_wp_error( $menu_id ) ) {
			$log[] = 'Menu “' . $name . '”: ' . $menu_id->get_error_message();
			continue;
		}
		foreach ( $items as $i => $item ) {
			$target = $item[1];
			$args   = array( 'menu-item-title' => $item[0], 'menu-item-status' => 'publish', 'menu-item-position' => $i + 1 );
			if ( $target instanceof WP_Post ) {
				$args += array( 'menu-item-object-id' => $target->ID, 'menu-item-object' => 'page', 'menu-item-type' => 'post_type' );
			} elseif ( is_string( $target ) ) {
				$args += array( 'menu-item-url' => $target, 'menu-item-type' => 'custom' );
			} else {
				continue;
			}
			wp_update_nav_menu_item( $menu_id, 0, $args );
		}
		$ids[ $name ] = (int) $menu_id;
		$log[]        = 'Menu “' . $name . '”: created';
	}
	$locations = get_theme_mod( 'nav_menu_locations', array() );
	$locations = is_array( $locations ) ? $locations : array();
	foreach ( array( 'primary' => 'AK Primary', 'services' => 'AK Services', 'footer' => 'AK Footer' ) as $loc => $name ) {
		if ( empty( $locations[ $loc ] ) && ! empty( $ids[ $name ] ) ) {
			$locations[ $loc ] = $ids[ $name ];
		}
	}
	set_theme_mod( 'nav_menu_locations', $locations );
	return $log;
}

/**
 * @return string[]
 */
function ak_brand_install_settings(): array {
	$log     = array();
	$home    = get_page_by_path( 'home' );
	$journal = get_page_by_path( 'journal' );
	$privacy = get_page_by_path( 'privacy-policy' );
	if ( $home && $journal ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $home->ID );
		update_option( 'page_for_posts', $journal->ID );
		$log[] = 'Front page set to “Home”, journal set as the posts page.';
	}
	if ( $privacy ) {
		update_option( 'wp_page_for_privacy_policy', $privacy->ID );
		$log[] = 'Privacy policy page assigned.';
	}
	if ( '' === (string) get_option( 'permalink_structure' ) ) {
		update_option( 'permalink_structure', '/%postname%/' );
		$log[] = 'Pretty permalinks switched on (/%postname%/).';
	}
	$name = (string) get_option( 'blogname' );
	if ( '' === $name || in_array( $name, array( 'My WordPress Website', 'WordPress', 'My Blog' ), true ) ) {
		update_option( 'blogname', ak_brand_studio( 'name' ) );
	}
	$tag = (string) get_option( 'blogdescription' );
	if ( '' === $tag || 'Just another WordPress site' === $tag ) {
		update_option( 'blogdescription', ak_brand_studio( 'promise' ) );
	}
	if ( '' === (string) get_option( 'timezone_string' ) && 0.0 === (float) get_option( 'gmt_offset' ) ) {
		update_option( 'timezone_string', 'Europe/London' );
	}
	foreach ( array( array( 'hello-world', 'post' ), array( 'sample-page', 'page' ) ) as $sample ) {
		$found = get_posts( array( 'name' => $sample[0], 'post_type' => $sample[1], 'post_status' => 'publish', 'numberposts' => 1 ) );
		if ( $found && $found[0]->post_modified_gmt === $found[0]->post_date_gmt && false !== strpos( $found[0]->post_content, 'WordPress' ) ) {
			wp_trash_post( $found[0]->ID );
			$log[] = 'WordPress sample “' . $found[0]->post_title . '” moved to the bin.';
		}
	}
	if ( ! get_option( 'akbrand_installed' ) ) {
		update_option( 'default_comment_status', 'closed' );
		update_option( 'default_ping_status', 'closed' );
	}
	update_option( 'akbrand_installed', array( 'version' => AK_SEED_VERSION, 'time' => time() ), false );
	flush_rewrite_rules();
	$log[] = 'Done. The site is ready.';
	return $log;
}

/* ── Tools → AK launch content ─────────────────────────────────────── */

add_action(
	'admin_menu',
	static function () {
		add_management_page( __( 'AK launch content', 'ak-brand-core' ), __( 'AK launch content', 'ak-brand-core' ), 'manage_options', 'ak-install', 'ak_brand_render_installer' );
	}
);

add_action(
	'wp_ajax_ak_install_step',
	static function () {
		check_ajax_referer( 'ak_install', 'nonce' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'log' => array( 'Not allowed.' ) ), 403 );
		}
		$step = isset( $_POST['step'] ) ? sanitize_text_field( wp_unslash( $_POST['step'] ) ) : '';
		$res  = ak_brand_install_run_step( $step, array( 'replace' => ! empty( $_POST['replace'] ) ) );
		wp_send_json( $res );
	}
);

/* Without JavaScript: run every step in one request. */
add_action(
	'admin_post_ak_install_all',
	static function () {
		check_admin_referer( 'ak_install', 'nonce' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Not allowed.', 'ak-brand-core' ) );
		}
		$log = array();
		foreach ( ak_brand_install_steps() as $step ) {
			$res = ak_brand_install_run_step( $step, array( 'replace' => ! empty( $_POST['replace'] ) ) );
			$log = array_merge( $log, $res['log'] );
		}
		set_transient( 'ak_install_log', $log, 10 * MINUTE_IN_SECONDS );
		wp_safe_redirect( admin_url( 'tools.php?page=ak-install&done=1' ) );
		exit;
	}
);

function ak_brand_render_installer(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$installed = get_option( 'akbrand_installed' );
	$conflicts = ak_brand_install_conflicts();
	$theme_ok  = 'ak-studio' === get_stylesheet();
	$log       = isset( $_GET['done'] ) ? get_transient( 'ak_install_log' ) : false; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'AK launch content', 'ak-brand-core' ); ?></h1>
		<p style="max-width:720px"><?php esc_html_e( 'Installs the complete launch site: every page with its copy, the six service pages, ten projects from London Fashion Day and Odessa Fashion Day with their photographs and films, six journal entries, menus, the front page and the privacy settings.', 'ak-brand-core' ); ?></p>
		<?php if ( ! $theme_ok ) : ?>
			<div class="notice notice-warning inline"><p><?php esc_html_e( 'Activate the AK Studio theme first so the menus are assigned to it.', 'ak-brand-core' ); ?></p></div>
		<?php endif; ?>
		<?php if ( $installed ) : ?>
			<p><strong><?php echo esc_html( sprintf( /* translators: 1: version, 2: date */ __( 'Installed: version %1$s on %2$s. Running it again adds anything missing and refreshes items you have not edited.', 'ak-brand-core' ), $installed['version'], wp_date( 'j F Y, H:i', (int) $installed['time'] ) ) ); ?></strong></p>
		<?php endif; ?>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" id="ak-install-form">
			<input type="hidden" name="action" value="ak_install_all">
			<?php wp_nonce_field( 'ak_install', 'nonce' ); ?>
			<?php if ( $conflicts ) : ?>
				<p><?php esc_html_e( 'These pages already exist at the same address and will be kept as they are unless you choose to replace them:', 'ak-brand-core' ); ?>
					<?php echo esc_html( implode( ', ', array_map( static fn( $p ) => $p->post_title . ' (/' . get_page_uri( $p ) . '/)', $conflicts ) ) ); ?></p>
				<p><label><input type="checkbox" name="replace" value="1"> <?php esc_html_e( 'Replace them with the launch copy (their old content stays in revisions; Elementor layouts are switched off for these pages).', 'ak-brand-core' ); ?></label></p>
			<?php endif; ?>
			<p><button type="submit" class="button button-primary button-hero"><?php echo esc_html( $installed ? __( 'Run again', 'ak-brand-core' ) : __( 'Install the launch content', 'ak-brand-core' ) ); ?></button></p>
		</form>
		<div id="ak-install-progress" hidden>
			<progress max="100" value="0" style="width:min(720px,100%);height:14px"></progress>
			<p id="ak-install-status"></p>
		</div>
		<pre id="ak-install-log" style="max-width:900px;max-height:420px;overflow:auto;background:#fff;border:1px solid #dcdcde;padding:12px<?php echo $log ? '' : ';display:none'; ?>"><?php echo $log ? esc_html( implode( "\n", (array) $log ) ) : ''; ?></pre>
		<p id="ak-install-next" <?php echo $log ? '' : 'hidden'; ?>><a class="button" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'View the site', 'ak-brand-core' ); ?></a>
			<a class="button" href="<?php echo esc_url( admin_url( 'options-general.php?page=ak-studio' ) ); ?>"><?php esc_html_e( 'Check the studio settings', 'ak-brand-core' ); ?></a></p>
	</div>
	<script>
	(() => {
		const form = document.getElementById('ak-install-form');
		const steps = <?php echo wp_json_encode( ak_brand_install_steps() ); ?>;
		const box = document.getElementById('ak-install-progress');
		const bar = box.querySelector('progress');
		const status = document.getElementById('ak-install-status');
		const out = document.getElementById('ak-install-log');
		const next = document.getElementById('ak-install-next');
		form.addEventListener('submit', async (e) => {
			e.preventDefault();
			const btn = form.querySelector('button');
			btn.disabled = true; box.hidden = false; out.style.display = 'block'; out.textContent = '';
			const replace = form.querySelector('[name="replace"]');
			for (let i = 0; i < steps.length; i++) {
				status.textContent = `Step ${i + 1} of ${steps.length}: ${steps[i]}`;
				const body = new FormData();
				body.append('action', 'ak_install_step');
				body.append('nonce', form.querySelector('[name="nonce"]').value);
				body.append('step', steps[i]);
				if (replace && replace.checked) body.append('replace', '1');
				let res;
				try {
					res = await (await fetch(ajaxurl, { method: 'POST', body, credentials: 'same-origin' })).json();
				} catch (err) {
					res = { ok: false, log: [`Step ${steps[i]} failed: ${err.message}. Run again to continue — finished steps are skipped.`] };
				}
				out.textContent += (res.log || []).join('\n') + '\n';
				out.scrollTop = out.scrollHeight;
				bar.value = Math.round(((i + 1) / steps.length) * 100);
				if (!res.ok) { status.textContent = 'Stopped. Fix the problem above and run again.'; btn.disabled = false; return; }
			}
			status.textContent = 'Finished.';
			next.hidden = false;
			btn.disabled = false;
		});
	})();
	</script>
	<?php
}

/* Nudge after activation until the content is installed. */
add_action(
	'admin_notices',
	static function () {
		if ( get_option( 'akbrand_installed' ) || ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$screen = get_current_screen();
		if ( $screen && 'tools_page_ak-install' === $screen->id ) {
			return;
		}
		echo '<div class="notice notice-info"><p><strong>' . esc_html__( 'AK Brand Core is active.', 'ak-brand-core' ) . '</strong> ' .
			'<a href="' . esc_url( admin_url( 'tools.php?page=ak-install' ) ) . '">' . esc_html__( 'Install the launch content →', 'ak-brand-core' ) . '</a></p></div>';
	}
);

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	/**
	 * Install the AK launch content.
	 *
	 * [--replace]
	 * : Replace pages that already exist at the same address.
	 */
	WP_CLI::add_command(
		'ak-brand install',
		static function ( $args, $assoc ) {
			foreach ( ak_brand_install_steps() as $step ) {
				$res = ak_brand_install_run_step( $step, array( 'replace' => ! empty( $assoc['replace'] ) ) );
				foreach ( $res['log'] as $line ) {
					WP_CLI::log( $line );
				}
				if ( ! $res['ok'] ) {
					WP_CLI::error( 'Stopped at ' . $step );
				}
			}
			WP_CLI::success( 'Launch content installed.' );
		}
	);
}
