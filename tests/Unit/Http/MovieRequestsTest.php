<?php

declare(strict_types=1);

namespace KinopoiskDev\Tests\Unit\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use KinopoiskDev\Exceptions\KinopoiskDevException;
use KinopoiskDev\Filter\MovieSearchFilter;
use KinopoiskDev\Http\MovieRequests;
use KinopoiskDev\Models\Movie;
use KinopoiskDev\Responses\Api\MovieAwardDocsResponseDto;
use KinopoiskDev\Responses\Api\MovieDocsResponseDto;
use KinopoiskDev\Responses\Api\SearchMovieResponseDto;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 * @group http
 * @group movie-requests
 */
class MovieRequestsTest extends TestCase {

	private MockHandler   $mockHandler;
	private HandlerStack  $handlerStack;
	private Client        $httpClient;
	private MovieRequests $movieRequests;

	public function test_getMovieById_withValidId_returnsMovie(): void {
		$movieData = [
			'id'          => 123,
			'name'        => 'Test Movie',
			'year'        => 2023,
			'description' => 'Test description',
		];

		$response = new Response(200, [], json_encode($movieData));
		$this->mockHandler->append($response);

		$movie = $this->movieRequests->getMovieById(123);

		$this->assertInstanceOf(Movie::class, $movie);
		$this->assertEquals(123, $movie->id);
		$this->assertEquals('Test Movie', $movie->name);
		$this->assertEquals(2023, $movie->year);
	}

	public function test_getMovieById_withInvalidId_throwsException(): void {
		$errorResponse = new Response(404, [], json_encode(['error' => 'Movie not found']));
		$this->mockHandler->append($errorResponse);

		$this->expectException(KinopoiskDevException::class);
		$this->expectExceptionMessage('Ошибка HTTP запроса: Client error: `GET /v1.4//movie/999999` resulted in a `404 Not Found` response:');

		$this->movieRequests->getMovieById(999999);
	}

	public function test_getRandomMovie_withoutFilters_returnsMovie(): void {
		$movieData = [
			'id'   => 456,
			'name' => 'Random Movie',
			'year' => 2022,
		];

		$response = new Response(200, [], json_encode($movieData));
		$this->mockHandler->append($response);

		$movie = $this->movieRequests->getRandomMovie();

		$this->assertInstanceOf(Movie::class, $movie);
		$this->assertEquals(456, $movie->id);
		$this->assertEquals('Random Movie', $movie->name);
	}

	public function test_getRandomMovie_withFilters_returnsFilteredMovie(): void {
		$movieData = [
			'id'   => 789,
			'name' => 'Filtered Movie',
			'year' => 2023,
		];

		$response = new Response(200, [], json_encode($movieData));
		$this->mockHandler->append($response);

		$filter = new MovieSearchFilter();
		$filter->year(2023)->rating(7.0);

		$movie = $this->movieRequests->getRandomMovie($filter);

		$this->assertInstanceOf(Movie::class, $movie);
		$this->assertEquals(789, $movie->id);
	}

	/**
	 * @dataProvider possibleValuesFieldProvider
	 */
	public function test_getPossibleValuesByField_withValidField_returnsValues(string $field): void {
		$valuesData = [
			['name' => 'драма', 'slug' => 'drama'],
			['name' => 'комедия', 'slug' => 'komediya'],
			['name' => 'боевик', 'slug' => 'boevik'],
		];

		$response = new Response(200, [], json_encode($valuesData));
		$this->mockHandler->append($response);

		$values = $this->movieRequests->getPossibleValuesByField($field);

		$this->assertIsArray($values);
		$this->assertCount(3, $values);
		$this->assertEquals('драма', $values[0]['name']);
		$this->assertEquals('drama', $values[0]['slug']);
	}

	public function possibleValuesFieldProvider(): array {
		return [
			'genres'      => ['genres.name'],
			'countries'   => ['countries.name'],
			'type'        => ['type'],
			'type_number' => ['typeNumber'],
			'status'      => ['status'],
		];
	}

	public function test_getPossibleValuesByField_withInvalidField_throwsException(): void {
		$this->expectException(KinopoiskDevException::class);
		$this->expectExceptionMessage('Лишь следующие поля поддерживаются для этого запроса: genres.name, countries.name, type, typeNumber, status');

		$this->movieRequests->getPossibleValuesByField('invalid_field');
	}

