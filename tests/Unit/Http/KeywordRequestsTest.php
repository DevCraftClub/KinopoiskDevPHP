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
use KinopoiskDev\Http\KeywordRequests;
use KinopoiskDev\Filter\KeywordSearchFilter;
use KinopoiskDev\Models\Keyword;
use KinopoiskDev\Responses\Api\KeywordDocsResponseDto;
use KinopoiskDev\Exceptions\KinopoiskDevException;

/**
 * @group unit
 * @group http
 * @group keyword-requests
 */
class KeywordRequestsTest extends TestCase
{
    private MockHandler $mockHandler;
    private HandlerStack $handlerStack;
    private Client $httpClient;
    private KeywordRequests $keywordRequests;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->mockHandler = new MockHandler();
        $this->handlerStack = HandlerStack::create($this->mockHandler);
        $this->httpClient = new Client(['handler' => $this->handlerStack]);
        
        $this->keywordRequests = new KeywordRequests(
            apiToken: 'MOCK123-TEST456-UNIT789-TOKEN01',
            httpClient: $this->httpClient
        );
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function test_searchKeywords_withoutFilters_returnsKeywords(): void
    {
        $keywordsData = [
            'docs' => [
                [
                    'id' => 166128951,
                    'title' => 'Эни (корейская анимация)',
                    'movies' => [
                        [
                            'id' => 7782314
                        ]
                    ],
                    'needsValidation' => false,
                    'isValid' => null,
                    'moviesValidated' => false,
                    'createdAt' => '2025-06-04T22:19:41.962Z',
                    'updatedAt' => '2025-06-04T22:19:41.962Z'
                ]
            ],
            'total' => 34373,
            'limit' => 10,
            'page' => 1,
            'pages' => 3438
        ];
        
        $response = new Response(200, [], json_encode($keywordsData));
        $this->mockHandler->append($response);
        
        $result = $this->keywordRequests->searchKeywords();
        
        $this->assertInstanceOf(KeywordDocsResponseDto::class, $result);
        $this->assertEquals(34373, $result->total);
        $this->assertCount(1, $result->docs);
        $this->assertEquals(166128951, $result->docs[0]->id);
        $this->assertEquals('Эни (корейская анимация)', $result->docs[0]->title);
    }

    public function test_searchKeywords_withFilters_returnsFilteredKeywords(): void
    {
        $keywordsData = [
            'docs' => [
                [
                    'id' => 300014755,
                    'title' => 'Чеболь',
                    'movies' => [
                        [
                            'id' => 6412404
                        ],
                        [
                            'id' => 5948425
                        ]
                    ],
                    'needsValidation' => false,
                    'isValid' => null,
                    'moviesValidated' => false,
                    'createdAt' => '2025-06-04T22:18:26.061Z',
                    'updatedAt' => '2025-06-04T22:18:34.232Z'
                ]
            ],
            'total' => 1,
            'limit' => 10,
            'page' => 1,
            'pages' => 1
        ];
        
        $response = new Response(200, [], json_encode($keywordsData));
        $this->mockHandler->append($response);
        
        $filter = new KeywordSearchFilter();
        $filter->title('Чеболь');
        
        $result = $this->keywordRequests->searchKeywords($filter);
        
        $this->assertInstanceOf(KeywordDocsResponseDto::class, $result);
        $this->assertEquals(1, $result->total);
        $this->assertCount(1, $result->docs);
        $this->assertEquals(300014755, $result->docs[0]->id);
        $this->assertEquals('Чеболь', $result->docs[0]->title);
    }

    public function test_getKeywordsByTitle_withValidTitle_returnsKeywords(): void
    {
        $keywordsData = [
            'docs' => [
                [
                    'id' => 9819,
                    'title' => 'Много шума из ничего Шекспира',
                    'movies' => [
                        [
                            'id' => 4308301
                        ]
                    ],
                    'needsValidation' => false,
                    'isValid' => null,
                    'moviesValidated' => false,
                    'createdAt' => '2025-05-07T01:00:49.366Z',
                    'updatedAt' => '2025-05-07T01:00:49.366Z'
                ]
            ],
            'total' => 1,
            'limit' => 10,
            'page' => 1,
            'pages' => 1
        ];
        
        $response = new Response(200, [], json_encode($keywordsData));
        $this->mockHandler->append($response);
        
        $result = $this->keywordRequests->getKeywordsByTitle('Шекспир');
        
        $this->assertInstanceOf(KeywordDocsResponseDto::class, $result);
        $this->assertEquals(1, $result->total);
        $this->assertCount(1, $result->docs);
        $this->assertEquals(9819, $result->docs[0]->id);
        $this->assertEquals('Много шума из ничего Шекспира', $result->docs[0]->title);
    }

