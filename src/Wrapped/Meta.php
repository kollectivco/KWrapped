<?php
declare(strict_types=1);

namespace KontentainmentWrapped\Wrapped;

final class Meta
{
	public const SUBTITLE = '_kt_wrapped_subtitle';
	public const YEAR = '_kt_wrapped_year_season';
	public const THEME = '_kt_wrapped_theme_preset';
	public const COVER_IMAGE_ID = '_kt_wrapped_cover_image_id';
	public const SHARE_TEXT = '_kt_wrapped_share_text';
	public const OUTRO_CTA_TEXT = '_kt_wrapped_outro_cta_text';
	public const OUTRO_CTA_URL = '_kt_wrapped_outro_cta_url';
	public const SLIDES = '_kt_wrapped_slides';

	public static function defaults(): array
	{
		return array(
			self::SUBTITLE       => '',
			self::YEAR           => '',
			self::THEME          => 'editorial-noir',
			self::COVER_IMAGE_ID => 0,
			self::SHARE_TEXT     => '',
			self::OUTRO_CTA_TEXT => '',
			self::OUTRO_CTA_URL  => '',
			self::SLIDES         => array(),
		);
	}
}
