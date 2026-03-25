<?php
declare(strict_types=1);

namespace KontentainmentWrapped\Frontend;

final class SlideRenderer
{
	public function render(array $slide, array $edition, int $index, int $total): void
	{
		$type = sanitize_key((string) ($slide['type'] ?? 'cover'));
		$file = KT_WRAPPED_PATH . 'templates/slides/' . $type . '.php';

		if (! file_exists($file)) {
			$file = KT_WRAPPED_PATH . 'templates/slides/fallback.php';
		}

		$background_image = ! empty($slide['background_image_id']) ? wp_get_attachment_image_url((int) $slide['background_image_id'], 'full') : '';
		include $file;
	}
}