    public function test_getKeywordsForMovie_withValidMovieId_returnsKeywords(): void
    {
        $keywordsData = [
            'docs' => [
                [
                    'id' => 5558,
                    'title' => 'Сцена в баре',
                    'movies' => [
                        [
                            'id' => 4645243
                        ]
                    ],
                    'needsValidation' => false,
                    'isValid' => null,
                    'moviesValidated' => false,
                    'createdAt' => '2025-05-07T00:45:58.613Z',
                    'updatedAt' => '2025-05-07T00:45:58.613Z'
                ]
            ],
            'total' => 1,
            'limit' => 10,
            'page' => 1,
            'pages' => 1
        ];
        
        $response = new Response(200, [], json_encode($keywordsData));
        $this->mockHandler->append($response);
        
        $result = $this->keywordRequests->getKeywordsForMovie(4645243);
        
        $this->assertInstanceOf(KeywordDocsResponseDto::class, $result);
        $this->assertEquals(1, $result->total);
        $this->assertCount(1, $result->docs);
        $this->assertEquals(5558, $result->docs[0]->id);
        $this->assertEquals('Сцена в баре', $result->docs[0]->title);
    }

    public function test_getKeywordById_withValidId_returnsKeyword(): void
    {
        $keywordsData = [
            'docs' => [
                [
                    'id' => 12087,
                    'title' => 'Ящик Пандоры',
                    'movies' => [
                        [
                            'id' => 4437843
                        ]
                    ],
                    'needsValidation' => false,
                    'isValid' => null,
                    'moviesValidated' => false,
                    'createdAt' => '2025-05-07T00:45:49.976Z',
                    'updatedAt' => '2025-05-07T00:45:49.976Z'
                ]
            ],
            'total' => 1,
            'limit' => 1,
            'page' => 1,
            'pages' => 1
        ];
        
        $response = new Response(200, [], json_encode($keywordsData));
        $this->mockHandler->append($response);
        
        $result = $this->keywordRequests->getKeywordById(12087);
        
        $this->assertInstanceOf(Keyword::class, $result);
        $this->assertEquals(12087, $result->id);
        $this->assertEquals('Ящик Пандоры', $result->title);
    }

    public function test_getKeywordById_withInvalidId_returnsNull(): void
    {
        $keywordsData = [
            'docs' => [],
            'total' => 0,
            'limit' => 1,
            'page' => 1,
            'pages' => 0
        ];
        
        $response = new Response(200, [], json_encode($keywordsData));
        $this->mockHandler->append($response);
        
        $result = $this->keywordRequests->getKeywordById(999999);
        
        $this->assertNull($result);
    }

    public function test_getPopularKeywords_returnsKeywords(): void
    {
        $keywordsData = [
            'docs' => [
                [
                    'id' => 29489,
                    'title' => 'Датчики',
                    'movies' => [
                        [
                            'id' => 4551715
                        ]
                    ],
                    'needsValidation' => false,
                    'isValid' => null,
                    'moviesValidated' => false,
                    'createdAt' => '2025-05-07T00:55:03.427Z',
                    'updatedAt' => '2025-05-07T00:55:03.427Z'
                ]
            ],
            'total' => 1,
            'limit' => 10,
            'page' => 1,
            'pages' => 1
        ];
        
        $response = new Response(200, [], json_encode($keywordsData));
        $this->mockHandler->append($response);
        
        $result = $this->keywordRequests->getPopularKeywords();
        
        $this->assertInstanceOf(KeywordDocsResponseDto::class, $result);
        $this->assertEquals(1, $result->total);
        $this->assertCount(1, $result->docs);
        $this->assertEquals(29489, $result->docs[0]->id);
        $this->assertEquals('Датчики', $result->docs[0]->title);
    }

    public function test_searchKeywords_withInvalidLimit_throwsException(): void
    {
        $this->expectException(KinopoiskDevException::class);
        $this->expectExceptionMessage('Лимит не должен превышать 250');
        
        $this->keywordRequests->searchKeywords(null, 1, 251);
    }

    public function test_searchKeywords_withInvalidPage_throwsException(): void
    {
        $this->expectException(KinopoiskDevException::class);
        $this->expectExceptionMessage('Номер страницы не должен быть меньше 1');
        
        $this->keywordRequests->searchKeywords(null, 0, 10);
    }

    public function test_searchKeywords_withServerError_throwsException(): void
    {
        $errorResponse = new Response(500, [], json_encode(['error' => 'Internal server error']));
        $this->mockHandler->append($errorResponse);

        $this->expectException(KinopoiskDevException::class);
        $this->expectExceptionMessage('Ошибка HTTP запроса: Server error: `GET /v1.4/keyword?page.eq=1&limit.eq=10` resulted in a `500 Internal Server Error` response:');

        $this->keywordRequests->searchKeywords();
    }

    public function test_searchKeywords_withUnauthorized_throwsException(): void
    {
        $errorResponse = new Response(401, [], json_encode(['error' => 'Unauthorized']));
        $this->mockHandler->append($errorResponse);

        $this->expectException(KinopoiskDevException::class);
        $this->expectExceptionMessage('Ошибка HTTP запроса: Client error: `GET /v1.4/keyword?page.eq=1&limit.eq=10` resulted in a `401 Unauthorized` response:');

        $this->keywordRequests->searchKeywords();
    }

    public function test_searchKeywords_withForbidden_throwsException(): void
    {
        $errorResponse = new Response(403, [], json_encode(['error' => 'Forbidden']));
        $this->mockHandler->append($errorResponse);

        $this->expectException(KinopoiskDevException::class);
        $this->expectExceptionMessage('Ошибка HTTP запроса: Client error: `GET /v1.4/keyword?page.eq=1&limit.eq=10` resulted in a `403 Forbidden` response:');

        $this->keywordRequests->searchKeywords();
    }
} 