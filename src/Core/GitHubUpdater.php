<?php
declare(strict_types=1);

namespace KontentainmentWrapped\Core;

final class GitHubUpdater
{
	private const CACHE_KEY = 'kt_wrapped_github_release_data';
	private const CACHE_TTL = 21600;
	private const RELEASE_ASSET = 'kontentainment-wrapped.zip';

	public function register(): void
	{
		add_filter('pre_set_site_transient_update_plugins', array($this, 'inject_update'));
		add_filter('plugins_api', array($this, 'plugins_api'), 20, 3);
		add_action('upgrader_process_complete', array($this, 'clear_cache'), 10, 2);
	}

	public function inject_update($transient)
	{
		if (! is_object($transient)) {
			return $transient;
		}

		$release = $this->get_latest_release();
		if (empty($release['version']) || empty($release['package']) || version_compare($release['version'], KT_WRAPPED_VERSION, '<=')) {
			return $transient;
		}

		$update              = new \stdClass();
		$update->slug        = dirname(KT_WRAPPED_BASENAME);
		$update->plugin      = KT_WRAPPED_BASENAME;
		$update->new_version = $release['version'];
		$update->url         = KT_WRAPPED_UPDATE_URI;
		$update->package     = $release['package'];

		$transient->response[KT_WRAPPED_BASENAME] = $update;

		return $transient;
	}

	public function plugins_api($result, string $action, object $args)
	{
		if ('plugin_information' !== $action || empty($args->slug) || dirname(KT_WRAPPED_BASENAME) !== $args->slug) {
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
			delete_site_transient(self::CACHE_KEY);
		}
	}

	private function get_latest_release(): array
	{
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
			return array();
		}

		$body = json_decode((string) wp_remote_retrieve_body($response), true);
		if (! is_array($body)) {
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

		return $data;
	}
}
