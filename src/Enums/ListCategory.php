<?php

namespace KinopoiskDev\Enums;

enum ListCategory: string {

	case ONLINE = 'Онлайн-кинотеатр';
	case AWARD  = 'Премии';
	case FEE    = 'Сборы';
	case SERIES = 'Сериалы';
	case MOVIE  = 'Фильмы';

	public static function getCategories(): array {
		static $categories = NULL;

		if ($categories === NULL) {
			$categories = [
				self::ONLINE,
                self::AWARD,
                self::FEE,
                self::SERIES,
                self::MOVIE,
			];
		}

		return $categories;
	}

}