	public function test_getMovieAwards_withoutFilters_returnsAwards(): void {
		$awardsData = [
			'docs'  => [
				[
					'id'         => '6608cf153bb63c827e2c35d6',
					'nomination' => [
						'award' => [
							'title' => 'Оскар',
							'year'  => 2024,
						],
						'title' => 'Лучший грим и прически',
					],
					'winning'    => FALSE,
					'movieId'    => 4664634,
					'createdAt'  => '2024-03-31T02:48:53.006Z',
					'updatedAt'  => '2024-03-31T02:48:53.006Z',
				],
			],
			'total' => 1,
			'limit' => 10,
			'page'  => 1,
			'pages' => 1,
		];

		$response = new Response(200, [], json_encode($awardsData));
		$this->mockHandler->append($response);

		$result = $this->movieRequests->getMovieAwards();

		$this->assertInstanceOf(MovieAwardDocsResponseDto::class, $result);
		$this->assertEquals(1, $result->total);
		$this->assertEquals(10, $result->limit);
		$this->assertEquals(1, $result->page);
	}

	public function test_getMovieAwards_withFilters_returnsFilteredAwards(): void {
		$awardsData = [
			'docs'  => [
				[
					'id'         => '6608cf143bb63c827e2c35c8',
					'nomination' => [
						'award' => [
							'title' => 'Оскар',
							'year'  => 2024,
						],
						'title' => 'Лучшая мужская роль',
					],
					'winning'    => TRUE,
					'movieId'    => 4664634,
					'createdAt'  => '2024-03-31T02:48:52.928Z',
					'updatedAt'  => '2024-03-31T02:48:52.928Z',
				],
			],
			'total' => 1,
			'limit' => 50,
			'page'  => 1,
			'pages' => 1,
		];

		$response = new Response(200, [], json_encode($awardsData));
		$this->mockHandler->append($response);

		$filter = new MovieSearchFilter();
		$filter->year(2023);

		$result = $this->movieRequests->getMovieAwards($filter, 1, 50);

		$this->assertInstanceOf(MovieAwardDocsResponseDto::class, $result);
		$this->assertEquals(1, $result->total);
		$this->assertEquals(50, $result->limit);
	}

	public function test_searchByName_withValidQuery_returnsMovies(): void {
		$searchData = [
			'docs'  => [
				[
					'id'   => 123,
					'name' => 'Test Movie',
					'year' => 2023,
				],
			],
			'total' => 1,
			'limit' => 10,
			'page'  => 1,
			'pages' => 1,
		];

		$response = new Response(200, [], json_encode($searchData));
		$this->mockHandler->append($response);

		$result = $this->movieRequests->searchByName('Test Movie');

		$this->assertInstanceOf(SearchMovieResponseDto::class, $result);
		$this->assertEquals(1, $result->total);
		$this->assertCount(1, $result->docs);
		$this->assertEquals('Test Movie', $result->docs[0]->name);
	}

	public function test_getLatestMovies_withYear_returnsMovies(): void {
		$moviesData = [
			'docs'  => [
				[
					'id'   => 456,
					'name' => 'Latest Movie',
					'year' => 2023,
				],
			],
			'total' => 1,
			'limit' => 10,
			'page'  => 1,
			'pages' => 1,
		];

		$response = new Response(200, [], json_encode($moviesData));
		$this->mockHandler->append($response);

		$result = $this->movieRequests->getLatestMovies(2023);

		$this->assertInstanceOf(MovieDocsResponseDto::class, $result);
		$this->assertEquals(1, $result->total);
		$this->assertCount(1, $result->docs);
	}

	public function test_getMoviesByGenre_withSingleGenre_returnsMovies(): void {
		$moviesData = [
			'docs'  => [
				[
					'id'     => 789,
					'name'   => 'Action Movie',
					'genres' => [['name' => 'Action']],
				],
			],
			'total' => 1,
			'limit' => 10,
			'page'  => 1,
			'pages' => 1,
		];

		$response = new Response(200, [], json_encode($moviesData));
		$this->mockHandler->append($response);

		$result = $this->movieRequests->getMoviesByGenre('Action');

		$this->assertInstanceOf(MovieDocsResponseDto::class, $result);
		$this->assertEquals(1, $result->total);
	}

	public function test_getMoviesByGenre_withMultipleGenres_returnsMovies(): void {
		$moviesData = [
			'docs'  => [
				[
					'id'     => 101,
					'name'   => 'Action Drama',
					'genres' => [['name' => 'Action'], ['name' => 'Drama']],
				],
			],
			'total' => 1,
			'limit' => 10,
			'page'  => 1,
			'pages' => 1,
		];

		$response = new Response(200, [], json_encode($moviesData));
		$this->mockHandler->append($response);

		$result = $this->movieRequests->getMoviesByGenre(['Action', 'Drama']);

		$this->assertInstanceOf(MovieDocsResponseDto::class, $result);
		$this->assertEquals(1, $result->total);
	}

