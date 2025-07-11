<?php

namespace KinopoiskDev\Models;

use Lombok\Getter;
use Lombok\Setter;

/**
 * Модель коллекции фильмов
 *
 * Эта модель представляет коллекцию или список фильмов из API Kinopoisk.dev,
 * такие как топ-250, жанровые подборки, тематические списки и другие коллекции.
 *
 * @package KinopoiskDev\Models
 * @since   1.0.0
 * @author  Maxim Harder
 * @version 1.0.0
 * @link    https://kinopoiskdev.readme.io/reference/
 */
#[Setter, Getter]
 class Lists implements BaseModel {

	/**
	 * Категория коллекции
	 *
	 * @var string|null Категория коллекции (например: "Лучшие фильмы", "Жанровые подборки")
	 */
	public ?string $category;

	/**
	 * Уникальный идентификатор коллекции
	 *
	 * @var string|null Slug коллекции, используемый в URL (например: "top250", "popular-films")
	 */
	public ?string $slug;

	/**
	 * Количество фильмов в коллекции
	 *
	 * @var int|null Общее количество фильмов, входящих в данную коллекцию
	 */
	public ?int $moviesCount;

	/**
	 * Обложка коллекции
	 *
	 * @var ShortImage|null Изображение-обложка коллекции
	 */
	public ?ShortImage $cover;

	/**
	 * Название коллекции
	 *
	 * @var string Человекочитаемое название коллекции
	 */
	public string $name;

	/**
	 * Дата последнего обновления
	 *
	 * @var string|null Дата и время последнего обновления коллекции в формате ISO 8601
	 */
	public ?string $updatedAt;

	/**
	 * Дата создания
	 *
	 * @var string|null Дата и время создания коллекции в формате ISO 8601
	 */
	public ?string $createdAt;

	/**
	 * Конструктор модели коллекции
	 *
	 * @param   string|null      $category     Категория коллекции
	 * @param   string|null      $slug         Уникальный идентификатор коллекции
	 * @param   int|null         $moviesCount  Количество фильмов в коллекции
	 * @param   ShortImage|null  $cover        Обложка коллекции
	 * @param   string           $name         Название коллекции
	 * @param   string|null      $updatedAt    Дата последнего обновления
	 * @param   string|null      $createdAt    Дата создания
	 */
	public function __construct(
		?string     $category = NULL,
		?string     $slug = NULL,
		?int        $moviesCount = NULL,
		?ShortImage $cover = NULL,
		string      $name = '',
		?string     $updatedAt = NULL,
		?string     $createdAt = NULL,
	) {
		$this->category    = $category;
		$this->slug        = $slug;
		$this->moviesCount = $moviesCount;
		$this->cover       = $cover;
		$this->name        = $name;
		$this->updatedAt   = $updatedAt;
		$this->createdAt   = $createdAt;
	}

	/**
	 * Создает объект из JSON строки
	 *
	 * @param   string  $json  JSON строка
	 *
	 * @return static Экземпляр модели
	 */
	public static function fromJson(string $json): static {
		$data     = json_decode($json, TRUE, 512, JSON_THROW_ON_ERROR);
		$instance = self::fromArray($data);
		$instance->validate();

		return $instance;
	}

	/**
	 * Создает экземпляр модели из массива данных
	 *
	 * @param   array<string, mixed>  $data  Массив данных от API
	 *
	 * @return static Экземпляр модели коллекции
	 */
	public static function fromArray(array $data): self {
		return new self(
			category   : $data['category'] ?? NULL,
			slug       : $data['slug'] ?? NULL,
			moviesCount: $data['moviesCount'] ?? NULL,
			cover      : isset($data['cover']) ? ShortImage::fromArray($data['cover']) : NULL,
			name       : $data['name'] ?? '',
			updatedAt  : $data['updatedAt'] ?? NULL,
			createdAt  : $data['createdAt'] ?? NULL,
		);
	}

	/**
	 * Валидирует данные модели
	 *
	 * @return bool True если данные валидны
	 */
	public function validate(): bool {
		return !empty($this->name);
	}

	/**
	 * Получает URL коллекции на сайте
	 *
	 * @return string|null URL коллекции или null, если slug отсутствует
	 */
	public function getUrl(): ?string {
		return $this->slug ? "https://www.kinopoisk.ru/lists/{$this->slug}/" : NULL;
	}

	/**
	 * Проверяет, является ли коллекция популярной (содержит много фильмов)
	 *
	 * @param   int  $threshold  Минимальное количество фильмов для считания коллекции популярной (по умолчанию 100)
	 *
	 * @return bool True, если коллекция популярная
	 */
	public function isPopular(int $threshold = 100): bool {
		return ($this->moviesCount ?? 0) >= $threshold;
	}

	/**
	 * Возвращает краткую информацию о коллекции
	 *
	 * @return string Краткая информация о коллекции
	 */
	public function getSummary(): string {
		$moviesText   = $this->moviesCount ? " ({$this->moviesCount} фильмов)" : '';
		$categoryText = $this->category ? " в категории \"{$this->category}\"" : '';

		return "Коллекция \"{$this->name}\"{$moviesText}{$categoryText}";
	}

	/**
	 * Возвращает JSON представление объекта
	 *
	 * @param   int  $flags  Флаги для json_encode
	 *
	 * @return string JSON строка
	 */
	public function toJson(int $flags = JSON_THROW_ON_ERROR|JSON_UNESCAPED_UNICODE): string {
		return json_encode($this->toArray(), $flags);
	}

	/**
	 * Преобразует модель в массив
	 *
	 * @param   bool  $includeNulls  Включать ли null значения
	 *
	 * @return array<string, mixed> Массив данных модели
	 */
	public function toArray(bool $includeNulls = TRUE): array {
		return [
			'category'    => $this->category,
			'slug'        => $this->slug,
			'moviesCount' => $this->moviesCount,
			'cover'       => $this->cover?->toArray($includeNulls),
			'name'        => $this->name,
			'updatedAt'   => $this->updatedAt,
			'createdAt'   => $this->createdAt,
		];
	}

}