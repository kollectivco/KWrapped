<?php
/**
 * Plugin Name: Kontentainment Wrapped
 * Plugin URI: https://github.com/kollectivco/KWrapped
 * Description: Premium story-driven wrapped editions for WordPress.
 * Version: 1.1.0
 * Author: Codex
 * Update URI: https://github.com/kollectivco/KWrapped
 * Text Domain: kontentainment-wrapped
 */

declare(strict_types=1);

if (! defined('ABSPATH')) {
	exit;
}

define('KT_WRAPPED_VERSION', '1.1.0');
define('KT_WRAPPED_FILE', __FILE__);
define('KT_WRAPPED_PATH', plugin_dir_path(__FILE__));
define('KT_WRAPPED_URL', plugin_dir_url(__FILE__));
define('KT_WRAPPED_BASENAME', plugin_basename(__FILE__));
define('KT_WRAPPED_GITHUB_REPO', 'kollectivco/KWrapped');
define('KT_WRAPPED_UPDATE_URI', 'https://github.com/kollectivco/KWrapped');

spl_autoload_register(
	static function (string $class): void {
		$prefix   = 'KontentainmentWrapped\\';
		$base_dir = KT_WRAPPED_PATH . 'src/';

		if (0 !== strpos($class, $prefix)) {
			return;
		}

		$relative_class = substr($class, strlen($prefix));
		$file           = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

		if (file_exists($file)) {
			require_once $file;
		}
	}
);

register_activation_hook(KT_WRAPPED_FILE, array('KontentainmentWrapped\\Core\\Activator', 'activate'));
register_deactivation_hook(KT_WRAPPED_FILE, array('KontentainmentWrapped\\Core\\Deactivator', 'deactivate'));

add_action(
	'plugins_loaded',
	static function (): void {
		$plugin = new KontentainmentWrapped\Core\Plugin();
		$plugin->boot();
	}
);
