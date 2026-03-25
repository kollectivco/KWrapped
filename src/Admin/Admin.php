<?php
declare(strict_types=1);

namespace KontentainmentWrapped\Admin;

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
			array($this, 'redirect_to_editions'),
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

	public function redirect_to_editions(): void
	{
		wp_safe_redirect(admin_url('edit.php?post_type=kt_wrapped'));
		exit;
	}

	public function admin_head(): void
	{
		global $submenu;

		if (isset($submenu['kt-wrapped-dashboard'][0][0])) {
			$submenu['kt-wrapped-dashboard'][0][0] = __('Overview', 'kontentainment-wrapped');
		}
	}
}
