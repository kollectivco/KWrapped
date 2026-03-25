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
		<?php foreach ($items as $item) : ?>
			<?php
			$item_text = (string) $item;
			$rank      = '';
			$label     = $item_text;
			if (preg_match('/^\s*(\d+)\.\s*(.+)$/', $item_text, $matches)) {
				$rank  = $matches[1];
				$label = $matches[2];
			}
			?>
			<li>
				<?php if ($rank) : ?>
					<span class="kt-wrapped-ranking-rank"><?php echo esc_html($rank); ?></span>
				<?php endif; ?>
				<span class="kt-wrapped-ranking-label"><?php echo esc_html($label); ?></span>
			</li>
		<?php endforeach; ?>
	</ol>
</div>
