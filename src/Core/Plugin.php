<?php
declare(strict_types=1);

namespace KontentainmentWrapped\Core;

use KontentainmentWrapped\Admin\Admin;
use KontentainmentWrapped\Admin\MetaBoxes;
use KontentainmentWrapped\Admin\SaveHandler;
use KontentainmentWrapped\Admin\SeedDemo;
use KontentainmentWrapped\Frontend\Frontend;
use KontentainmentWrapped\Frontend\TemplateLoader;
use KontentainmentWrapped\Wrapped\PostType;

final class Plugin
{
	public function boot(): void
	{
		(new GitHubUpdater())->register();
		(new PostType())->register();
		(new Assets())->register();
		(new Admin())->register();
		(new MetaBoxes())->register();
		(new SaveHandler())->register();
		(new Frontend())->register();
		(new TemplateLoader())->register();
		(new SeedDemo())->register();
	}
}
