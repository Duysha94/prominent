<?php
/**
 * What the deployment tells the owner.
 *
 * A managed build changes the site by itself, so it owes the owner a plain
 * account of what it did — including, and especially, when it deleted
 * something. Silence would be the wrong default for a system with delete
 * rights.
 *
 * @package ak-zeyna-child
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'admin_notices',
	function () {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$report = get_transient( 'ak_deploy_report' );
		if ( ! is_array( $report ) ) {
			return;
		}
		delete_transient( 'ak_deploy_report' );

		$failed = ! empty( $report['errors'] );
		$counts = array();
		foreach ( array( 'created', 'updated', 'deleted', 'healed' ) as $k ) {
			if ( ! empty( $report[ $k ] ) ) {
				$counts[] = count( $report[ $k ] ) . ' ' . $k;
			}
		}
		?>
		<div class="notice notice-<?php echo $failed ? 'error' : 'success'; ?>">
			<p>
				<strong><?php esc_html_e( 'AK Brand Studio', 'ak-zeyna-child' ); ?></strong> —
				<?php
				if ( $failed ) {
					printf(
						/* translators: %s: version number. */
						esc_html__( 'deployment to %s FAILED and was not recorded. It will retry on the next admin page load.', 'ak-zeyna-child' ),
						esc_html( $report['to'] )
					);
				} else {
					printf(
						/* translators: 1: previous version, 2: new version, 3: summary counts. */
						esc_html__( 'deployed %1$s → %2$s. %3$s.', 'ak-zeyna-child' ),
						esc_html( $report['from'] ? $report['from'] : 'new install' ),
						esc_html( $report['to'] ),
						esc_html( $counts ? implode( ', ', $counts ) : __( 'Nothing to change', 'ak-zeyna-child' ) )
					);
				}
				?>
			</p>
			<?php if ( ! empty( $report['migrations'] ) ) : ?>
				<p><em><?php esc_html_e( 'Migrations:', 'ak-zeyna-child' ); ?></em> <?php echo esc_html( implode( ' · ', $report['migrations'] ) ); ?></p>
			<?php endif; ?>
			<?php if ( ! empty( $report['deleted'] ) ) : ?>
				<details>
					<summary><?php echo esc_html( sprintf( /* translators: %d: number of deleted items. */ __( '%d items removed', 'ak-zeyna-child' ), count( $report['deleted'] ) ) ); ?></summary>
					<ul style="margin:.5rem 0 0 1.5rem;list-style:disc">
						<?php foreach ( array_slice( $report['deleted'], 0, 60 ) as $line ) : ?>
							<li><code><?php echo esc_html( $line ); ?></code></li>
						<?php endforeach; ?>
					</ul>
				</details>
			<?php endif; ?>
			<?php if ( $failed ) : ?>
				<ul style="margin:.5rem 0 0 1.5rem;list-style:disc">
					<?php foreach ( $report['errors'] as $err ) : ?>
						<li><code><?php echo esc_html( $err ); ?></code></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
		<?php
	}
);
