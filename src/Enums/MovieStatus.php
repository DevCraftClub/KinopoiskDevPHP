<?php

namespace KinopoiskDev\Enums;

/**
 * Enum для статусов фильмов
 *
 * Этот enum содержит все возможные статусы фильмов, которые могут быть
 * возвращены API Kinopoisk.dev
 */
enum MovieStatus: string {

	case FILMING         = 'filming';
	case PRE_PRODUCTION  = 'pre-production';
	case COMPLETED       = 'completed';
	case ANNOUNCED       = 'announced';
	case POST_PRODUCTION = 'post-production';

	/**
	 * Возвращает человекочитаемое название статуса фильма
	 */
	public function getLabel(): string {
		return match ($this) {
			self::FILMING         => 'В производстве',
			self::PRE_PRODUCTION  => 'Пре-продакшн',
			self::COMPLETED       => 'Завершен',
			self::ANNOUNCED       => 'Анонсирован',
			self::POST_PRODUCTION => 'Пост-продакшн',
		};
	}

}