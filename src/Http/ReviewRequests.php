<?php

namespace KinopoiskDev\Http;

use KinopoiskDev\Exceptions\KinopoiskDevException;
use KinopoiskDev\Kinopoisk;
use KinopoiskDev\Models\Review;
use KinopoiskDev\Models\ReviewInfo;
use KinopoiskDev\Responses\ReviewDocsResponseDto;
use KinopoiskDev\Types\ReviewSearchFilter;

/**
 * Класс для API-запросов, связанных с рецензиями
 *
 * Этот класс предоставляет методы для всех конечных точек рецензий API Kinopoisk.dev.
 *
 * @package KinopoiskDev\Http
 * @since   1.0.0
 * @author  Maxim Harder
 * @version 1.0.0
 */
class ReviewRequests extends Kinopoisk {

	/**
	 * Поиск рецензий по различным критериям
	 *
	 * @api    /v1.4/review
	 * @link   https://kinopoiskdev.readme.io/reference/reviewcontroller_findmanybyqueryv1_4
	 *
	 * @param   ReviewSearchFilter|null  $filters  Объект фильтра для поиска
	 * @param   int                      $page     Номер страницы (по умолчанию: 1)
	 * @param   int                      $limit    Количество результатов на странице (по умолчанию: 10, макс: 250)
	 *
	 * @return ReviewDocsResponseDto Результаты поиска с пагинацией
	 * @throws KinopoiskDevException При ошибках API
	 * @throws \JsonException При ошибках парсинга JSON
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

		$filters->addFilter('page', $page);
		$filters->addFilter('limit', $limit);
		$queryParams = $filters->getFilters();

		$response = $this->makeRequest('GET', '/review', $queryParams);
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
	 * Получает положительные рецензии
	 *
	 * @param   int  $page   Номер страницы
	 * @param   int  $limit  Результатов на странице
	 *
	 * @return ReviewDocsResponseDto Положительные рецензии
	 * @throws KinopoiskDevException При ошибках API
	 * @throws \JsonException При ошибках парсинга JSON
	 */
	public function getPositiveReviews(int $page = 1, int $limit = 10): ReviewDocsResponseDto {
		$filters = new ReviewSearchFilter();
		$filters->type('Позитивный');

		return $this->searchReviews($filters, $page, $limit);
	}

	/**
	 * Получает отрицательные рецензии
	 *
	 * @param   int  $page   Номер страницы
	 * @param   int  $limit  Результатов на странице
	 *
	 * @return ReviewDocsResponseDto Отрицательные рецензии
	 * @throws KinopoiskDevException При ошибках API
	 * @throws \JsonException При ошибках парсинга JSON
	 */
	public function getNegativeReviews(int $page = 1, int $limit = 10): ReviewDocsResponseDto {
		$filters = new ReviewSearchFilter();
		$filters->type('Негативный');

		return $this->searchReviews($filters, $page, $limit);
	}

}
