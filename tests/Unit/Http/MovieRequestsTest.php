<?php

declare(strict_types=1);

namespace Tests\Unit\Http;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use KinopoiskDev\Http\MovieRequests;
use KinopoiskDev\Filter\MovieSearchFilter;
use KinopoiskDev\Models\Movie;
use KinopoiskDev\Models\MovieAward;
use KinopoiskDev\Responses\Api\MovieDocsResponseDto;
use KinopoiskDev\Responses\Api\MovieAwardDocsResponseDto;
use KinopoiskDev\Responses\Api\SearchMovieResponseDto;
use KinopoiskDev\Responses\Api\PossibleValueDto;
use KinopoiskDev\Exceptions\KinopoiskDevException;
use KinopoiskDev\Exceptions\KinopoiskResponseException;
use KinopoiskDev\Enums\FilterField;

/**
 * @group unit
 * @group http
 * @group movie-requests
 */
class MovieRequestsTest extends TestCase
{
    private const string VALID_API_TOKEN = 'ABC1DEF-2GH3IJK-4LM5NOP-6QR7STU';
    
    private MockHandler $mockHandler;
    private HandlerStack $handlerStack;
    private Client $httpClient;
    private MovieRequests $movieRequests;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->mockHandler = new MockHandler();
        $this->handlerStack = HandlerStack::create($this->mockHandler);
        $this->httpClient = new Client(['handler' => $this->handlerStack]);
        
        $this->movieRequests = new MovieRequests(
            apiToken: self::VALID_API_TOKEN,
            httpClient: $this->httpClient
        );
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function test_getMovieById_withValidId_returnsMovie(): void
    {
        $movieData = [
            'id' => 123,
            'name' => 'Test Movie',
            'year' => 2023,
            'description' => 'Test description'
        ];
        
        $response = new Response(200, [], json_encode($movieData));
        $this->mockHandler->append($response);
        
        $movie = $this->movieRequests->getMovieById(123);
        
        $this->assertInstanceOf(Movie::class, $movie);
        $this->assertEquals(123, $movie->getId());
        $this->assertEquals('Test Movie', $movie->getName());
        $this->assertEquals(2023, $movie->getYear());
    }

    public function test_getMovieById_withInvalidId_throwsException(): void
    {
        $errorResponse = new Response(404, [], json_encode(['error' => 'Movie not found']));
        $this->mockHandler->append($errorResponse);
        
        $this->expectException(KinopoiskResponseException::class);
        $this->expectExceptionMessage('Not Found: Запрашиваемый ресурс не найден');
        
        $this->movieRequests->getMovieById(999999);
    }

    public function test_getRandomMovie_withoutFilters_returnsMovie(): void
    {
        $movieData = [
            'id' => 456,
            'name' => 'Random Movie',
            'year' => 2022
        ];
        
        $response = new Response(200, [], json_encode($movieData));
        $this->mockHandler->append($response);
        
        $movie = $this->movieRequests->getRandomMovie();
        
        $this->assertInstanceOf(Movie::class, $movie);
        $this->assertEquals(456, $movie->getId());
        $this->assertEquals('Random Movie', $movie->getName());
    }

    public function test_getRandomMovie_withFilters_returnsFilteredMovie(): void
    {
        $movieData = [
            'id' => 789,
            'name' => 'Filtered Movie',
            'year' => 2023
        ];
        
        $response = new Response(200, [], json_encode($movieData));
        $this->mockHandler->append($response);
        
        $filter = new MovieSearchFilter();
        $filter->year(2023)->rating(7.0);
        
        $movie = $this->movieRequests->getRandomMovie($filter);
        
        $this->assertInstanceOf(Movie::class, $movie);
        $this->assertEquals(789, $movie->getId());
    }

    /**
     * @dataProvider possibleValuesFieldProvider
     */
    public function test_getPossibleValuesByField_withValidField_returnsValues(string $field): void
    {
        $valuesData = [
            ['name' => 'Action', 'slug' => 'action'],
            ['name' => 'Drama', 'slug' => 'drama'],
            ['name' => 'Comedy', 'slug' => 'comedy']
        ];
        
        $response = new Response(200, [], json_encode($valuesData));
        $this->mockHandler->append($response);
        
        $values = $this->movieRequests->getPossibleValuesByField($field);
        
        $this->assertIsArray($values);
        $this->assertCount(3, $values);
        $this->assertEquals('Action', $values[0]['name']);
        $this->assertEquals('action', $values[0]['slug']);
    }

