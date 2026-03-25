<?php
declare(strict_types=1);

namespace KontentainmentWrapped\Wrapped;

final class PostType
{
	public function register(): void
	{
		add_action('init', array(__CLASS__, 'register_post_type'));
		add_filter('post_row_actions', array($this, 'add_duplicate_link'), 10, 2);
		add_action('admin_action_kt_wrapped_duplicate', array($this, 'handle_duplicate'));
		add_filter('display_post_states', array($this, 'add_archived_state'), 10, 2);
	}

	public static function register_post_type(): void
	{
		register_post_type(
			'kt_wrapped',
			array(
				'labels' => array(
					'name'               => __('Wrapped Editions', 'kontentainment-wrapped'),
					'singular_name'      => __('Wrapped Edition', 'kontentainment-wrapped'),
					'add_new_item'       => __('Add Wrapped Edition', 'kontentainment-wrapped'),
					'edit_item'          => __('Edit Wrapped Edition', 'kontentainment-wrapped'),
					'new_item'           => __('New Wrapped Edition', 'kontentainment-wrapped'),
					'view_item'          => __('View Wrapped Edition', 'kontentainment-wrapped'),
					'search_items'       => __('Search Wrapped Editions', 'kontentainment-wrapped'),
					'not_found'          => __('No wrapped editions found.', 'kontentainment-wrapped'),
					'menu_name'          => __('Wrapped Editions', 'kontentainment-wrapped'),
				),
				'public'              => true,
				'show_ui'             => true,
				'show_in_menu'        => false,
				'show_in_rest'        => false,
				'supports'            => array('title', 'thumbnail'),
				'has_archive'         => false,
				'rewrite'             => array('slug' => 'wrapped'),
				'menu_icon'           => 'dashicons-format-gallery',
				'capability_type'     => 'post',
				'publicly_queryable'  => true,
				'query_var'           => true,
			)
		);

		register_post_status(
			'archived',
			array(
				'label'                     => _x('Archived', 'post', 'kontentainment-wrapped'),
				'public'                    => false,
				'internal'                  => false,
				'protected'                 => true,
				'show_in_admin_status_list' => true,
				'show_in_admin_all_list'    => true,
				'label_count'               => _n_noop('Archived <span class="count">(%s)</span>', 'Archived <span class="count">(%s)</span>', 'kontentainment-wrapped'),
			)
		);
	}

	public function add_duplicate_link(array $actions, \WP_Post $post): array
	{
		if ('kt_wrapped' !== $post->post_type || ! current_user_can('edit_posts')) {
			return $actions;
		}

		$url = wp_nonce_url(
			admin_url('admin.php?action=kt_wrapped_duplicate&post=' . $post->ID),
			'kt_wrapped_duplicate_' . $post->ID
		);

		$actions['kt_wrapped_duplicate'] = sprintf(
			'<a href="%1$s">%2$s</a>',
			esc_url($url),
			esc_html__('Duplicate', 'kontentainment-wrapped')
		);

		return $actions;
	}

	public function handle_duplicate(): void
	{
		$post_id = isset($_GET['post']) ? absint($_GET['post']) : 0;

		if (! $post_id || ! current_user_can('edit_post', $post_id)) {
			wp_die(esc_html__('You are not allowed to duplicate this edition.', 'kontentainment-wrapped'));
		}

		check_admin_referer('kt_wrapped_duplicate_' . $post_id);

		$post = get_post($post_id);

		if (! $post || 'kt_wrapped' !== $post->post_type) {
			wp_die(esc_html__('Invalid wrapped edition.', 'kontentainment-wrapped'));
		}

		$new_id = wp_insert_post(
			array(
				'post_type'   => 'kt_wrapped',
				'post_status' => 'draft',
				'post_title'  => $post->post_title . ' (Copy)',
			)
		);

		if (is_wp_error($new_id)) {
			wp_die(esc_html__('Could not duplicate edition.', 'kontentainment-wrapped'));
		}

		$meta = get_post_meta($post_id);
		foreach ($meta as $key => $values) {
			foreach ($values as $value) {
				add_post_meta($new_id, $key, maybe_unserialize($value));
			}
		}

		wp_safe_redirect(admin_url('post.php?post=' . $new_id . '&action=edit'));
		exit;
	}

	public function add_archived_state(array $states, \WP_Post $post): array
	{
		if ('archived' === $post->post_status) {
			$states[] = __('Archived', 'kontentainment-wrapped');
		}

		return $states;
	}
}
