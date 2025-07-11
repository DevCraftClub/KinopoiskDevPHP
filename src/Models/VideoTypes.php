<?php

declare(strict_types=1);

namespace KinopoiskDev\Models;

/**
 * Класс для представления коллекции видеоматериалов
 *
 * Содержит коллекцию видеоматериалов, связанных с фильмом или сериалом,
 * включая трейлеры, тизеры и другие типы видео. Используется для группировки
 * и организации видеоконтента по категориям.
 *
 * @package KinopoiskDev\Models
 * @since   1.0.0
 * @author  Maxim Harder
 *
 * @version 1.0.0
 * @see     \KinopoiskDev\Models\Video Для отдельных видеоматериалов
 * @see     \KinopoiskDev\Models\Movie Для использования в информации о фильмах
 */
 class VideoTypes implements BaseModel {

	/**
	 * Конструктор для создания объекта коллекции видеоматериалов
	 *
	 * Создает новый экземпляр класса VideoTypes с указанным массивом трейлеров.
	 * Параметр является опциональным и может быть null при отсутствии
	 * видеоматериалов для данного фильма или сериала.
	 *
	 * @see VideoTypes::fromArray() Для создания объекта из массива данных API
	 * @see VideoTypes::toArray() Для преобразования объекта в массив
	 * @see Video Для структуры отдельного видеоматериала
	 *
	 * @param   array|null  $trailers  Массив объектов Video с трейлерами или null
	 */
	public function __construct(
		public ?array $trailers = NULL,
	) {}

	/**
	 * Создает объект VideoTypes из массива данных API
	 *
	 * Фабричный метод для создания экземпляра класса VideoTypes из массива данных,
	 * полученных от API Kinopoisk.dev. Безопасно обрабатывает отсутствующие
	 * значения и преобразует вложенные массивы трейлеров в объекты Video.
	 *
	 * @see VideoTypes::toArray() Для обратного преобразования в массив
	 * @see Video::fromArray() Для создания отдельных видеоматериалов
	 *
	 * @param   array  $data  Массив данных о видеоматериалах от API, содержащий ключи:
	 *                        - trailers: array|null - массив данных о трейлерах
	 *
	 * @return \KinopoiskDev\Models\VideoTypes Новый экземпляр класса VideoTypes с данными из массива
	 */
	public static function fromArray(array $data): static {
		$trailers = NULL;
		if (isset($data['trailers']) && is_array($data['trailers'])) {
			$trailers = array_map(fn ($trailer) => Video::fromArray($trailer), $data['trailers']);
		}

		return new self(
			trailers: $trailers,
		);
	}

	/**
	 * Преобразует объект в массив данных
	 *
	 * Конвертирует текущий экземпляр класса VideoTypes в массив,
	 * совместимый с форматом API Kinopoisk.dev. Преобразует все вложенные
	 * объекты Video в массивы. Используется для сериализации данных
	 * при отправке запросов к API или для экспорта данных.
	 *
	 * @see VideoTypes::fromArray() Для создания объекта из массива
	 * @see Video::toArray() Для преобразования отдельных видеоматериалов в массивы
	 *
	 * @return array Массив с данными о видеоматериалах, содержащий ключи:
	 *               - trailers: array|null - массив данных о трейлерах или null
	 */
	public function toArray(bool $includeNulls = true): array {
		$trailers = NULL;
		if ($this->trailers !== NULL) {
			$trailers = array_map(fn ($trailer) => $trailer->toArray(), $this->trailers);
		}

		return [
			'trailers' => $trailers,
		];
	}


	/**
	 * Валидирует данные модели
	 *
	 * @return bool True если данные валидны
	 * @throws \KinopoiskDev\Exceptions\ValidationException При ошибке валидации
	 */
	public function validate(): bool {
		return true; // Basic validation - override in specific models if needed
	}

	/**
	 * Возвращает JSON представление объекта
	 *
	 * @param int $flags Флаги для json_encode
	 * @return string JSON строка
	 * @throws \JsonException При ошибке сериализации
	 */
	public function toJson(int $flags = JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE): string {
		return json_encode($this->toArray(), $flags);
	}

	/**
	 * Создает объект из JSON строки
	 *
	 * @param string $json JSON строка
	 * @return static Экземпляр модели
	 * @throws \JsonException При ошибке парсинга
	 * @throws \KinopoiskDev\Exceptions\ValidationException При некорректных данных
	 */
	public static function fromJson(string $json): static {
		$data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
		$instance = static::fromArray($data);
		$instance->validate();
		return $instance;
	}


}
