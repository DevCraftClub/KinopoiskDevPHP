<?php

declare(strict_types=1);

namespace KinopoiskDev\Models;

/**
 * Класс для представления видеоматериала
 *
 * Содержит информацию о видеоматериале, связанном с фильмом или сериалом,
 * включая URL, название, тип, размер и источник. Используется для хранения
 * данных о трейлерах, тизерах и других видеоматериалах.
 *
 * @package KinopoiskDev\Models
 * @since   1.0.0
 * @author  Maxim Harder
 * @version 1.0.0
 * @see     \KinopoiskDev\Models\VideoTypes Для коллекции видеоматериалов
 * @see     \KinopoiskDev\Models\Movie Для использования в информации о фильмах
 */
class Video {

	/**
	 * Конструктор для создания объекта видеоматериала
	 *
	 * Создает новый экземпляр класса Video с указанными параметрами.
	 * Большинство параметров являются опциональными и могут быть null при отсутствии
	 * соответствующей информации в источнике данных.
	 *
	 * @see Video::fromArray() Для создания объекта из массива данных API
	 * @see Video::toArray() Для преобразования объекта в массив
	 *
	 * @param   string|null  $url   URL видеоматериала
	 * @param   string|null  $name  Название видеоматериала
	 * @param   string|null  $site  Источник видеоматериала (например, YouTube)
	 * @param   int|null     $size  Размер видеоматериала
	 * @param   string|null  $type  Тип видеоматериала (например, трейлер, тизер)
	 */
	public function __construct(
		public readonly ?string $url = null,
		public readonly ?string $name = null,
		public readonly ?string $site = null,
		public readonly ?int $size = null,
		public readonly ?string $type = null,
	) {}

	/**
	 * Создает объект Video из массива данных API
	 *
	 * Фабричный метод для создания экземпляра класса Video из массива данных,
	 * полученных от API Kinopoisk.dev. Безопасно обрабатывает отсутствующие
	 * значения, устанавливая их в null.
	 *
	 * @see Video::toArray() Для обратного преобразования в массив
	 *
	 * @param   array  $data  Массив данных о видеоматериале от API, содержащий ключи:
	 *                        - url: string|null - URL видеоматериала
	 *                        - name: string|null - название видеоматериала
	 *                        - site: string|null - источник видеоматериала
	 *                        - size: int|null - размер видеоматериала
	 *                        - type: string|null - тип видеоматериала
	 *
	 * @return \KinopoiskDev\Models\Video Новый экземпляр класса Video с данными из массива
	 */
	public static function fromArray(array $data): self {
		return new self(
			url: $data['url'] ?? null,
			name: $data['name'] ?? null,
			site: $data['site'] ?? null,
			size: $data['size'] ?? null,
			type: $data['type'] ?? null,
		);
	}

	/**
	 * Преобразует объект в массив данных
	 *
	 * Конвертирует текущий экземпляр класса Video в массив,
	 * совместимый с форматом API Kinopoisk.dev. Используется для сериализации
	 * данных при отправке запросов к API или для экспорта данных.
	 *
	 * @see Video::fromArray() Для создания объекта из массива
	 *
	 * @return array Массив с данными о видеоматериале, содержащий все поля объекта
	 */
	public function toArray(): array {
		return [
			'url' => $this->url,
			'name' => $this->name,
			'site' => $this->site,
			'size' => $this->size,
			'type' => $this->type,
		];
	}
}
