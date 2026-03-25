<?php
declare(strict_types=1);

namespace KontentainmentWrapped\Admin;

use KontentainmentWrapped\Wrapped\Meta;

final class SeedDemo
{
	public function register(): void
	{
		add_action('admin_init', array(__CLASS__, 'create_demo_edition'));
	}

	public static function create_demo_edition(): void
	{
		$existing = get_posts(
			array(
				'post_type'      => 'kt_wrapped',
				'post_status'    => array('publish', 'draft', 'archived'),
				'posts_per_page' => 1,
				'meta_key'       => '_kt_wrapped_seed_demo',
				'meta_value'     => '1',
				'fields'         => 'ids',
			)
		);

		if (! empty($existing)) {
			return;
		}

		$post_id = wp_insert_post(
			array(
				'post_type'   => 'kt_wrapped',
				'post_status' => 'publish',
				'post_title'  => 'Kontentainment Wrapped 2025',
				'post_name'   => 'kontentainment-wrapped-2025',
			)
		);

		if (is_wp_error($post_id) || ! $post_id) {
			return;
		}

		update_post_meta($post_id, '_kt_wrapped_seed_demo', '1');
		update_post_meta($post_id, Meta::SUBTITLE, 'The year we turned campaigns into culture');
		update_post_meta($post_id, Meta::YEAR, '2025');
		update_post_meta($post_id, Meta::THEME, 'electric-flare');
		update_post_meta($post_id, Meta::SHARE_TEXT, 'I just viewed Kontentainment Wrapped.');
		update_post_meta($post_id, Meta::OUTRO_CTA_TEXT, 'Explore the studio');
		update_post_meta($post_id, Meta::OUTRO_CTA_URL, home_url('/'));

		update_post_meta(
			$post_id,
			Meta::SLIDES,
			array(
				array(
					'id'                  => wp_generate_uuid4(),
					'type'                => 'cover',
					'internal_name'       => 'opening-cover',
					'title'               => 'Kontentainment Wrapped',
					'subtitle'            => '2025 in bold, cinematic motion',
					'body_text'           => 'A curated year of campaigns, creators, breakout moments, and audience obsession.',
					'short_caption'       => '',
					'background_image_id' => 0,
					'theme_variant'       => 'flare',
					'text_alignment'      => 'left',
					'overlay_strength'    => 'high',
					'export_enabled'      => true,
					'share_enabled'       => true,
					'is_active'           => true,
					'is_hidden'           => false,
					'order_index'         => 0,
					'config'              => array(
						'cta_label' => 'Start Story',
					),
				),
				array(
					'id'                  => wp_generate_uuid4(),
					'type'                => 'big_number',
					'internal_name'       => 'headline-number',
					'title'               => '128M',
					'subtitle'            => 'total earned impressions',
					'body_text'           => 'One year. Multiple launches. A very online audience.',
					'short_caption'       => '',
					'background_image_id' => 0,
					'theme_variant'       => 'pulse',
					'text_alignment'      => 'left',
					'overlay_strength'    => 'medium',
					'export_enabled'      => true,
					'share_enabled'       => true,
					'is_active'           => true,
					'is_hidden'           => false,
					'order_index'         => 1,
					'config'              => array(
						'label'  => 'Audience Reach',
						'suffix' => '+',
					),
				),
				array(
					'id'                  => wp_generate_uuid4(),
					'type'                => 'ranking_list',
					'internal_name'       => 'top-campaigns',
					'title'               => 'Top 5 Campaigns',
					'subtitle'            => 'The projects that broke through the feed',
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
					'order_index'         => 2,
					'config'              => array(
						'items' => array(
							'1. Midnight Rebrand',
							'2. Summer Screen Takeover',
							'3. Creator Sprint Live',
							'4. Red Room Premiere',
							'5. Future Fandom Launch',
						),
					),
				),
				array(
					'id'                  => wp_generate_uuid4(),
					'type'                => 'final_share',
					'internal_name'       => 'finale',
					'title'               => 'That was your Wrapped',
					'subtitle'            => 'Save a slide, share the story, run it back.',
					'body_text'           => 'Kontentainment turns moments into movement.',
					'short_caption'       => '',
					'background_image_id' => 0,
					'theme_variant'       => 'outro',
					'text_alignment'      => 'left',
					'overlay_strength'    => 'high',
					'export_enabled'      => true,
					'share_enabled'       => true,
					'is_active'           => true,
					'is_hidden'           => false,
					'order_index'         => 3,
					'config'              => array(
						'replay_label' => 'Replay Story',
					),
				),
			)
		);
	}
}
