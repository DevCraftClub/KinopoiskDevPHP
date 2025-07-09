<?php

namespace KinopoiskDev\Http;

use KinopoiskDev\Exceptions\KinopoiskDevException;
use KinopoiskDev\Kinopoisk;
use KinopoiskDev\Models\Season;
use KinopoiskDev\Responses\Api\SeasonDocsResponseDto;
use KinopoiskDev\Filter\SeasonSearchFilter;

/**
 * Класс для API-запросов, связанных с сезонами
 *
 * Этот класс предоставляет методы для всех конечных точек сезонов API Kinopoisk.dev.
 * Позволяет получать информацию о сезонах сериалов, их эпизодах и связанных данных.
 *
 * @package KinopoiskDev\Http
 * @since   1.0.0
 * @author  Maxim Harder
 * @version 1.0.0
 * @see     \KinopoiskDev\Models\Season Для структуры данных сезона
 * @see     \KinopoiskDev\Filter\SeasonSearchFilter Для фильтрации запросов
 */
class SeasonRequests extends Kinopoisk {

	/**
	 * Получает сезон по его ID
	 *
	 * @api  /v1.4/season/{id}
	 * @link https://kinopoiskdev.readme.io/reference/seasoncontroller_findonev1_4
	 *
	 * @param   int  $seasonId  Уникальный идентификатор сезона
	 *
	 * @return Season Сезон со всеми доступными данными
	 * @throws KinopoiskDevException При ошибках API
	 * @throws \JsonException При ошибках парсинга JSON
	 * @throws \KinopoiskDev\Exceptions\KinopoiskResponseException При работе с API
	 */
	public function getSeasonById(int $seasonId): Season {
		$response = $this->makeRequest('GET', "season/{$seasonId}");
		$data     = $this->parseResponse($response);

		return Season::fromArray($data);
	}

	/**
	 * Получает сезоны для определенного фильма/сериала
	 *
	 * @param   int  $movieId  Идентификатор фильма/сериала
	 * @param   int  $page     Номер страницы
	 * @param   int  $limit    Результатов на странице
	 *
	 * @return SeasonDocsResponseDto Сезоны для фильма/сериала
	 * @throws KinopoiskDevException При ошибках API
	 * @throws \JsonException При ошибках парсинга JSON
	 */
	public function getSeasonsForMovie(int $movieId, int $page = 1, int $limit = 10): SeasonDocsResponseDto {
		$filters = new SeasonSearchFilter();
		$filters->movieId($movieId);

		return $this->searchSeasons($filters, $page, $limit);
	}

	/**
	 * Ищет сезоны по различным критериям
	 *
	 * @api    /v1.4/season
	 * @link   https://kinopoiskdev.readme.io/reference/seasoncontroller_findmanyv1_4
	 *
	 * @param   SeasonSearchFilter|null  $filters  Объект фильтра для поиска
	 * @param   int                      $page     Номер страницы (по умолчанию: 1)
	 * @param   int                      $limit    Количество результатов на странице (по умолчанию: 10, макс: 250)
	 *
	 * @return SeasonDocsResponseDto Результаты поиска с пагинацией
	 * @throws KinopoiskDevException При ошибках API
	 * @throws \JsonException|\KinopoiskDev\Exceptions\KinopoiskResponseException При ошибках парсинга JSON
	 */
	public function searchSeasons(?SeasonSearchFilter $filters = NULL, int $page = 1, int $limit = 10): SeasonDocsResponseDto {
		if ($limit > 250) {
			throw new KinopoiskDevException('Лимит не должен превышать 250');
		}
		if ($page < 1) {
			throw new KinopoiskDevException('Номер страницы не должен быть меньше 1');
		}

		if (is_null($filters)) {
			$filters = new SeasonSearchFilter();
		}

		$filters->addFilter('page', $page);
		$filters->addFilter('limit', $limit);
		$queryParams = $filters->getFilters();

		$response = $this->makeRequest('GET', 'season', $queryParams);
		$data     = $this->parseResponse($response);

		return new SeasonDocsResponseDto(
			docs : array_map(fn ($seasonData) => Season::fromArray($seasonData), $data['docs'] ?? []),
			total: $data['total'] ?? 0,
			limit: $data['limit'] ?? $limit,
			page : $data['page'] ?? $page,
			pages: $data['pages'] ?? 1,
		);
	}

	/**
	 * Получает определенный сезон по номеру для фильма
	 *
	 * @param   int  $movieId       Идентификатор фильма/сериала
	 * @param   int  $seasonNumber  Номер сезона
	 *
	 * @return Season|null Сезон или null если не найден
	 * @throws KinopoiskDevException При ошибках API
	 * @throws \JsonException|\KinopoiskDev\Exceptions\KinopoiskResponseException При ошибках парсинга JSON
	 */
	public function getSeasonByNumber(int $movieId, int $seasonNumber): ?Season {
		$filters = new SeasonSearchFilter();
		$filters->movieId($movieId)->number($seasonNumber);

		$result = $this->searchSeasons($filters, 1, 1);

		return $result->docs[0] ?? NULL;
	}

}