    public function possibleValuesFieldProvider(): array
    {
        return [
            'genres' => [FilterField::GENRES->value],
            'countries' => [FilterField::COUNTRIES->value],
            'type' => [FilterField::TYPE->value],
            'type_number' => [FilterField::TYPE_NUMBER->value],
            'status' => [FilterField::STATUS->value],
        ];
    }

    public function test_getPossibleValuesByField_withInvalidField_throwsException(): void
    {
        $this->expectException(KinopoiskDevException::class);
        $this->expectExceptionMessage('Лишь следующие поля поддерживаются для этого запроса: genres, countries, type, type_number, status');
        
        $this->movieRequests->getPossibleValuesByField('invalid_field');
    }

    public function test_getMovieAwards_withoutFilters_returnsAwards(): void
    {
        $awardsData = [
            'docs' => [
                [
                    'id' => 1,
                    'name' => 'Oscar',
                    'nomination' => 'Best Picture'
                ]
            ],
            'total' => 1,
            'limit' => 10,
            'page' => 1,
            'pages' => 1
        ];
        
        $response = new Response(200, [], json_encode($awardsData));
        $this->mockHandler->append($response);
        
        $result = $this->movieRequests->getMovieAwards();
        
        $this->assertInstanceOf(MovieAwardDocsResponseDto::class, $result);
        $this->assertEquals(1, $result->total);
        $this->assertEquals(10, $result->limit);
        $this->assertEquals(1, $result->page);
    }

