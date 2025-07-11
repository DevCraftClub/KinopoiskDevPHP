<?php

declare(strict_types=1);

namespace KinopoiskDev\Tests\Unit\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use KinopoiskDev\Exceptions\KinopoiskDevException;
use KinopoiskDev\Filter\SeasonSearchFilter;
use KinopoiskDev\Http\SeasonRequests;
use KinopoiskDev\Models\Season;
use KinopoiskDev\Responses\Api\SeasonDocsResponseDto;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 * @group http
 * @group season-requests
 */
class SeasonRequestsTest extends TestCase {

	private MockHandler    $mockHandler;
	private HandlerStack   $handlerStack;
	private Client         $httpClient;
	private SeasonRequests $seasonRequests;

	public function test_getSeasonById_withValidId_returnsSeason(): void {
		$seasonData = [
			'movieId'       => 404900,
			'number'        => 0,
			'airDate'       => '2009-02-17T00:00:00.000Z',
			'createdAt'     => '2023-08-07T18:45:45.440Z',
			'description'   => '',
			'duration'      => 29,
			'enDescription' => '',
			'enName'        => 'Specials',
			'episodes'      => [
				[
					'number'        => 1,
					'name'          => 'Эпизод 1',
					'enName'        => 'Good Cop / Bad Cop',
					'airDate'       => '2009-02-17T00:00:00.000Z',
					'description'   => '',
					'enDescription' => 'Season 1 Minisode 1: Hank and Marie celebrate Valentine\'s Day.',
					'still'         => [
						'url'        => 'https://image.openmoviedb.com/tmdb-images/original/t729tFVXPetnJlJ2VsUZQz0rX6v.jpg',
						'previewUrl' => 'https://image.openmoviedb.com/tmdb-images/w500/t729tFVXPetnJlJ2VsUZQz0rX6v.jpg',
					],
				],
			],
			'name'          => 'Спецматериалы',
			'poster'        => [
				'url'        => 'https://image.openmoviedb.com/tmdb-images/original/40dT79mDEZwXkQiZNBgSaydQFDP.jpg',
				'previewUrl' => 'https://image.openmoviedb.com/tmdb-images/w500/40dT79mDEZwXkQiZNBgSaydQFDP.jpg',
			],
			'updatedAt'     => '2025-07-01T12:56:42.769Z',
			'source'        => 'tmdb',
			'episodesCount' => 9,
			'id'            => '64d13bc37c7b690ee6d41e9f',
		];

		$response = new Response(200, [], json_encode($seasonData));
		$this->mockHandler->append($response);

		$result = $this->seasonRequests->getSeasonById(123);

		$this->assertInstanceOf(Season::class, $result);
		$this->assertEquals(404900, $result->movieId);
		$this->assertEquals(0, $result->number);
		$this->assertEquals('Спецматериалы', $result->name);
		$this->assertEquals('Specials', $result->enName);
	}

	public function test_searchSeasons_withoutFilters_returnsSeasons(): void {
		$seasonsData = [
			'docs'  => [
				[
					'movieId'       => 404900,
					'number'        => 5,
					'episodesCount' => 16,
					'episodes'      => [
						[
							'number'        => 1,
							'name'          => 'Живи свободным или умри',
							'enName'        => 'Live Free or Die',
							'airDate'       => '2012-07-15T00:00:00.000Z',
							'description'   => 'Уолтер, Джесси и Майк активно заметают следы. Скайлер паникует из-за очнувшегося Теда.',
							'enDescription' => 'Walt deals with the aftermath of the nursing home bombing.',
							'still'         => [
								'url'        => 'https://image.openmoviedb.com/tmdb-images/original/uShB5dWoA3xIivZ9jvwWnGCVvt4.jpg',
								'previewUrl' => 'https://image.openmoviedb.com/tmdb-images/w500/uShB5dWoA3xIivZ9jvwWnGCVvt4.jpg',
							],
						],
					],
					'name'          => 'Сезон 5',
					'enName'        => 'Season 5',
					'airDate'       => '2012-07-15T00:00:00.000Z',
					'createdAt'     => '2023-08-07T18:45:45.440Z',
					'updatedAt'     => '2025-07-01T12:56:42.769Z',
					'source'        => 'tmdb',
					'id'            => '64d13bc37c7b690ee6d41e9f',
				],
			],
			'total' => 1,
			'limit' => 10,
			'page'  => 1,
			'pages' => 1,
		];

		$response = new Response(200, [], json_encode($seasonsData));
		$this->mockHandler->append($response);

		$result = $this->seasonRequests->searchSeasons();

		$this->assertInstanceOf(SeasonDocsResponseDto::class, $result);
		$this->assertEquals(1, $result->total);
		$this->assertCount(1, $result->docs);
		$this->assertEquals(404900, $result->docs[0]->movieId);
		$this->assertEquals(5, $result->docs[0]->number);
	}

