<?php

namespace KinopoiskDev\Responses\Api;

use KinopoiskDev\Models\Keyword;
use KinopoiskDev\Responses\BaseDocsResponseDto;

/**
 * DTO для ответа API с ключевыми словами
 *
 * Этот класс представляет структурированный ответ от API Kinopoisk.dev
 * при запросе списка ключевых слов с поддержкой пагинации.
 *
 * @package KinopoiskDev\Responses\Api
 * @since   1.0.0
 * @author  Maxim Harder
 * @version 1.0.0
 * @link    https://kinopoiskdev.readme.io/reference/keywordcontroller_findmanyv1_4
 *
 * @extends BaseDocsResponseDto<Keyword>
 */
class KeywordDocsResponseDto extends BaseDocsResponseDto {

	/**
	 * Массив ключевых слов
	 *
	 * @var Keyword[] Список объектов ключевых слов, полученных от API
	 */
	public array $docs;

	/**
	 * Конструктор DTO ответа с ключевыми словами
	 *
	 * @param Keyword[] $docs  Массив объектов ключевых слов
	 * @param int       $total Общее количество ключевых слов, соответствующих запросу
	 * @param int       $limit Максимальное количество ключевых слов на странице
	 * @param int       $page  Номер текущей страницы
	 * @param int       $pages Общее количество страниц
	 */
	public function __construct(
		array $docs,
		int $total,
		int $limit,
		int $page,
		int $pages
	) {
		parent::__construct($docs, $total, $limit, $page, $pages);
	}

	/**
	 * Получает все названия ключевых слов
	 *
	 * @return string[] Массив названий ключевых слов
	 */
	public function getKeywordTitles(): array {
		return array_filter(array_map(fn(Keyword $keyword) => $keyword->title, $this->docs));
	}

	/**
	 * Фильтрует ключевые слова по популярности
	 *
	 * @param int $threshold Минимальное количество связанных фильмов
	 *
	 * @return Keyword[] Массив популярных ключевых слов
	 */
	public function getPopularKeywords(int $threshold = 10): array {
		return array_filter(
			$this->docs,
			fn(Keyword $keyword) => $keyword->isPopular($threshold)
		);
	}

	/**
	 * Ищет ключевые слова, содержащие указанный текст
	 *
	 * @param string $searchText Текст для поиска в названиях
	 *
	 * @return Keyword[] Массив найденных ключевых слов
	 */
	public function searchByTitle(string $searchText): array {
		$searchText = mb_strtolower($searchText);
		
		return array_filter(
			$this->docs,
			fn(Keyword $keyword) => $keyword->title 
				&& str_contains(mb_strtolower($keyword->title), $searchText)
		);
	}

	/**
	 * Группирует ключевые слова по количеству связанных фильмов
	 *
	 * @return array<string, Keyword[]> Массив групп ключевых слов
	 */
	public function groupByPopularity(): array {
		$groups = [
			'very_popular' => [], // 100+ фильмов
			'popular' => [],      // 10-99 фильмов
			'moderate' => [],     // 2-9 фильмов
			'rare' => []          // 0-1 фильм
		];

		foreach ($this->docs as $keyword) {
			$moviesCount = $keyword->getMoviesCount();
			
			if ($moviesCount >= 100) {
				$groups['very_popular'][] = $keyword;
			} elseif ($moviesCount >= 10) {
				$groups['popular'][] = $keyword;
			} elseif ($moviesCount >= 2) {
				$groups['moderate'][] = $keyword;
			} else {
				$groups['rare'][] = $keyword;
			}
		}

		return $groups;
	}

	/**
	 * Получает ключевые слова, связанные с указанным фильмом
	 *
	 * @param int $movieId ID фильма
	 *
	 * @return Keyword[] Массив ключевых слов, связанных с фильмом
	 */
	public function getKeywordsForMovie(int $movieId): array {
		return array_filter(
			$this->docs,
			fn(Keyword $keyword) => $keyword->isRelatedToMovie($movieId)
		);
	}

	/**
	 * Получает статистику по ключевым словам
	 *
	 * @return array<string, mixed> Статистика
	 */
	public function getStatistics(): array {
		$totalMovies = 0;
		$keywordsWithMovies = 0;
		$popularKeywords = 0;

		foreach ($this->docs as $keyword) {
			$moviesCount = $keyword->getMoviesCount();
			$totalMovies += $moviesCount;
			
			if ($moviesCount > 0) {
				$keywordsWithMovies++;
			}
			
			if ($keyword->isPopular()) {
				$popularKeywords++;
			}
		}

		return [
			'total_keywords' => count($this->docs),
			'keywords_with_movies' => $keywordsWithMovies,
			'popular_keywords' => $popularKeywords,
			'total_movie_relations' => $totalMovies,
			'average_movies_per_keyword' => count($this->docs) > 0 
				? round($totalMovies / count($this->docs), 2) 
				: 0
		];
	}

	/**
	 * Получает недавно созданные ключевые слова
	 *
	 * @param int $days Количество дней для считания "недавними"
	 *
	 * @return Keyword[] Массив недавно созданных ключевых слов
	 */
	public function getRecentlyCreated(int $days = 30): array {
		return array_filter(
			$this->docs,
			fn(Keyword $keyword) => $keyword->isRecentlyCreated($days)
		);
	}

}