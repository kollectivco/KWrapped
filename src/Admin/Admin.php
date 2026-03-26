<?php
declare(strict_types=1);

namespace KontentainmentWrapped\Admin;

use KontentainmentWrapped\Core\GitHubUpdater;
use WP_Query;

final class Admin
{
	public function register(): void
	{
		add_action('admin_menu', array($this, 'menu'));
		add_action('admin_head', array($this, 'admin_head'));
		add_action('admin_action_kt_wrapped_check_updates', array($this, 'handle_manual_update_check'));
		add_action('admin_notices', array($this, 'render_update_notice'));
		add_action('admin_notices', array($this, 'render_location_notice'));
	}

	public function menu(): void
	{
		$parent_slug = 'kt-wrapped-dashboard';

		add_menu_page(
			__('Kontentainment Wrapped', 'kontentainment-wrapped'),
			__('Kontentainment Wrapped', 'kontentainment-wrapped'),
			'edit_posts',
			$parent_slug,
			array($this, 'render_overview'),
			'dashicons-format-gallery',
			25
		);

		add_submenu_page(
			$parent_slug,
			__('All Editions', 'kontentainment-wrapped'),
			__('All Editions', 'kontentainment-wrapped'),
			'edit_posts',
			'edit.php?post_type=kt_wrapped'
		);

		add_submenu_page(
			$parent_slug,
			__('Add New Edition', 'kontentainment-wrapped'),
			__('Add New', 'kontentainment-wrapped'),
			'edit_posts',
			'post-new.php?post_type=kt_wrapped'
		);
	}