	public function test_searchSeasons_withFilters_returnsFilteredSeasons(): void {
		$seasonsData = [
			'docs'  => [
				[
					'movieId'       => 404900,
					'number'        => 1,
					'episodesCount' => 7,
					'episodes'      => [
						[
							'number'        => 1,
							'name'          => 'Пилот',
							'enName'        => 'Pilot',
							'airDate'       => '2008-01-20T00:00:00.000Z',
							'description'   => 'Первый эпизод сериала',
							'enDescription' => 'First episode of the series',
							'still'         => [
								'url'        => 'https://image.openmoviedb.com/tmdb-images/original/pilot.jpg',
								'previewUrl' => 'https://image.openmoviedb.com/tmdb-images/w500/pilot.jpg',
							],
						],
					],
					'name'          => 'Сезон 1',
					'enName'        => 'Season 1',
					'airDate'       => '2008-01-20T00:00:00.000Z',
					'createdAt'     => '2023-08-07T18:45:45.440Z',
					'updatedAt'     => '2025-07-01T12:56:42.769Z',
					'source'        => 'tmdb',
					'id'            => '64d13bc37c7b690ee6d41e9f',
				],
			],
			'total' => 1,
			'limit' => 10,
			'page'  => 1,
			'pages' => 1,
		];

		$response = new Response(200, [], json_encode($seasonsData));
		$this->mockHandler->append($response);

		$filter = new SeasonSearchFilter();
		$filter->number(1);

		$result = $this->seasonRequests->searchSeasons($filter);

		$this->assertInstanceOf(SeasonDocsResponseDto::class, $result);
		$this->assertEquals(1, $result->total);
		$this->assertCount(1, $result->docs);
		$this->assertEquals(1, $result->docs[0]->number);
	}

	public function test_getSeasonsForMovie_withValidMovieId_returnsSeasons(): void {
		$seasonsData = [
			'docs'  => [
				[
					'movieId'       => 404900,
					'number'        => 1,
					'episodesCount' => 7,
					'episodes'      => [
						[
							'number'        => 1,
							'name'          => 'Пилот',
							'enName'        => 'Pilot',
							'airDate'       => '2008-01-20T00:00:00.000Z',
							'description'   => 'Первый эпизод сериала',
							'enDescription' => 'First episode of the series',
							'still'         => [
								'url'        => 'https://image.openmoviedb.com/tmdb-images/original/pilot.jpg',
								'previewUrl' => 'https://image.openmoviedb.com/tmdb-images/w500/pilot.jpg',
							],
						],
					],
					'name'          => 'Сезон 1',
					'enName'        => 'Season 1',
					'airDate'       => '2008-01-20T00:00:00.000Z',
					'createdAt'     => '2023-08-07T18:45:45.440Z',
					'updatedAt'     => '2025-07-01T12:56:42.769Z',
					'source'        => 'tmdb',
					'id'            => '64d13bc37c7b690ee6d41e9f',
				],
			],
			'total' => 1,
			'limit' => 10,
			'page'  => 1,
			'pages' => 1,
		];

		$response = new Response(200, [], json_encode($seasonsData));
		$this->mockHandler->append($response);

		$result = $this->seasonRequests->getSeasonsForMovie(404900);

		$this->assertInstanceOf(SeasonDocsResponseDto::class, $result);
		$this->assertEquals(1, $result->total);
		$this->assertCount(1, $result->docs);
		$this->assertEquals(404900, $result->docs[0]->movieId);
	}

