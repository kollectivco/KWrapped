<?php
declare(strict_types=1);

$config = is_array($slide['config'] ?? null) ? $slide['config'] : array();
$items  = is_array($config['items'] ?? null) ? $config['items'] : array();
?>
<div class="kt-wrapped-slide__bg kt-wrapped-slide__bg--music-chart"<?php echo $background_image ? ' style="background-image:url(' . esc_url($background_image) . ')"' : ''; ?>></div>
<div class="kt-wrapped-slide__grain" aria-hidden="true"></div>
<div class="kt-wrapped-slide__content kt-wrapped-slide__content--music-chart">
	<div class="kt-wrapped-music-chart-board">
		<div class="kt-wrapped-music-chart-board__header">
			<div>
				<p class="kt-wrapped-kicker" dir="auto"><?php esc_html_e('Weekly Chart', 'kontentainment-wrapped'); ?></p>
				<h2 class="kt-wrapped-title kt-wrapped-title--medium" dir="auto"><?php echo esc_html((string) ($config['chart_title'] ?? $slide['title'] ?? '')); ?></h2>
			</div>
			<div class="kt-wrapped-music-chart-board__range" dir="auto">
				<strong><?php echo esc_html((string) ($config['date_range'] ?? '')); ?></strong>
				<?php if (! empty($slide['subtitle'])) : ?>
					<span><?php echo esc_html((string) $slide['subtitle']); ?></span>
				<?php endif; ?>
			</div>
		</div>
		<div class="kt-wrapped-music-chart-board__labels" aria-hidden="true">
			<span><?php esc_html_e('Rank', 'kontentainment-wrapped'); ?></span>
			<span><?php esc_html_e('Track', 'kontentainment-wrapped'); ?></span>
			<span><?php esc_html_e('Move', 'kontentainment-wrapped'); ?></span>
		</div>
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
					<span class="kt-wrapped-music-chart-row__trend-icon" aria-hidden="true">
						<?php
						switch ((string) ($item['trend_type'] ?? 'same')) {
							case 'up':
								echo '↗';
								break;
							case 'down':
								echo '↘';
								break;
							case 'new':
								echo '✦';
								break;
							default:
								echo '•';
								break;
						}
						?>
					</span>
					<?php echo esc_html((string) ($item['trend_value'] ?: strtoupper((string) ($item['trend_type'] ?? '')))); ?>
				</span>
			</li>
		<?php endforeach; ?>
		</ol>
	</div>
</div>
