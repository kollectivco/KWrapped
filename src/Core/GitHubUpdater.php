<?php
declare(strict_types=1);

namespace KontentainmentWrapped\Core;

final class GitHubUpdater
{
	private const CACHE_KEY = 'kt_wrapped_github_release_data';
	private const CACHE_TTL = 21600;
	private const RELEASE_ASSET = 'kontentainment-wrapped.zip';
	private const STATUS_CACHE_KEY = 'kt_wrapped_github_release_status';

	public function register(): void
	{
		add_filter('pre_set_site_transient_update_plugins', array($this, 'inject_update'));
		add_filter('plugins_api', array($this, 'plugins_api'), 20, 3);
		add_filter('plugin_action_links_' . KT_WRAPPED_BASENAME, array($this, 'plugin_row_actions'));
		add_filter('auto_update_plugin', array($this, 'respect_auto_updates'), 10, 2);
		add_action('upgrader_process_complete', array($this, 'clear_cache'), 10, 2);
	}

	public function inject_update($transient)
	{
		if (! is_object($transient)) {
			return $transient;
		}

		$release = $this->get_latest_release();
		$plugin_data = $this->build_plugin_update_data($release);

		if (! isset($transient->response) || ! is_array($transient->response)) {
			$transient->response = array();
		}

		if (! isset($transient->no_update) || ! is_array($transient->no_update)) {
			$transient->no_update = array();
		}

		unset($transient->response[KT_WRAPPED_BASENAME], $transient->no_update[KT_WRAPPED_BASENAME]);

		if (empty($plugin_data)) {
			return $transient;
		}

		if (! empty($release['version']) && ! empty($release['package']) && version_compare($release['version'], KT_WRAPPED_VERSION, '>')) {
			$transient->response[KT_WRAPPED_BASENAME] = $plugin_data;
		} else {
			$transient->no_update[KT_WRAPPED_BASENAME] = $plugin_data;
		}

		return $transient;
	}

	public function plugins_api($result, string $action, object $args)
	{
		if ('plugin_information' !== $action || empty($args->slug) || KT_WRAPPED_SLUG !== $args->slug) {
			return $result;
		}

		$release = $this->get_latest_release();
		if (empty($release['version'])) {
			return $result;
		}

		$info                 = new \stdClass();
		$info->name           = 'Kontentainment Wrapped';
		$info->slug           = dirname(KT_WRAPPED_BASENAME);
		$info->version        = $release['version'];
		$info->author         = '<a href="https://github.com/kollectivco">kollectivco</a>';
		$info->homepage       = KT_WRAPPED_UPDATE_URI;
		$info->download_link  = $release['package'];
		$info->requires       = '6.0';
		$info->tested         = '6.6';
		$info->requires_php   = '7.4';
		$info->last_updated   = $release['published_at'];
		$info->sections       = array(
			'description' => __('Premium story-driven wrapped editions for WordPress.', 'kontentainment-wrapped'),
			'changelog'   => $release['body'],
		);
		$info->banners        = array();

		return $info;
	}

	public function clear_cache($upgrader, array $hook_extra): void
	{
		if (empty($hook_extra['plugins']) || ! is_array($hook_extra['plugins'])) {
			return;
		}

		if (in_array(KT_WRAPPED_BASENAME, $hook_extra['plugins'], true)) {
			$this->clear_update_cache();
		}
	}

	public function get_latest_release(bool $force_refresh = false): array
	{
		if ($force_refresh) {
			$this->clear_update_cache();
		}

		$cached = get_site_transient(self::CACHE_KEY);
		if (is_array($cached)) {
			return $cached;
		}

		$response = wp_remote_get(
			sprintf('https://api.github.com/repos/%s/releases', KT_WRAPPED_GITHUB_REPO),
			array(
				'headers' => array(
					'Accept'     => 'application/vnd.github+json',
					'User-Agent' => 'Kontentainment-Wrapped-Updater',
				),
				'timeout' => 15,
			)
		);

		if (is_wp_error($response) || 200 !== wp_remote_retrieve_response_code($response)) {
			set_site_transient(
				self::STATUS_CACHE_KEY,
				array(
					'status'  => 'error',
					'message' => is_wp_error($response) ? $response->get_error_message() : __('GitHub returned an unexpected response.', 'kontentainment-wrapped'),
				),
				MINUTE_IN_SECONDS * 10
			);
			return array();
		}

		$body = json_decode((string) wp_remote_retrieve_body($response), true);
		if (! is_array($body)) {
			set_site_transient(
				self::STATUS_CACHE_KEY,
				array(
					'status'  => 'error',
					'message' => __('The GitHub release response could not be parsed.', 'kontentainment-wrapped'),
				),
				MINUTE_IN_SECONDS * 10
			);
			return array();
		}

		$latest_release = array();
		foreach ($body as $release) {
			if (! is_array($release) || ! empty($release['draft']) || ! empty($release['prerelease'])) {
				continue;
			}

			$assets = isset($release['assets']) && is_array($release['assets']) ? $release['assets'] : array();
			foreach ($assets as $asset) {
				if (($asset['name'] ?? '') === self::RELEASE_ASSET && ! empty($asset['browser_download_url'])) {
					$latest_release = $release;
					break 2;
				}
			}
		}

		if (empty($latest_release)) {
			set_site_transient(
				self::STATUS_CACHE_KEY,
				array(
					'status'  => 'missing_release',
					'message' => __('No eligible GitHub release with the packaged plugin ZIP was found.', 'kontentainment-wrapped'),
				),
				MINUTE_IN_SECONDS * 10
			);
			return array();
		}

		$package = '';
		foreach ((array) $latest_release['assets'] as $asset) {
			if (($asset['name'] ?? '') === self::RELEASE_ASSET && ! empty($asset['browser_download_url'])) {
				$package = (string) $asset['browser_download_url'];
				break;
			}
		}

		$data = array(
			'version'      => ltrim((string) ($latest_release['tag_name'] ?? ''), 'v'),
			'package'      => esc_url_raw($package),
			'body'         => wp_kses_post(wpautop((string) ($latest_release['body'] ?? ''))),
			'published_at' => ! empty($latest_release['published_at']) ? gmdate('Y-m-d', strtotime((string) $latest_release['published_at'])) : '',
		);

		set_site_transient(self::CACHE_KEY, $data, self::CACHE_TTL);
		set_site_transient(
			self::STATUS_CACHE_KEY,
			array(
				'status'  => 'ok',
				'message' => __('GitHub release data refreshed successfully.', 'kontentainment-wrapped'),
			),
			self::CACHE_TTL
		);

		return $data;
	}

