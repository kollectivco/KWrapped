<?php
declare(strict_types=1);

$items = $slide['config']['items'] ?? array();
$items = is_array($items) ? $items : array();
?>
<div class="kt-wrapped-slide__bg kt-wrapped-slide__bg--mesh"<?php echo $background_image ? ' style="background-image:url(' . esc_url($background_image) . ')"' : ''; ?>></div>
<div class="kt-wrapped-slide__content kt-wrapped-slide__content--ranking">
	<p class="kt-wrapped-kicker"><?php esc_html_e('Ranking', 'kontentainment-wrapped'); ?></p>
	<h2 class="kt-wrapped-title kt-wrapped-title--medium"><?php echo esc_html((string) ($slide['title'] ?? '')); ?></h2>
	<p class="kt-wrapped-subtitle"><?php echo esc_html((string) ($slide['subtitle'] ?? '')); ?></p>
	<ol class="kt-wrapped-ranking-list">
		<?php foreach ($items as $item_index => $item) : ?>
			<?php $item = is_array($item) ? $item : array(); ?>
			<li>
				<span class="kt-wrapped-ranking-rank"><?php echo esc_html((string) ($item_index + 1)); ?></span>
				<span class="kt-wrapped-ranking-label">
					<strong dir="auto"><?php echo esc_html((string) ($item['title'] ?? '')); ?></strong>
					<?php if (! empty($item['subtitle'])) : ?>
						<small dir="auto"><?php echo esc_html((string) $item['subtitle']); ?></small>
					<?php endif; ?>
				</span>
				<?php if (! empty($item['stat'])) : ?>
					<span class="kt-wrapped-ranking-stat" dir="auto"><?php echo esc_html((string) $item['stat']); ?></span>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
	</ol>
</div>