	public function render_overview(): void
	{
		if (! current_user_can('edit_posts')) {
			wp_die(esc_html__('You are not allowed to access this page.', 'kontentainment-wrapped'));
		}

		$counts          = wp_count_posts('kt_wrapped');
		$total_editions  = isset($counts->publish, $counts->draft, $counts->archived) ? (int) $counts->publish + (int) $counts->draft + (int) $counts->archived : 0;
		$published_count = isset($counts->publish) ? (int) $counts->publish : 0;
		$draft_count     = isset($counts->draft) ? (int) $counts->draft : 0;
		$latest_editions = new WP_Query(
			array(
				'post_type'      => 'kt_wrapped',
				'post_status'    => array('publish', 'draft', 'archived'),
				'posts_per_page' => 3,
				'orderby'        => 'modified',
				'order'          => 'DESC',
				'no_found_rows'  => true,
			)
		);
		$updater = new GitHubUpdater();
		$status  = $updater->get_status_snapshot();
		$check_updates_url = wp_nonce_url(
			admin_url('admin.php?action=kt_wrapped_check_updates'),
			'kt_wrapped_manual_update_check'
		);
		?>
		<div class="wrap kt-wrapped-overview">
			<div class="kt-wrapped-overview__hero">
				<div>
					<p class="kt-wrapped-overview__eyebrow"><?php esc_html_e('Story-first editorial builder', 'kontentainment-wrapped'); ?></p>
					<h1><?php esc_html_e('Kontentainment Wrapped', 'kontentainment-wrapped'); ?></h1>
					<p class="kt-wrapped-overview__intro">
						<?php esc_html_e('Create curated Wrapped editions with immersive story slides, polished sharing moments, and a mobile-first viewer experience.', 'kontentainment-wrapped'); ?>
					</p>
				</div>
				<div class="kt-wrapped-overview__actions">
					<a class="button button-primary button-hero" href="<?php echo esc_url(admin_url('post-new.php?post_type=kt_wrapped')); ?>">
						<?php esc_html_e('Add New Edition', 'kontentainment-wrapped'); ?>
					</a>
					<a class="button" href="<?php echo esc_url(admin_url('edit.php?post_type=kt_wrapped')); ?>">
						<?php esc_html_e('All Editions', 'kontentainment-wrapped'); ?>
					</a>
				</div>
			</div>

			<div class="kt-wrapped-overview__stats">
				<div class="kt-wrapped-overview-card">
					<span class="kt-wrapped-overview-card__label"><?php esc_html_e('Total Editions', 'kontentainment-wrapped'); ?></span>
					<strong class="kt-wrapped-overview-card__value"><?php echo esc_html((string) $total_editions); ?></strong>
				</div>
				<div class="kt-wrapped-overview-card">
					<span class="kt-wrapped-overview-card__label"><?php esc_html_e('Published', 'kontentainment-wrapped'); ?></span>
					<strong class="kt-wrapped-overview-card__value"><?php echo esc_html((string) $published_count); ?></strong>
				</div>
				<div class="kt-wrapped-overview-card">
					<span class="kt-wrapped-overview-card__label"><?php esc_html_e('Drafts', 'kontentainment-wrapped'); ?></span>
					<strong class="kt-wrapped-overview-card__value"><?php echo esc_html((string) $draft_count); ?></strong>
				</div>
			</div>

			<div class="kt-wrapped-overview__grid">
				<section class="kt-wrapped-overview-panel">
					<div class="kt-wrapped-overview-panel__header">
						<h2><?php esc_html_e('Getting Started', 'kontentainment-wrapped'); ?></h2>
						<p><?php esc_html_e('A strong Wrapped story lands best when it moves quickly and escalates visually.', 'kontentainment-wrapped'); ?></p>
					</div>
					<ol class="kt-wrapped-overview-list">
						<li><?php esc_html_e('Start with a bold cover slide that sets the tone immediately.', 'kontentainment-wrapped'); ?></li>
						<li><?php esc_html_e('Follow with a high-impact number or ranking to hook the viewer early.', 'kontentainment-wrapped'); ?></li>
						<li><?php esc_html_e('Add variety in the middle with spotlight, quote, or mosaic slides.', 'kontentainment-wrapped'); ?></li>
						<li><?php esc_html_e('End with a strong finale that rewards replay and sharing.', 'kontentainment-wrapped'); ?></li>
					</ol>
				</section>

				<section class="kt-wrapped-overview-panel">
					<div class="kt-wrapped-overview-panel__header">
						<h2><?php esc_html_e('Recent Editions', 'kontentainment-wrapped'); ?></h2>
						<p><?php esc_html_e('Jump back into your latest work or start a new story from scratch.', 'kontentainment-wrapped'); ?></p>
					</div>
					<?php if ($latest_editions->have_posts()) : ?>
						<ul class="kt-wrapped-overview-editions">
							<?php
							while ($latest_editions->have_posts()) :
								$latest_editions->the_post();
								?>
								<li class="kt-wrapped-overview-editions__item">
									<div>
										<strong><?php the_title(); ?></strong>
										<span><?php echo esc_html(ucfirst((string) get_post_status(get_the_ID()))); ?></span>
									</div>
									<a href="<?php echo esc_url(get_edit_post_link(get_the_ID())); ?>">
										<?php esc_html_e('Edit', 'kontentainment-wrapped'); ?>
									</a>
								</li>
							<?php endwhile; ?>
						</ul>
						<?php wp_reset_postdata(); ?>
					<?php else : ?>
						<div class="kt-wrapped-overview-empty">
							<h3><?php esc_html_e('No Wrapped editions yet', 'kontentainment-wrapped'); ?></h3>
							<p><?php esc_html_e('Create your first edition to start building a story-driven Wrapped experience.', 'kontentainment-wrapped'); ?></p>
							<a class="button button-primary" href="<?php echo esc_url(admin_url('post-new.php?post_type=kt_wrapped')); ?>">
								<?php esc_html_e('Create Your First Edition', 'kontentainment-wrapped'); ?>
							</a>
						</div>
					<?php endif; ?>
				</section>
			</div>

			<section class="kt-wrapped-overview-panel kt-wrapped-overview-panel--footer">
				<div class="kt-wrapped-overview-panel__header">
					<h2><?php esc_html_e('Release Status', 'kontentainment-wrapped'); ?></h2>
					<p><?php esc_html_e('GitHub Releases remain the source of truth for WordPress updates.', 'kontentainment-wrapped'); ?></p>
				</div>
				<div class="kt-wrapped-overview-update">
					<div class="kt-wrapped-overview-update__meta">
						<div class="kt-wrapped-overview-update__item">
							<span><?php esc_html_e('Installed', 'kontentainment-wrapped'); ?></span>
							<strong><?php echo esc_html($status['current_version']); ?></strong>
						</div>
						<div class="kt-wrapped-overview-update__item">
							<span><?php esc_html_e('Latest GitHub Release', 'kontentainment-wrapped'); ?></span>
							<strong><?php echo esc_html($status['latest_version'] ?: __('Unavailable', 'kontentainment-wrapped')); ?></strong>
						</div>
						<div class="kt-wrapped-overview-update__item">
							<span><?php esc_html_e('Update Status', 'kontentainment-wrapped'); ?></span>
							<strong>
								<?php
								if ($status['has_update']) {
									esc_html_e('Update available', 'kontentainment-wrapped');
								} elseif ('error' === $status['status']) {
									esc_html_e('Check failed', 'kontentainment-wrapped');
								} else {
									esc_html_e('Up to date', 'kontentainment-wrapped');
								}
								?>
							</strong>
						</div>
						<div class="kt-wrapped-overview-update__item">
							<span><?php esc_html_e('Auto-updates', 'kontentainment-wrapped'); ?></span>
							<strong><?php echo esc_html($status['auto_updates'] ? __('Available in Plugins screen', 'kontentainment-wrapped') : __('Unavailable', 'kontentainment-wrapped')); ?></strong>
						</div>
					</div>
					<?php if (! empty($status['message'])) : ?>
						<p class="kt-wrapped-overview-update__note"><?php echo esc_html($status['message']); ?></p>
					<?php endif; ?>
					<div class="kt-wrapped-overview__actions">
						<a class="button button-primary button-hero" href="<?php echo esc_url($check_updates_url); ?>">
							<?php esc_html_e('Check for Updates', 'kontentainment-wrapped'); ?>
						</a>
						<a class="button" href="<?php echo esc_url(admin_url('plugins.php')); ?>">
							<?php esc_html_e('Manage Plugins', 'kontentainment-wrapped'); ?>
						</a>
					</div>
				</div>
			</section>
		</div>
		<?php
	}

