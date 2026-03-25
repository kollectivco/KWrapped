<?php
declare(strict_types=1);

namespace KontentainmentWrapped\Frontend;

final class Frontend
{
	public function register(): void
	{
		add_filter('body_class', array($this, 'body_class'));
	}

	public function body_class(array $classes): array
	{
		if (is_singular('kt_wrapped')) {
			$classes[] = 'kt-wrapped-body';
		}

		return $classes;
	}
}
