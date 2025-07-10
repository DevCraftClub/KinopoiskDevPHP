<?php

declare(strict_types=1);

namespace KinopoiskDev\Models;

/**
 * Класс для представления диапазона годов
 *
 * Содержит информацию о диапазоне годов, включая начальный и конечный год.
 * Используется для хранения периодов выпуска сериалов, временных рамок
 * для поиска и фильтрации, а также других диапазонов дат в годах.
 *
 * @package KinopoiskDev\Models
 * @since   1.0.0
 * @author  Maxim Harder
 *
 * @version 1.0.0
 * @see     \KinopoiskDev\Models\Movie Для использования в информации о фильмах
 */
readonly class YearRange implements BaseModel {

	/**
	 * Конструктор для создания объекта диапазона годов
	 *
	 * Создает новый экземпляр класса YearRange с указанными начальным и конечным годами.
	 * Оба параметра являются опциональными и могут быть null при отсутствии
	 * соответствующей информации в источнике данных.
	 *
	 * @see YearRange::fromArray() Для создания объекта из массива данных API
	 * @see YearRange::toArray() Для преобразования объекта в массив
	 *
	 * @param   int|null  $start  Начальный год диапазона или null
	 * @param   int|null  $end    Конечный год диапазона или null
	 */
	public function __construct(
		public ?int $start = null,
		public ?int $end = null,
	) {}

	/**
	 * Создает объект YearRange из массива данных API
	 *
	 * Фабричный метод для создания экземпляра класса YearRange из массива данных,
	 * полученных от API Kinopoisk.dev. Безопасно обрабатывает отсутствующие
	 * значения, устанавливая их в null.
	 *
	 * @see YearRange::toArray() Для обратного преобразования в массив
	 *
	 * @param   array  $data  Массив данных о диапазоне годов от API, содержащий ключи:
	 *                        - start: int|null - начальный год диапазона
	 *                        - end: int|null - конечный год диапазона
	 *
	 * @return \KinopoiskDev\Models\YearRange Новый экземпляр класса YearRange с данными из массива
	 */
	public static function fromArray(array $data): static {
		return new self(
			start: $data['start'] ?? null,
			end: $data['end'] ?? null,
		);
	}

	/**
	 * Преобразует объект в массив данных
	 *
	 * Конвертирует текущий экземпляр класса YearRange в массив,
	 * совместимый с форматом API Kinopoisk.dev. Используется для сериализации
	 * данных при отправке запросов к API или для экспорта данных.
	 *
	 * @see YearRange::fromArray() Для создания объекта из массива
	 *
	 * @return array Массив с данными о диапазоне годов, содержащий ключи:
	 *               - start: int|null - начальный год диапазона
	 *               - end: int|null - конечный год диапазона
	 */
	public function toArray(bool $includeNulls = true): array {
		return [
			'start' => $this->start,
			'end' => $this->end,
		];
	}
}
