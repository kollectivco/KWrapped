<?php
declare(strict_types=1);

namespace KontentainmentWrapped\Core;

use KontentainmentWrapped\Admin\SeedDemo;
use KontentainmentWrapped\Wrapped\PostType;

final class Activator
{
	public static function activate(): void
	{
		PostType::register_post_type();
		flush_rewrite_rules();
		SeedDemo::create_demo_edition();
	}
}
