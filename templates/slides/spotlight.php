<?php
declare(strict_types=1);
?>
<div class="kt-wrapped-slide__bg kt-wrapped-slide__bg--spotlight"<?php echo $background_image ? ' style="background-image:url(' . esc_url($background_image) . ')"' : ''; ?>></div>
<div class="kt-wrapped-slide__orb" aria-hidden="true"></div>
<div class="kt-wrapped-slide__content kt-wrapped-slide__content--spotlight">
	<div class="kt-wrapped-spotlight-frame">
		<p class="kt-wrapped-kicker"><?php esc_html_e('Spotlight', 'kontentainment-wrapped'); ?></p>
		<h2 class="kt-wrapped-title kt-wrapped-title--medium"><?php echo esc_html((string) ($slide['title'] ?? '')); ?></h2>
		<p class="kt-wrapped-subtitle"><?php echo esc_html((string) ($slide['subtitle'] ?? '')); ?></p>
		<div class="kt-wrapped-body-copy"><?php echo wp_kses_post(wpautop((string) ($slide['body_text'] ?? ''))); ?></div>
	</div>
</div>
