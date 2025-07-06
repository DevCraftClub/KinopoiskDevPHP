<?php

namespace KinopoiskDev\Http;

use KinopoiskDev\Kinopoisk;
use KinopoiskDev\Models\Movie;
use KinopoiskDev\Responses\MovieDocsResponseDto;
use KinopoiskDev\Types\MovieSearchFilter;

class MovieRequests extends Kinopoisk {

	/**
	 * @api  /v1.4/movie/{id}
	 * @link https://kinopoiskdev.readme.io/reference/moviecontroller_findonev1_4
	 * @throws \KinopoiskDev\Exceptions\KinopoiskDevException|\JsonException
	 */
	public function getMovieById(int $movieId): Movie {
		$response = $this->makeRequest('GET', "/movie/{$movieId}");
		$data     = $this->parseResponse($response);

		return Movie::fromArray($data);
	}

	/**
	 * @api    /v1.4/movie
	 * @link   https://kinopoiskdev.readme.io/reference/moviecontroller_findmanybyqueryv1_4
	 * @throws \JsonException
	 * @throws \KinopoiskDev\Exceptions\KinopoiskDevException
	 */
	public function searchMovies(?MovieSearchFilter $filters = NULL, int $page = 1, int $limit = 10): MovieDocsResponseDto {
		if (is_null($filters)) {
			$filters = new MovieSearchFilter();
		}
		$filters->addFilter('page', $page);
		$filters->addFilter('limit', $limit);
		$queryParams = $filters->getFilters();

		$response = $this->makeRequest('GET', '/movie', $queryParams);
		$data     = $this->parseResponse($response);

		return new MovieDocsResponseDto(
			docs : array_map(fn ($movieData) => Movie::fromArray($movieData), $data['docs'] ?? []),
			total: $data['total'] ?? 0,
			limit: $data['limit'] ?? $limit,
			page : $data['page'] ?? $page,
			pages: $data['pages'] ?? 1,
		);
	}

	/**
	 * @api    /v1.4/movie/random
	 * @link   https://kinopoiskdev.readme.io/reference/moviecontroller_getrandommoviev1_4
	 * @throws \KinopoiskDev\Exceptions\KinopoiskDevException
	 * @throws \JsonException
	 */
	public function getRandomMovie(?MovieSearchFilter $filters = NULL): Movie {
		if (is_null($filters)) {
			$filters = new MovieSearchFilter();
		}
		$queryParams = $filters->getFilters();

		$response = $this->makeRequest('GET', '/movie/random', $queryParams);
		$data     = $this->parseResponse($response);

		return Movie::fromArray($data);
	}

	/**
	 * @api    /v1.4/movie/awards
	 * @link   https://kinopoiskdev.readme.io/reference/moviecontroller_getrandommoviev1_4
	 * @throws \KinopoiskDev\Exceptions\KinopoiskDevException
	 * @throws \JsonException
	 */
	public function getMovieAwards(?MovieSearchFilter $filters = NULL, int $page = 1, int $limit = 10): Movie {
		if (is_null($filters)) {
			$filters = new MovieSearchFilter();
		}
		$filters->addFilter('page', $page);
		$filters->addFilter('limit', $limit);
		$queryParams = $filters->getFilters();

		$response = $this->makeRequest('GET', '/movie/random', $queryParams);
		$data     = $this->parseResponse($response);

		return Movie::fromArray($data);
	}

}