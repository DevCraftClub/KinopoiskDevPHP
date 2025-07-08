<?php

namespace KinopoiskDev\Enums;

/**
 * Перечисление типов студий
 *
 * Определяет возможные типы студий в системе Kinopoisk:
 * - Производство: кинокомпании, занимающиеся производством фильмов
 * - Спецэффекты: студии, специализирующиеся на создании визуальных эффектов
 * - Прокат: дистрибьюторские компании
 * - Студия дубляжа: студии, занимающиеся озвучиванием и дубляжом
 *
 * @package KinopoiskDev\Enums
 * @since   1.0.0
 * @author  Maxim Harder
 * @version 1.0.0
 */
enum StudioType: string {

	/**
	 * Производственная студия/кинокомпания
	 *
	 * Компании, занимающиеся непосредственно производством фильмов и сериалов
	 */
	case PRODUCTION = 'Производство';

	/**
	 * Студия спецэффектов
	 *
	 * Компании, специализирующиеся на создании визуальных и компьютерных эффектов
	 */
	case SPECIAL_EFFECTS = 'Спецэффекты';

	/**
	 * Прокатная компания
	 *
	 * Дистрибьюторы, занимающиеся распространением и показом фильмов
	 */
	case DISTRIBUTION = 'Прокат';

	/**
	 * Студия дубляжа
	 *
	 * Компании, занимающиеся озвучиванием, дубляжом и локализацией контента
	 */
	case DUBBING_STUDIO = 'Студия дубляжа';

	/**
	 * Получает все доступные типы студий
	 *
	 * @return array<string> Массив всех возможных типов студий
	 */
	public static function getAllTypes(): array {
		return array_map(fn(self $type) => $type->value, self::cases());
	}

	/**
	 * Проверяет, является ли переданное значение валидным типом студии
	 *
	 * @param   string  $value  Значение для проверки
	 *
	 * @return bool True, если значение является валидным типом студии
	 */
	public static function isValidType(string $value): bool {
		return in_array($value, self::getAllTypes(), true);
	}

	/**
	 * Получает тип студии по строковому значению
	 *
	 * @param   string  $value  Строковое значение типа
	 *
	 * @return self|null Объект enum или null, если значение не найдено
	 */
	public static function fromString(string $value): ?self {
		foreach (self::cases() as $case) {
			if ($case->value === $value) {
				return $case;
			}
		}
		return null;
	}

	/**
	 * Получает описание типа студии
	 *
	 * @return string Человекочитаемое описание типа студии
	 */
	public function getDescription(): string {
		return match ($this) {
			self::PRODUCTION      => 'Кинокомпания, занимающаяся производством фильмов и сериалов',
			self::SPECIAL_EFFECTS => 'Студия, специализирующаяся на создании визуальных и компьютерных эффектов',
			self::DISTRIBUTION    => 'Дистрибьюторская компания, занимающаяся прокатом и распространением фильмов',
			self::DUBBING_STUDIO  => 'Студия, занимающаяся озвучиванием, дубляжом и локализацией контента',
		};
	}

	/**
	 * Получает английское название типа студии
	 *
	 * @return string Английское название типа
	 */
	public function getEnglishName(): string {
		return match ($this) {
			self::PRODUCTION      => 'Production',
			self::SPECIAL_EFFECTS => 'Special Effects',
			self::DISTRIBUTION    => 'Distribution',
			self::DUBBING_STUDIO  => 'Dubbing Studio',
		};
	}

}