<?php
/**
 * Wrapped single template.
 *
 * @var WP_Post $post
 */

declare(strict_types=1);

use KontentainmentWrapped\Frontend\SlideRenderer;
use KontentainmentWrapped\Wrapped\Meta;

if (! defined('ABSPATH')) {
	exit;
}

$post_id = get_the_ID();
$theme   = get_post_meta($post_id, Meta::THEME, true) ?: 'editorial-noir';
$slides  = get_post_meta($post_id, Meta::SLIDES, true);
$slides  = is_array($slides) ? array_values(array_filter($slides, static fn(array $slide): bool => ! empty($slide['is_active']) && empty($slide['is_hidden']))) : array();

usort(
	$slides,
	static function (array $a, array $b): int {
		return (int) ($a['order_index'] ?? 0) <=> (int) ($b['order_index'] ?? 0);
	}
);

$edition = array(
	'id'             => $post_id,
	'title'          => get_the_title($post_id),
	'subtitle'       => (string) get_post_meta($post_id, Meta::SUBTITLE, true),
	'year'           => (string) get_post_meta($post_id, Meta::YEAR, true),
	'theme'          => $theme,
	'share_text'     => (string) get_post_meta($post_id, Meta::SHARE_TEXT, true),
	'outro_cta_text' => (string) get_post_meta($post_id, Meta::OUTRO_CTA_TEXT, true),
	'outro_cta_url'  => (string) get_post_meta($post_id, Meta::OUTRO_CTA_URL, true),
);

$renderer = new SlideRenderer();
$has_slides = ! empty($slides);
$slide_duration = static function (string $type): int {
	switch ($type) {
		case 'cover':
			return 7200;
		case 'big_number':
			return 5800;
		case 'ranking_list':
			return 7600;
		case 'quote':
			return 6200;
		case 'final_share':
			return 9000;
		default:
			return 6500;
	}
};
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
	<?php wp_head(); ?>
