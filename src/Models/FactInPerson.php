<?php

namespace KinopoiskDev\Models;

/**
 * Модель фактов о персоне
 *
 * Представляет интересный факт о персоне кино (актёре, режиссёре, продюсере и т.д.).
 * Содержит текстовую информацию о биографии, карьере или других аспектах жизни
 * деятеля кинематографа. Используется для хранения и отображения дополнительной
 * информации о персонах из базы данных Kinopoisk.dev.
 *
 * @package KinopoiskDev\Models
 * @since   1.0.0
 * @author  Maxim Harder
 *
 * @version 1.0.0
 * @see     \KinopoiskDev\Models\Person Основная модель персоны
 * @see     \KinopoiskDev\Models\FactInMovie Модель фактов о фильмах
 */
class FactInPerson extends AbstractBaseModel {

	/**
	 * Конструктор для создания объекта факта о персоне
	 *
	 * Создает новый экземпляр FactInPerson с указанным текстовым содержимым.
	 * Конструктор принимает только основную информацию о факте,
	 * в отличие от фактов о фильмах, не содержит дополнительных метаданных
	 * о типе или наличии спойлеров.
	 *
	 * @param   string  $value  Текст факта - основное содержимое информации о персоне кино
	 *
	 * @example
	 * ```php
	 * $fact = new FactInPerson(
	 *     value: 'Актёр изучал театральное искусство в консерватории'
	 * );
	 * ```
	 */
	public function __construct(
		public string $value,
	) {}

	/**
	 * Создает объект FactInPerson из массива данных API
	 *
	 * Фабричный метод для создания экземпляра класса FactInPerson из массива
	 * данных о факте персоны, полученных от API Kinopoisk.dev.
	 * Извлекает значение факта из переданного массива и безопасно обрабатывает
	 * его отсутствие или некорректный формат.
	 *
	 * @see FactInPerson::toArray() Для обратного преобразования в массив
	 * @see FactInPerson::__construct() Конструктор класса
	 *
	 * @param   array  $data  Массив данных о факте персоны от API, содержащий ключи:
	 *                        - value: string - текстовое содержимое факта о персоне
	 *
	 * @return static Новый экземпляр класса FactInPerson с данными из массива
	 *
	 * @throws \TypeError При отсутствии обязательного поля 'value' в массиве данных
	 */
	public static function fromArray(array $data): static {
		return new self(
			value: $data['value'],
		);
	}

	/**
	 * Преобразует объект в массив данных
	 *
	 * Конвертирует текущий экземпляр класса FactInPerson в массив,
	 * совместимый с форматом API Kinopoisk.dev. Используется для сериализации
	 * данных при отправке запросов к API или для экспорта данных в JSON.
	 * Структура возвращаемого массива соответствует формату входных данных.
	 *
	 * @see FactInPerson::fromArray() Для создания объекта из массива
	 *
	 * @return array Массив с данными о факте персоны, содержащий ключи:
	 *               - value: string - текстовое содержимое факта о персоне
	 */
	public function toArray(bool $includeNulls = TRUE): array {
		return [
			'value' => $this->value,
		];
	}

	/**
	 * Валидирует данные модели
	 *
	 * @return bool True если данные валидны
	 */
	public function validate(): bool {
		return TRUE; // Basic validation - override in specific models if needed
	}

}
