<?php
declare(strict_types=1);

$config = is_array($slide['config'] ?? null) ? $slide['config'] : array();
$cards  = is_array($config['cards'] ?? null) ? $config['cards'] : array();
?>
<div class="kt-wrapped-slide__bg kt-wrapped-slide__bg--music"<?php echo $background_image ? ' style="background-image:url(' . esc_url($background_image) . ')"' : ''; ?>></div>
<div class="kt-wrapped-slide__grain" aria-hidden="true"></div>
<div class="kt-wrapped-slide__content kt-wrapped-slide__content--music">
	<p class="kt-wrapped-kicker" dir="auto"><?php echo esc_html((string) ($config['section_title'] ?? $slide['title'] ?? '')); ?></p>
	<h2 class="kt-wrapped-title kt-wrapped-title--medium" dir="auto"><?php echo esc_html((string) ($slide['title'] ?? '')); ?></h2>
	<?php if (! empty($config['subtitle']) || ! empty($slide['subtitle'])) : ?>
		<p class="kt-wrapped-subtitle" dir="auto"><?php echo esc_html((string) ($config['subtitle'] ?? $slide['subtitle'] ?? '')); ?></p>
	<?php endif; ?>
	<div class="kt-wrapped-music-top-cards">
		<?php foreach ($cards as $card) : ?>
			<?php $image_url = ! empty($card['image_id']) ? wp_get_attachment_image_url((int) $card['image_id'], 'large') : ''; ?>
			<div class="kt-wrapped-music-card">
				<div class="kt-wrapped-music-card__cover"<?php echo $image_url ? ' style="background-image:url(' . esc_url($image_url) . ')"' : ''; ?>>
					<?php if (! empty($card['badge'])) : ?>
						<span class="kt-wrapped-music-card__badge" dir="auto"><?php echo esc_html((string) $card['badge']); ?></span>
					<?php endif; ?>
				</div>
				<div class="kt-wrapped-music-card__body">
					<div>
						<h3 class="kt-wrapped-music-card__title" dir="auto"><?php echo esc_html((string) ($card['track_title'] ?? '')); ?></h3>
						<p class="kt-wrapped-music-card__meta" dir="auto"><?php echo esc_html((string) ($card['artist_name'] ?? '')); ?></p>
					</div>
					<div class="kt-wrapped-music-card__player" aria-hidden="true">
						<span></span><span></span><span></span>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</div>