</head>
<body <?php body_class('kt-wrapped-template'); ?>>
<?php wp_body_open(); ?>
<main class="kt-wrapped-viewer theme-<?php echo esc_attr($theme); ?>" data-share-text="<?php echo esc_attr($edition['share_text']); ?>" data-slide-count="<?php echo esc_attr((string) count($slides)); ?>">
	<div class="kt-wrapped-viewer__progress" aria-hidden="true">
		<?php if ($has_slides) : ?>
			<?php foreach ($slides as $index => $slide) : ?>
				<span class="kt-wrapped-progress-segment<?php echo 0 === $index ? ' is-active' : ''; ?>"><span class="kt-wrapped-progress-fill"></span></span>
			<?php endforeach; ?>
		<?php else : ?>
			<span class="kt-wrapped-progress-segment is-active"><span class="kt-wrapped-progress-fill"></span></span>
		<?php endif; ?>
	</div>

	<div class="kt-wrapped-viewer__nav-zone kt-wrapped-viewer__nav-zone--prev" data-kt-nav="prev" aria-hidden="true"></div>
	<div class="kt-wrapped-viewer__nav-zone kt-wrapped-viewer__nav-zone--next" data-kt-nav="next" aria-hidden="true"></div>

	<section class="kt-wrapped-stage" data-kt-stage>
		<?php if ($has_slides) : ?>
			<?php foreach ($slides as $index => $slide) : ?>
				<article
					class="kt-wrapped-slide<?php echo 0 === $index ? ' is-active' : ''; ?> align-<?php echo esc_attr($slide['text_alignment'] ?? 'left'); ?> overlay-<?php echo esc_attr($slide['overlay_strength'] ?? 'medium'); ?>"
					data-slide-index="<?php echo esc_attr((string) $index); ?>"
					data-slide-type="<?php echo esc_attr((string) ($slide['type'] ?? 'cover')); ?>"
					data-slide-duration="<?php echo esc_attr((string) $slide_duration((string) ($slide['type'] ?? 'cover'))); ?>"
					data-export-enabled="<?php echo ! empty($slide['export_enabled']) ? '1' : '0'; ?>"
					data-share-enabled="<?php echo ! empty($slide['share_enabled']) ? '1' : '0'; ?>"
				>
					<?php $renderer->render($slide, $edition, $index, count($slides)); ?>
				</article>
			<?php endforeach; ?>
		<?php else : ?>
			<article class="kt-wrapped-slide is-active align-left overlay-medium" data-slide-index="0" data-slide-type="empty" data-export-enabled="0" data-share-enabled="0">
				<div class="kt-wrapped-slide__bg kt-wrapped-slide__bg--outro"></div>
				<div class="kt-wrapped-slide__content">
					<p class="kt-wrapped-kicker"><?php esc_html_e('Wrapped', 'kontentainment-wrapped'); ?></p>
					<h1 class="kt-wrapped-title kt-wrapped-title--medium"><?php esc_html_e('No active slides yet', 'kontentainment-wrapped'); ?></h1>
					<p class="kt-wrapped-subtitle"><?php esc_html_e('This edition is set up, but it does not have any visible slides to play yet.', 'kontentainment-wrapped'); ?></p>
				</div>
			</article>
		<?php endif; ?>
	</section>

	<div class="kt-wrapped-viewer__meta">
		<p class="kt-wrapped-viewer__edition"><?php echo esc_html($edition['title']); ?></p>
		<p class="kt-wrapped-viewer__position" data-kt-position><?php echo $has_slides ? esc_html('1 / ' . count($slides)) : esc_html__('Ready', 'kontentainment-wrapped'); ?></p>
	</div>

	<div class="kt-wrapped-viewer__controls">
		<button type="button" class="kt-wrapped-control kt-wrapped-control--nav" data-kt-button="prev"><?php esc_html_e('Back', 'kontentainment-wrapped'); ?></button>
		<div class="kt-wrapped-control-cluster">
			<button type="button" class="kt-wrapped-control kt-wrapped-control--ghost" data-kt-action="save"><?php esc_html_e('Save', 'kontentainment-wrapped'); ?></button>
			<button type="button" class="kt-wrapped-control kt-wrapped-control--ghost" data-kt-action="share"><?php esc_html_e('Share', 'kontentainment-wrapped'); ?></button>
		</div>
		<button type="button" class="kt-wrapped-control kt-wrapped-control--nav" data-kt-button="next"><?php esc_html_e('Next', 'kontentainment-wrapped'); ?></button>
	</div>

	<div class="kt-wrapped-viewer__hint" data-kt-hint><?php esc_html_e('Tap to move through the story', 'kontentainment-wrapped'); ?></div>
	<div class="kt-wrapped-toast" data-kt-toast hidden></div>
	<div class="kt-wrapped-export-stage" aria-hidden="true">
		<div class="kt-wrapped-export-canvas theme-<?php echo esc_attr($theme); ?>" data-kt-export-canvas></div>
	</div>
	<div class="kt-wrapped-share-sheet" data-kt-share-sheet hidden>
		<div class="kt-wrapped-share-sheet__panel">
			<p class="kt-wrapped-share-sheet__eyebrow"><?php esc_html_e('Story Ready', 'kontentainment-wrapped'); ?></p>
			<h2 class="kt-wrapped-share-sheet__title" data-kt-share-sheet-title><?php esc_html_e('Preparing your story card…', 'kontentainment-wrapped'); ?></h2>
			<p class="kt-wrapped-share-sheet__body" data-kt-share-sheet-body><?php esc_html_e('We are packaging this slide for sharing.', 'kontentainment-wrapped'); ?></p>
			<div class="kt-wrapped-share-sheet__actions" data-kt-share-sheet-actions>
				<button type="button" class="kt-wrapped-ghost" data-kt-share-sheet-close><?php esc_html_e('Close', 'kontentainment-wrapped'); ?></button>
			</div>
		</div>
	</div>
</main>
<?php wp_footer(); ?>
</body>
</html>
