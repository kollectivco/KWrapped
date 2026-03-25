<?php
declare(strict_types=1);

namespace KontentainmentWrapped\Admin;

use KontentainmentWrapped\Wrapped\Meta;
use KontentainmentWrapped\Wrapped\SlideSchema;

final class SaveHandler
{
	public function register(): void
	{
		add_action('save_post_kt_wrapped', array($this, 'save'), 10, 2);
		add_action('admin_notices', array($this, 'render_notices'));
	}

	public function save(int $post_id, \WP_Post $post): void
	{
		if (! isset($_POST['kt_wrapped_nonce']) || ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['kt_wrapped_nonce'])), 'kt_wrapped_save_meta')) {
			return;
		}

		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return;
		}

		if (wp_is_post_revision($post_id) || 'kt_wrapped' !== $post->post_type) {
			return;
		}

		if (! current_user_can('edit_post', $post_id)) {
			return;
		}

		update_post_meta($post_id, Meta::SUBTITLE, sanitize_text_field(wp_unslash($_POST['kt_wrapped_subtitle'] ?? '')));
		update_post_meta($post_id, Meta::YEAR, sanitize_text_field(wp_unslash($_POST['kt_wrapped_year_season'] ?? '')));

		$theme = sanitize_key(wp_unslash($_POST['kt_wrapped_theme_preset'] ?? 'editorial-noir'));
		if (! in_array($theme, array('editorial-noir', 'electric-flare', 'sunset-drive', 'acid-pop'), true)) {
			$theme = 'editorial-noir';
		}

		update_post_meta($post_id, Meta::THEME, $theme);
		update_post_meta($post_id, Meta::COVER_IMAGE_ID, absint($_POST['kt_wrapped_cover_image_id'] ?? 0));
		update_post_meta($post_id, Meta::SHARE_TEXT, sanitize_textarea_field(wp_unslash($_POST['kt_wrapped_share_text'] ?? '')));
		update_post_meta($post_id, Meta::OUTRO_CTA_TEXT, sanitize_text_field(wp_unslash($_POST['kt_wrapped_outro_cta_text'] ?? '')));
		update_post_meta($post_id, Meta::OUTRO_CTA_URL, esc_url_raw(wp_unslash($_POST['kt_wrapped_outro_cta_url'] ?? '')));

		$slides_input = isset($_POST['kt_wrapped_slides']) && is_array($_POST['kt_wrapped_slides']) ? wp_unslash($_POST['kt_wrapped_slides']) : array();
		$slides       = array();
		$warnings     = array();

		foreach ($slides_input as $index => $slide) {
			if (! is_array($slide)) {
				continue;
			}

			$type = sanitize_key((string) ($slide['type'] ?? 'cover'));
			if (! array_key_exists($type, SlideSchema::supported_types())) {
				$type = 'cover';
			}

			$slide_id = sanitize_text_field((string) ($slide['id'] ?? ''));
			if ('' === $slide_id) {
				$slide_id = wp_generate_uuid4();
			}

			$config = SlideSchema::sanitize_config((array) ($slide['config'] ?? array()), $type);

			$normalized = SlideSchema::normalize_slide(
				array(
					'id'                  => $slide_id,
					'type'                => $type,
					'internal_name'       => $slide['internal_name'] ?? '',
					'title'               => $slide['title'] ?? '',
					'subtitle'            => $slide['subtitle'] ?? '',
					'body_text'           => $slide['body_text'] ?? '',
					'short_caption'       => $slide['short_caption'] ?? '',
					'background_image_id' => $slide['background_image_id'] ?? 0,
					'theme_variant'       => $slide['theme_variant'] ?? 'default',
					'text_alignment'      => $slide['text_alignment'] ?? 'left',
					'overlay_strength'    => $slide['overlay_strength'] ?? 'medium',
					'export_enabled'      => $slide['export_enabled'] ?? false,
					'share_enabled'       => $slide['share_enabled'] ?? false,
					'is_active'           => $slide['is_active'] ?? false,
					'is_hidden'           => $slide['is_hidden'] ?? false,
					'order_index'         => $slide['order_index'] ?? $index,
					'config'              => $config,
				),
				(int) $index
			);

			$slide_errors = SlideSchema::validate_slide($normalized);
			if (! empty($slide_errors)) {
				$warnings[] = sprintf(
					/* translators: 1: slide number 2: warnings */
					__('Slide %1$d: %2$s', 'kontentainment-wrapped'),
					(int) $index + 1,
					implode(' ', $slide_errors)
				);
			}

			$slides[] = $normalized;
		}

		usort(
			$slides,
			static function (array $a, array $b): int {
				return (int) $a['order_index'] <=> (int) $b['order_index'];
			}
		);

		$slides = array_values(
			array_map(
				static function (array $slide, int $order): array {
					$slide['order_index'] = $order;
					return $slide;
				},
				$slides,
				array_keys($slides)
			)
		);

		update_post_meta($post_id, Meta::SLIDES, $slides);

		if (! empty($warnings)) {
			set_transient('kt_wrapped_warnings_' . get_current_user_id() . '_' . $post_id, $warnings, 60);
		} else {
			delete_transient('kt_wrapped_warnings_' . get_current_user_id() . '_' . $post_id);
		}
	}

	public function render_notices(): void
	{
		global $pagenow;

		if ('post.php' !== $pagenow || ! isset($_GET['post'])) {
			return;
		}

		$post_id = absint($_GET['post']);
		if (! $post_id || 'kt_wrapped' !== get_post_type($post_id)) {
			return;
		}

		$warnings = get_transient('kt_wrapped_warnings_' . get_current_user_id() . '_' . $post_id);
		if (empty($warnings) || ! is_array($warnings)) {
			return;
		}

		delete_transient('kt_wrapped_warnings_' . get_current_user_id() . '_' . $post_id);
		?>
		<div class="notice notice-warning">
			<p><strong><?php esc_html_e('Kontentainment Wrapped content guidance', 'kontentainment-wrapped'); ?></strong></p>
			<ul>
				<?php foreach ($warnings as $warning) : ?>
					<li><?php echo esc_html((string) $warning); ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php
	}
}
