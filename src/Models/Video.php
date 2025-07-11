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
 *
 * @version 1.0.0
 * @see     \KinopoiskDev\Models\VideoTypes Для коллекции видеоматериалов
 * @see     \KinopoiskDev\Models\Movie Для использования в информации о фильмах
 */
 class Video implements BaseModel {

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
		public ?string $url = NULL,
		public ?string $name = NULL,
		public ?string $site = NULL,
		public ?int    $size = NULL,
		public ?string $type = NULL,
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
	public static function fromArray(array $data): static {
		return new self(
			url : $data['url'] ?? NULL,
			name: $data['name'] ?? NULL,
			site: $data['site'] ?? NULL,
			size: $data['size'] ?? NULL,
			type: $data['type'] ?? NULL,
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
	public function toArray(bool $includeNulls = true): array {
		return [
			'url'  => $this->url,
			'name' => $this->name,
			'site' => $this->site,
			'size' => $this->size,
			'type' => $this->type,
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
