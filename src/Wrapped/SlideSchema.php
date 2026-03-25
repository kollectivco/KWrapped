<?php
declare(strict_types=1);

namespace KontentainmentWrapped\Wrapped;

final class SlideSchema
{
	public static function supported_types(): array
	{
		return array(
			'cover'        => __('Cover', 'kontentainment-wrapped'),
			'big_number'   => __('Big Number', 'kontentainment-wrapped'),
			'ranking_list' => __('Ranking List', 'kontentainment-wrapped'),
			'spotlight'    => __('Spotlight', 'kontentainment-wrapped'),
			'quote'        => __('Quote', 'kontentainment-wrapped'),
			'mosaic'       => __('Mosaic', 'kontentainment-wrapped'),
			'final_share'  => __('Final Share', 'kontentainment-wrapped'),
		);
	}

	public static function default_slide(int $index = 0): array
	{
		$slide = array(
			'id'                  => wp_generate_uuid4(),
			'type'                => 'cover',
			'internal_name'       => 'slide-' . ($index + 1),
			'title'               => '',
			'subtitle'            => '',
			'body_text'           => '',
			'short_caption'       => '',
			'background_image_id' => 0,
			'theme_variant'       => 'default',
			'text_alignment'      => 'left',
			'overlay_strength'    => 'medium',
			'export_enabled'      => true,
			'share_enabled'       => true,
			'is_active'           => true,
			'is_hidden'           => false,
			'order_index'         => $index,
			'config'              => array(),
		);

		$slide['config'] = self::default_config('cover');

		return $slide;
	}

	public static function default_config(string $type): array
	{
		switch ($type) {
			case 'cover':
				return array(
					'cta_label' => __('Start Story', 'kontentainment-wrapped'),
				);
			case 'big_number':
				return array(
					'label'           => __('Headline Metric', 'kontentainment-wrapped'),
					'suffix'          => '',
					'supporting_line' => __('Set the scale of the story in one line.', 'kontentainment-wrapped'),
				);
			case 'ranking_list':
				return array(
					'items' => array(
						array('title' => __('First standout item', 'kontentainment-wrapped'), 'subtitle' => __('Why it mattered', 'kontentainment-wrapped'), 'stat' => '128M'),
						array('title' => __('Second standout item', 'kontentainment-wrapped'), 'subtitle' => __('A short editorial angle', 'kontentainment-wrapped'), 'stat' => '94M'),
						array('title' => __('Third standout item', 'kontentainment-wrapped'), 'subtitle' => __('Keep it punchy', 'kontentainment-wrapped'), 'stat' => '72M'),
					),
				);
			case 'spotlight':
				return array(
					'highlight_stat' => __('Breakout Moment', 'kontentainment-wrapped'),
					'kicker'         => __('Spotlight', 'kontentainment-wrapped'),
				);
			case 'quote':
				return array(
					'kicker' => __('Editorial', 'kontentainment-wrapped'),
					'author' => __('Source / Context', 'kontentainment-wrapped'),
				);
			case 'mosaic':
				return array(
					'items' => array(
						array('title' => __('Moment One', 'kontentainment-wrapped'), 'label' => __('Launch', 'kontentainment-wrapped')),
						array('title' => __('Moment Two', 'kontentainment-wrapped'), 'label' => __('Creator', 'kontentainment-wrapped')),
						array('title' => __('Moment Three', 'kontentainment-wrapped'), 'label' => __('Audience', 'kontentainment-wrapped')),
						array('title' => __('Moment Four', 'kontentainment-wrapped'), 'label' => __('Impact', 'kontentainment-wrapped')),
					),
				);
			case 'final_share':
				return array(
					'replay_label' => __('Replay Story', 'kontentainment-wrapped'),
				);
			default:
				return array();
		}
	}

	public static function default_slide_content(string $type): array
	{
		switch ($type) {
			case 'cover':
				return array(
					'title'     => __('Your Wrapped Starts Here', 'kontentainment-wrapped'),
					'subtitle'  => __('Set the tone with a bold opening line.', 'kontentainment-wrapped'),
					'body_text' => __('Use one short paragraph to frame the edition and pull the viewer into the story.', 'kontentainment-wrapped'),
				);
			case 'big_number':
				return array(
					'title'     => '128M',
					'subtitle'  => __('Total audience reached', 'kontentainment-wrapped'),
					'body_text' => __('Give the number context in one clean supporting sentence.', 'kontentainment-wrapped'),
				);
			case 'ranking_list':
				return array(
					'title'     => __('Top Performers', 'kontentainment-wrapped'),
					'subtitle'  => __('Rank the moments that defined the year.', 'kontentainment-wrapped'),
					'body_text' => '',
				);
			case 'spotlight':
				return array(
					'title'     => __('The Breakout Star', 'kontentainment-wrapped'),
					'subtitle'  => __('One featured person, campaign, or launch.', 'kontentainment-wrapped'),
					'body_text' => __('Explain why this deserved the spotlight in two short lines.', 'kontentainment-wrapped'),
				);
			case 'quote':
				return array(
					'title'     => __('A short, dramatic editorial line that captures the mood.', 'kontentainment-wrapped'),
					'subtitle'  => __('Optional source or attribution', 'kontentainment-wrapped'),
					'body_text' => '',
				);
			case 'mosaic':
				return array(
					'title'     => __('Highlights Reel', 'kontentainment-wrapped'),
					'subtitle'  => __('Curate a set of standout moments.', 'kontentainment-wrapped'),
					'body_text' => '',
				);
			case 'final_share':
				return array(
					'title'     => __('That was your Wrapped', 'kontentainment-wrapped'),
					'subtitle'  => __('End on a line that feels conclusive and shareable.', 'kontentainment-wrapped'),
					'body_text' => __('Invite the viewer to save, share, or replay.', 'kontentainment-wrapped'),
				);
			default:
				return array(
					'title'     => '',
					'subtitle'  => '',
					'body_text' => '',
				);
		}
	}

