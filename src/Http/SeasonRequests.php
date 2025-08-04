<?php

namespace KinopoiskDev\Http;

use KinopoiskDev\Exceptions\KinopoiskDevException;
use KinopoiskDev\Filter\SeasonSearchFilter;
use KinopoiskDev\Kinopoisk;
use KinopoiskDev\Models\Season;
use KinopoiskDev\Responses\Api\SeasonDocsResponseDto;

/**
 * Класс для API-запросов, связанных с сезонами
 *
 * Предоставляет методы для работы с сезонами сериалов через API Kinopoisk.dev.
 * Включает получение информации о сезонах, их эпизодах, поиск по различным
 * критериям и фильтрацию. Поддерживает работу с многосезонными сериалами.
 *
 * Основные возможности:
 * - Получение сезона по ID
 * - Получение всех сезонов сериала
 * - Поиск сезонов по различным критериям
 * - Получение сезона по номеру и ID фильма
 * - Фильтрация по номеру сезона, количеству эпизодов
 * - Пагинация результатов
 *
 * @package KinopoiskDev\Http
 * @since   1.0.0
 * @author  Maxim Harder
 * @version 1.0.0
 *
 * @see     \KinopoiskDev\Filter\SeasonSearchFilter Для настройки фильтрации
 * @see     \KinopoiskDev\Models\Season Модель сезона
 * @see     \KinopoiskDev\Responses\Api\SeasonDocsResponseDto Ответ с сезонами
 * @link    https://kinopoiskdev.readme.io/reference/
 *
 * @example
 * ```php
 * $seasonRequests = new SeasonRequests('your-api-token');
 *
 * // Получение сезона по ID
 * $season = $seasonRequests->getSeasonById(123);
 *
 * // Получение всех сезонов сериала
 * $seasons = $seasonRequests->getSeasonsForMovie(456);
 *
 * // Поиск сезонов с фильтрами
 * $filter = new SeasonSearchFilter();
 * $filter->number(1)->episodesCount(10, 20);
 * $seasons = $seasonRequests->searchSeasons($filter, 1, 20);
 *
 * // Получение конкретного сезона по номеру
 * $season = $seasonRequests->getSeasonByNumber(456, 2);
 * ```
 */
class SeasonRequests extends Kinopoisk {

	/**
	 * Получает сезон по его ID
	 *
	 * Выполняет запрос к API для получения полной информации о сезоне
	 * по его уникальному идентификатору. Возвращает объект Season
	 * со всеми доступными данными: названием, номером, эпизодами,
	 * датами выхода и другими метаданными.
	 *
	 * @api     /v1.4/season/{id}
	 * @since   1.0.0
	 *
	 * @link    https://kinopoiskdev.readme.io/reference/seasoncontroller_findonev1_4
	 *
	 * @param   int  $seasonId  Уникальный идентификатор сезона в системе Kinopoisk
	 *
	 * @return Season Сезон со всеми доступными данными
	 * @throws KinopoiskDevException При ошибках API или проблемах с сетью
	 * @throws KinopoiskResponseException При ошибках HTTP-запроса (401, 403, 404)
	 * @throws \JsonException При ошибках парсинга JSON-ответа
	 *
	 * @example
	 * ```php
	 * $season = $seasonRequests->getSeasonById(123);
	 * echo $season->name; // Название сезона
	 * echo $season->number; // Номер сезона
	 * echo count($season->episodes); // Количество эпизодов
	 * ```
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
	 * @param   int                      $limit    Количество результатов на странице (по умолчанию: 10)
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
	 * Получает сезон по ID фильма и номеру сезона
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