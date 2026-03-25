<?php
declare(strict_types=1);

$items = $slide['config']['items'] ?? array();
$items = is_array($items) ? $items : array();
?>
<div class="kt-wrapped-slide__bg kt-wrapped-slide__bg--mesh"<?php echo $background_image ? ' style="background-image:url(' . esc_url($background_image) . ')"' : ''; ?>></div>
<div class="kt-wrapped-slide__content kt-wrapped-slide__content--mosaic">
	<p class="kt-wrapped-kicker"><?php esc_html_e('Highlights', 'kontentainment-wrapped'); ?></p>
	<h2 class="kt-wrapped-title kt-wrapped-title--medium"><?php echo esc_html((string) ($slide['title'] ?? '')); ?></h2>
	<?php if (! empty($slide['subtitle'])) : ?>
		<p class="kt-wrapped-subtitle"><?php echo esc_html((string) $slide['subtitle']); ?></p>
	<?php endif; ?>
	<div class="kt-wrapped-mosaic">
		<?php foreach ($items as $item_index => $item) : ?>
			<div class="kt-wrapped-mosaic__card kt-wrapped-mosaic__card--<?php echo esc_attr((string) (($item_index % 4) + 1)); ?>">
				<span class="kt-wrapped-mosaic__index"><?php echo esc_html(str_pad((string) ($item_index + 1), 2, '0', STR_PAD_LEFT)); ?></span>
				<span class="kt-wrapped-mosaic__label"><?php echo esc_html((string) $item); ?></span>
			</div>
		<?php endforeach; ?>
	</div>
</div>