	public function test_getSeasonByNumber_withValidMovieIdAndNumber_returnsSeason(): void {
		$seasonsData = [
			'docs'  => [
				[
					'movieId'       => 404900,
					'number'        => 2,
					'episodesCount' => 13,
					'episodes'      => [
						[
							'number'        => 1,
							'name'          => 'Семь тридцать семь',
							'enName'        => 'Seven Thirty-Seven',
							'airDate'       => '2009-03-08T00:00:00.000Z',
							'description'   => 'Второй сезон начинается',
							'enDescription' => 'Second season begins',
							'still'         => [
								'url'        => 'https://image.openmoviedb.com/tmdb-images/original/737.jpg',
								'previewUrl' => 'https://image.openmoviedb.com/tmdb-images/w500/737.jpg',
							],
						],
					],
					'name'          => 'Сезон 2',
					'enName'        => 'Season 2',
					'airDate'       => '2009-03-08T00:00:00.000Z',
					'createdAt'     => '2023-08-07T18:45:45.440Z',
					'updatedAt'     => '2025-07-01T12:56:42.769Z',
					'source'        => 'tmdb',
					'id'            => '64d13bc37c7b690ee6d41e9f',
				],
			],
			'total' => 1,
			'limit' => 1,
			'page'  => 1,
			'pages' => 1,
		];

		$response = new Response(200, [], json_encode($seasonsData));
		$this->mockHandler->append($response);

		$result = $this->seasonRequests->getSeasonByNumber(404900, 2);

		$this->assertInstanceOf(Season::class, $result);
		$this->assertEquals(404900, $result->movieId);
		$this->assertEquals(2, $result->number);
		$this->assertEquals('Сезон 2', $result->name);
	}

	public function test_getSeasonByNumber_withInvalidMovieIdAndNumber_returnsNull(): void {
		$seasonsData = [
			'docs'  => [],
			'total' => 0,
			'limit' => 1,
			'page'  => 1,
			'pages' => 0,
		];

		$response = new Response(200, [], json_encode($seasonsData));
		$this->mockHandler->append($response);

		$result = $this->seasonRequests->getSeasonByNumber(999999, 999);

		$this->assertNull($result);
	}

	public function test_searchSeasons_withInvalidLimit_throwsException(): void {
		$this->expectException(KinopoiskDevException::class);
		$this->expectExceptionMessage('Лимит не должен превышать 250');

		$this->seasonRequests->searchSeasons(NULL, 1, 251);
	}

	public function test_searchSeasons_withInvalidPage_throwsException(): void {
		$this->expectException(KinopoiskDevException::class);
		$this->expectExceptionMessage('Номер страницы не должен быть меньше 1');

		$this->seasonRequests->searchSeasons(NULL, 0, 10);
	}

	public function test_getSeasonById_withServerError_throwsException(): void {
		$errorResponse = new Response(500, [], json_encode(['error' => 'Internal server error']));
		$this->mockHandler->append($errorResponse);

		$this->expectException(KinopoiskDevException::class);
		$this->expectExceptionMessage('Ошибка HTTP запроса: Server error: `GET /v1.4/season/123` resulted in a `500 Internal Server Error` response:');

		$this->seasonRequests->getSeasonById(123);
	}

	public function test_searchSeasons_withUnauthorized_throwsException(): void {
		$errorResponse = new Response(401, [], json_encode(['error' => 'Unauthorized']));
		$this->mockHandler->append($errorResponse);

		$this->expectException(KinopoiskDevException::class);
		$this->expectExceptionMessage('Ошибка HTTP запроса: Client error: `GET /v1.4/season?page.eq=1&limit.eq=10` resulted in a `401 Unauthorized` response:');

		$this->seasonRequests->searchSeasons();
	}

	public function test_searchSeasons_withForbidden_throwsException(): void {
		$errorResponse = new Response(403, [], json_encode(['error' => 'Forbidden']));
		$this->mockHandler->append($errorResponse);

		$this->expectException(KinopoiskDevException::class);
		$this->expectExceptionMessage('Ошибка HTTP запроса: Client error: `GET /v1.4/season?page.eq=1&limit.eq=10` resulted in a `403 Forbidden` response:');

		$this->seasonRequests->searchSeasons();
	}

	protected function setUp(): void {
		parent::setUp();

		$this->mockHandler  = new MockHandler();
		$this->handlerStack = HandlerStack::create($this->mockHandler);
		$this->httpClient   = new Client(['handler' => $this->handlerStack]);

		$this->seasonRequests = new SeasonRequests(
			apiToken  : 'MOCK123-TEST456-UNIT789-TOKEN01',
			httpClient: $this->httpClient,
		);
	}

	protected function tearDown(): void {
		parent::tearDown();
	}

}