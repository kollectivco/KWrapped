<?php
declare(strict_types=1);

namespace KontentainmentWrapped\Admin;

use KontentainmentWrapped\Wrapped\Meta;
use KontentainmentWrapped\Wrapped\SlideSchema;

final class MetaBoxes
{
	public function register(): void
	{
		add_action('add_meta_boxes', array($this, 'add'));
	}

	public function add(): void
	{
		add_meta_box(
			'kt-wrapped-settings',
			__('Wrapped Settings', 'kontentainment-wrapped'),
			array($this, 'render_settings'),
			'kt_wrapped',
			'normal',
			'high'
		);

		add_meta_box(
			'kt-wrapped-slides',
			__('Slides Manager', 'kontentainment-wrapped'),
			array($this, 'render_slides'),
			'kt_wrapped',
			'normal',
			'default'
		);
	}

	public function render_settings(\WP_Post $post): void
	{
		wp_nonce_field('kt_wrapped_save_meta', 'kt_wrapped_nonce');

		$defaults = Meta::defaults();
		$values   = array();
		foreach ($defaults as $key => $default) {
			$values[$key] = get_post_meta($post->ID, $key, true);
			if ('' === $values[$key] || null === $values[$key]) {
				$values[$key] = $default;
			}
		}

		$image_id  = absint($values[Meta::COVER_IMAGE_ID]);
		$image_url = $image_id ? wp_get_attachment_image_url($image_id, 'medium') : '';
		?>
		<div class="kt-wrapped-admin-grid">
			<div class="kt-wrapped-field">
				<label for="kt_wrapped_subtitle"><?php esc_html_e('Subtitle', 'kontentainment-wrapped'); ?></label>
				<input type="text" id="kt_wrapped_subtitle" name="kt_wrapped_subtitle" value="<?php echo esc_attr((string) $values[Meta::SUBTITLE]); ?>" />
				<p class="description"><?php esc_html_e('Keep it short and editorial. Aim for 4 to 10 words.', 'kontentainment-wrapped'); ?></p>
			</div>
			<div class="kt-wrapped-field">
				<label for="kt_wrapped_year_season"><?php esc_html_e('Year / Season', 'kontentainment-wrapped'); ?></label>
				<input type="text" id="kt_wrapped_year_season" name="kt_wrapped_year_season" value="<?php echo esc_attr((string) $values[Meta::YEAR]); ?>" />
				<p class="description"><?php esc_html_e('Examples: 2025, Summer 2025, Awards Season.', 'kontentainment-wrapped'); ?></p>
			</div>
			<div class="kt-wrapped-field">
				<label for="kt_wrapped_theme_preset"><?php esc_html_e('Theme Preset', 'kontentainment-wrapped'); ?></label>
				<select id="kt_wrapped_theme_preset" name="kt_wrapped_theme_preset">
					<?php foreach ($this->theme_presets() as $value => $label) : ?>
						<option value="<?php echo esc_attr($value); ?>" <?php selected($values[Meta::THEME], $value); ?>><?php echo esc_html($label); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="kt-wrapped-field kt-wrapped-field--wide">
				<label for="kt_wrapped_share_text"><?php esc_html_e('Share Text', 'kontentainment-wrapped'); ?></label>
				<textarea id="kt_wrapped_share_text" name="kt_wrapped_share_text" rows="3"><?php echo esc_textarea((string) $values[Meta::SHARE_TEXT]); ?></textarea>
				<p class="description"><?php esc_html_e('Write the default share caption. Keep it punchy and human.', 'kontentainment-wrapped'); ?></p>
			</div>
			<div class="kt-wrapped-field">
				<label for="kt_wrapped_outro_cta_text"><?php esc_html_e('Outro CTA Text', 'kontentainment-wrapped'); ?></label>
				<input type="text" id="kt_wrapped_outro_cta_text" name="kt_wrapped_outro_cta_text" value="<?php echo esc_attr((string) $values[Meta::OUTRO_CTA_TEXT]); ?>" />
			</div>
			<div class="kt-wrapped-field">
				<label for="kt_wrapped_outro_cta_url"><?php esc_html_e('Outro CTA URL', 'kontentainment-wrapped'); ?></label>
				<input type="url" id="kt_wrapped_outro_cta_url" name="kt_wrapped_outro_cta_url" value="<?php echo esc_attr((string) $values[Meta::OUTRO_CTA_URL]); ?>" />
			</div>
			<div class="kt-wrapped-field kt-wrapped-field--wide">
				<label><?php esc_html_e('Cover Image', 'kontentainment-wrapped'); ?></label>
				<div class="kt-wrapped-media-control" data-target="kt_wrapped_cover_image_id" data-preview="kt_wrapped_cover_preview">
					<input type="hidden" id="kt_wrapped_cover_image_id" name="kt_wrapped_cover_image_id" value="<?php echo esc_attr((string) $image_id); ?>" />
					<div class="kt-wrapped-media-preview" id="kt_wrapped_cover_preview">
						<?php if ($image_url) : ?>
							<img src="<?php echo esc_url($image_url); ?>" alt="" />
						<?php else : ?>
							<span><?php esc_html_e('No image selected', 'kontentainment-wrapped'); ?></span>
						<?php endif; ?>
					</div>
					<button type="button" class="button button-secondary kt-wrapped-select-media"><?php esc_html_e('Choose Cover Image', 'kontentainment-wrapped'); ?></button>
				</div>
			</div>
			<div class="kt-wrapped-field kt-wrapped-field--wide">
				<div class="kt-wrapped-guidance-box">
					<h4><?php esc_html_e('Suggested Wrapped Flow', 'kontentainment-wrapped'); ?></h4>
					<ol>
						<li><?php esc_html_e('Start strong with a cover slide.', 'kontentainment-wrapped'); ?></li>
						<li><?php esc_html_e('Hook early with a big number.', 'kontentainment-wrapped'); ?></li>
						<li><?php esc_html_e('Add variety in the middle with ranking, spotlight, quote, or mosaic slides.', 'kontentainment-wrapped'); ?></li>
						<li><?php esc_html_e('Finish with a clear final share moment.', 'kontentainment-wrapped'); ?></li>
					</ol>
				</div>
			</div>
		</div>
		<?php
	}

