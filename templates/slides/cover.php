<?php
declare(strict_types=1);
?>
<div class="kt-wrapped-slide__bg"<?php echo $background_image ? ' style="background-image:url(' . esc_url($background_image) . ')"' : ''; ?>></div>
<div class="kt-wrapped-slide__content">
	<p class="kt-wrapped-kicker"><?php echo esc_html((string) ($edition['year'] ?? '')); ?></p>
	<h1 class="kt-wrapped-title"><?php echo esc_html((string) ($slide['title'] ?? $edition['title'])); ?></h1>
	<p class="kt-wrapped-subtitle"><?php echo esc_html((string) ($slide['subtitle'] ?? $edition['subtitle'])); ?></p>
	<?php if (! empty($slide['body_text'])) : ?>
		<div class="kt-wrapped-body-copy"><?php echo wp_kses_post(wpautop((string) $slide['body_text'])); ?></div>
	<?php endif; ?>
	<div class="kt-wrapped-slide__footer">
		<button type="button" class="kt-wrapped-cta" data-kt-button="next"><?php echo esc_html((string) ($slide['config']['cta_label'] ?? __('Start Story', 'kontentainment-wrapped'))); ?></button>
	</div>
</div>
