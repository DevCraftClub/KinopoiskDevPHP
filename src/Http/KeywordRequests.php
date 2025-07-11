<?php

namespace KinopoiskDev\Http;

use KinopoiskDev\Exceptions\KinopoiskDevException;
use KinopoiskDev\Filter\KeywordSearchFilter;
use KinopoiskDev\Kinopoisk;
use KinopoiskDev\Models\Keyword;
use KinopoiskDev\Responses\Api\KeywordDocsResponseDto;

/**
 * Класс для API-запросов, связанных с ключевыми словами
 *
 * Этот класс предоставляет методы для всех конечных точек ключевых слов API Kinopoisk.dev.
 * Позволяет получать информацию о тематических метках, которые используются для
 * категоризации и поиска фильмов по содержанию.
 *
 * @package KinopoiskDev\Http
 * @since   1.0.0
 * @author  Maxim Harder
 * @version 1.0.0
 * @see     \KinopoiskDev\Models\Keyword Для структуры данных ключевого слова
 * @see     \KinopoiskDev\Filter\KeywordSearchFilter Для фильтрации запросов
 */
class KeywordRequests extends Kinopoisk {

	/**
	 * Получает ключевые слова по названию
	 *
	 * Выполняет поиск ключевых слов по точному или частичному совпадению названия.
	 * Полезно для поиска тематических категорий фильмов.
	 *
	 * @param   string  $title  Название ключевого слова для поиска
	 * @param   int     $page   Номер страницы результатов
	 * @param   int     $limit  Количество результатов на странице
	 *
	 * @return KeywordDocsResponseDto Ключевые слова с подходящими названиями
	 * @throws KinopoiskDevException При ошибках API
	 * @throws \JsonException При ошибках парсинга JSON
	 */
	public function getKeywordsByTitle(string $title, int $page = 1, int $limit = 10): KeywordDocsResponseDto {
		$filters = new KeywordSearchFilter();
		$filters->title($title);

		return $this->searchKeywords($filters, $page, $limit);
	}

	/**
	 * Ищет ключевые слова по различным критериям
	 *
	 * Основной метод для поиска ключевых слов с поддержкой сложных фильтров.
	 * Позволяет искать по названию ключевого слова, связанным фильмам и другим параметрам.
	 *
	 * @api    /v1.4/keyword
	 * @link   https://kinopoiskdev.readme.io/reference/keywordcontroller_findmanyv1_4
	 *
	 * @param   KeywordSearchFilter|null  $filters  Объект фильтра для поиска ключевых слов
	 * @param   int                       $page     Номер страницы результатов (по умолчанию: 1)
	 * @param   int                       $limit    Количество результатов на странице (по умолчанию: 10, максимум: 250)
	 *
	 * @return KeywordDocsResponseDto Результаты поиска с пагинацией
	 * @throws KinopoiskDevException При ошибках API
	 */
	public function searchKeywords(?KeywordSearchFilter $filters = NULL, int $page = 1, int $limit = 10): KeywordDocsResponseDto {
		if ($limit > 250) {
			throw new KinopoiskDevException('Лимит не должен превышать 250');
		}
		if ($page < 1) {
			throw new KinopoiskDevException('Номер страницы не должен быть меньше 1');
		}

		if (is_null($filters)) {
			$filters = new KeywordSearchFilter();
		}

		$filters->addFilter('page', $page);
		$filters->addFilter('limit', $limit);
		$queryParams = $filters->getFilters();

		$response = $this->makeRequest('GET', 'keyword', $queryParams);
		$data     = $this->parseResponse($response);

		return new KeywordDocsResponseDto(
			docs : array_map(fn ($keywordData) => Keyword::fromArray($keywordData), $data['docs'] ?? []),
			total: $data['total'] ?? 0,
			limit: $data['limit'] ?? $limit,
			page : $data['page'] ?? $page,
			pages: $data['pages'] ?? 1,
		);
	}

	/**
	 * Получает ключевые слова для определенного фильма
	 *
	 * Находит все ключевые слова, которые связаны с указанным фильмом.
	 * Полезно для анализа тематики и содержания конкретного фильма.
	 *
	 * @param   int  $movieId  Идентификатор фильма
	 * @param   int  $page     Номер страницы результатов
	 * @param   int  $limit    Количество результатов на странице
	 *
	 * @return KeywordDocsResponseDto Ключевые слова, связанные с фильмом
	 * @throws KinopoiskDevException При ошибках API
	 * @throws \JsonException При ошибках парсинга JSON
	 */
	public function getKeywordsForMovie(int $movieId, int $page = 1, int $limit = 10): KeywordDocsResponseDto {
		$filters = new KeywordSearchFilter();
		$filters->movieId($movieId);

		return $this->searchKeywords($filters, $page, $limit);
	}

	/**
	 * Получает ключевое слово по его ID
	 *
	 * Выполняет поиск конкретного ключевого слова по его уникальному идентификатору.
	 *
	 * @param   int  $keywordId  Уникальный идентификатор ключевого слова
	 *
	 * @return Keyword|null Ключевое слово или null если не найдено
	 * @throws KinopoiskDevException При ошибках API
	 * @throws \JsonException При ошибках парсинга JSON
	 */
	public function getKeywordById(int $keywordId): ?Keyword {
		$filters = new KeywordSearchFilter();
		$filters->id($keywordId);

		$result = $this->searchKeywords($filters, 1, 1);

		return $result->docs[0] ?? NULL;
	}

	/**
	 * Получает популярные ключевые слова
	 *
	 * Возвращает ключевые слова, отсортированные по популярности
	 * (количеству связанных с ними фильмов).
	 *
	 * @param   int  $page   Номер страницы результатов
	 * @param   int  $limit  Количество результатов на странице
	 *
	 * @return KeywordDocsResponseDto Популярные ключевые слова
	 * @throws KinopoiskDevException При ошибках API
	 * @throws \JsonException При ошибках парсинга JSON
	 */
	public function getPopularKeywords(int $page = 1, int $limit = 10): KeywordDocsResponseDto {
		$filters = new KeywordSearchFilter();
		$filters->sortByPopularity();

		return $this->searchKeywords($filters, $page, $limit);
	}

}