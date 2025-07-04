<?php

namespace KinopoiskDev\Enums;

/**
 * Enum для рейтингов MPAA
 *
 * Этот enum содержит все возможные рейтинги MPAA, которые могут быть
 * возвращены API Kinopoisk.dev
 */
enum RatingMpaa: string {

	case G    = 'g';
	case PG   = 'pg';
	case PG13 = 'pg13';
	case R    = 'r';
	case NC17 = 'nc17';

	/**
	 * Возвращает описание рейтинга MPAA
	 */
	public function getDescription(): string {
		return match ($this) {
			self::G    => 'General Audiences (без ограничений)',
			self::PG   => 'Parental Guidance Suggested (рекомендуется присутствие родителей)',
			self::PG13 => 'Parents Strongly Cautioned (дети до 13 лет допускаются на фильм только с родителями)',
			self::R    => 'Restricted (до 17 лет обязательно присутствие взрослого)',
			self::NC17 => 'No One 17 & Under Admitted (лица до 18 лет не допускаются)',
		};
	}

}