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
 * @version 1.0.0
 * @see     \KinopoiskDev\Models\Image Для полной информации об изображении
 * @see     \KinopoiskDev\Models\Movie Для использования в информации о фильмах
 */
class ShortImage {

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
		public readonly ?string $url = null,
		public readonly ?string $previewUrl = null,
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
	public static function fromArray(array $data): self {
		return new self(
			url: $data['url'] ?? null,
			previewUrl: $data['previewUrl'] ?? null,
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
	public function toArray(): array {
		return [
			'url' => $this->url,
			'previewUrl' => $this->previewUrl,
		];
	}
}
