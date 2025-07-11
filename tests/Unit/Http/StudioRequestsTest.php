<?php

declare(strict_types=1);

namespace KinopoiskDev\Tests\Unit\Http;

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
            apiToken: 'MOCK123-TEST456-UNIT789-TOKEN01',
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
            'id' => '694777',
            'subType' => 'studio',
            'title' => 'Sunny Smiles Film',
            'type' => 'Производство',
            'movies' => [
                [
                    'id' => 5121842
                ]
            ],
            'createdAt' => '2025-07-05T13:11:36.726Z',
            'updatedAt' => '2025-07-05T13:11:36.726Z'
        ];
        
        $response = new Response(200, [], json_encode($studioData));
        $this->mockHandler->append($response);
        
        $studio = $this->studioRequests->getStudioById(123);
        
        $this->assertInstanceOf(Studio::class, $studio);
        $this->assertEquals('694777', $studio->id);
        $this->assertEquals('Sunny Smiles Film', $studio->title);
        $this->assertInstanceOf(\KinopoiskDev\Enums\StudioType::class, $studio->type);
        $this->assertEquals('Производство', $studio->type->value);
    }

    public function test_getStudioById_withInvalidId_throwsException(): void
    {
        $errorResponse = new Response(404, [], json_encode(['error' => 'Studio not found']));
        $this->mockHandler->append($errorResponse);
        
        $this->expectException(KinopoiskDevException::class);
        $this->expectExceptionMessage('Ошибка HTTP запроса: Client error: `GET /v1.4/studio/999999` resulted in a `404 Not Found` response:');
        
        $this->studioRequests->getStudioById(999999);
    }

    public function test_getRandomStudio_withoutFilters_returnsStudio(): void
    {
        $studiosData = [
            'docs' => [
                [
                    'id' => '695111',
                    'subType' => 'studio',
                    'title' => 'BlockFilm',
                    'type' => 'Производство',
                    'movies' => [
                        [
                            'id' => 5024373
                        ]
                    ],
                    'createdAt' => '2025-07-05T13:11:36.475Z',
                    'updatedAt' => '2025-07-05T13:11:36.475Z'
                ]
            ],
            'total' => 1,
            'limit' => 1,
            'page' => 1,
            'pages' => 1
        ];
        
        $response = new Response(200, [], json_encode($studiosData));
        $this->mockHandler->append($response);
        
        $studio = $this->studioRequests->getRandomStudio();
        
        $this->assertInstanceOf(Studio::class, $studio);
        $this->assertEquals('695111', $studio->id);
        $this->assertEquals('BlockFilm', $studio->title);
    }

    public function test_getRandomStudio_withFilters_returnsFilteredStudio(): void
    {
        $studiosData = [
            'docs' => [
                [
                    'id' => '789',
                    'subType' => 'studio',
                    'title' => 'Filtered Studio',
                    'type' => 'Производство',
                    'movies' => [
                        [
                            'id' => 123456
                        ]
                    ],
                    'createdAt' => '2025-07-05T13:11:36.475Z',
                    'updatedAt' => '2025-07-05T13:11:36.475Z'
                ]
            ],
            'total' => 1,
            'limit' => 1,
            'page' => 1,
            'pages' => 1
        ];
        
        $response = new Response(200, [], json_encode($studiosData));
        $this->mockHandler->append($response);
        
        $filter = new StudioSearchFilter();
        $filter->type('Производство');
        
        $studio = $this->studioRequests->getRandomStudio($filter);
        
        $this->assertInstanceOf(Studio::class, $studio);
        $this->assertEquals('789', $studio->id);
    }

    public function test_searchStudios_withFilters_returnsStudios(): void
    {
        $studiosData = [
            'docs' => [
                [
                    'id' => '597748',
                    'subType' => 'studio',
                    'title' => 'Pith Quest Films',
                    'type' => 'Производство',
                    'movies' => [
                        [
                            'id' => 1378893
                        ]
                    ],
                    'createdAt' => '2025-07-05T13:11:20.025Z',
                    'updatedAt' => '2025-07-05T13:11:20.025Z'
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
        $this->assertEquals('Pith Quest Films', $result->docs[0]->title);
    }

    public function test_searchStudiosByName_withValidQuery_returnsStudios(): void
    {
        $studiosData = [
            'docs' => [
                [
                    'id' => '202',
                    'subType' => 'studio',
                    'title' => 'Name Search Studio',
                    'type' => 'Дистрибуция',
                    'movies' => [
                        [
                            'id' => 123456
                        ]
                    ],
                    'createdAt' => '2025-07-05T13:11:36.475Z',
                    'updatedAt' => '2025-07-05T13:11:36.475Z'
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
        
        $this->assertInstanceOf(StudioDocsResponseDto::class, $result);
        $this->assertEquals(1, $result->total);
        $this->assertCount(1, $result->docs);
        $this->assertEquals('Name Search Studio', $result->docs[0]->title);
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
        
        $result = $this->studioRequests->getStudiosByType('production');
        
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
        
        $result = $this->studioRequests->getStudiosByCountry('USA');
        
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
        
        $this->expectException(KinopoiskDevException::class);
        $this->expectExceptionMessage('Ошибка HTTP запроса: Server error: `GET /v1.4/studio/1` resulted in a `500 Internal Server Error` response:');
        
        $this->studioRequests->getStudioById(1);
    }

    public function test_makeRequest_withUnauthorized_throwsException(): void
    {
        $errorResponse = new Response(401, [], json_encode(['error' => 'Unauthorized']));
        $this->mockHandler->append($errorResponse);
        
        $this->expectException(KinopoiskDevException::class);
        $this->expectExceptionMessage('Ошибка HTTP запроса: Client error: `GET /v1.4/studio/1` resulted in a `401 Unauthorized` response:');
        
        $this->studioRequests->getStudioById(1);
    }

    public function test_makeRequest_withForbidden_throwsException(): void
    {
        $errorResponse = new Response(403, [], json_encode(['error' => 'Forbidden']));
        $this->mockHandler->append($errorResponse);
        
        $this->expectException(KinopoiskDevException::class);
        $this->expectExceptionMessage('Ошибка HTTP запроса: Client error: `GET /v1.4/studio/1` resulted in a `403 Forbidden` response:');
        
        $this->studioRequests->getStudioById(1);
    }
} 