<?php
declare(strict_types=1);
?>
<div class="kt-wrapped-slide__bg kt-wrapped-slide__bg--gradient"<?php echo $background_image ? ' style="background-image:url(' . esc_url($background_image) . ')"' : ''; ?>></div>
<div class="kt-wrapped-slide__content kt-wrapped-slide__content--number">
	<p class="kt-wrapped-kicker"><?php echo esc_html((string) ($slide['config']['label'] ?? __('Big Number', 'kontentainment-wrapped'))); ?></p>
	<h2 class="kt-wrapped-number"><?php echo esc_html((string) ($slide['title'] ?? '0')); ?><span><?php echo esc_html((string) ($slide['config']['suffix'] ?? '')); ?></span></h2>
	<p class="kt-wrapped-subtitle"><?php echo esc_html((string) ($slide['subtitle'] ?? '')); ?></p>
	<?php if (! empty($slide['body_text'])) : ?>
		<div class="kt-wrapped-body-copy"><?php echo wp_kses_post(wpautop((string) $slide['body_text'])); ?></div>
	<?php endif; ?>
</div>
