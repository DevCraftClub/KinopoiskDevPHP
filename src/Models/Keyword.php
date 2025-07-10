<?php

namespace KinopoiskDev\Models;

use Lombok\Getter;
use Lombok\Helper;
use Lombok\Setter;

/**
 * Модель ключевого слова
 *
 * Эта модель представляет ключевое слово (тематическую метку) из API Kinopoisk.dev,
 * которое используется для категоризации и поиска фильмов по содержанию и тематике.
 *
 * @package KinopoiskDev\Models
 * @since   1.0.0
 * @author  Maxim Harder
 * @version 1.0.0
 * @link    https://kinopoiskdev.readme.io/reference/keywordcontroller_findmanyv1_4
 */
#[Setter, Getter]
class Keyword implements BaseModel {

	/**
	 * Уникальный идентификатор ключевого слова
	 *
	 * @var int ID ключевого слова в базе данных Kinopoisk.dev
	 */
	public int $id;

	/**
	 * Название ключевого слова
	 *
	 * @var string|null Текстовое представление ключевого слова (например: "семья", "фантастика", "1980-е")
	 */
	public ?string $title;

	/**
	 * Связанные фильмы
	 *
	 * @var MovieFromKeyword[] Массив фильмов, связанных с этим ключевым словом
	 */
	public array $movies;

	/**
	 * Дата последнего обновления
	 *
	 * @var string Дата и время последнего обновления записи в формате ISO 8601
	 */
	public string $updatedAt;

	/**
	 * Дата создания записи
	 *
	 * @var string Дата и время создания записи в формате ISO 8601
	 */
	public string $createdAt;

	/**
	 * Конструктор модели ключевого слова
	 *
	 * @param int                   $id        Уникальный идентификатор
	 * @param string|null           $title     Название ключевого слова
	 * @param MovieFromKeyword[]    $movies    Связанные фильмы
	 * @param string                $updatedAt Дата последнего обновления
	 * @param string                $createdAt Дата создания
	 */
	public function __construct(
		int $id,
		?string $title = null,
		array $movies = [],
		string $updatedAt = '',
		string $createdAt = ''
	) {
		$this->id = $id;
		$this->title = $title;
		$this->movies = $movies;
		$this->updatedAt = $updatedAt;
		$this->createdAt = $createdAt;
	}

	/**
	 * Создает экземпляр модели из массива данных
	 *
	 * @param   array  $data  Массив данных от API
	 *
	 * @return static Экземпляр модели ключевого слова
	 */
	public static function fromArray(array<string, mixed> $data): static {
		$movies = [];
		if (isset($data['movies']) && is_array($data['movies'])) {
			foreach ($data['movies'] as $movieData) {
				if (is_array($movieData)) {
					$movies[] = MovieFromKeyword::fromArray($movieData);
				}
			}
		}

		return new static(
			id: $data['id'] ?? 0,
			title: $data['title'] ?? null,
			movies: $movies,
			updatedAt: $data['updatedAt'] ?? '',
			createdAt: $data['createdAt'] ?? ''
		);
	}

	/**
	 * Преобразует модель в массив
	 *
	 * @return array<string, mixed> Массив данных модели
	 */
	public function toArray(bool $includeNulls = true): array<string, mixed> {
		return [
			'id' => $this->id,
			'title' => $this->title,
			'movies' => array_map(fn($movie) => $movie->toArray(), $this->movies),
			'updatedAt' => $this->updatedAt,
			'createdAt' => $this->createdAt
		];
	}

	/**
	 * Возвращает количество связанных фильмов
	 *
	 * @return int Количество фильмов, использующих это ключевое слово
	 */
	public function getMoviesCount(): int {
		return count($this->movies);
	}

	/**
	 * Проверяет, является ли ключевое слово популярным
	 *
	 * @param int $threshold Минимальное количество фильмов для считания популярным (по умолчанию 10)
	 *
	 * @return bool True, если ключевое слово популярное
	 */
	public function isPopular(int $threshold = 10): bool {
		return $this->getMoviesCount() >= $threshold;
	}

	/**
	 * Получает список ID всех связанных фильмов
	 *
	 * @return int[] Массив ID фильмов
	 */
	public function getMovieIds(): array {
		return array_map(fn($movie) => $movie->id ?? 0, $this->movies);
	}

	/**
	 * Проверяет, связано ли ключевое слово с указанным фильмом
	 *
	 * @param int $movieId ID фильма для проверки
	 *
	 * @return bool True, если ключевое слово связано с фильмом
	 */
	public function isRelatedToMovie(int $movieId): bool {
		return in_array($movieId, $this->getMovieIds(), true);
	}

	/**
	 * Возвращает краткую информацию о ключевом слове
	 *
	 * @return string Краткое описание ключевого слова
	 */
	public function getSummary(): string {
		$moviesText = $this->getMoviesCount() > 0 
			? " ({$this->getMoviesCount()} фильмов)" 
			: '';
			
		return "Ключевое слово \"{$this->title}\"{$moviesText}";
	}

	/**
	 * Проверяет, недавно ли было создано ключевое слово
	 *
	 * @param int $days Количество дней для считания "недавним" (по умолчанию 30)
	 *
	 * @return bool True, если ключевое слово создано недавно
	 */
	public function isRecentlyCreated(int $days = 30): bool {
		$createdTimestamp = strtotime($this->createdAt);
		$threshold = strtotime("-{$days} days");
		
		return $createdTimestamp >= $threshold;
	}

	/**
	 * Валидирует данные модели
	 *
	 * @return bool True если данные валидны
	 */
	public function validate(): bool {
		return $this->id > 0;
	}

	/**
	 * Возвращает JSON представление объекта
	 *
	 * @param int $flags Флаги для json_encode
	 * @return string JSON строка
	 */
	public function toJson(int $flags = JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE): string {
		return json_encode($this->toArray(), $flags);
	}

	/**
	 * Создает объект из JSON строки
	 *
	 * @param string $json JSON строка
	 * @return static Экземпляр модели
	 */
	public static function fromJson(string $json): static {
		$data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
		$instance = static::fromArray($data);
		$instance->validate();
		return $instance;
	}

}