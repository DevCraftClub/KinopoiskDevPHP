<?php

namespace KinopoiskDev\Models;

/**
 * Модель фактов из фильма
 *
 * Представляет интересный факт о фильме, сериале или другом произведении.
 * Может содержать как обычную информацию, так и спойлеры, а также
 * имеет определенный тип (например, "блупер", "ошибка" и т.д.).
 *
 * @package KinopoiskDev\Models
 * @since   1.0.0
 * @author  Maxim Harder
 * @version 1.0.0
 * @see     \KinopoiskDev\Models\Movie Основная модель фильма
 * @see     \KinopoiskDev\Models\SearchMovie Поисковая модель фильма
 */
class FactInMovie {

	/**
	 * Конструктор для создания объекта факта о фильме
	 *
	 * Создает новый экземпляр FactInMovie с указанным содержимым факта
	 * и дополнительными метаданными о типе и наличии спойлеров.
	 *
	 * @param   string       $value    Текст факта - основное содержимое информации о фильме
	 * @param   string|null  $type     Тип факта (например, "блупер", "ошибка", "интересный факт")
	 * @param   bool|null    $spoiler  Содержит ли факт спойлеры (true - да, false - нет, null - неизвестно)
	 *
	 * @example
	 * ```php
	 * $fact = new FactInMovie(
	 *     value: 'Во время съёмок актёр травмировал руку',
	 *     type: 'блупер',
	 *     spoiler: false
	 * );
	 * ```
	 */
	public function __construct(
		public readonly string  $value,
		public readonly ?string $type = NULL,
		public readonly ?bool   $spoiler = NULL,
	) {}

}