	public static function authoring_guidance(): array
	{
		return array(
			'cover'        => __('Open with a sharp headline and one framing thought.', 'kontentainment-wrapped'),
			'big_number'   => __('Lead with one unforgettable number, then explain why it matters.', 'kontentainment-wrapped'),
			'ranking_list' => __('Keep ranked items concise, readable, and visually distinct.', 'kontentainment-wrapped'),
			'spotlight'    => __('Treat this like a hero profile with one standout angle.', 'kontentainment-wrapped'),
			'quote'        => __('Write like an editorial pull quote, not a paragraph.', 'kontentainment-wrapped'),
			'mosaic'       => __('Curate moments that feel varied but connected.', 'kontentainment-wrapped'),
			'final_share'  => __('End with a clear emotional wrap-up and strong call to share or replay.', 'kontentainment-wrapped'),
		);
	}

	public static function normalize_slide(array $slide, int $index): array
	{
		$slide = wp_parse_args($slide, self::default_slide($index));
		$type  = array_key_exists($slide['type'], self::supported_types()) ? $slide['type'] : 'cover';

		return array(
			'id'                  => sanitize_text_field((string) $slide['id']),
			'type'                => sanitize_key($type),
			'internal_name'       => sanitize_text_field((string) $slide['internal_name']),
			'title'               => sanitize_text_field((string) $slide['title']),
			'subtitle'            => sanitize_text_field((string) $slide['subtitle']),
			'body_text'           => wp_kses_post((string) $slide['body_text']),
			'short_caption'       => sanitize_text_field((string) $slide['short_caption']),
			'background_image_id' => absint($slide['background_image_id']),
			'theme_variant'       => sanitize_key((string) $slide['theme_variant']),
			'text_alignment'      => in_array($slide['text_alignment'], array('left', 'center', 'right'), true) ? $slide['text_alignment'] : 'left',
			'overlay_strength'    => in_array($slide['overlay_strength'], array('low', 'medium', 'high'), true) ? $slide['overlay_strength'] : 'medium',
			'export_enabled'      => ! empty($slide['export_enabled']),
			'share_enabled'       => ! empty($slide['share_enabled']),
			'is_active'           => ! empty($slide['is_active']),
			'is_hidden'           => ! empty($slide['is_hidden']),
			'order_index'         => absint($slide['order_index']),
			'config'              => self::sanitize_config((array) $slide['config'], $type),
		);
	}

	public static function validate_slide(array $slide): array
	{
		$errors = array();
		$type   = (string) ($slide['type'] ?? 'cover');

		if (empty($slide['title']) && in_array($type, array('cover', 'big_number', 'ranking_list', 'spotlight', 'quote', 'mosaic', 'final_share'), true)) {
			$errors[] = __('Title is required.', 'kontentainment-wrapped');
		}

		if ('ranking_list' === $type && empty($slide['config']['items'])) {
			$errors[] = __('Add at least one ranked item.', 'kontentainment-wrapped');
		}

		if ('mosaic' === $type && empty($slide['config']['items'])) {
			$errors[] = __('Add at least two mosaic cards.', 'kontentainment-wrapped');
		}

		if ('big_number' === $type && empty($slide['title'])) {
			$errors[] = __('Big Number slides need the headline number in the title field.', 'kontentainment-wrapped');
		}

		if ('spotlight' === $type && empty($slide['body_text'])) {
			$errors[] = __('Spotlight slides should include a short explanation.', 'kontentainment-wrapped');
		}

		return $errors;
	}

	public static function sanitize_config(array $config, string $type): array
	{
		$defaults  = self::default_config($type);
		$sanitized = array();

		switch ($type) {
			case 'ranking_list':
				$items = isset($config['items']) && is_array($config['items']) ? $config['items'] : array();
				$sanitized['items'] = array_values(
					array_filter(
						array_map(
							static function ($item): array {
								$item = is_array($item) ? $item : array();
								return array(
									'title'    => sanitize_text_field((string) ($item['title'] ?? '')),
									'subtitle' => sanitize_text_field((string) ($item['subtitle'] ?? '')),
									'stat'     => sanitize_text_field((string) ($item['stat'] ?? '')),
								);
							},
							$items
						),
						static function (array $item): bool {
							return '' !== $item['title'];
						}
					)
				);
				break;
			case 'mosaic':
				$items = isset($config['items']) && is_array($config['items']) ? $config['items'] : array();
				$sanitized['items'] = array_values(
					array_filter(
						array_map(
							static function ($item): array {
								$item = is_array($item) ? $item : array();
								return array(
									'title' => sanitize_text_field((string) ($item['title'] ?? '')),
									'label' => sanitize_text_field((string) ($item['label'] ?? '')),
								);
							},
							$items
						),
						static function (array $item): bool {
							return '' !== $item['title'];
						}
					)
				);
				break;
			default:
				foreach ($config as $key => $value) {
					$key = sanitize_key((string) $key);
					if (is_array($value)) {
						$sanitized[$key] = array_map('sanitize_text_field', wp_unslash($value));
						continue;
					}

					$sanitized[$key] = sanitize_text_field((string) $value);
				}
				break;
		}

		return wp_parse_args($sanitized, $defaults);
	}
}
