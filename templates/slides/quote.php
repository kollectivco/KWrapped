<?php
declare(strict_types=1);
?>
<div class="kt-wrapped-slide__bg kt-wrapped-slide__bg--quote"<?php echo $background_image ? ' style="background-image:url(' . esc_url($background_image) . ')"' : ''; ?>></div>
<div class="kt-wrapped-slide__quote-mark" aria-hidden="true">"</div>
<div class="kt-wrapped-slide__content kt-wrapped-slide__content--quote">
	<p class="kt-wrapped-kicker"><?php esc_html_e('Editorial', 'kontentainment-wrapped'); ?></p>
	<blockquote class="kt-wrapped-quote"><?php echo esc_html((string) ($slide['title'] ?? '')); ?></blockquote>
	<p class="kt-wrapped-subtitle"><?php echo esc_html((string) ($slide['subtitle'] ?? '')); ?></p>
</div>
