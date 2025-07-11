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
use KinopoiskDev\Http\StudioRequests;
use KinopoiskDev\Filter\StudioSearchFilter;
use KinopoiskDev\Models\Studio;
use KinopoiskDev\Responses\Api\StudioDocsResponseDto;
use KinopoiskDev\Responses\Api\SearchStudioResponseDto;
use KinopoiskDev\Exceptions\KinopoiskDevException;
use KinopoiskDev\Exceptions\KinopoiskResponseException;

/**
 * @group unit
 * @group http
 * @group studio-requests
 */
class StudioRequestsTest extends TestCase
{
    private MockHandler $mockHandler;
    private HandlerStack $handlerStack;
    private Client $httpClient;
    private StudioRequests $studioRequests;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->mockHandler = new MockHandler();
        $this->handlerStack = HandlerStack::create($this->mockHandler);
        $this->httpClient = new Client(['handler' => $this->handlerStack]);
        
        $this->studioRequests = new StudioRequests(
            apiToken: $_ENV['KINOPOISK_API_TOKEN'],
            httpClient: $this->httpClient
        );
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function test_getStudioById_withValidId_returnsStudio(): void
    {
        $studioData = [
            'id' => 123,
            'name' => 'Test Studio',
            'type' => 'production',
            'country' => 'USA'
        ];
        
        $response = new Response(200, [], json_encode($studioData));
        $this->mockHandler->append($response);
        
        $studio = $this->studioRequests->getStudioById(123);
        
        $this->assertInstanceOf(Studio::class, $studio);
        $this->assertEquals(123, $studio->getId());
        $this->assertEquals('Test Studio', $studio->getName());
        $this->assertEquals('production', $studio->getType());
    }

    public function test_getStudioById_withInvalidId_throwsException(): void
    {
        $errorResponse = new Response(404, [], json_encode(['error' => 'Studio not found']));
        $this->mockHandler->append($errorResponse);
        
        $this->expectException(KinopoiskResponseException::class);
        $this->expectExceptionMessage('Not Found: Запрашиваемый ресурс не найден');
        
        $this->studioRequests->getStudioById(999999);
    }

    public function test_getRandomStudio_withoutFilters_returnsStudio(): void
    {
        $studioData = [
            'id' => 456,
            'name' => 'Random Studio',
            'type' => 'distribution',
            'country' => 'UK'
        ];
        
        $response = new Response(200, [], json_encode($studioData));
        $this->mockHandler->append($response);
        
        $studio = $this->studioRequests->getRandomStudio();
        
        $this->assertInstanceOf(Studio::class, $studio);
        $this->assertEquals(456, $studio->getId());
        $this->assertEquals('Random Studio', $studio->getName());
    }

    public function test_getRandomStudio_withFilters_returnsFilteredStudio(): void
    {
        $studioData = [
            'id' => 789,
            'name' => 'Filtered Studio',
            'type' => 'production',
            'country' => 'USA'
        ];
        
        $response = new Response(200, [], json_encode($studioData));
        $this->mockHandler->append($response);
        
        $filter = new StudioSearchFilter();
        $filter->type('production');
        
        $studio = $this->studioRequests->getRandomStudio($filter);
        
        $this->assertInstanceOf(Studio::class, $studio);
        $this->assertEquals(789, $studio->getId());
    }

    public function test_searchStudios_withFilters_returnsStudios(): void
    {
        $studiosData = [
            'docs' => [
                [
                    'id' => 101,
                    'name' => 'Search Result Studio',
                    'type' => 'production',
                    'country' => 'USA'
                ]
            ],
            'total' => 1,
            'limit' => 10,
            'page' => 1,
            'pages' => 1
        ];
        
        $response = new Response(200, [], json_encode($studiosData));
        $this->mockHandler->append($response);
        
        $filter = new StudioSearchFilter();
        $filter->name('Search');
        
        $result = $this->studioRequests->searchStudios($filter, 1, 10);
        
        $this->assertInstanceOf(StudioDocsResponseDto::class, $result);
        $this->assertEquals(1, $result->total);
        $this->assertCount(1, $result->docs);
        $this->assertEquals('Search Result Studio', $result->docs[0]->name);
    }

    public function test_searchStudiosByName_withValidQuery_returnsStudios(): void
    {
        $studiosData = [
            'docs' => [
                [
                    'id' => 202,
                    'name' => 'Name Search Studio',
                    'type' => 'distribution',
                    'country' => 'UK'
                ]
            ],
            'total' => 1,
            'limit' => 10,
            'page' => 1,
            'pages' => 1
        ];
        
        $response = new Response(200, [], json_encode($studiosData));
        $this->mockHandler->append($response);
        
        $result = $this->studioRequests->searchStudiosByName('Name Search');
        
        $this->assertInstanceOf(SearchStudioResponseDto::class, $result);
        $this->assertEquals(1, $result->total);
        $this->assertCount(1, $result->docs);
        $this->assertEquals('Name Search Studio', $result->docs[0]->name);
    }

    public function test_getStudiosByType_withValidType_returnsStudios(): void
    {
        $studiosData = [
            'docs' => [
                [
                    'id' => 303,
                    'name' => 'Production Studio One',
                    'type' => 'production',
                    'country' => 'USA'
                ],
                [
                    'id' => 304,
                    'name' => 'Production Studio Two',
                    'type' => 'production',
                    'country' => 'Canada'
                ]
            ],
            'total' => 2,
            'limit' => 10,
            'page' => 1,
            'pages' => 1
        ];
        
        $response = new Response(200, [], json_encode($studiosData));
        $this->mockHandler->append($response);
        
        $result = $this->studioRequests->getStudiosByType('production');
        
        $this->assertInstanceOf(StudioDocsResponseDto::class, $result);
        $this->assertEquals(2, $result->total);
        $this->assertCount(2, $result->docs);
    }

