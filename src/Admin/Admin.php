<?php
declare(strict_types=1);

namespace KontentainmentWrapped\Admin;

use WP_Query;

final class Admin
{
	public function register(): void
	{
		add_action('admin_menu', array($this, 'menu'));
		add_action('admin_head', array($this, 'admin_head'));
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
				</div>
				<p>
					<?php
					echo esc_html(
						sprintf(
							/* translators: %s: plugin version */
							__('Running Kontentainment Wrapped version %s. Publish updates through GitHub Releases using the packaged plugin ZIP.', 'kontentainment-wrapped'),
							KT_WRAPPED_VERSION
						)
					);
					?>
				</p>
			</section>
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
