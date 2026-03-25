<?php
declare(strict_types=1);

$config = is_array($slide['config'] ?? null) ? $slide['config'] : array();
$items  = is_array($config['items'] ?? null) ? $config['items'] : array();
?>
<div class="kt-wrapped-slide__bg kt-wrapped-slide__bg--music-grid"<?php echo $background_image ? ' style="background-image:url(' . esc_url($background_image) . ')"' : ''; ?>></div>
<div class="kt-wrapped-slide__grain" aria-hidden="true"></div>
<div class="kt-wrapped-slide__content kt-wrapped-slide__content--music-grid">
	<p class="kt-wrapped-kicker" dir="auto"><?php echo esc_html((string) ($config['grid_title'] ?? $slide['title'] ?? '')); ?></p>
	<h2 class="kt-wrapped-title kt-wrapped-title--medium" dir="auto"><?php echo esc_html((string) ($slide['title'] ?? '')); ?></h2>
	<?php if (! empty($config['subtitle']) || ! empty($slide['subtitle'])) : ?>
		<p class="kt-wrapped-subtitle" dir="auto"><?php echo esc_html((string) ($config['subtitle'] ?? $slide['subtitle'] ?? '')); ?></p>
	<?php endif; ?>
	<div class="kt-wrapped-music-grid">
		<?php foreach ($items as $item_index => $item) : ?>
			<?php $image_url = ! empty($item['image_id']) ? wp_get_attachment_image_url((int) $item['image_id'], 'medium') : ''; ?>
			<div class="kt-wrapped-music-grid__item">
				<div class="kt-wrapped-music-grid__image"<?php echo $image_url ? ' style="background-image:url(' . esc_url($image_url) . ')"' : ''; ?>></div>
				<div class="kt-wrapped-music-grid__copy">
					<span class="kt-wrapped-music-grid__rank"><?php echo esc_html(str_pad((string) ($item_index + 1), 2, '0', STR_PAD_LEFT)); ?></span>
					<strong dir="auto"><?php echo esc_html((string) ($item['title'] ?? '')); ?></strong>
					<span dir="auto"><?php echo esc_html((string) ($item['subtitle'] ?? '')); ?></span>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</div>
