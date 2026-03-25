<?php
declare(strict_types=1);

$config = is_array($slide['config'] ?? null) ? $slide['config'] : array();
$items  = is_array($config['items'] ?? null) ? $config['items'] : array();
?>
<div class="kt-wrapped-slide__bg kt-wrapped-slide__bg--music-chart"<?php echo $background_image ? ' style="background-image:url(' . esc_url($background_image) . ')"' : ''; ?>></div>
<div class="kt-wrapped-slide__grain" aria-hidden="true"></div>
<div class="kt-wrapped-slide__content kt-wrapped-slide__content--music-chart">
	<p class="kt-wrapped-kicker" dir="auto"><?php echo esc_html((string) ($config['date_range'] ?? '')); ?></p>
	<h2 class="kt-wrapped-title kt-wrapped-title--medium" dir="auto"><?php echo esc_html((string) ($config['chart_title'] ?? $slide['title'] ?? '')); ?></h2>
	<?php if (! empty($slide['subtitle'])) : ?>
		<p class="kt-wrapped-subtitle" dir="auto"><?php echo esc_html((string) $slide['subtitle']); ?></p>
	<?php endif; ?>
	<ol class="kt-wrapped-music-chart-list">
		<?php foreach ($items as $item_index => $item) : ?>
			<?php $image_url = ! empty($item['image_id']) ? wp_get_attachment_image_url((int) $item['image_id'], 'medium') : ''; ?>
			<li class="kt-wrapped-music-chart-row">
				<span class="kt-wrapped-music-chart-row__rank"><?php echo esc_html(str_pad((string) ($item_index + 1), 2, '0', STR_PAD_LEFT)); ?></span>
				<div class="kt-wrapped-music-chart-row__cover"<?php echo $image_url ? ' style="background-image:url(' . esc_url($image_url) . ')"' : ''; ?>></div>
				<div class="kt-wrapped-music-chart-row__copy">
					<strong dir="auto"><?php echo esc_html((string) ($item['title'] ?? '')); ?></strong>
					<span dir="auto"><?php echo esc_html((string) ($item['subtitle'] ?? '')); ?></span>
				</div>
				<span class="kt-wrapped-music-chart-row__trend is-<?php echo esc_attr((string) ($item['trend_type'] ?? 'same')); ?>" dir="auto">
					<?php echo esc_html((string) ($item['trend_value'] ?: strtoupper((string) ($item['trend_type'] ?? '')))); ?>
				</span>
			</li>
		<?php endforeach; ?>
	</ol>
</div>
