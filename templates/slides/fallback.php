<?php
declare(strict_types=1);
?>
<div class="kt-wrapped-slide__bg"<?php echo $background_image ? ' style="background-image:url(' . esc_url($background_image) . ')"' : ''; ?>></div>
<div class="kt-wrapped-slide__content">
	<h2 class="kt-wrapped-title kt-wrapped-title--medium"><?php echo esc_html((string) ($slide['title'] ?? 'Untitled Slide')); ?></h2>
	<p class="kt-wrapped-subtitle"><?php echo esc_html((string) ($slide['subtitle'] ?? '')); ?></p>
	<div class="kt-wrapped-body-copy"><?php echo wp_kses_post(wpautop((string) ($slide['body_text'] ?? ''))); ?></div>
</div>
