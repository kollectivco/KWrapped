<?php
declare(strict_types=1);

namespace KontentainmentWrapped\Core;

use KontentainmentWrapped\Wrapped\SlideSchema;

final class Assets
{
	public function register(): void
	{
		add_action('admin_enqueue_scripts', array($this, 'enqueue_admin'));
		add_action('wp_enqueue_scripts', array($this, 'enqueue_public'));
	}

	public function enqueue_admin(string $hook): void
	{
		global $post_type;

		if ('kt_wrapped' !== $post_type) {
			return;
		}

		wp_enqueue_media();
		wp_enqueue_style(
			'kt-wrapped-admin',
			KT_WRAPPED_URL . 'assets/admin/css/admin.css',
			array(),
			KT_WRAPPED_VERSION
		);
		wp_enqueue_script(
			'kt-wrapped-admin',
			KT_WRAPPED_URL . 'assets/admin/js/admin.js',
			array('jquery', 'jquery-ui-sortable'),
			KT_WRAPPED_VERSION,
			true
		);

		wp_localize_script(
			'kt-wrapped-admin',
			'ktWrappedAdmin',
			array(
				'mediaTitle'      => __('Choose image', 'kontentainment-wrapped'),
				'mediaButton'     => __('Use image', 'kontentainment-wrapped'),
				'slideDefaults'   => array(
					'content' => array(
						'cover'        => SlideSchema::default_slide_content('cover'),
						'big_number'   => SlideSchema::default_slide_content('big_number'),
						'ranking_list' => SlideSchema::default_slide_content('ranking_list'),
						'spotlight'    => SlideSchema::default_slide_content('spotlight'),
						'quote'        => SlideSchema::default_slide_content('quote'),
						'mosaic'       => SlideSchema::default_slide_content('mosaic'),
						'final_share'  => SlideSchema::default_slide_content('final_share'),
					),
					'guidance' => SlideSchema::authoring_guidance(),
				),
			)
		);
	}

	public function enqueue_public(): void
	{
		if (! is_singular('kt_wrapped')) {
			return;
		}

		wp_enqueue_style(
			'kt-wrapped-public',
			KT_WRAPPED_URL . 'assets/public/css/viewer.css',
			array(),
			KT_WRAPPED_VERSION
		);
		wp_enqueue_script(
			'kt-wrapped-public',
			KT_WRAPPED_URL . 'assets/public/js/viewer.js',
			array(),
			KT_WRAPPED_VERSION,
			true
		);

		wp_enqueue_script(
			'kt-wrapped-share',
			KT_WRAPPED_URL . 'assets/public/js/share.js',
			array('kt-wrapped-public'),
			KT_WRAPPED_VERSION,
			true
		);

		wp_localize_script(
			'kt-wrapped-share',
			'ktWrappedViewer',
			array(
				'downloadMessage' => __('Image downloaded. You can upload it to Instagram Stories from your camera roll.', 'kontentainment-wrapped'),
				'shareMessage'    => __('Share is not available here, so we downloaded the image for you instead.', 'kontentainment-wrapped'),
			)
		);
	}
}
