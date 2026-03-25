<?php
declare(strict_types=1);
?>
<div class="kt-wrapped-slide__bg kt-wrapped-slide__bg--outro"<?php echo $background_image ? ' style="background-image:url(' . esc_url($background_image) . ')"' : ''; ?>></div>
<div class="kt-wrapped-slide__glow" aria-hidden="true"></div>
<div class="kt-wrapped-slide__content kt-wrapped-slide__content--final">
	<p class="kt-wrapped-kicker"><?php esc_html_e('Finale', 'kontentainment-wrapped'); ?></p>
	<h2 class="kt-wrapped-title"><?php echo esc_html((string) ($slide['title'] ?? '')); ?></h2>
	<p class="kt-wrapped-subtitle"><?php echo esc_html((string) ($slide['subtitle'] ?? '')); ?></p>
	<?php if (! empty($slide['body_text'])) : ?>
		<div class="kt-wrapped-body-copy"><?php echo wp_kses_post(wpautop((string) $slide['body_text'])); ?></div>
	<?php endif; ?>
	<div class="kt-wrapped-final-pulse">
		<span></span><span></span><span></span>
	</div>
	<p class="kt-wrapped-final-note"><?php esc_html_e('Save your favorite frame or send it straight into your share sheet.', 'kontentainment-wrapped'); ?></p>
	<div class="kt-wrapped-final-actions">
		<button type="button" class="kt-wrapped-cta" data-kt-action="share"><?php esc_html_e('Share This Slide', 'kontentainment-wrapped'); ?></button>
		<button type="button" class="kt-wrapped-ghost" data-kt-action="save"><?php esc_html_e('Save Image', 'kontentainment-wrapped'); ?></button>
		<button type="button" class="kt-wrapped-ghost" data-kt-action="replay"><?php echo esc_html((string) ($slide['config']['replay_label'] ?? __('Replay Story', 'kontentainment-wrapped'))); ?></button>
		<?php if (! empty($edition['outro_cta_text']) && ! empty($edition['outro_cta_url'])) : ?>
			<a class="kt-wrapped-ghost" href="<?php echo esc_url($edition['outro_cta_url']); ?>"><?php echo esc_html((string) $edition['outro_cta_text']); ?></a>
		<?php endif; ?>
	</div>
</div>
