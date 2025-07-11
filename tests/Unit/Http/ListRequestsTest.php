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
use KinopoiskDev\Http\ListRequests;
use KinopoiskDev\Filter\MovieSearchFilter;
use KinopoiskDev\Models\Lists;
use KinopoiskDev\Responses\Api\ListDocsResponseDto;
use KinopoiskDev\Exceptions\KinopoiskDevException;

/**
 * @group unit
 * @group http
 * @group list-requests
 */
class ListRequestsTest extends TestCase
{
    private MockHandler $mockHandler;
    private HandlerStack $handlerStack;
    private Client $httpClient;
    private ListRequests $listRequests;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->mockHandler = new MockHandler();
        $this->handlerStack = HandlerStack::create($this->mockHandler);
        $this->httpClient = new Client(['handler' => $this->handlerStack]);
        
        $this->listRequests = new ListRequests(
            apiToken: 'MOCK123-TEST456-UNIT789-TOKEN01',
            httpClient: $this->httpClient
        );
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function test_getListBySlug_withValidSlug_returnsList(): void
    {
        $listData = [
            'category' => 'top',
            'slug' => 'top250',
            'moviesCount' => 250,
            'cover' => [
                'url' => 'https://image.openmoviedb.com/kinopoisk-images/cover.jpg',
                'previewUrl' => 'https://image.openmoviedb.com/kinopoisk-images/cover-preview.jpg'
            ],
            'name' => 'Топ-250 лучших фильмов',
            'updatedAt' => '2025-01-15T10:30:00.000Z',
            'createdAt' => '2023-01-01T00:00:00.000Z'
        ];
        
        $response = new Response(200, [], json_encode($listData));
        $this->mockHandler->append($response);
        
        $result = $this->listRequests->getListBySlug('top250');
        
        $this->assertInstanceOf(Lists::class, $result);
        $this->assertEquals('top250', $result->slug);
        $this->assertEquals('Топ-250 лучших фильмов', $result->name);
        $this->assertEquals(250, $result->moviesCount);
        $this->assertEquals('top', $result->category);
    }

    public function test_getAllLists_withoutFilters_returnsLists(): void
    {
        $listsData = [
            'docs' => [
                [
                    'category' => 'top',
                    'slug' => 'top250',
                    'moviesCount' => 250,
                    'cover' => [
                        'url' => 'https://image.openmoviedb.com/kinopoisk-images/cover.jpg',
                        'previewUrl' => 'https://image.openmoviedb.com/kinopoisk-images/cover-preview.jpg'
                    ],
                    'name' => 'Топ-250 лучших фильмов',
                    'updatedAt' => '2025-01-15T10:30:00.000Z',
                    'createdAt' => '2023-01-01T00:00:00.000Z'
                ],
                [
                    'category' => 'popular',
                    'slug' => 'popular-films',
                    'moviesCount' => 100,
                    'cover' => [
                        'url' => 'https://image.openmoviedb.com/kinopoisk-images/popular.jpg',
                        'previewUrl' => 'https://image.openmoviedb.com/kinopoisk-images/popular-preview.jpg'
                    ],
                    'name' => 'Популярные фильмы',
                    'updatedAt' => '2025-01-15T10:30:00.000Z',
                    'createdAt' => '2023-01-01T00:00:00.000Z'
                ]
            ],
            'total' => 2,
            'limit' => 10,
            'page' => 1,
            'pages' => 1
        ];
        
        $response = new Response(200, [], json_encode($listsData));
        $this->mockHandler->append($response);
        
        $result = $this->listRequests->getAllLists();
        
        $this->assertInstanceOf(ListDocsResponseDto::class, $result);
        $this->assertEquals(2, $result->total);
        $this->assertCount(2, $result->docs);
        $this->assertEquals('top250', $result->docs[0]->slug);
        $this->assertEquals('popular-films', $result->docs[1]->slug);
    }

    public function test_getAllLists_withFilters_returnsFilteredLists(): void
    {
        $listsData = [
            'docs' => [
                [
                    'category' => 'top',
                    'slug' => 'top250',
                    'moviesCount' => 250,
                    'cover' => [
                        'url' => 'https://image.openmoviedb.com/kinopoisk-images/cover.jpg',
                        'previewUrl' => 'https://image.openmoviedb.com/kinopoisk-images/cover-preview.jpg'
                    ],
                    'name' => 'Топ-250 лучших фильмов',
                    'updatedAt' => '2025-01-15T10:30:00.000Z',
                    'createdAt' => '2023-01-01T00:00:00.000Z'
                ]
            ],
            'total' => 1,
            'limit' => 10,
            'page' => 1,
            'pages' => 1
        ];
        
        $response = new Response(200, [], json_encode($listsData));
        $this->mockHandler->append($response);
        
        $filter = new MovieSearchFilter();
        $filter->addFilter('category', 'top');
        
        $result = $this->listRequests->getAllLists($filter);
        
        $this->assertInstanceOf(ListDocsResponseDto::class, $result);
        $this->assertEquals(1, $result->total);
        $this->assertCount(1, $result->docs);
        $this->assertEquals('top', $result->docs[0]->category);
    }

