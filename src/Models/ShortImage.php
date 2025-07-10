<?php

declare(strict_types=1);

namespace KinopoiskDev\Models;

/**
 * Класс для представления упрощенной информации об изображении
 *
 * Содержит основные данные об изображении, включая URL полного изображения
 * и URL превью. Используется для хранения информации о постерах, фонах
 * и других изображениях, связанных с фильмами и сериалами.
 *
 * @package KinopoiskDev\Models
 * @since   1.0.0
 * @author  Maxim Harder
 *
 * @version 1.0.0
 * @see     \KinopoiskDev\Models\Image Для полной информации об изображении
 * @see     \KinopoiskDev\Models\Movie Для использования в информации о фильмах
 */
readonly class ShortImage implements BaseModel {

	/**
	 * Конструктор для создания объекта упрощенного изображения
	 *
	 * Создает новый экземпляр класса ShortImage с указанными параметрами.
	 * Все параметры являются опциональными и могут быть null при отсутствии
	 * соответствующей информации в источнике данных.
	 *
	 * @see ShortImage::fromArray() Для создания объекта из массива данных API
	 * @see ShortImage::toArray() Для преобразования объекта в массив
	 *
	 * @param   string|null  $url         URL полного изображения
	 * @param   string|null  $previewUrl  URL превью изображения
	 */
	public function __construct(
		public ?string $url = NULL,
		public ?string $previewUrl = NULL,
	) {}

	/**
	 * Создает объект ShortImage из массива данных API
	 *
	 * Фабричный метод для создания экземпляра класса ShortImage из массива данных,
	 * полученных от API Kinopoisk.dev. Безопасно обрабатывает отсутствующие
	 * значения, устанавливая их в null.
	 *
	 * @see ShortImage::toArray() Для обратного преобразования в массив
	 *
	 * @param   array  $data  Массив данных об изображении от API, содержащий ключи:
	 *                        - url: string|null - URL полного изображения
	 *                        - previewUrl: string|null - URL превью изображения
	 *
	 * @return \KinopoiskDev\Models\ShortImage Новый экземпляр класса ShortImage с данными из массива
	 */
	public static function fromArray(array $data): static {
		return new self(
			url       : $data['url'] ?? NULL,
			previewUrl: $data['previewUrl'] ?? NULL,
		);
	}

	/**
	 * Преобразует объект в массив данных
	 *
	 * Конвертирует текущий экземпляр класса ShortImage в массив,
	 * совместимый с форматом API Kinopoisk.dev. Используется для сериализации
	 * данных при отправке запросов к API или для экспорта данных.
	 *
	 * @see ShortImage::fromArray() Для создания объекта из массива
	 *
	 * @return array Массив с данными об изображении, содержащий все поля объекта
	 */
	public function toArray(bool $includeNulls = true): array {
		return [
			'url'        => $this->url,
			'previewUrl' => $this->previewUrl,
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
