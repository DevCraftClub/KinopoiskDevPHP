<?php

namespace KinopoiskDev\Enums;

enum ImageType: string {

	case BACKDROP   = 'backdrops';
	case COVER      = 'cover';
	case FRAME      = 'frame';
	case PROMO      = 'promo';
	case SCREENSHOT = 'screenshot';
	case SHOOTING   = 'shooting';
	case STILL      = 'still';
	case WALLPAPER  = 'wallpaper';

	public static function allTypes(): array {
		static $types = NULL;

		if ($types === NULL) {
			$types = [self::BACKDROP, self::COVER, self::FRAME, self::PROMO, self::SCREENSHOT, self::SHOOTING, self::STILL, self::WALLPAPER];
		}

		return $types;
	}

}
