<?php

declare(strict_types=1);

namespace KinopoiskDev\Tests\Integration;

use KinopoiskDev\Filter\MovieSearchFilter;
use KinopoiskDev\Http\MovieRequests;
use KinopoiskDev\Models\Movie;
use KinopoiskDev\Responses\Api\MovieDocsResponseDto;
use PHPUnit\Framework\TestCase;

/**
 * Интеграционные тесты для MovieRequests
 *
 * Тестирование реальных запросов к API Kinopoisk.dev
 * с проверкой корректности обработки ответов.
 *
 * @package KinopoiskDev\Tests\Integration
 * @since   1.0.0
 * @author  Maxim Harder
 * @version 1.0.0
 * @group   integration
 * @group   api_requests
 */
final class MovieRequestsTest extends TestCase {

	private const string API_TOKEN = 'G3DZPDT-0RF4PH5-Q88SA1A-8BDT9PZ';

	private MovieRequests $movieRequests;

	protected function setUp(): void {
		$this->movieRequests = new MovieRequests(
			apiToken: self::API_TOKEN,
			httpClient: null,
			useCache: false, // Отключаем кэш для интеграционных тестов
		);
	}

	/**
	 * @test
	 * @group movie_by_id
	 */
	public function testGetMovieByIdReturnsValidMovie(): void {
		$movieId = 666; // Брат (1997)
		$movie = $this->movieRequests->getMovieById($movieId);

		$this->assertInstanceOf(Movie::class, $movie);
		$this->assertSame($movieId, $movie->getId());
		$this->assertIsString($movie->getName());
		$this->assertNotEmpty($movie->getName());
		$this->assertIsInt($movie->getYear());
		$this->assertGreaterThan(1900, $movie->getYear());
	}

	/**
	 * @test
	 * @group movie_by_id
	 */
	public function testGetMovieByIdWithNonExistentId(): void {
		$movieId = 99999999; // Несуществующий ID
		$movie = $this->movieRequests->getMovieById($movieId);

		$this->assertNull($movie);
	}

	/**
	 * @test
	 * @group movie_search
	 */
	public function testSearchMoviesWithBasicFilter(): void {
		$filter = new MovieSearchFilter();
		$filter->year(2020)->withRatingBetween(7.0, 10.0);

		$response = $this->movieRequests->searchMovies($filter, page: 1, limit: 5);

		$this->assertInstanceOf(MovieDocsResponseDto::class, $response);
		$this->assertIsArray($response->docs);
		$this->assertLessThanOrEqual(5, count($response->docs));
		$this->assertGreaterThanOrEqual(0, $response->total);

		foreach ($response->docs as $movie) {
			$this->assertInstanceOf(Movie::class, $movie);
			$this->assertSame(2020, $movie->getYear());
			if ($movie->getRating()?->kp !== null) {
				$this->assertGreaterThanOrEqual(7.0, $movie->getRating()->kp);
			}
		}
	}

	/**
	 * @test
	 * @group movie_search
	 */
	public function testSearchMoviesWithGenreFilter(): void {
		$filter = new MovieSearchFilter();
		$filter->withIncludedGenres(['драма'])
			   ->withRatingBetween(8.0, 10.0)
			   ->onlyMovies();

		$response = $this->movieRequests->searchMovies($filter, page: 1, limit: 10);

		$this->assertInstanceOf(MovieDocsResponseDto::class, $response);
		$this->assertGreaterThan(0, $response->total);

		foreach ($response->docs as $movie) {
			$this->assertInstanceOf(Movie::class, $movie);
			$this->assertFalse($movie->getIsSeries());

			$genreNames = $movie->getGenreNames();
			$this->assertContains('драма', $genreNames);

			if ($movie->getRating()?->kp !== null) {
				$this->assertGreaterThanOrEqual(8.0, $movie->getRating()->kp);
			}
		}
	}

	/**
	 * @test
	 * @group movie_search
	 */
	public function testSearchMoviesWithCountryFilter(): void {
		$filter = new MovieSearchFilter();
		$filter->withIncludedCountries(['Россия'])
			   ->yearRange(2010, 2024)
			   ->withRatingBetween(7.5, 10.0);

		$response = $this->movieRequests->searchMovies($filter, page: 1, limit: 15);

		$this->assertInstanceOf(MovieDocsResponseDto::class, $response);

		foreach ($response->docs as $movie) {
			$this->assertInstanceOf(Movie::class, $movie);
			$this->assertGreaterThanOrEqual(2010, $movie->getYear());
			$this->assertLessThanOrEqual(2024, $movie->getYear());

			$countryNames = $movie->getCountryNames();
			$this->assertContains('Россия', $countryNames);
		}
	}

