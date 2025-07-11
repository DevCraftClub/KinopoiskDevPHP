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
use KinopoiskDev\Http\ReviewRequests;
use KinopoiskDev\Filter\ReviewSearchFilter;
use KinopoiskDev\Models\Review;
use KinopoiskDev\Responses\Api\ReviewDocsResponseDto;
use KinopoiskDev\Exceptions\KinopoiskDevException;

/**
 * @group unit
 * @group http
 * @group review-requests
 */
class ReviewRequestsTest extends TestCase
{
    private MockHandler $mockHandler;
    private HandlerStack $handlerStack;
    private Client $httpClient;
    private ReviewRequests $reviewRequests;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->mockHandler = new MockHandler();
        $this->handlerStack = HandlerStack::create($this->mockHandler);
        $this->httpClient = new Client(['handler' => $this->handlerStack]);
        
        $this->reviewRequests = new ReviewRequests(
            apiToken: 'MOCK123-TEST456-UNIT789-TOKEN01',
            httpClient: $this->httpClient
        );
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function test_searchReviews_withoutFilters_returnsReviews(): void
    {
        $reviewsData = [
            'docs' => [
                [
                    'id' => 3532908,
                    'movieId' => 1047492,
                    'title' => 'Дочерняя компания франшизы',
                    'type' => 'Нейтральный',
                    'review' => 'Спин-офф франшизы «Джона Уика» готовился к выходу ещё в прошлом году...',
                    'date' => '2025-07-06T09:14:27Z',
                    'author' => 'LonelyThrowBack',
                    'userRating' => 0,
                    'authorId' => 14240401,
                    'reviewLikes' => 0,
                    'reviewDislikes' => 0,
                    'createdAt' => '2025-07-06T13:00:09.550Z',
                    'updatedAt' => '2025-07-06T13:00:09.550Z'
                ]
            ],
            'total' => 837966,
            'limit' => 10,
            'page' => 1,
            'pages' => 83797
        ];
        
        $response = new Response(200, [], json_encode($reviewsData));
        $this->mockHandler->append($response);
        
        $result = $this->reviewRequests->searchReviews();
        
        $this->assertInstanceOf(ReviewDocsResponseDto::class, $result);
        $this->assertEquals(837966, $result->total);
        $this->assertCount(1, $result->docs);
        $this->assertEquals(3532908, $result->docs[0]->id);
        $this->assertEquals('Дочерняя компания франшизы', $result->docs[0]->title);
        $this->assertEquals('Нейтральный', $result->docs[0]->type);
    }

    public function test_searchReviews_withFilters_returnsFilteredReviews(): void
    {
        $reviewsData = [
            'docs' => [
                [
                    'id' => 3532811,
                    'movieId' => 5512084,
                    'title' => '',
                    'type' => 'Позитивный',
                    'review' => 'Досмотрел \'Гангстерленд\' (1 сезон). Конечно, не зря потратил свое время...',
                    'date' => '2025-07-05T19:11:17Z',
                    'author' => 'vaganov.max2015 - 8374',
                    'userRating' => 0,
                    'authorId' => 18088073,
                    'reviewLikes' => 2,
                    'reviewDislikes' => 0,
                    'createdAt' => '2025-07-06T19:00:05.777Z',
                    'updatedAt' => '2025-07-06T19:00:05.777Z'
                ]
            ],
            'total' => 1,
            'limit' => 10,
            'page' => 1,
            'pages' => 1
        ];
        
        $response = new Response(200, [], json_encode($reviewsData));
        $this->mockHandler->append($response);
        
        $filter = new ReviewSearchFilter();
        $filter->type('Позитивный');
        
        $result = $this->reviewRequests->searchReviews($filter);
        
        $this->assertInstanceOf(ReviewDocsResponseDto::class, $result);
        $this->assertEquals(1, $result->total);
        $this->assertCount(1, $result->docs);
        $this->assertEquals('Позитивный', $result->docs[0]->type);
    }

    public function test_getPositiveReviews_returnsPositiveReviews(): void
    {
        $reviewsData = [
            'docs' => [
                [
                    'id' => 3532638,
                    'movieId' => 412344,
                    'title' => '\'Менталист (им. сущ) - человек, использующий остроту ума, гипноз и силу внушения. Мастер управлять мыслями и поведением\'.',
                    'type' => 'Позитивный',
                    'review' => 'Сериал Менталист - детектив \'с изюминкой\'...',
                    'date' => '2025-07-05T09:37:37Z',
                    'author' => 'Kat.ri.na2023',
                    'userRating' => 0,
                    'authorId' => 144989501,
                    'reviewLikes' => 0,
                    'reviewDislikes' => 0,
                    'createdAt' => '2025-07-05T17:54:26.819Z',
                    'updatedAt' => '2025-07-05T17:54:26.819Z'
                ]
            ],
            'total' => 1,
            'limit' => 10,
            'page' => 1,
            'pages' => 1
        ];
        
        $response = new Response(200, [], json_encode($reviewsData));
        $this->mockHandler->append($response);
        
        $result = $this->reviewRequests->getPositiveReviews();
        
        $this->assertInstanceOf(ReviewDocsResponseDto::class, $result);
        $this->assertEquals(1, $result->total);
        $this->assertCount(1, $result->docs);
        $this->assertEquals('Позитивный', $result->docs[0]->type);
    }

