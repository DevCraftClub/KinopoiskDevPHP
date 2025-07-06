<?php

namespace KinopoiskDev\Enums;

/**
 * Enum для пола персон
 *
 * Этот enum содержит все возможные значения пола персон, которые могут быть
 * возвращены API Kinopoisk.dev
 */
enum PersonSex: string {

	case MALE   = 'male';
	case FEMALE = 'female';

	/**
	 * Возвращает название пола на русском языке
	 */
	public function getRussianName(): string {
		return match ($this) {
			self::MALE   => 'мужской',
			self::FEMALE => 'женский',
		};
	}

}