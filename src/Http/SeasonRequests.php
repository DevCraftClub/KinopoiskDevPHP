<?php

namespace KinopoiskDev\Http;

use KinopoiskDev\Exceptions\KinopoiskDevException;
use KinopoiskDev\Kinopoisk;
use KinopoiskDev\Models\Season;
use KinopoiskDev\Responses\Api\SeasonDocsResponseDto;
use KinopoiskDev\Responses\EpisodeDocsResponseDto;
use KinopoiskDev\Types\EpisodeSearchFilter;
use KinopoiskDev\Types\SeasonSearchFilter;

/**
 * Klasse für Season-spezifische API-Anfragen
 *
 * Diese Klasse bietet Methoden für alle Season-Endpunkte der Kinopoisk.dev API.
 *
 * @package KinopoiskDev\Http
 * @since   1.0.0
 * @author  Maxim Harder
 * @version 1.0.0
 */
class SeasonRequests extends Kinopoisk {

	/**
	 * Holt eine Season nach ihrer ID
	 *
	 * @api  /v1.4/season/{id}
	 * @link https://kinopoiskdev.readme.io/reference/seasoncontroller_findonev1_4
	 *
	 * @param   string  $seasonId  Die eindeutige ID der Season
	 *
	 * @return Season Die Season mit allen verfügbaren Daten
	 * @throws KinopoiskDevException Bei API-Fehlern
	 * @throws \JsonException Bei JSON-Parsing-Fehlern
	 */
	public function getSeasonById(string $seasonId): Season {
		$response = $this->makeRequest('GET', "/season/{$seasonId}");
		$data     = $this->parseResponse($response);

		return Season::fromArray($data);
	}

	/**
	 * Holt Seasons für einen bestimmten Film/Serie
	 *
	 * @param   int  $movieId  Film/Serie-ID
	 * @param   int  $page     Seitennummer
	 * @param   int  $limit    Ergebnisse pro Seite
	 *
	 * @return SeasonDocsResponseDto Seasons für den Film/Serie
	 * @throws KinopoiskDevException Bei API-Fehlern
	 * @throws \JsonException Bei JSON-Parsing-Fehlern
	 */
	public function getSeasonsForMovie(int $movieId, int $page = 1, int $limit = 10): SeasonDocsResponseDto {
		$filters = new SeasonSearchFilter();
		$filters->movieId($movieId);

		return $this->searchSeasons($filters, $page, $limit);
	}

	/**
	 * Sucht Seasons nach verschiedenen Kriterien
	 *
	 * @api    /v1.4/season
	 * @link   https://kinopoiskdev.readme.io/reference/seasoncontroller_findmanybyqueryv1_4
	 *
	 * @param   SeasonSearchFilter|null  $filters  Filter-Objekt für die Suche
	 * @param   int                      $page     Seitennummer (Standard: 1)
	 * @param   int                      $limit    Anzahl Ergebnisse pro Seite (Standard: 10, Max: 250)
	 *
	 * @return SeasonDocsResponseDto Suchergebnisse mit Paginierung
	 * @throws KinopoiskDevException Bei API-Fehlern
	 * @throws \JsonException Bei JSON-Parsing-Fehlern
	 */
	public function searchSeasons(?SeasonSearchFilter $filters = NULL, int $page = 1, int $limit = 10): SeasonDocsResponseDto {
		if ($limit > 250) {
			throw new KinopoiskDevException('Limit darf nicht größer als 250 sein');
		}

		if (is_null($filters)) {
			$filters = new SeasonSearchFilter();
		}

		$filters->addFilter('page', $page);
		$filters->addFilter('limit', $limit);
		$queryParams = $filters->getFilters();

		$response = $this->makeRequest('GET', '/season', $queryParams);
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
	 * Holt eine bestimmte Season-Nummer für einen Film
	 *
	 * @param   int  $movieId       Film/Serie-ID
	 * @param   int  $seasonNumber  Season-Nummer
	 *
	 * @return Season|null Die Season oder null wenn nicht gefunden
	 * @throws KinopoiskDevException Bei API-Fehlern
	 * @throws \JsonException Bei JSON-Parsing-Fehlern
	 */
	public function getSeasonByNumber(int $movieId, int $seasonNumber): ?Season {
		$filters = new SeasonSearchFilter();
		$filters->movieId($movieId)->number($seasonNumber);

		$result = $this->searchSeasons($filters, 1, 1);

		return $result->docs[0] ?? NULL;
	}

}