    public function test_getStudiosByType_withMultipleTypes_returnsStudios(): void
    {
        $studiosData = [
            'docs' => [
                [
                    'id' => 405,
                    'name' => 'Multi Type Studio',
                    'type' => 'production',
                    'country' => 'USA'
                ]
            ],
            'total' => 1,
            'limit' => 10,
            'page' => 1,
            'pages' => 1
        ];
        
        $response = new Response(200, [], json_encode($studiosData));
        $this->mockHandler->append($response);
        
        $result = $this->studioRequests->getStudiosByType(['production', 'distribution']);
        
        $this->assertInstanceOf(StudioDocsResponseDto::class, $result);
        $this->assertEquals(1, $result->total);
    }

    public function test_getStudiosByCountry_withValidCountry_returnsStudios(): void
    {
        $studiosData = [
            'docs' => [
                [
                    'id' => 506,
                    'name' => 'USA Studio',
                    'type' => 'production',
                    'country' => 'USA'
                ]
            ],
            'total' => 1,
            'limit' => 10,
            'page' => 1,
            'pages' => 1
        ];
        
        $response = new Response(200, [], json_encode($studiosData));
        $this->mockHandler->append($response);
        
        $result = $this->studioRequests->getStudiosByCountry('USA');
        
        $this->assertInstanceOf(StudioDocsResponseDto::class, $result);
        $this->assertEquals(1, $result->total);
        $this->assertCount(1, $result->docs);
    }

    public function test_getStudiosByCountry_withMultipleCountries_returnsStudios(): void
    {
        $studiosData = [
            'docs' => [
                [
                    'id' => 607,
                    'name' => 'Multi Country Studio',
                    'type' => 'distribution',
                    'country' => 'UK'
                ]
            ],
            'total' => 1,
            'limit' => 10,
            'page' => 1,
            'pages' => 1
        ];
        
        $response = new Response(200, [], json_encode($studiosData));
        $this->mockHandler->append($response);
        
        $result = $this->studioRequests->getStudiosByCountry(['USA', 'UK']);
        
        $this->assertInstanceOf(StudioDocsResponseDto::class, $result);
        $this->assertEquals(1, $result->total);
    }

    public function test_getStudiosByYear_withValidYear_returnsStudios(): void
    {
        $studiosData = [
            'docs' => [
                [
                    'id' => 708,
                    'name' => '2023 Studio',
                    'type' => 'production',
                    'country' => 'USA',
                    'year' => 2023
                ]
            ],
            'total' => 1,
            'limit' => 10,
            'page' => 1,
            'pages' => 1
        ];
        
        $response = new Response(200, [], json_encode($studiosData));
        $this->mockHandler->append($response);
        
        $result = $this->studioRequests->getStudiosByYear(2023);
        
        $this->assertInstanceOf(StudioDocsResponseDto::class, $result);
        $this->assertEquals(1, $result->total);
    }

    public function test_getStudiosByYearRange_withValidRange_returnsStudios(): void
    {
        $studiosData = [
            'docs' => [
                [
                    'id' => 809,
                    'name' => 'Range Studio',
                    'type' => 'distribution',
                    'country' => 'UK',
                    'year' => 2022
                ]
            ],
            'total' => 1,
            'limit' => 10,
            'page' => 1,
            'pages' => 1
        ];
        
        $response = new Response(200, [], json_encode($studiosData));
        $this->mockHandler->append($response);
        
        $result = $this->studioRequests->getStudiosByYearRange(2020, 2025);
        
        $this->assertInstanceOf(StudioDocsResponseDto::class, $result);
        $this->assertEquals(1, $result->total);
    }

    public function test_makeRequest_withNetworkError_throwsException(): void
    {
        $request = new Request('GET', 'http://example.com');
        $exception = new RequestException('Network error', $request);
        
        $this->mockHandler->append($exception);
        
        $this->expectException(KinopoiskDevException::class);
        $this->expectExceptionMessage('Ошибка HTTP запроса: Network error');
        
        $this->studioRequests->getStudioById(123);
    }

    public function test_makeRequest_withServerError_throwsException(): void
    {
        $errorResponse = new Response(500, [], json_encode(['error' => 'Internal server error']));
        $this->mockHandler->append($errorResponse);
        
        $this->expectException(KinopoiskResponseException::class);
        $this->expectExceptionMessage('Internal Server Error: Внутренняя ошибка сервера');
        
        $this->studioRequests->getStudioById(123);
    }

    public function test_makeRequest_withUnauthorized_throwsException(): void
    {
        $errorResponse = new Response(401, [], json_encode(['error' => 'Unauthorized']));
        $this->mockHandler->append($errorResponse);
        
        $this->expectException(KinopoiskResponseException::class);
        $this->expectExceptionMessage('Unauthorized: Неверный API токен или токен отсутствует');
        
        $this->studioRequests->getStudioById(123);
    }

    public function test_makeRequest_withForbidden_throwsException(): void
    {
        $errorResponse = new Response(403, [], json_encode(['error' => 'Forbidden']));
        $this->mockHandler->append($errorResponse);
        
        $this->expectException(KinopoiskResponseException::class);
        $this->expectExceptionMessage('Forbidden: Доступ запрещен');
        
        $this->studioRequests->getStudioById(123);
    }
} 