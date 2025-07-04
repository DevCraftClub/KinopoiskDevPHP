<?php

namespace KinopoiskDev\Http;

use KinopoiskDev\Kinopoisk;
use KinopoiskDev\Models\Movie;
use KinopoiskDev\Responses\MovieDocsResponseDto;

class MovieRequests extends Kinopoisk {

	/**
	 * @throws \KinopoiskDev\Exceptions\KinopoiskDevException|\JsonException
	 */
	public function getMovieById(int $movieId): Movie {
		$response = $this->makeRequest('GET', "/movie/{$movieId}");
		$data     = $this->parseResponse($response);

		return Movie::fromArray($data);
	}

	/**
	 * @api       /movie
	 * @link      https://kinopoiskdev.readme.io/reference/moviecontroller_findmanybyqueryv1_4
	 * @throws \JsonException
	 * @throws \KinopoiskDev\Exceptions\KinopoiskDevException
	 */
	public function searchMovies(array $filters = [], int $page = 1, int $limit = 10): MovieDocsResponseDto {
		$queryParams = array_merge($filters, [
			'page'  => $page,
			'limit' => $limit,
		]);

		$possibleFilters = [
			'id',
			'externalId',
			'name',
			'enName',
			'alternativeName',
			'names',
			'description',
			'shortDescription',
			'slogan',
			'type',
			'typeNumber',
			'isSeries',
			'status',
			'year',
			'releaseYears',
			'rating',
			'ratingMpaa',
			'ageRating',
			'votes',
			'seasonsInfo',
			'budget',
			'audience',
			'movieLength',
			'seriesLength',
			'totalSeriesLength',
			'genres',
			'countries',
			'poster',
			'backdrop',
			'logo',
			'ticketsOnSale',
			'videos',
			'networks',
			'persons',
			'facts',
			'fees',
			'premiere',
			'similarMovies',
			'sequelsAndPrequels',
			'watchability',
			'lists',
			'top10',
			'top250',
			'updatedAt',
			'createdAt',
		];

		$response = $this->makeRequest('GET', '/movie', $queryParams);
		$data     = $this->parseResponse($response);

		return [
			'docs'  => array_map(fn ($movieData) => Movie::fromArray($movieData), $data['docs'] ?? []),
			'total' => $data['total'] ?? 0,
			'limit' => $data['limit'] ?? $limit,
			'page'  => $data['page'] ?? $page,
			'pages' => $data['pages'] ?? 1,
		];
	}

	/**
	 * @throws \KinopoiskDev\Exceptions\KinopoiskDevException
	 * @throws \JsonException
	 */
	public function getRandomMovie(): Movie {
		$response = $this->makeRequest('GET', '/movie/random');
		$data     = $this->parseResponse($response);

		return Movie::fromArray($data);
	}

}