    public function test_getPopularLists_returnsLists(): void
    {
        $listsData = [
            'docs' => [
                [
                    'category' => 'popular',
                    'slug' => 'popular-films',
                    'moviesCount' => 100,
                    'cover' => [
                        'url' => 'https://image.openmoviedb.com/kinopoisk-images/popular.jpg',
                        'previewUrl' => 'https://image.openmoviedb.com/kinopoisk-images/popular-preview.jpg'
                    ],
                    'name' => 'Популярные фильмы',
                    'updatedAt' => '2025-01-15T10:30:00.000Z',
                    'createdAt' => '2023-01-01T00:00:00.000Z'
                ]
            ],
            'total' => 1,
            'limit' => 10,
            'page' => 1,
            'pages' => 1
        ];
        
        $response = new Response(200, [], json_encode($listsData));
        $this->mockHandler->append($response);
        
        $result = $this->listRequests->getPopularLists();
        
        $this->assertInstanceOf(ListDocsResponseDto::class, $result);
        $this->assertEquals(1, $result->total);
        $this->assertCount(1, $result->docs);
        $this->assertEquals('popular-films', $result->docs[0]->slug);
    }

    public function test_getListsByCategory_withValidCategory_returnsLists(): void
    {
        $listsData = [
            'docs' => [
                [
                    'category' => 'action',
                    'slug' => 'action-movies',
                    'moviesCount' => 50,
                    'cover' => [
                        'url' => 'https://image.openmoviedb.com/kinopoisk-images/action.jpg',
                        'previewUrl' => 'https://image.openmoviedb.com/kinopoisk-images/action-preview.jpg'
                    ],
                    'name' => 'Боевики',
                    'updatedAt' => '2025-01-15T10:30:00.000Z',
                    'createdAt' => '2023-01-01T00:00:00.000Z'
                ]
            ],
            'total' => 1,
            'limit' => 10,
            'page' => 1,
            'pages' => 1
        ];
        
        $response = new Response(200, [], json_encode($listsData));
        $this->mockHandler->append($response);
        
        $result = $this->listRequests->getListsByCategory('action');
        
        $this->assertInstanceOf(ListDocsResponseDto::class, $result);
        $this->assertEquals(1, $result->total);
        $this->assertCount(1, $result->docs);
        $this->assertEquals('action', $result->docs[0]->category);
        $this->assertEquals('action-movies', $result->docs[0]->slug);
    }

    public function test_getAllLists_withInvalidLimit_throwsException(): void
    {
        $this->expectException(KinopoiskDevException::class);
        $this->expectExceptionMessage('Лимит не должен превышать 250');
        
        $this->listRequests->getAllLists(null, 1, 251);
    }

    public function test_getAllLists_withInvalidPage_throwsException(): void
    {
        $this->expectException(KinopoiskDevException::class);
        $this->expectExceptionMessage('Номер страницы не должен быть меньше 1');
        
        $this->listRequests->getAllLists(null, 0, 10);
    }

    public function test_getListBySlug_withServerError_throwsException(): void
    {
        $errorResponse = new Response(500, [], json_encode(['error' => 'Internal server error']));
        $this->mockHandler->append($errorResponse);

        $this->expectException(KinopoiskDevException::class);
        $this->expectExceptionMessage('Ошибка HTTP запроса: Server error: `GET /v1.4//list/top250` resulted in a `500 Internal Server Error` response:');

        $this->listRequests->getListBySlug('top250');
    }

    public function test_getAllLists_withUnauthorized_throwsException(): void
    {
        $errorResponse = new Response(401, [], json_encode(['error' => 'Unauthorized']));
        $this->mockHandler->append($errorResponse);

        $this->expectException(KinopoiskDevException::class);
        $this->expectExceptionMessage('Ошибка HTTP запроса: Client error: `GET /v1.4/list?page.eq=1&limit.eq=10` resulted in a `401 Unauthorized` response:');

        $this->listRequests->getAllLists();
    }

    public function test_getAllLists_withForbidden_throwsException(): void
    {
        $errorResponse = new Response(403, [], json_encode(['error' => 'Forbidden']));
        $this->mockHandler->append($errorResponse);

        $this->expectException(KinopoiskDevException::class);
        $this->expectExceptionMessage('Ошибка HTTP запроса: Client error: `GET /v1.4/list?page.eq=1&limit.eq=10` resulted in a `403 Forbidden` response:');

        $this->listRequests->getAllLists();
    }

    public function test_getListBySlug_withNotFound_throwsException(): void
    {
        $errorResponse = new Response(404, [], json_encode(['error' => 'Not found']));
        $this->mockHandler->append($errorResponse);

        $this->expectException(KinopoiskDevException::class);
        $this->expectExceptionMessage('Ошибка HTTP запроса: Client error: `GET /v1.4//list/invalid-slug` resulted in a `404 Not Found` response:');

        $this->listRequests->getListBySlug('invalid-slug');
    }
} 