	/**
	 * @test
	 * @group movie_search
	 */
	public function testSearchMoviesWithComplexFilter(): void {
		$filter = new MovieSearchFilter();
		$filter->withIncludedGenres(['триллер', 'криминал'])
			   ->withExcludedGenres(['ужасы'])
			   ->withIncludedCountries(['США'])
			   ->withYearBetween(2000, 2020)
			   ->withRatingBetween(7.0, 10.0)
			   ->withMinVotes(50000)
			   ->onlyMovies()
			   ->sortByKinopoiskRating();

		$response = $this->movieRequests->searchMovies($filter, page: 1, limit: 20);

		$this->assertInstanceOf(MovieDocsResponseDto::class, $response);

		foreach ($response->docs as $movie) {
			$this->assertInstanceOf(Movie::class, $movie);
			$this->assertFalse($movie->getIsSeries());
			$this->assertGreaterThanOrEqual(2000, $movie->getYear());
			$this->assertLessThanOrEqual(2020, $movie->getYear());

			$genreNames = $movie->getGenreNames();
			$this->assertTrue(
				in_array('триллер', $genreNames) || in_array('криминал', $genreNames),
				'Фильм должен содержать хотя бы один из указанных жанров'
			);
			$this->assertNotContains('ужасы', $genreNames);

			$countryNames = $movie->getCountryNames();
			$this->assertContains('США', $countryNames);
		}
	}

	/**
	 * @test
	 * @group movie_search
	 */
	public function testSearchMoviesByName(): void {
		$searchQuery = 'Матрица';
		$filter = new MovieSearchFilter();
		$filter->searchByName($searchQuery);

		$response = $this->movieRequests->searchMovies($filter, page: 1, limit: 10);

		$this->assertInstanceOf(MovieDocsResponseDto::class, $response);
		$this->assertGreaterThan(0, $response->total);

		$foundMatrixMovie = false;
		foreach ($response->docs as $movie) {
			$this->assertInstanceOf(Movie::class, $movie);
			if (str_contains(mb_strtolower($movie->getName() ?? ''), mb_strtolower($searchQuery))) {
				$foundMatrixMovie = true;
				break;
			}
		}

		$this->assertTrue($foundMatrixMovie, 'Должен найтись хотя бы один фильм с названием "Матрица"');
	}

	/**
	 * @test
	 * @group movie_random
	 */
	public function testGetRandomMovieReturnsValidMovie(): void {
		$movie = $this->movieRequests->getRandomMovie();

		$this->assertInstanceOf(Movie::class, $movie);
		$this->assertIsInt($movie->getId());
		$this->assertGreaterThan(0, $movie->getId());
		$this->assertIsString($movie->getName());
		$this->assertNotEmpty($movie->getName());
	}

	/**
	 * @test
	 * @group movie_awards
	 */
	public function testGetMovieAwardsReturnsValidData(): void {
		$filter = new MovieSearchFilter();
		$filter->withRatingBetween(8.0, 10.0)->onlyMovies();

		$response = $this->movieRequests->getMovieAwards($filter, page: 1, limit: 5);

		$this->assertInstanceOf(MovieAwardDocsResponseDto::class, $response);
		$this->assertIsArray($response->docs);
	}

	/**
	 * @test
	 * @group movie_pagination
	 */
	public function testPaginationWorksCorrectly(): void {
		$filter = new MovieSearchFilter();
		$filter->year(2020)->withRatingBetween(6.0, 10.0);

		// Первая страница
		$page1 = $this->movieRequests->searchMovies($filter, page: 1, limit: 5);
		
		// Вторая страница
		$page2 = $this->movieRequests->searchMovies($filter, page: 2, limit: 5);

		$this->assertInstanceOf(MovieDocsResponseDto::class, $page1);
		$this->assertInstanceOf(MovieDocsResponseDto::class, $page2);

		$this->assertSame(1, $page1->page);
		$this->assertSame(2, $page2->page);
		$this->assertSame($page1->total, $page2->total);

		// Проверяем, что фильмы на разных страницах разные
		$page1Ids = array_map(fn($movie) => $movie->getId(), $page1->docs);
		$page2Ids = array_map(fn($movie) => $movie->getId(), $page2->docs);

		$this->assertEmpty(array_intersect($page1Ids, $page2Ids), 
			'Фильмы на разных страницах не должны пересекаться');
	}

	/**
	 * @test
	 * @group movie_sorting
	 */
	public function testSortingByRatingWorks(): void {
		$filter = new MovieSearchFilter();
		$filter->withIncludedGenres(['драма'])
			   ->withRatingBetween(7.0, 10.0)
			   ->withMinVotes(10000)
			   ->sortByKinopoiskRating();

		$response = $this->movieRequests->searchMovies($filter, page: 1, limit: 10);

		$this->assertInstanceOf(MovieDocsResponseDto::class, $response);
		$this->assertGreaterThan(1, count($response->docs));

		$previousRating = 10.0;
		foreach ($response->docs as $movie) {
			$currentRating = $movie->getRating()?->kp ?? 0;
			$this->assertLessThanOrEqual($previousRating, $currentRating, 
				'Фильмы должны быть отсортированы по убыванию рейтинга');
			$previousRating = $currentRating;
		}
	}

