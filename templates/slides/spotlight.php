<?php
declare(strict_types=1);
?>
<div class="kt-wrapped-slide__bg kt-wrapped-slide__bg--spotlight"<?php echo $background_image ? ' style="background-image:url(' . esc_url($background_image) . ')"' : ''; ?>></div>
<div class="kt-wrapped-slide__orb" aria-hidden="true"></div>
<div class="kt-wrapped-slide__content kt-wrapped-slide__content--spotlight">
	<div class="kt-wrapped-spotlight-frame">
		<p class="kt-wrapped-kicker" dir="auto"><?php echo esc_html((string) (($slide['config']['kicker'] ?? '') ?: __('Spotlight', 'kontentainment-wrapped'))); ?></p>
		<h2 class="kt-wrapped-title kt-wrapped-title--medium" dir="auto"><?php echo esc_html((string) ($slide['title'] ?? '')); ?></h2>
		<p class="kt-wrapped-subtitle" dir="auto"><?php echo esc_html((string) ($slide['subtitle'] ?? '')); ?></p>
		<?php if (! empty($slide['config']['highlight_stat'])) : ?>
			<p class="kt-wrapped-music-spotlight__stat" dir="auto"><?php echo esc_html((string) $slide['config']['highlight_stat']); ?></p>
		<?php endif; ?>
		<div class="kt-wrapped-body-copy" dir="auto"><?php echo wp_kses_post(wpautop((string) ($slide['body_text'] ?? ''))); ?></div>
	</div>
</div>