    public function test_getMovieAwards_withFilters_returnsFilteredAwards(): void
    {
        $awardsData = [
            'docs' => [
                [
                    'id' => 2,
                    'name' => 'Golden Globe',
                    'nomination' => 'Best Actor'
                ]
            ],
            'total' => 1,
            'limit' => 50,
            'page' => 1,
            'pages' => 1
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

    public function test_searchByName_withValidQuery_returnsMovies(): void
    {
        $searchData = [
            'docs' => [
                [
                    'id' => 123,
                    'name' => 'Test Movie',
                    'year' => 2023
                ]
            ],
            'total' => 1,
            'limit' => 10,
            'page' => 1,
            'pages' => 1
        ];
        
        $response = new Response(200, [], json_encode($searchData));
        $this->mockHandler->append($response);
        
        $result = $this->movieRequests->searchByName('Test Movie');
        
        $this->assertInstanceOf(SearchMovieResponseDto::class, $result);
        $this->assertEquals(1, $result->total);
        $this->assertCount(1, $result->docs);
        $this->assertEquals('Test Movie', $result->docs[0]->name);
    }

    public function test_getLatestMovies_withYear_returnsMovies(): void
    {
        $moviesData = [
            'docs' => [
                [
                    'id' => 456,
                    'name' => 'Latest Movie',
                    'year' => 2023
                ]
            ],
            'total' => 1,
            'limit' => 10,
            'page' => 1,
            'pages' => 1
        ];
        
        $response = new Response(200, [], json_encode($moviesData));
        $this->mockHandler->append($response);
        
        $result = $this->movieRequests->getLatestMovies(2023);
        
        $this->assertInstanceOf(MovieDocsResponseDto::class, $result);
        $this->assertEquals(1, $result->total);
        $this->assertCount(1, $result->docs);
    }

    public function test_getMoviesByGenre_withSingleGenre_returnsMovies(): void
    {
        $moviesData = [
            'docs' => [
                [
                    'id' => 789,
                    'name' => 'Action Movie',
                    'genres' => [['name' => 'Action']]
                ]
            ],
            'total' => 1,
            'limit' => 10,
            'page' => 1,
            'pages' => 1
        ];
        
        $response = new Response(200, [], json_encode($moviesData));
        $this->mockHandler->append($response);
        
        $result = $this->movieRequests->getMoviesByGenre('Action');
        
        $this->assertInstanceOf(MovieDocsResponseDto::class, $result);
        $this->assertEquals(1, $result->total);
    }

    public function test_getMoviesByGenre_withMultipleGenres_returnsMovies(): void
    {
        $moviesData = [
            'docs' => [
                [
                    'id' => 101,
                    'name' => 'Action Drama',
                    'genres' => [['name' => 'Action'], ['name' => 'Drama']]
                ]
            ],
            'total' => 1,
            'limit' => 10,
            'page' => 1,
            'pages' => 1
        ];
        
        $response = new Response(200, [], json_encode($moviesData));
        $this->mockHandler->append($response);
        
        $result = $this->movieRequests->getMoviesByGenre(['Action', 'Drama']);
        
        $this->assertInstanceOf(MovieDocsResponseDto::class, $result);
        $this->assertEquals(1, $result->total);
    }

    public function test_searchMovies_withFilters_returnsMovies(): void
    {
        $moviesData = [
            'docs' => [
                [
                    'id' => 202,
                    'name' => 'Filtered Movie',
                    'year' => 2023,
                    'rating' => ['kp' => 8.5]
                ]
            ],
            'total' => 1,
            'limit' => 20,
            'page' => 1,
            'pages' => 1
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

    public function test_getMoviesByCountry_withSingleCountry_returnsMovies(): void
    {
        $moviesData = [
            'docs' => [
                [
                    'id' => 303,
                    'name' => 'Russian Movie',
                    'countries' => [['name' => 'Россия']]
                ]
            ],
            'total' => 1,
            'limit' => 10,
            'page' => 1,
            'pages' => 1
        ];
        
        $response = new Response(200, [], json_encode($moviesData));
        $this->mockHandler->append($response);
        
        $result = $this->movieRequests->getMoviesByCountry('Россия');
        
        $this->assertInstanceOf(MovieDocsResponseDto::class, $result);
        $this->assertEquals(1, $result->total);
    }

    public function test_getMoviesByYearRange_withValidRange_returnsMovies(): void
    {
        $moviesData = [
            'docs' => [
                [
                    'id' => 404,
                    'name' => 'Range Movie',
                    'year' => 2022
                ]
            ],
            'total' => 1,
            'limit' => 10,
            'page' => 1,
            'pages' => 1
        ];
        
        $response = new Response(200, [], json_encode($moviesData));
        $this->mockHandler->append($response);
        
        $result = $this->movieRequests->getMoviesByYearRange(2020, 2025);
        
        $this->assertInstanceOf(MovieDocsResponseDto::class, $result);
        $this->assertEquals(1, $result->total);
    }

    public function test_makeRequest_withNetworkError_throwsException(): void
    {
        $request = new Request('GET', 'http://example.com');
        $exception = new RequestException('Network error', $request);
        
        $this->mockHandler->append($exception);
        
        $this->expectException(KinopoiskDevException::class);
        $this->expectExceptionMessage('Ошибка HTTP запроса: Network error');
        
        $this->movieRequests->getMovieById(123);
    }

    public function test_makeRequest_withServerError_throwsException(): void
    {
        $errorResponse = new Response(500, [], json_encode(['error' => 'Internal server error']));
        $this->mockHandler->append($errorResponse);
        
        $this->expectException(KinopoiskResponseException::class);
        $this->expectExceptionMessage('Internal Server Error: Внутренняя ошибка сервера');
        
        $this->movieRequests->getMovieById(123);
    }

    public function test_makeRequest_withUnauthorized_throwsException(): void
    {
        $errorResponse = new Response(401, [], json_encode(['error' => 'Unauthorized']));
        $this->mockHandler->append($errorResponse);
        
        $this->expectException(KinopoiskResponseException::class);
        $this->expectExceptionMessage('Unauthorized: Неверный API токен или токен отсутствует');
        
        $this->movieRequests->getMovieById(123);
    }

    public function test_makeRequest_withForbidden_throwsException(): void
    {
        $errorResponse = new Response(403, [], json_encode(['error' => 'Forbidden']));
        $this->mockHandler->append($errorResponse);
        
        $this->expectException(KinopoiskResponseException::class);
        $this->expectExceptionMessage('Forbidden: Доступ запрещен');
        
        $this->movieRequests->getMovieById(123);
    }
} 