	public function render_slides(\WP_Post $post): void
	{
		$slides = get_post_meta($post->ID, Meta::SLIDES, true);
		$slides = is_array($slides) ? $slides : array();
		usort(
			$slides,
			static function (array $a, array $b): int {
				return (int) ($a['order_index'] ?? 0) <=> (int) ($b['order_index'] ?? 0);
			}
		);
		?>
		<div class="kt-wrapped-slides-shell">
			<div class="kt-wrapped-slides-toolbar">
				<div>
					<p><?php esc_html_e('Build the narrative slide-by-slide. Drag cards to reorder the story flow.', 'kontentainment-wrapped'); ?></p>
					<p class="description"><?php esc_html_e('Write short, punchy copy. Favor visual hierarchy over long explanations.', 'kontentainment-wrapped'); ?></p>
				</div>
				<button type="button" class="button button-primary" id="kt-wrapped-add-slide"><?php esc_html_e('Add Slide', 'kontentainment-wrapped'); ?></button>
			</div>

			<div class="kt-wrapped-slides-list" id="kt-wrapped-slides-list">
				<?php
				if (empty($slides)) {
					$this->render_slide_card(SlideSchema::default_slide(0), 0);
				} else {
					foreach ($slides as $index => $slide) {
						$this->render_slide_card((array) $slide, (int) $index);
					}
				}
				?>
			</div>
		</div>

		<script type="text/html" id="tmpl-kt-wrapped-slide-card">
			<?php
			$placeholder = SlideSchema::default_slide(999);
			$this->render_slide_card($placeholder, 999, true);
			?>
		</script>
		<?php
	}