	public function test_searchMovies_withFilters_returnsMovies(): void {
		$moviesData = [
			'docs'  => [
				[
					'id'     => 202,
					'name'   => 'Filtered Movie',
					'year'   => 2023,
					'rating' => ['kp' => 8.5],
				],
			],
			'total' => 1,
			'limit' => 20,
			'page'  => 1,
			'pages' => 1,
		];

		$response = new Response(200, [], json_encode($moviesData));
		$this->mockHandler->append($response);

		$filter = new MovieSearchFilter();
		$filter->year(2023)->rating(8.0);

		$result = $this->movieRequests->searchMovies($filter, 1, 20);

		$this->assertInstanceOf(MovieDocsResponseDto::class, $result);
		$this->assertEquals(1, $result->total);
		$this->assertEquals(20, $result->limit);
	}

	public function test_getMoviesByCountry_withSingleCountry_returnsMovies(): void {
		$moviesData = [
			'docs'  => [
				[
					'id'        => 303,
					'name'      => 'Russian Movie',
					'countries' => [['name' => 'Россия']],
				],
			],
			'total' => 1,
			'limit' => 10,
			'page'  => 1,
			'pages' => 1,
		];

		$response = new Response(200, [], json_encode($moviesData));
		$this->mockHandler->append($response);

		$result = $this->movieRequests->getMoviesByCountry('Россия');

		$this->assertInstanceOf(MovieDocsResponseDto::class, $result);
		$this->assertEquals(1, $result->total);
	}

	public function test_getMoviesByYearRange_withValidRange_returnsMovies(): void {
		$moviesData = [
			'docs'  => [
				[
					'id'   => 404,
					'name' => 'Range Movie',
					'year' => 2022,
				],
			],
			'total' => 1,
			'limit' => 10,
			'page'  => 1,
			'pages' => 1,
		];

		$response = new Response(200, [], json_encode($moviesData));
		$this->mockHandler->append($response);

		$result = $this->movieRequests->getMoviesByYearRange(2020, 2025);

		$this->assertInstanceOf(MovieDocsResponseDto::class, $result);
		$this->assertEquals(1, $result->total);
	}

	public function test_makeRequest_withNetworkError_throwsException(): void {
		$request   = new Request('GET', 'http://example.com');
		$exception = new RequestException('Network error', $request);

		$this->mockHandler->append($exception);

		$this->expectException(KinopoiskDevException::class);
		$this->expectExceptionMessage('Ошибка HTTP запроса: Network error');

		$this->movieRequests->getMovieById(123);
	}

	public function test_makeRequest_withServerError_throwsException(): void {
		$errorResponse = new Response(500, [], json_encode(['error' => 'Internal server error']));
		$this->mockHandler->append($errorResponse);

		$this->expectException(KinopoiskDevException::class);
		$this->expectExceptionMessage('Ошибка HTTP запроса: Server error: `GET /v1.4//movie/123` resulted in a `500 Internal Server Error` response:');

		$this->movieRequests->getMovieById(123);
	}

	public function test_makeRequest_withUnauthorized_throwsException(): void {
		$errorResponse = new Response(401, [], json_encode(['error' => 'Unauthorized']));
		$this->mockHandler->append($errorResponse);

		$this->expectException(KinopoiskDevException::class);
		$this->expectExceptionMessage('Ошибка HTTP запроса: Client error: `GET /v1.4//movie/123` resulted in a `401 Unauthorized` response:');

		$this->movieRequests->getMovieById(123);
	}

	public function test_makeRequest_withForbidden_throwsException(): void {
		$errorResponse = new Response(403, [], json_encode(['error' => 'Forbidden']));
		$this->mockHandler->append($errorResponse);

		$this->expectException(KinopoiskDevException::class);
		$this->expectExceptionMessage('Ошибка HTTP запроса: Client error: `GET /v1.4//movie/123` resulted in a `403 Forbidden` response:');

		$this->movieRequests->getMovieById(123);
	}

	protected function setUp(): void {
		parent::setUp();

		$this->mockHandler  = new MockHandler();
		$this->handlerStack = HandlerStack::create($this->mockHandler);
		$this->httpClient   = new Client(['handler' => $this->handlerStack]);

		$this->movieRequests = new MovieRequests(
			apiToken  : 'MOCK123-TEST456-UNIT789-TOKEN01',
			httpClient: $this->httpClient,
		);
	}

	protected function tearDown(): void {
		parent::tearDown();
	}

}