<?php

declare(strict_types=1);

namespace KinopoiskDev\Models;

/**
 * Класс для представления информации о сезоне сериала
 *
 * Содержит данные о конкретном сезоне сериала, включая номер сезона
 * и количество эпизодов в нем. Используется для структурирования
 * информации о сериалах с несколькими сезонами.
 *
 * @package KinopoiskDev\Models
 * @since   1.0.0
 * @author  Maxim Harder
 *
 * @version 1.0.0
 * @see     \KinopoiskDev\Models\Movie Для использования в информации о сериалах
 */
class SeasonInfo extends AbstractBaseModel {

	/**
	 * Конструктор для создания объекта информации о сезоне
	 *
	 * Создает новый экземпляр класса SeasonInfo с указанными параметрами.
	 * Все параметры являются опциональными и могут быть null при отсутствии
	 * соответствующей информации в источнике данных.
	 *
	 * @see SeasonInfo::fromArray() Для создания объекта из массива данных API
	 * @see SeasonInfo::toArray() Для преобразования объекта в массив
	 *
	 * @param   int|null  $number         Номер сезона
	 * @param   int|null  $episodesCount  Количество эпизодов в сезоне
	 */
	public function __construct(
		public ?int $number = NULL,
		public ?int $episodesCount = NULL,
	) {}

	/**
	 * Создает объект SeasonInfo из массива данных API
	 *
	 * Фабричный метод для создания экземпляра класса SeasonInfo из массива данных,
	 * полученных от API Kinopoisk.dev. Безопасно обрабатывает отсутствующие
	 * значения, устанавливая их в null.
	 *
	 * @see SeasonInfo::toArray() Для обратного преобразования в массив
	 *
	 * @param   array  $data  Массив данных о сезоне от API, содержащий ключи:
	 *                        - number: int|null - номер сезона
	 *                        - episodesCount: int|null - количество эпизодов в сезоне
	 *
	 * @return \KinopoiskDev\Models\SeasonInfo Новый экземпляр класса SeasonInfo с данными из массива
	 */
	public static function fromArray(array $data): static {
		return new self(
			number       : $data['number'] ?? NULL,
			episodesCount: $data['episodesCount'] ?? NULL,
		);
	}

	/**
	 * Преобразует объект в массив данных
	 *
	 * Конвертирует текущий экземпляр класса SeasonInfo в массив,
	 * совместимый с форматом API Kinopoisk.dev. Используется для сериализации
	 * данных при отправке запросов к API или для экспорта данных.
	 *
	 * @see SeasonInfo::fromArray() Для создания объекта из массива
	 *
	 * @return array Массив с данными о сезоне, содержащий все поля объекта
	 */
	public function toArray(bool $includeNulls = TRUE): array {
		return [
			'number'        => $this->number,
			'episodesCount' => $this->episodesCount,
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
