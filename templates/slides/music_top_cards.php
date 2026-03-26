<?php
declare(strict_types=1);

$config = is_array($slide['config'] ?? null) ? $slide['config'] : array();
$cards  = is_array($config['cards'] ?? null) ? $config['cards'] : array();
$carousel_id = 'kt-wrapped-music-cards-' . $index;
?>
<div class="kt-wrapped-slide__bg kt-wrapped-slide__bg--music"<?php echo $background_image ? ' style="background-image:url(' . esc_url($background_image) . ')"' : ''; ?>></div>
<div class="kt-wrapped-slide__grain" aria-hidden="true"></div>
<div class="kt-wrapped-slide__content kt-wrapped-slide__content--music">
	<p class="kt-wrapped-kicker" dir="auto"><?php echo esc_html((string) ($config['section_title'] ?? $slide['title'] ?? '')); ?></p>
	<h2 class="kt-wrapped-title kt-wrapped-title--medium" dir="auto"><?php echo esc_html((string) ($slide['title'] ?? '')); ?></h2>
	<?php if (! empty($config['subtitle']) || ! empty($slide['subtitle'])) : ?>
		<p class="kt-wrapped-subtitle" dir="auto"><?php echo esc_html((string) ($config['subtitle'] ?? $slide['subtitle'] ?? '')); ?></p>
	<?php endif; ?>
	<div class="kt-wrapped-music-top-cards" data-kt-music-cards-slider data-kt-nav-ignore data-kt-music-cards-autoplay="true" data-kt-music-cards-id="<?php echo esc_attr($carousel_id); ?>">
		<div class="kt-wrapped-music-top-cards__viewport">
			<div class="kt-wrapped-music-top-cards__track">
		<?php foreach ($cards as $card_index => $card) : ?>
			<?php $image_url = ! empty($card['image_id']) ? wp_get_attachment_image_url((int) $card['image_id'], 'large') : ''; ?>
			<div class="kt-wrapped-music-card<?php echo 0 === $card_index ? ' is-active' : ''; ?>" data-kt-music-card data-kt-nav-ignore>
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
		<?php if (count($cards) > 1) : ?>
			<div class="kt-wrapped-music-top-cards__controls" data-kt-nav-ignore>
				<button type="button" class="kt-wrapped-music-top-cards__button" data-kt-music-cards-action="prev" aria-label="<?php esc_attr_e('Previous card', 'kontentainment-wrapped'); ?>">‹</button>
				<div class="kt-wrapped-music-top-cards__dots" aria-hidden="true">
					<?php foreach ($cards as $card_index => $card) : ?>
						<span class="kt-wrapped-music-top-cards__dot<?php echo 0 === $card_index ? ' is-active' : ''; ?>" data-kt-music-cards-dot></span>
					<?php endforeach; ?>
				</div>
				<button type="button" class="kt-wrapped-music-top-cards__button" data-kt-music-cards-action="next" aria-label="<?php esc_attr_e('Next card', 'kontentainment-wrapped'); ?>">›</button>
			</div>
		<?php endif; ?>
	</div>
</div>