    public function test_getNegativeReviews_returnsNegativeReviews(): void
    {
        $reviewsData = [
            'docs' => [
                [
                    'id' => 3532849,
                    'movieId' => 1047492,
                    'title' => 'Балерина',
                    'type' => 'Негативный',
                    'review' => 'А собственно, в чём заключается наименование фильма \'Балерина\'?...',
                    'date' => '2025-07-05T21:36:57Z',
                    'author' => 'Александр К - 3007',
                    'userRating' => 0,
                    'authorId' => 198046516,
                    'reviewLikes' => 1,
                    'reviewDislikes' => 1,
                    'createdAt' => '2025-07-06T13:00:09.551Z',
                    'updatedAt' => '2025-07-06T13:00:09.551Z'
                ]
            ],
            'total' => 1,
            'limit' => 10,
            'page' => 1,
            'pages' => 1
        ];
        
        $response = new Response(200, [], json_encode($reviewsData));
        $this->mockHandler->append($response);
        
        $result = $this->reviewRequests->getNegativeReviews();
        
        $this->assertInstanceOf(ReviewDocsResponseDto::class, $result);
        $this->assertEquals(1, $result->total);
        $this->assertCount(1, $result->docs);
        $this->assertEquals('Негативный', $result->docs[0]->type);
    }

    public function test_searchReviews_withMovieIdFilter_returnsMovieReviews(): void
    {
        $reviewsData = [
            'docs' => [
                [
                    'id' => 3532879,
                    'movieId' => 4295935,
                    'title' => 'Девчонка в железном гробу',
                    'type' => 'Нейтральный',
                    'review' => '«Наследницу» Железного человека Рири Уильямс исключают из МТИ...',
                    'date' => '2025-07-06T05:34:21Z',
                    'author' => '89082675799',
                    'userRating' => 0,
                    'authorId' => 7054925,
                    'reviewLikes' => 1,
                    'reviewDislikes' => 0,
                    'createdAt' => '2025-07-06T19:00:12.751Z',
                    'updatedAt' => '2025-07-06T19:00:12.751Z'
                ]
            ],
            'total' => 1,
            'limit' => 10,
            'page' => 1,
            'pages' => 1
        ];
        
        $response = new Response(200, [], json_encode($reviewsData));
        $this->mockHandler->append($response);
        
        $filter = new ReviewSearchFilter();
        $filter->movieId(4295935);
        
        $result = $this->reviewRequests->searchReviews($filter);
        
        $this->assertInstanceOf(ReviewDocsResponseDto::class, $result);
        $this->assertEquals(1, $result->total);
        $this->assertCount(1, $result->docs);
        $this->assertEquals(4295935, $result->docs[0]->movieId);
    }

    public function test_searchReviews_withAuthorFilter_returnsAuthorReviews(): void
    {
        $reviewsData = [
            'docs' => [
                [
                    'id' => 3532549,
                    'movieId' => 161242,
                    'title' => 'Лучший фэнтезийный сериал конца 90-х',
                    'type' => 'Позитивный',
                    'review' => 'Как-то раз на просторах интернета я искал, что посмотреть...',
                    'date' => '2025-07-04T21:44:12Z',
                    'author' => 'Kotinets',
                    'userRating' => 0,
                    'authorId' => 111369473,
                    'reviewLikes' => 0,
                    'reviewDislikes' => 0,
                    'createdAt' => '2025-07-05T15:19:15.499Z',
                    'updatedAt' => '2025-07-05T15:19:15.499Z'
                ]
            ],
            'total' => 1,
            'limit' => 10,
            'page' => 1,
            'pages' => 1
        ];
        
        $response = new Response(200, [], json_encode($reviewsData));
        $this->mockHandler->append($response);
        
        $filter = new ReviewSearchFilter();
        $filter->author('Kotinets');
        
        $result = $this->reviewRequests->searchReviews($filter);
        
        $this->assertInstanceOf(ReviewDocsResponseDto::class, $result);
        $this->assertEquals(1, $result->total);
        $this->assertCount(1, $result->docs);
        $this->assertEquals('Kotinets', $result->docs[0]->author);
    }

    public function test_searchReviews_withInvalidLimit_throwsException(): void
    {
        $this->expectException(KinopoiskDevException::class);
        $this->expectExceptionMessage('Лимит не должен превышать 250');
        
        $this->reviewRequests->searchReviews(null, 1, 251);
    }

    public function test_searchReviews_withInvalidPage_throwsException(): void
    {
        $this->expectException(KinopoiskDevException::class);
        $this->expectExceptionMessage('Номер страницы не должен быть меньше 1');
        
        $this->reviewRequests->searchReviews(null, 0, 10);
    }

    public function test_searchReviews_withServerError_throwsException(): void
    {
        $errorResponse = new Response(500, [], json_encode(['error' => 'Internal server error']));
        $this->mockHandler->append($errorResponse);

        $this->expectException(KinopoiskDevException::class);
        $this->expectExceptionMessage('Ошибка HTTP запроса: Server error: `GET /v1.4//review?page.eq=1&limit.eq=10` resulted in a `500 Internal Server Error` response:');

        $this->reviewRequests->searchReviews();
    }

    public function test_searchReviews_withUnauthorized_throwsException(): void
    {
        $errorResponse = new Response(401, [], json_encode(['error' => 'Unauthorized']));
        $this->mockHandler->append($errorResponse);

        $this->expectException(KinopoiskDevException::class);
        $this->expectExceptionMessage('Ошибка HTTP запроса: Client error: `GET /v1.4//review?page.eq=1&limit.eq=10` resulted in a `401 Unauthorized` response:');

        $this->reviewRequests->searchReviews();
    }

    public function test_searchReviews_withForbidden_throwsException(): void
    {
        $errorResponse = new Response(403, [], json_encode(['error' => 'Forbidden']));
        $this->mockHandler->append($errorResponse);

        $this->expectException(KinopoiskDevException::class);
        $this->expectExceptionMessage('Ошибка HTTP запроса: Client error: `GET /v1.4//review?page.eq=1&limit.eq=10` resulted in a `403 Forbidden` response:');

        $this->reviewRequests->searchReviews();
    }
} 