	/**
	 * @test
	 * @group movie_filtering
	 */
	public function testSeriesOnlyFilter(): void {
		$filter = new MovieSearchFilter();
		$filter->onlySeries()
			   ->withRatingBetween(8.0, 10.0)
			   ->withIncludedGenres(['драма']);

		$response = $this->movieRequests->searchMovies($filter, page: 1, limit: 10);

		$this->assertInstanceOf(MovieDocsResponseDto::class, $response);

		foreach ($response->docs as $movie) {
			$this->assertInstanceOf(Movie::class, $movie);
			$this->assertTrue($movie->getIsSeries(), 'Все результаты должны быть сериалами');
		}
	}

	/**
	 * @test
	 * @group movie_filtering
	 */
	public function testMoviesOnlyFilter(): void {
		$filter = new MovieSearchFilter();
		$filter->onlyMovies()
			   ->withRatingBetween(8.0, 10.0)
			   ->withIncludedGenres(['комедия']);

		$response = $this->movieRequests->searchMovies($filter, page: 1, limit: 10);

		$this->assertInstanceOf(MovieDocsResponseDto::class, $response);

		foreach ($response->docs as $movie) {
			$this->assertInstanceOf(Movie::class, $movie);
			$this->assertFalse($movie->getIsSeries(), 'Все результаты должны быть фильмами');
		}
	}

	/**
	 * @test
	 * @group movie_top
	 */
	public function testTop250MoviesFilter(): void {
		$filter = new MovieSearchFilter();
		$filter->inTop250();

		$response = $this->movieRequests->searchMovies($filter, page: 1, limit: 10);

		$this->assertInstanceOf(MovieDocsResponseDto::class, $response);
		$this->assertGreaterThan(0, $response->total);

		foreach ($response->docs as $movie) {
			$this->assertInstanceOf(Movie::class, $movie);
			$this->assertNotNull($movie->getTop250());
			$this->assertLessThanOrEqual(250, $movie->getTop250());
		}
	}

	/**
	 * @test
	 * @group performance
	 */
	public function testLargeResultSetHandling(): void {
		$filter = new MovieSearchFilter();
		$filter->withRatingBetween(1.0, 10.0); // Широкий диапазон для большого количества результатов

		$startTime = microtime(true);
		$response = $this->movieRequests->searchMovies($filter, page: 1, limit: 250);
		$endTime = microtime(true);

		$this->assertInstanceOf(MovieDocsResponseDto::class, $response);
		$this->assertLessThanOrEqual(250, count($response->docs));
		$this->assertLessThan(10.0, $endTime - $startTime, 'Запрос должен выполняться за разумное время');
	}

	/**
	 * @test
	 * @group edge_cases
	 */
	public function testEmptyResultSet(): void {
		$filter = new MovieSearchFilter();
		$filter->year(1800) // Год, в который точно не было фильмов
			   ->withRatingBetween(9.5, 10.0);

		$response = $this->movieRequests->searchMovies($filter, page: 1, limit: 10);

		$this->assertInstanceOf(MovieDocsResponseDto::class, $response);
		$this->assertSame(0, $response->total);
		$this->assertEmpty($response->docs);
	}

	/**
	 * @test
	 * @group data_integrity
	 */
	public function testMovieDataIntegrity(): void {
		$movieId = 666; // Брат (1997)
		$movie = $this->movieRequests->getMovieById($movieId);

		$this->assertInstanceOf(Movie::class, $movie);

		// Проверяем основные поля
		$this->assertIsInt($movie->getId());
		$this->assertIsString($movie->getName());
		$this->assertIsInt($movie->getYear());

		// Проверяем рейтинг
		if ($movie->getRating() !== null) {
			$this->assertIsFloat($movie->getRating()->kp);
			$this->assertGreaterThanOrEqual(0.0, $movie->getRating()->kp);
			$this->assertLessThanOrEqual(10.0, $movie->getRating()->kp);
		}

		// Проверяем жанры
		$this->assertIsArray($movie->getGenres());
		foreach ($movie->getGenres() as $genre) {
			$this->assertNotEmpty($genre->name);
		}

		// Проверяем страны
		$this->assertIsArray($movie->getCountries());
		foreach ($movie->getCountries() as $country) {
			$this->assertNotEmpty($country->name);
		}

		// Проверяем постер
		if ($movie->getPoster() !== null) {
			$this->assertIsString($movie->getPoster()->url);
			$this->assertStringStartsWith('http', $movie->getPoster()->url);
		}
	}

	protected function tearDown(): void {
		unset($this->movieRequests);
	}
}