	private function render_slide_card(array $slide, int $index, bool $is_template = false): void
	{
		$type             = (string) ($slide['type'] ?? 'cover');
		$slide            = wp_parse_args($slide, SlideSchema::default_slide($index));
		$slide['config']  = wp_parse_args((array) ($slide['config'] ?? array()), SlideSchema::default_config($type));
		$image_id         = absint($slide['background_image_id'] ?? 0);
		$image_url        = $image_id ? wp_get_attachment_image_url($image_id, 'medium') : '';
		$key              = $is_template ? '{{INDEX}}' : (string) $index;
		$guidance         = SlideSchema::authoring_guidance();
		$content_defaults = SlideSchema::default_slide_content($type);
		$summary          = $slide['subtitle'] ?: $slide['body_text'] ?: __('Add a short supporting line.', 'kontentainment-wrapped');
		$warnings         = SlideSchema::validate_slide($slide);
		?>
		<div class="kt-wrapped-slide-card<?php echo 0 === $index ? ' is-expanded' : ' is-collapsed'; ?>" data-slide-index="<?php echo esc_attr($key); ?>">
			<div class="kt-wrapped-slide-card__handle" title="<?php esc_attr_e('Drag to reorder', 'kontentainment-wrapped'); ?>">::</div>
			<div class="kt-wrapped-slide-card__header">
				<button type="button" class="kt-wrapped-slide-toggle" aria-expanded="<?php echo 0 === $index ? 'true' : 'false'; ?>">
					<div class="kt-wrapped-slide-card__title-group">
						<div class="kt-wrapped-slide-card__eyebrow">
							<span class="kt-wrapped-slide-number-badge"><?php echo esc_html((string) ($index + 1)); ?></span>
							<span class="kt-wrapped-slide-type-badge"><?php echo esc_html(SlideSchema::supported_types()[$type] ?? ucfirst($type)); ?></span>
							<?php if (! empty($slide['is_active'])) : ?>
								<span class="kt-wrapped-slide-status"><?php esc_html_e('Active', 'kontentainment-wrapped'); ?></span>
							<?php endif; ?>
							<?php if (! empty($warnings)) : ?>
								<span class="kt-wrapped-slide-status kt-wrapped-slide-status--warning"><?php esc_html_e('Needs Attention', 'kontentainment-wrapped'); ?></span>
							<?php endif; ?>
						</div>
						<h3 class="kt-wrapped-slide-card__title"><?php echo esc_html($slide['title'] ?: __('Untitled Slide', 'kontentainment-wrapped')); ?></h3>
						<p class="kt-wrapped-slide-card__summary"><?php echo esc_html(wp_trim_words(wp_strip_all_tags((string) $summary), 14, '…')); ?></p>
					</div>
					<span class="kt-wrapped-slide-toggle__icon" aria-hidden="true"></span>
				</button>
				<div class="kt-wrapped-slide-card__tools">
					<p class="description kt-wrapped-slide-card__guidance"><?php echo esc_html($guidance[$type] ?? ''); ?></p>
				</div>
				<div class="kt-wrapped-slide-actions">
					<button type="button" class="button-link-delete kt-wrapped-duplicate-slide"><?php esc_html_e('Duplicate', 'kontentainment-wrapped'); ?></button>
					<button type="button" class="button-link-delete kt-wrapped-remove-slide"><?php esc_html_e('Remove', 'kontentainment-wrapped'); ?></button>
				</div>
			</div>

			<input type="hidden" class="kt-wrapped-order-index" name="kt_wrapped_slides[<?php echo esc_attr($key); ?>][order_index]" value="<?php echo esc_attr((string) ($slide['order_index'] ?? $index)); ?>" />
			<input type="hidden" name="kt_wrapped_slides[<?php echo esc_attr($key); ?>][id]" value="<?php echo esc_attr((string) ($slide['id'] ?? '')); ?>" />

			<div class="kt-wrapped-slide-card__body">
				<div class="kt-wrapped-slide-section">
					<div class="kt-wrapped-slide-section__header">
						<h4><?php esc_html_e('Quick Mode', 'kontentainment-wrapped'); ?></h4>
						<p class="description"><?php esc_html_e('Fill the essentials first. Everything else is optional.', 'kontentainment-wrapped'); ?></p>
					</div>
					<div class="kt-wrapped-admin-grid">
						<div class="kt-wrapped-field">
							<label><?php esc_html_e('Slide Type', 'kontentainment-wrapped'); ?></label>
							<select class="kt-wrapped-slide-type" name="kt_wrapped_slides[<?php echo esc_attr($key); ?>][type]">
								<?php foreach (SlideSchema::supported_types() as $type_value => $type_label) : ?>
									<option value="<?php echo esc_attr($type_value); ?>" <?php selected($type, $type_value); ?>><?php echo esc_html($type_label); ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<div class="kt-wrapped-field">
							<label><?php esc_html_e('Background Image', 'kontentainment-wrapped'); ?></label>
							<div class="kt-wrapped-media-control" data-target="kt_wrapped_background_<?php echo esc_attr($key); ?>" data-preview="kt_wrapped_background_preview_<?php echo esc_attr($key); ?>">
								<input type="hidden" id="kt_wrapped_background_<?php echo esc_attr($key); ?>" name="kt_wrapped_slides[<?php echo esc_attr($key); ?>][background_image_id]" value="<?php echo esc_attr((string) $image_id); ?>" />
								<div class="kt-wrapped-media-preview" id="kt_wrapped_background_preview_<?php echo esc_attr($key); ?>">
									<?php if ($image_url) : ?>
										<img src="<?php echo esc_url($image_url); ?>" alt="" />
									<?php else : ?>
										<span><?php esc_html_e('No image selected', 'kontentainment-wrapped'); ?></span>
									<?php endif; ?>
								</div>
								<button type="button" class="button button-secondary kt-wrapped-select-media"><?php esc_html_e('Choose Image', 'kontentainment-wrapped'); ?></button>
							</div>
						</div>
						<div class="kt-wrapped-field kt-wrapped-field--wide">
							<label><?php esc_html_e('Title', 'kontentainment-wrapped'); ?></label>
							<input class="kt-wrapped-primary-title" type="text" name="kt_wrapped_slides[<?php echo esc_attr($key); ?>][title]" value="<?php echo esc_attr((string) ($slide['title'] ?: ($content_defaults['title'] ?? ''))); ?>" />
							<p class="description"><?php esc_html_e('Main headline. Keep it short and high-impact.', 'kontentainment-wrapped'); ?></p>
						</div>
						<div class="kt-wrapped-field kt-wrapped-field--wide">
							<label><?php esc_html_e('Subtitle', 'kontentainment-wrapped'); ?></label>
							<input class="kt-wrapped-primary-subtitle" type="text" name="kt_wrapped_slides[<?php echo esc_attr($key); ?>][subtitle]" value="<?php echo esc_attr((string) ($slide['subtitle'] ?: ($content_defaults['subtitle'] ?? ''))); ?>" />
							<p class="description"><?php esc_html_e('Supporting line. Aim for one short thought.', 'kontentainment-wrapped'); ?></p>
						</div>
						<div class="kt-wrapped-field kt-wrapped-field--wide">
							<label><?php esc_html_e('Body Text', 'kontentainment-wrapped'); ?></label>
							<textarea class="kt-wrapped-primary-body" rows="3" name="kt_wrapped_slides[<?php echo esc_attr($key); ?>][body_text]"><?php echo esc_textarea((string) ($slide['body_text'] ?: ($content_defaults['body_text'] ?? ''))); ?></textarea>
							<p class="description"><?php esc_html_e('Optional. Use only when the slide needs context.', 'kontentainment-wrapped'); ?></p>
						</div>
					</div>

					<div class="kt-wrapped-slide-config kt-wrapped-slide-config--quick">
						<?php foreach (array_keys(SlideSchema::supported_types()) as $panel_type) : ?>
							<div class="kt-wrapped-slide-config-panel<?php echo $panel_type === $type ? ' is-active' : ''; ?>" data-slide-type-panel="<?php echo esc_attr($panel_type); ?>">
								<?php $this->render_config_fields($panel_type, wp_parse_args((array) $slide['config'], SlideSchema::default_config($panel_type)), $key); ?>
							</div>
						<?php endforeach; ?>
					</div>
				</div>

				<div class="kt-wrapped-slide-advanced">
					<button type="button" class="kt-wrapped-slide-advanced__toggle" aria-expanded="false">
						<span><?php esc_html_e('Advanced Settings', 'kontentainment-wrapped'); ?></span>
						<span class="kt-wrapped-slide-toggle__icon" aria-hidden="true"></span>
					</button>
					<div class="kt-wrapped-slide-advanced__panel">
						<div class="kt-wrapped-admin-grid">
							<div class="kt-wrapped-field">
								<label><?php esc_html_e('Internal Name', 'kontentainment-wrapped'); ?></label>
								<input type="text" name="kt_wrapped_slides[<?php echo esc_attr($key); ?>][internal_name]" value="<?php echo esc_attr((string) ($slide['internal_name'] ?? '')); ?>" />
								<p class="description"><?php esc_html_e('Admin-only label for organization.', 'kontentainment-wrapped'); ?></p>
							</div>
							<div class="kt-wrapped-field">
								<label><?php esc_html_e('Text Alignment', 'kontentainment-wrapped'); ?></label>
								<select name="kt_wrapped_slides[<?php echo esc_attr($key); ?>][text_alignment]">
									<option value="left" <?php selected($slide['text_alignment'] ?? 'left', 'left'); ?>><?php esc_html_e('Left', 'kontentainment-wrapped'); ?></option>
									<option value="center" <?php selected($slide['text_alignment'] ?? 'left', 'center'); ?>><?php esc_html_e('Center', 'kontentainment-wrapped'); ?></option>
									<option value="right" <?php selected($slide['text_alignment'] ?? 'left', 'right'); ?>><?php esc_html_e('Right', 'kontentainment-wrapped'); ?></option>
								</select>
							</div>
							<div class="kt-wrapped-field">
								<label><?php esc_html_e('Overlay Strength', 'kontentainment-wrapped'); ?></label>
								<select name="kt_wrapped_slides[<?php echo esc_attr($key); ?>][overlay_strength]">
									<option value="low" <?php selected($slide['overlay_strength'] ?? 'medium', 'low'); ?>><?php esc_html_e('Low', 'kontentainment-wrapped'); ?></option>
									<option value="medium" <?php selected($slide['overlay_strength'] ?? 'medium', 'medium'); ?>><?php esc_html_e('Medium', 'kontentainment-wrapped'); ?></option>
									<option value="high" <?php selected($slide['overlay_strength'] ?? 'medium', 'high'); ?>><?php esc_html_e('High', 'kontentainment-wrapped'); ?></option>
								</select>
							</div>
							<div class="kt-wrapped-field">
								<label><?php esc_html_e('Publishing', 'kontentainment-wrapped'); ?></label>
								<label><input type="checkbox" name="kt_wrapped_slides[<?php echo esc_attr($key); ?>][is_active]" value="1" <?php checked(! empty($slide['is_active'])); ?> /> <?php esc_html_e('Active', 'kontentainment-wrapped'); ?></label>
								<label><input type="checkbox" name="kt_wrapped_slides[<?php echo esc_attr($key); ?>][share_enabled]" value="1" <?php checked(! empty($slide['share_enabled'])); ?> /> <?php esc_html_e('Share Enabled', 'kontentainment-wrapped'); ?></label>
								<label><input type="checkbox" name="kt_wrapped_slides[<?php echo esc_attr($key); ?>][export_enabled]" value="1" <?php checked(! empty($slide['export_enabled'])); ?> /> <?php esc_html_e('Export Enabled', 'kontentainment-wrapped'); ?></label>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	private function render_config_fields(string $type, array $config, string $key): void
	{
		switch ($type) {
			case 'cover':
				$this->render_text_config_field($key, 'cta_label', __('Start CTA Label', 'kontentainment-wrapped'), (string) ($config['cta_label'] ?? ''), __('Example: Start Story', 'kontentainment-wrapped'));
				break;
			case 'big_number':
				$this->render_text_config_field($key, 'label', __('Metric Label', 'kontentainment-wrapped'), (string) ($config['label'] ?? ''), __('Short label above the number.', 'kontentainment-wrapped'));
				$this->render_text_config_field($key, 'suffix', __('Suffix', 'kontentainment-wrapped'), (string) ($config['suffix'] ?? ''), __('Optional: +, %, hrs', 'kontentainment-wrapped'));
				$this->render_text_config_field($key, 'supporting_line', __('Supporting Line', 'kontentainment-wrapped'), (string) ($config['supporting_line'] ?? ''), __('Optional short line to add context.', 'kontentainment-wrapped'));
				break;
			case 'ranking_list':
				$this->render_repeatable_items($key, 'items', __('Ranked Items', 'kontentainment-wrapped'), (array) ($config['items'] ?? array()), array('title' => __('Title', 'kontentainment-wrapped'), 'subtitle' => __('Subtitle', 'kontentainment-wrapped'), 'stat' => __('Stat', 'kontentainment-wrapped')));
				break;
			case 'spotlight':
				$this->render_text_config_field($key, 'kicker', __('Kicker', 'kontentainment-wrapped'), (string) ($config['kicker'] ?? ''), __('Short overline above the title.', 'kontentainment-wrapped'));
				$this->render_text_config_field($key, 'highlight_stat', __('Highlight Stat', 'kontentainment-wrapped'), (string) ($config['highlight_stat'] ?? ''), __('Optional short badge or standout metric.', 'kontentainment-wrapped'));
				break;
			case 'quote':
				$this->render_text_config_field($key, 'kicker', __('Kicker', 'kontentainment-wrapped'), (string) ($config['kicker'] ?? ''), __('Example: Editorial, Audience Mood', 'kontentainment-wrapped'));
				$this->render_text_config_field($key, 'author', __('Author / Source', 'kontentainment-wrapped'), (string) ($config['author'] ?? ''), __('Optional attribution.', 'kontentainment-wrapped'));
				break;
			case 'mosaic':
				$this->render_repeatable_items($key, 'items', __('Mosaic Cards', 'kontentainment-wrapped'), (array) ($config['items'] ?? array()), array('title' => __('Card Title', 'kontentainment-wrapped'), 'label' => __('Card Label', 'kontentainment-wrapped')));
				break;
			case 'music_top_cards':
				$this->render_text_config_field($key, 'section_title', __('Section Title', 'kontentainment-wrapped'), (string) ($config['section_title'] ?? ''), __('Short section label above the cards.', 'kontentainment-wrapped'));
				$this->render_text_config_field($key, 'subtitle', __('Section Subtitle', 'kontentainment-wrapped'), (string) ($config['subtitle'] ?? ''), __('Optional supporting line for the trio.', 'kontentainment-wrapped'));
				$this->render_music_top_cards($key, (array) ($config['cards'] ?? array()));
				break;
			case 'music_chart_week':
				$this->render_text_config_field($key, 'chart_title', __('Chart Title', 'kontentainment-wrapped'), (string) ($config['chart_title'] ?? ''), __('Example: Top Tracks of the Week', 'kontentainment-wrapped'));
				$this->render_text_config_field($key, 'date_range', __('Date Range', 'kontentainment-wrapped'), (string) ($config['date_range'] ?? ''), __('Example: March 18 — March 24', 'kontentainment-wrapped'));
				$this->render_music_chart_week($key, (array) ($config['items'] ?? array()));
				break;
			case 'music_top_grid':
				$this->render_text_config_field($key, 'grid_title', __('Grid Title', 'kontentainment-wrapped'), (string) ($config['grid_title'] ?? ''), __('Use a clean headline for the list.', 'kontentainment-wrapped'));
				$this->render_text_config_field($key, 'subtitle', __('Grid Subtitle', 'kontentainment-wrapped'), (string) ($config['subtitle'] ?? ''), __('Optional line to frame the top list.', 'kontentainment-wrapped'));
				$this->render_music_top_grid($key, (array) ($config['items'] ?? array()));
				break;
			case 'music_spotlight':
				$this->render_text_config_field($key, 'kicker', __('Kicker', 'kontentainment-wrapped'), (string) ($config['kicker'] ?? ''), __('Example: Album Spotlight, Track of the Week', 'kontentainment-wrapped'));
				$this->render_music_spotlight_media($key, absint($config['image_id'] ?? 0));
				$this->render_text_config_field($key, 'highlight_stat', __('Highlight Stat', 'kontentainment-wrapped'), (string) ($config['highlight_stat'] ?? ''), __('Optional badge like 12 weeks charting or #1 this weekend.', 'kontentainment-wrapped'));
				$this->render_textarea_config_field($key, 'description', __('Short Description', 'kontentainment-wrapped'), (string) ($config['description'] ?? ''), __('One tight paragraph is enough.', 'kontentainment-wrapped'));
				break;
			case 'final_share':
				$this->render_text_config_field($key, 'replay_label', __('Replay Label', 'kontentainment-wrapped'), (string) ($config['replay_label'] ?? ''), __('Example: Replay Story', 'kontentainment-wrapped'));
				break;
		}
	}

	private function render_text_config_field(string $key, string $field, string $label, string $value, string $help): void
	{
		?>
		<div class="kt-wrapped-field">
			<label><?php echo esc_html($label); ?></label>
			<input type="text" name="kt_wrapped_slides[<?php echo esc_attr($key); ?>][config][<?php echo esc_attr($field); ?>]" value="<?php echo esc_attr($value); ?>" />
			<p class="description"><?php echo esc_html($help); ?></p>
		</div>
		<?php
	}

	private function render_textarea_config_field(string $key, string $field, string $label, string $value, string $help): void
	{
		?>
		<div class="kt-wrapped-field kt-wrapped-field--wide">
			<label><?php echo esc_html($label); ?></label>
			<textarea rows="3" name="kt_wrapped_slides[<?php echo esc_attr($key); ?>][config][<?php echo esc_attr($field); ?>]"><?php echo esc_textarea($value); ?></textarea>
			<p class="description"><?php echo esc_html($help); ?></p>
		</div>
		<?php
	}

	private function render_repeatable_items(string $key, string $field, string $label, array $items, array $columns): void
	{
		if (empty($items)) {
			$items = array(array_fill_keys(array_keys($columns), ''));
		}
		?>
		<div class="kt-wrapped-field kt-wrapped-field--wide">
			<label><?php echo esc_html($label); ?></label>
			<div class="kt-wrapped-repeatable-list">
				<?php foreach ($items as $item_index => $item) : ?>
					<div class="kt-wrapped-repeatable-row">
						<?php foreach ($columns as $column_key => $column_label) : ?>
							<input type="text" name="kt_wrapped_slides[<?php echo esc_attr($key); ?>][config][<?php echo esc_attr($field); ?>][<?php echo esc_attr((string) $item_index); ?>][<?php echo esc_attr($column_key); ?>]" value="<?php echo esc_attr((string) ($item[$column_key] ?? '')); ?>" placeholder="<?php echo esc_attr($column_label); ?>" />
						<?php endforeach; ?>
					</div>
				<?php endforeach; ?>
			</div>
			<p class="description"><?php esc_html_e('Keep each item brief and easy to scan on mobile.', 'kontentainment-wrapped'); ?></p>
		</div>
		<?php
	}

	private function render_music_top_cards(string $key, array $cards): void
	{
		if (empty($cards)) {
			$cards = SlideSchema::default_config('music_top_cards')['cards'];
		}
		?>
		<div class="kt-wrapped-field kt-wrapped-field--wide">
			<div class="kt-wrapped-collection-header">
				<label><?php esc_html_e('Featured Cards', 'kontentainment-wrapped'); ?></label>
				<button
					type="button"
					class="button button-secondary kt-wrapped-add-collection-item"
					data-kt-collection-add
					data-kt-collection-type="music-top-card"
					data-kt-collection-target="kt_wrapped_music_top_cards_list_<?php echo esc_attr($key); ?>"
				>
					<?php esc_html_e('Add Card', 'kontentainment-wrapped'); ?>
				</button>
			</div>
			<div class="kt-wrapped-music-list" id="kt_wrapped_music_top_cards_list_<?php echo esc_attr($key); ?>" data-kt-collection="music-top-card">
				<?php foreach ($cards as $item_index => $card) : ?>
					<?php $this->render_music_top_card_item($key, $item_index, $card); ?>
				<?php endforeach; ?>
			</div>
			<script type="text/html" id="tmpl-kt-wrapped-music-top-card-<?php echo esc_attr($key); ?>">
				<?php $this->render_music_top_card_item($key, 999, array(), true); ?>
			</script>
			<p class="description"><?php esc_html_e('Add as many cards as you need. The viewer will present them as a swipeable story carousel.', 'kontentainment-wrapped'); ?></p>
		</div>
		<?php
	}

	private function render_music_chart_week(string $key, array $items): void
	{
		if (empty($items)) {
			$items = SlideSchema::default_config('music_chart_week')['items'];
		}
		?>
		<div class="kt-wrapped-field kt-wrapped-field--wide">
			<label><?php esc_html_e('Chart Rows', 'kontentainment-wrapped'); ?></label>
			<div class="kt-wrapped-music-list">
				<?php foreach ($items as $item_index => $item) : ?>
					<?php
					$image_id  = absint($item['image_id'] ?? 0);
					$image_url = $image_id ? wp_get_attachment_image_url($image_id, 'medium') : '';
					$target_id = 'kt_wrapped_music_chart_' . $key . '_' . $item_index;
					$preview_id = 'kt_wrapped_music_chart_preview_' . $key . '_' . $item_index;
					?>
					<div class="kt-wrapped-music-item-card">
						<div class="kt-wrapped-media-control" data-target="<?php echo esc_attr($target_id); ?>" data-preview="<?php echo esc_attr($preview_id); ?>">
							<input type="hidden" id="<?php echo esc_attr($target_id); ?>" name="kt_wrapped_slides[<?php echo esc_attr($key); ?>][config][items][<?php echo esc_attr((string) $item_index); ?>][image_id]" value="<?php echo esc_attr((string) $image_id); ?>" />
							<div class="kt-wrapped-media-preview kt-wrapped-media-preview--compact" id="<?php echo esc_attr($preview_id); ?>">
								<?php if ($image_url) : ?>
									<img src="<?php echo esc_url($image_url); ?>" alt="" />
								<?php else : ?>
									<span><?php esc_html_e('Cover', 'kontentainment-wrapped'); ?></span>
								<?php endif; ?>
							</div>
							<button type="button" class="button button-secondary kt-wrapped-select-media"><?php esc_html_e('Choose Cover', 'kontentainment-wrapped'); ?></button>
						</div>
						<div class="kt-wrapped-repeatable-row kt-wrapped-repeatable-row--music-chart">
							<input type="text" name="kt_wrapped_slides[<?php echo esc_attr($key); ?>][config][items][<?php echo esc_attr((string) $item_index); ?>][title]" value="<?php echo esc_attr((string) ($item['title'] ?? '')); ?>" placeholder="<?php esc_attr_e('Title', 'kontentainment-wrapped'); ?>" />
							<input type="text" name="kt_wrapped_slides[<?php echo esc_attr($key); ?>][config][items][<?php echo esc_attr((string) $item_index); ?>][subtitle]" value="<?php echo esc_attr((string) ($item['subtitle'] ?? '')); ?>" placeholder="<?php esc_attr_e('Artist / Subtitle', 'kontentainment-wrapped'); ?>" />
							<select name="kt_wrapped_slides[<?php echo esc_attr($key); ?>][config][items][<?php echo esc_attr((string) $item_index); ?>][trend_type]">
								<?php foreach ($this->trend_options() as $value => $label) : ?>
									<option value="<?php echo esc_attr($value); ?>" <?php selected((string) ($item['trend_type'] ?? 'same'), $value); ?>><?php echo esc_html($label); ?></option>
								<?php endforeach; ?>
							</select>
							<input type="text" name="kt_wrapped_slides[<?php echo esc_attr($key); ?>][config][items][<?php echo esc_attr((string) $item_index); ?>][trend_value]" value="<?php echo esc_attr((string) ($item['trend_value'] ?? '')); ?>" placeholder="<?php esc_attr_e('Trend Value', 'kontentainment-wrapped'); ?>" />
						</div>
					</div>
				<?php endforeach; ?>
			</div>
			<p class="description"><?php esc_html_e('This slide is designed as a Top 10 board. Keep all ten rows populated for the strongest result.', 'kontentainment-wrapped'); ?></p>
		</div>
		<?php
	}

	private function render_music_top_card_item(string $key, int $item_index, array $card, bool $is_template = false): void
	{
		$image_id   = absint($card['image_id'] ?? 0);
		$image_url  = $image_id ? wp_get_attachment_image_url($image_id, 'medium') : '';
		$row_index  = $is_template ? '{{ITEM_INDEX}}' : (string) $item_index;
		$target_id  = 'kt_wrapped_music_top_cards_' . $key . '_' . $row_index;
		$preview_id = 'kt_wrapped_music_top_cards_preview_' . $key . '_' . $row_index;
		?>
		<div class="kt-wrapped-music-item-card" data-kt-collection-item>
			<div class="kt-wrapped-music-item-card__header">
				<strong><?php esc_html_e('Music Card', 'kontentainment-wrapped'); ?></strong>
				<button type="button" class="button-link-delete kt-wrapped-remove-collection-item" data-kt-collection-remove><?php esc_html_e('Remove', 'kontentainment-wrapped'); ?></button>
			</div>
			<div class="kt-wrapped-media-control" data-target="<?php echo esc_attr($target_id); ?>" data-preview="<?php echo esc_attr($preview_id); ?>">
				<input type="hidden" id="<?php echo esc_attr($target_id); ?>" name="kt_wrapped_slides[<?php echo esc_attr($key); ?>][config][cards][<?php echo esc_attr($row_index); ?>][image_id]" value="<?php echo esc_attr((string) $image_id); ?>" />
				<div class="kt-wrapped-media-preview kt-wrapped-media-preview--compact" id="<?php echo esc_attr($preview_id); ?>">
					<?php if ($image_url) : ?>
						<img src="<?php echo esc_url($image_url); ?>" alt="" />
					<?php else : ?>
						<span><?php esc_html_e('Cover', 'kontentainment-wrapped'); ?></span>
					<?php endif; ?>
				</div>
				<button type="button" class="button button-secondary kt-wrapped-select-media"><?php esc_html_e('Choose Cover', 'kontentainment-wrapped'); ?></button>
			</div>
			<div class="kt-wrapped-repeatable-row kt-wrapped-repeatable-row--music">
				<input type="text" name="kt_wrapped_slides[<?php echo esc_attr($key); ?>][config][cards][<?php echo esc_attr($row_index); ?>][track_title]" value="<?php echo esc_attr((string) ($card['track_title'] ?? '')); ?>" placeholder="<?php esc_attr_e('Track Title', 'kontentainment-wrapped'); ?>" />
				<input type="text" name="kt_wrapped_slides[<?php echo esc_attr($key); ?>][config][cards][<?php echo esc_attr($row_index); ?>][artist_name]" value="<?php echo esc_attr((string) ($card['artist_name'] ?? '')); ?>" placeholder="<?php esc_attr_e('Artist Name', 'kontentainment-wrapped'); ?>" />
				<input type="url" name="kt_wrapped_slides[<?php echo esc_attr($key); ?>][config][cards][<?php echo esc_attr($row_index); ?>][link]" value="<?php echo esc_attr((string) ($card['link'] ?? '')); ?>" placeholder="<?php esc_attr_e('Optional Link', 'kontentainment-wrapped'); ?>" />
				<input type="text" name="kt_wrapped_slides[<?php echo esc_attr($key); ?>][config][cards][<?php echo esc_attr($row_index); ?>][badge]" value="<?php echo esc_attr((string) ($card['badge'] ?? '')); ?>" placeholder="<?php esc_attr_e('Badge', 'kontentainment-wrapped'); ?>" />
			</div>
		</div>
		<?php
	}

	private function render_music_top_grid(string $key, array $items): void
	{
		if (empty($items)) {
			$items = SlideSchema::default_config('music_top_grid')['items'];
		}
		?>
		<div class="kt-wrapped-field kt-wrapped-field--wide">
			<label><?php esc_html_e('Grid Items', 'kontentainment-wrapped'); ?></label>
			<div class="kt-wrapped-music-list">
				<?php foreach ($items as $item_index => $item) : ?>
					<?php
					$image_id  = absint($item['image_id'] ?? 0);
					$image_url = $image_id ? wp_get_attachment_image_url($image_id, 'medium') : '';
					$target_id = 'kt_wrapped_music_grid_' . $key . '_' . $item_index;
					$preview_id = 'kt_wrapped_music_grid_preview_' . $key . '_' . $item_index;
					?>
					<div class="kt-wrapped-music-item-card">
						<div class="kt-wrapped-media-control" data-target="<?php echo esc_attr($target_id); ?>" data-preview="<?php echo esc_attr($preview_id); ?>">
							<input type="hidden" id="<?php echo esc_attr($target_id); ?>" name="kt_wrapped_slides[<?php echo esc_attr($key); ?>][config][items][<?php echo esc_attr((string) $item_index); ?>][image_id]" value="<?php echo esc_attr((string) $image_id); ?>" />
							<div class="kt-wrapped-media-preview kt-wrapped-media-preview--compact" id="<?php echo esc_attr($preview_id); ?>">
								<?php if ($image_url) : ?>
									<img src="<?php echo esc_url($image_url); ?>" alt="" />
								<?php else : ?>
									<span><?php esc_html_e('Image', 'kontentainment-wrapped'); ?></span>
								<?php endif; ?>
							</div>
							<button type="button" class="button button-secondary kt-wrapped-select-media"><?php esc_html_e('Choose Image', 'kontentainment-wrapped'); ?></button>
						</div>
						<div class="kt-wrapped-repeatable-row kt-wrapped-repeatable-row--music">
							<input type="text" name="kt_wrapped_slides[<?php echo esc_attr($key); ?>][config][items][<?php echo esc_attr((string) $item_index); ?>][title]" value="<?php echo esc_attr((string) ($item['title'] ?? '')); ?>" placeholder="<?php esc_attr_e('Title', 'kontentainment-wrapped'); ?>" />
							<input type="text" name="kt_wrapped_slides[<?php echo esc_attr($key); ?>][config][items][<?php echo esc_attr((string) $item_index); ?>][subtitle]" value="<?php echo esc_attr((string) ($item['subtitle'] ?? '')); ?>" placeholder="<?php esc_attr_e('Subtitle', 'kontentainment-wrapped'); ?>" />
						</div>
					</div>
				<?php endforeach; ?>
			</div>
			<p class="description"><?php esc_html_e('This layout works best when every item can be understood in two quick lines.', 'kontentainment-wrapped'); ?></p>
		</div>
		<?php
	}

	private function render_music_spotlight_media(string $key, int $image_id): void
	{
		$image_url = $image_id ? wp_get_attachment_image_url($image_id, 'medium') : '';
		$target_id = 'kt_wrapped_music_spotlight_' . $key;
		$preview_id = 'kt_wrapped_music_spotlight_preview_' . $key;
		?>
		<div class="kt-wrapped-field">
			<label><?php esc_html_e('Feature Image', 'kontentainment-wrapped'); ?></label>
			<div class="kt-wrapped-media-control" data-target="<?php echo esc_attr($target_id); ?>" data-preview="<?php echo esc_attr($preview_id); ?>">
				<input type="hidden" id="<?php echo esc_attr($target_id); ?>" name="kt_wrapped_slides[<?php echo esc_attr($key); ?>][config][image_id]" value="<?php echo esc_attr((string) $image_id); ?>" />
				<div class="kt-wrapped-media-preview kt-wrapped-media-preview--compact" id="<?php echo esc_attr($preview_id); ?>">
					<?php if ($image_url) : ?>
						<img src="<?php echo esc_url($image_url); ?>" alt="" />
					<?php else : ?>
						<span><?php esc_html_e('Feature Image', 'kontentainment-wrapped'); ?></span>
					<?php endif; ?>
				</div>
				<button type="button" class="button button-secondary kt-wrapped-select-media"><?php esc_html_e('Choose Image', 'kontentainment-wrapped'); ?></button>
			</div>
		</div>
		<?php
	}

	private function trend_options(): array
	{
		return array(
			'new'  => __('New', 'kontentainment-wrapped'),
			'up'   => __('Up', 'kontentainment-wrapped'),
			'down' => __('Down', 'kontentainment-wrapped'),
			'same' => __('Same', 'kontentainment-wrapped'),
		);
	}

	private function theme_presets(): array
	{
		return array(
			'editorial-noir' => __('Editorial Noir', 'kontentainment-wrapped'),
			'electric-flare' => __('Electric Flare', 'kontentainment-wrapped'),
			'sunset-drive'   => __('Sunset Drive', 'kontentainment-wrapped'),
			'acid-pop'       => __('Acid Pop', 'kontentainment-wrapped'),
		);
	}
}
