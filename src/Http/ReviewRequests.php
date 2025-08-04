<?php

namespace KinopoiskDev\Http;

use KinopoiskDev\Enums\ReviewType;
use KinopoiskDev\Exceptions\KinopoiskDevException;
use KinopoiskDev\Filter\ReviewSearchFilter;
use KinopoiskDev\Kinopoisk;
use KinopoiskDev\Models\Review;
use KinopoiskDev\Responses\Api\ReviewDocsResponseDto;

/**
 * Класс для API-запросов, связанных с рецензиями
 *
 * Предоставляет методы для работы с рецензиями пользователей на фильмы и сериалы
 * через API Kinopoisk.dev. Включает поиск рецензий по различным критериям,
 * фильтрацию по типу (позитивные, негативные, нейтральные) и получение
 * статистики по рецензиям.
 *
 * Основные возможности:
 * - Поиск рецензий по различным критериям
 * - Получение позитивных рецензий
 * - Получение негативных рецензий
 * - Фильтрация по автору, фильму, типу рецензии
 * - Пагинация результатов
 *
 * @package KinopoiskDev\Http
 * @since   1.0.0
 * @author  Maxim Harder
 * @version 1.0.0
 *
 * @see     \KinopoiskDev\Filter\ReviewSearchFilter Для настройки фильтрации
 * @see     \KinopoiskDev\Models\Review Модель рецензии
 * @see     \KinopoiskDev\Responses\Api\ReviewDocsResponseDto Ответ с рецензиями
 * @link    https://kinopoiskdev.readme.io/reference/
 *
 * @example
 * ```php
 * $reviewRequests = new ReviewRequests('your-api-token');
 *
 * // Поиск всех рецензий
 * $reviews = $reviewRequests->searchReviews();
 *
 * // Поиск с фильтрами
 * $filter = new ReviewSearchFilter();
 * $filter->movieId(123)->type('Позитивный');
 * $reviews = $reviewRequests->searchReviews($filter, 1, 20);
 *
 * // Получение позитивных рецензий
 * $positiveReviews = $reviewRequests->getPositiveReviews(1, 50);
 *
 * // Получение негативных рецензий
 * $negativeReviews = $reviewRequests->getNegativeReviews(1, 30);
 * ```
 */
class ReviewRequests extends Kinopoisk {

	/**
	 * Получает положительные рецензии
	 *
	 * Удобный метод для получения только позитивных рецензий.
	 * Автоматически применяет фильтр по типу "Позитивный" и
	 * возвращает рецензии с положительной оценкой фильма.
	 *
	 * @since   1.0.0
	 *
	 * @param   int  $page   Номер страницы результатов (по умолчанию: 1)
	 * @param   int  $limit  Количество результатов на странице (по умолчанию: 10)
	 *
	 * @return ReviewDocsResponseDto Положительные рецензии с пагинацией
	 * @throws KinopoiskDevException При ошибках API или валидации
	 * @throws KinopoiskResponseException При ошибках HTTP-запроса
	 * @throws \JsonException При ошибках парсинга JSON-ответа
	 *
	 * @example
	 * ```php
	 * // Получить позитивные рецензии
	 * $positiveReviews = $reviewRequests->getPositiveReviews();
	 *
	 * // Получить больше позитивных рецензий
	 * $positiveReviews = $reviewRequests->getPositiveReviews(1, 100);
	 * ```
	 */
	public function getPositiveReviews(int $page = 1, int $limit = 10): ReviewDocsResponseDto {
		$filters = new ReviewSearchFilter();
		$filters->type(ReviewType::POSITIVE);

		return $this->searchReviews($filters, $page, $limit);
	}

	/**
	 * Поиск рецензий по различным критериям
	 *
	 * Основной метод для поиска рецензий с использованием расширенной
	 * фильтрации. Поддерживает фильтрацию по фильму, автору, типу
	 * рецензии, дате создания и другим критериям. Включает валидацию
	 * параметров и автоматическую пагинацию.
	 *
	 * @api     /v1.4/review
	 * @since   1.0.0
	 *
	 * @link    https://kinopoiskdev.readme.io/reference/reviewcontroller_findmanybyqueryv1_4
	 *
	 * @param   ReviewSearchFilter|null  $filters  Объект фильтра для настройки критериев поиска
	 * @param   int                      $page     Номер страницы результатов (по умолчанию: 1)
	 * @param   int                      $limit    Количество результатов на странице (по умолчанию: 10, максимум: 250)
	 *
	 * @return ReviewDocsResponseDto Результаты поиска с пагинацией
	 * @throws KinopoiskDevException При ошибках валидации или превышении лимитов
	 * @throws KinopoiskResponseException При ошибках HTTP-запроса
	 * @throws \JsonException При ошибках парсинга JSON-ответа
	 *
	 * @example
	 * ```php
	 * // Простой поиск всех рецензий
	 * $reviews = $reviewRequests->searchReviews();
	 *
	 * // Поиск с фильтрами
	 * $filter = new ReviewSearchFilter();
	 * $filter->movieId(123)->type('Позитивный')->author('user123');
	 * $reviews = $reviewRequests->searchReviews($filter, 1, 50);
	 * ```
	 */
	public function searchReviews(?ReviewSearchFilter $filters = NULL, int $page = 1, int $limit = 10): ReviewDocsResponseDto {
		if ($limit > 250) {
			throw new KinopoiskDevException('Лимит не должен превышать 250');
		}
		if ($page < 1) {
			throw new KinopoiskDevException('Номер страницы не должен быть меньше 1');
		}

		if (is_null($filters)) {
			$filters = new ReviewSearchFilter();
		}

		$filters->setPageNumber($page);
		$filters->setMaxLimit($limit);
		$queryParams = $filters->getFilters();

		$response = $this->makeRequest('GET', 'review', $queryParams);
		$data     = $this->parseResponse($response);

		return new ReviewDocsResponseDto(
			docs : array_map(fn ($reviewData) => Review::fromArray($reviewData), $data['docs'] ?? []),
			total: $data['total'] ?? 0,
			limit: $data['limit'] ?? $limit,
			page : $data['page'] ?? $page,
			pages: $data['pages'] ?? 1,
		);
	}

	/**
	 * Получает отрицательные рецензии
	 *
	 * Удобный метод для получения только негативных рецензий.
	 * Автоматически применяет фильтр по типу "Негативный" и
	 * возвращает рецензии с отрицательной оценкой фильма.
	 *
	 * @since   1.0.0
	 *
	 * @param   int  $page   Номер страницы результатов (по умолчанию: 1)
	 * @param   int  $limit  Количество результатов на странице (по умолчанию: 10)
	 *
	 * @return ReviewDocsResponseDto Отрицательные рецензии с пагинацией
	 * @throws KinopoiskDevException При ошибках API или валидации
	 * @throws KinopoiskResponseException При ошибках HTTP-запроса
	 * @throws \JsonException При ошибках парсинга JSON-ответа
	 *
	 * @example
	 * ```php
	 * // Получить негативные рецензии
	 * $negativeReviews = $reviewRequests->getNegativeReviews();
	 *
	 * // Получить больше негативных рецензий
	 * $negativeReviews = $reviewRequests->getNegativeReviews(1, 50);
	 * ```
	 */
	public function getNegativeReviews(int $page = 1, int $limit = 10): ReviewDocsResponseDto {
		$filters = new ReviewSearchFilter();
		$filters->type(ReviewType::NEGATIVE);

		return $this->searchReviews($filters, $page, $limit);
	}

}
