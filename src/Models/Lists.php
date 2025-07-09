<?php

namespace KinopoiskDev\Models;

use Lombok\Getter;
use Lombok\Helper;
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
class Lists extends BaseModel {

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
	 * @param string|null      $category    Категория коллекции
	 * @param string|null      $slug        Уникальный идентификатор коллекции
	 * @param int|null         $moviesCount Количество фильмов в коллекции
	 * @param ShortImage|null  $cover       Обложка коллекции
	 * @param string           $name        Название коллекции
	 * @param string|null      $updatedAt   Дата последнего обновления
	 * @param string|null      $createdAt   Дата создания
	 */
	public function __construct(
		?string $category = null,
		?string $slug = null,
		?int $moviesCount = null,
		?ShortImage $cover = null,
		string $name = '',
		?string $updatedAt = null,
		?string $createdAt = null
	) {
		parent::__construct();
		
		$this->category = $category;
		$this->slug = $slug;
		$this->moviesCount = $moviesCount;
		$this->cover = $cover;
		$this->name = $name;
		$this->updatedAt = $updatedAt;
		$this->createdAt = $createdAt;
	}

	/**
	 * Получает URL коллекции на сайте
	 *
	 * @return string|null URL коллекции или null, если slug отсутствует
	 */
	public function getUrl(): ?string {
		return $this->slug ? "https://www.kinopoisk.ru/lists/{$this->slug}/" : null;
	}

	/**
	 * Проверяет, является ли коллекция популярной (содержит много фильмов)
	 *
	 * @param int $threshold Минимальное количество фильмов для считания коллекции популярной (по умолчанию 100)
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
		$moviesText = $this->moviesCount ? " ({$this->moviesCount} фильмов)" : '';
		$categoryText = $this->category ? " в категории \"{$this->category}\"" : '';
		
		return "Коллекция \"{$this->name}\"{$moviesText}{$categoryText}";
	}

}