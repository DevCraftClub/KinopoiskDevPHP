<?php

declare(strict_types=1);

namespace KinopoiskDev\Models;

/**
 * Класс для представления логотипа фильма или сериала
 *
 * Представляет информацию о логотипе произведения, включая URL изображения.
 * Используется для хранения и обработки данных логотипов фильмов и сериалов,
 * полученных от API Kinopoisk.dev. Поддерживает сериализацию в массив
 * и десериализацию из массива данных API.
 *
 * @package KinopoiskDev\Models
 * @since   1.0.0
 * @author  Maxim Harder
 *
 * @version 1.0.0
 * @see     \KinopoiskDev\Models\Movie Для основной модели фильма
 * @see     \KinopoiskDev\Models\SearchMovie Для поисковой модели фильма
 */
class Logo {

	/**
	 * Конструктор для создания объекта логотипа
	 *
	 * Создает новый экземпляр класса Logo с указанным URL изображения.
	 * Параметр является опциональным и может быть null при отсутствии
	 * логотипа для данного произведения.
	 *
	 * @see Logo::fromArray() Для создания объекта из массива данных API
	 * @see Logo::toArray() Для преобразования объекта в массив
	 *
	 * @param   string|null  $url  URL изображения логотипа (null если логотип отсутствует)
	 *
	 * @example
	 * ```php
	 * $logo = new Logo('https://example.com/logo.png');
	 * $emptyLogo = new Logo(); // без логотипа
	 * ```
	 */
	public function __construct(
		public readonly ?string $url = NULL,
	) {}

	/**
	 * Создает объект Logo из массива данных API
	 *
	 * Фабричный метод для создания экземпляра класса Logo из массива данных,
	 * полученных от API Kinopoisk.dev. Безопасно обрабатывает отсутствующие
	 * значения, устанавливая их в null.
	 *
	 * @see Logo::toArray() Для обратного преобразования в массив
	 *
	 * @param   array  $data  Массив данных о логотипе от API, содержащий ключи:
	 *                        - url: string|null - URL изображения логотипа
	 *
	 * @return Logo Новый экземпляр класса Logo с данными из массива
	 *
	 * @example
	 * ```php
	 * $logoData = ['url' => 'https://example.com/logo.png'];
	 * $logo = Logo::fromArray($logoData);
	 * ```
	 */
	public static function fromArray(array $data): self {
		return new self(
			url: $data['url'] ?? NULL,
		);
	}

	/**
	 * Преобразует объект в массив данных
	 *
	 * Конвертирует текущий экземпляр класса Logo в массив,
	 * совместимый с форматом API Kinopoisk.dev. Используется для
	 * сериализации данных при отправке запросов к API или для
	 * экспорта данных в JSON формат.
	 *
	 * @see Logo::fromArray() Для создания объекта из массива
	 *
	 * @return array Массив с данными о логотипе, содержащий ключи:
	 *               - url: string|null - URL изображения логотипа
	 *
	 * @example
	 * ```php
	 * $logo = new Logo('https://example.com/logo.png');
	 * $array = $logo->toArray(); // ['url' => 'https://example.com/logo.png']
	 * ```
	 */
	public function toArray(): array {
		return [
			'url' => $this->url,
		];
	}

}