	public function handle_manual_update_check(): void
	{
		if (! current_user_can('update_plugins')) {
			wp_die(esc_html__('You are not allowed to check for plugin updates.', 'kontentainment-wrapped'));
		}

		check_admin_referer('kt_wrapped_manual_update_check');

		$updater = new GitHubUpdater();
		$result  = $updater->manual_check();
		set_transient('kt_wrapped_update_notice_' . get_current_user_id(), $result, MINUTE_IN_SECONDS);

		$redirect = add_query_arg(
			array(
				'page'                    => 'kt-wrapped-dashboard',
				'kt_wrapped_update_notice' => 1,
			),
			admin_url('admin.php')
		);

		wp_safe_redirect($redirect);
		exit;
	}

	public function render_update_notice(): void
	{
		if (! is_admin() || ! current_user_can('update_plugins')) {
			return;
		}

		if (empty($_GET['kt_wrapped_update_notice'])) {
			return;
		}

		$screen = function_exists('get_current_screen') ? get_current_screen() : null;
		if (! $screen || false === strpos((string) $screen->id, 'kt-wrapped-dashboard')) {
			return;
		}

		$notice = get_transient('kt_wrapped_update_notice_' . get_current_user_id());
		if (empty($notice) || ! is_array($notice)) {
			return;
		}

		delete_transient('kt_wrapped_update_notice_' . get_current_user_id());

		$result  = sanitize_key((string) ($notice['result'] ?? 'current'));
		$message = sanitize_text_field((string) ($notice['message'] ?? ''));
		$class   = 'notice-info';

		if ('update' === $result) {
			$class = 'notice-success';
		} elseif ('error' === $result) {
			$class = 'notice-warning';
		}
		?>
		<div class="notice <?php echo esc_attr($class); ?> is-dismissible">
			<p><strong><?php esc_html_e('Kontentainment Wrapped updates', 'kontentainment-wrapped'); ?></strong></p>
			<p><?php echo esc_html($message); ?></p>
		</div>
		<?php
	}

	public function render_location_notice(): void
	{
		if (! current_user_can('activate_plugins')) {
			return;
		}

		if (dirname(KT_WRAPPED_BASENAME) === KT_WRAPPED_SLUG) {
			return;
		}
		?>
		<div class="notice notice-warning">
			<p><strong><?php esc_html_e('Kontentainment Wrapped plugin location warning', 'kontentainment-wrapped'); ?></strong></p>
			<p>
				<?php
				echo esc_html(
					sprintf(
						/* translators: 1: current plugin basename 2: canonical plugin basename */
						__('This copy is running from %1$s, but the canonical plugin location is %2$s. Remove duplicate or renamed plugin folders after confirming the correct copy is active.', 'kontentainment-wrapped'),
						KT_WRAPPED_BASENAME,
						KT_WRAPPED_CANONICAL_BASENAME
					)
				);
				?>
			</p>
		</div>
		<?php
	}

	public function admin_head(): void
	{
		global $submenu;

		if (isset($submenu['kt-wrapped-dashboard'][0][0])) {
			$submenu['kt-wrapped-dashboard'][0][0] = __('Overview', 'kontentainment-wrapped');
		}
	}
}
