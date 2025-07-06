<?php

namespace KinopoiskDev\Enums;

/**
 * Enum для профессий персон
 *
 * Этот enum содержит все возможные профессии персон, которые могут быть
 * возвращены API Kinopoisk.dev
 */
enum PersonProfession: string {

	case ACTOR       = 'actor';
	case DIRECTOR    = 'director';
	case WRITER      = 'writer';
	case PRODUCER    = 'producer';
	case COMPOSER    = 'composer';
	case OPERATOR    = 'operator';
	case DESIGN      = 'design';
	case EDITOR      = 'editor';
	case VOICE_ACTOR = 'voice_actor';
	case OTHER       = 'other';

	/**
	 * Создает экземпляр enum из русского названия профессии
	 */
	public static function fromRussianName(string $name): ?self {
		return match ($name) {
			'актер', 'актеры'                 => self::ACTOR,
			'режиссер', 'режиссеры'           => self::DIRECTOR,
			'сценарист', 'сценаристы'         => self::WRITER,
			'продюсер', 'продюсеры'           => self::PRODUCER,
			'композитор', 'композиторы'       => self::COMPOSER,
			'оператор', 'операторы'           => self::OPERATOR,
			'художник', 'художники'           => self::DESIGN,
			'монтажер', 'монтажеры'           => self::EDITOR,
			'актер дубляжа', 'актеры дубляжа' => self::VOICE_ACTOR,
			'актёр дубляжа', 'актёры дубляжа' => self::VOICE_ACTOR,
			default                           => self::OTHER,
		};
	}

	/**
	 * Возвращает название профессии на русском языке
	 */
	public function getRussianName(): string {
		return match ($this) {
			self::ACTOR       => 'актер',
			self::DIRECTOR    => 'режиссер',
			self::WRITER      => 'сценарист',
			self::PRODUCER    => 'продюсер',
			self::COMPOSER    => 'композитор',
			self::OPERATOR    => 'оператор',
			self::DESIGN      => 'художник',
			self::EDITOR      => 'монтажер',
			self::VOICE_ACTOR => 'актер дубляжа',
			self::OTHER       => 'другое',
		};
	}

	/**
	 * Возвращает множественное название профессии на русском языке
	 */
	public function getRussianPluralName(): string {
		return match ($this) {
			self::ACTOR       => 'актеры',
			self::DIRECTOR    => 'режиссеры',
			self::WRITER      => 'сценаристы',
			self::PRODUCER    => 'продюсеры',
			self::COMPOSER    => 'композиторы',
			self::OPERATOR    => 'операторы',
			self::DESIGN      => 'художники',
			self::EDITOR      => 'монтажеры',
			self::VOICE_ACTOR => 'актеры дубляжа',
			self::OTHER       => 'другие',
		};
	}

}