<?php
declare(strict_types=1);

$config = is_array($slide['config'] ?? null) ? $slide['config'] : array();
$feature_image = ! empty($config['image_id']) ? wp_get_attachment_image_url((int) $config['image_id'], 'large') : $background_image;
?>
<div class="kt-wrapped-slide__bg kt-wrapped-slide__bg--music-spotlight"<?php echo $background_image ? ' style="background-image:url(' . esc_url($background_image) . ')"' : ''; ?>></div>
<div class="kt-wrapped-slide__grain" aria-hidden="true"></div>
<div class="kt-wrapped-slide__content kt-wrapped-slide__content--music-spotlight">
	<div class="kt-wrapped-music-spotlight">
		<div class="kt-wrapped-music-spotlight__cover"<?php echo $feature_image ? ' style="background-image:url(' . esc_url($feature_image) . ')"' : ''; ?>></div>
		<div class="kt-wrapped-music-spotlight__copy">
			<p class="kt-wrapped-kicker" dir="auto"><?php echo esc_html((string) ($config['kicker'] ?? '')); ?></p>
			<h2 class="kt-wrapped-title kt-wrapped-title--medium" dir="auto"><?php echo esc_html((string) ($slide['title'] ?? '')); ?></h2>
			<p class="kt-wrapped-subtitle" dir="auto"><?php echo esc_html((string) ($slide['subtitle'] ?? '')); ?></p>
			<?php if (! empty($config['highlight_stat'])) : ?>
				<p class="kt-wrapped-music-spotlight__stat" dir="auto"><?php echo esc_html((string) $config['highlight_stat']); ?></p>
			<?php endif; ?>
			<div class="kt-wrapped-body-copy" dir="auto"><?php echo wp_kses_post(wpautop((string) (($config['description'] ?? '') ?: ($slide['body_text'] ?? '')))); ?></div>
		</div>
	</div>
</div>
