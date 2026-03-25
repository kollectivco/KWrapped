<?php
declare(strict_types=1);

namespace KontentainmentWrapped\Frontend;

final class TemplateLoader
{
	public function register(): void
	{
		add_filter('single_template', array($this, 'single_template'));
	}

	public function single_template(string $template): string
	{
		if (is_singular('kt_wrapped')) {
			$custom = KT_WRAPPED_PATH . 'templates/single-kt_wrapped.php';
			if (file_exists($custom)) {
				return $custom;
			}
		}

		return $template;
	}
}