	public function clear_update_cache(): void
	{
		delete_site_transient(self::CACHE_KEY);
		delete_site_transient(self::STATUS_CACHE_KEY);
		delete_site_transient('update_plugins');
	}

	public function manual_check(): array
	{
		$release = $this->get_latest_release(true);
		wp_update_plugins();

		if (empty($release['version'])) {
			$status = get_site_transient(self::STATUS_CACHE_KEY);
			return array(
				'result'  => 'error',
				'message' => is_array($status) && ! empty($status['message'])
					? (string) $status['message']
					: __('The update check failed. Please try again in a moment.', 'kontentainment-wrapped'),
			);
		}

		if (version_compare($release['version'], KT_WRAPPED_VERSION, '>')) {
			return array(
				'result'  => 'update',
				'message' => sprintf(
					/* translators: 1: installed version 2: latest version */
					__('Update found. Installed version %1$s can be updated to %2$s.', 'kontentainment-wrapped'),
					KT_WRAPPED_VERSION,
					$release['version']
				),
			);
		}

		return array(
			'result'  => 'current',
			'message' => sprintf(
				/* translators: %s: plugin version */
				__('No update available. You are already running version %s.', 'kontentainment-wrapped'),
				KT_WRAPPED_VERSION
			),
		);
	}

	public function get_status_snapshot(): array
	{
		$release = $this->get_latest_release();
		$status  = get_site_transient(self::STATUS_CACHE_KEY);
		$latest  = ! empty($release['version']) ? (string) $release['version'] : '';

		return array(
			'current_version' => KT_WRAPPED_VERSION,
			'latest_version'  => $latest,
			'status'          => ! empty($status['status']) ? (string) $status['status'] : ($latest ? 'ok' : 'unknown'),
			'message'         => ! empty($status['message']) ? (string) $status['message'] : '',
			'has_update'      => $latest ? version_compare($latest, KT_WRAPPED_VERSION, '>') : false,
			'auto_updates'    => (bool) wp_is_auto_update_enabled_for_type('plugin'),
		);
	}

	public function plugin_row_actions(array $actions): array
	{
		if (! current_user_can('update_plugins')) {
			return $actions;
		}

		$url = wp_nonce_url(
			admin_url('admin.php?page=kt-wrapped-dashboard&kt_wrapped_update_check=1'),
			'kt_wrapped_manual_update_check'
		);

		$actions['kt_wrapped_check_updates'] = sprintf(
			'<a href="%1$s">%2$s</a>',
			esc_url($url),
			esc_html__('Check for updates', 'kontentainment-wrapped')
		);

		return $actions;
	}

	public function respect_auto_updates($update, $item)
	{
		if (is_object($item) && ! empty($item->plugin) && KT_WRAPPED_BASENAME === $item->plugin) {
			return $update;
		}

		return $update;
	}

	private function build_plugin_update_data(array $release): ?\stdClass
	{
		if (empty($release['version'])) {
			return null;
		}

		$plugin              = new \stdClass();
		$plugin->id          = KT_WRAPPED_UPDATE_URI;
		$plugin->slug        = KT_WRAPPED_SLUG;
		$plugin->plugin      = KT_WRAPPED_BASENAME;
		$plugin->new_version = (string) $release['version'];
		$plugin->version     = (string) $release['version'];
		$plugin->url         = KT_WRAPPED_UPDATE_URI;
		$plugin->package     = ! empty($release['package']) ? (string) $release['package'] : '';
		$plugin->tested      = '6.6';
		$plugin->requires    = '6.0';
		$plugin->requires_php = '7.4';
		$plugin->icons       = array();
		$plugin->banners     = array();
		$plugin->banners_rtl = array();
		$plugin->autoupdate  = null;

		return $plugin;
	}
}
