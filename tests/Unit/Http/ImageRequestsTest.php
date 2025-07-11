<?php

declare(strict_types=1);

namespace KinopoiskDev\Tests\Unit\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use KinopoiskDev\Exceptions\KinopoiskDevException;
use KinopoiskDev\Filter\MovieSearchFilter;
use KinopoiskDev\Http\ImageRequests;
use KinopoiskDev\Responses\Api\ImageDocsResponseDto;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 * @group http
 * @group image-requests
 */
class ImageRequestsTest extends TestCase {

	private MockHandler   $mockHandler;
	private HandlerStack  $handlerStack;
	private Client        $httpClient;
	private ImageRequests $imageRequests;

	public function test_getImages_withoutFilters_returnsImages(): void {
		$imagesData = [
			'docs'  => [
				[
					'movieId'    => 40966,
					'type'       => 'still',
					'url'        => 'https://image.openmoviedb.com/kinopoisk-images/10768063/32c11330-7a08-440a-aa0d-7eff41e572c3/orig',
					'previewUrl' => 'https://image.openmoviedb.com/kinopoisk-images/10768063/32c11330-7a08-440a-aa0d-7eff41e572c3/360',
					'height'     => 635,
					'width'      => 1118,
					'createdAt'  => '2025-07-06T13:00:21.475Z',
					'updatedAt'  => '2025-07-06T13:00:21.475Z',
					'id'         => '686a73652737c1f3f932cd46',
				],
			],
			'total' => 2406957,
			'limit' => 10,
			'page'  => 1,
			'pages' => 240696,
		];

		$response = new Response(200, [], json_encode($imagesData));
		$this->mockHandler->append($response);

		$result = $this->imageRequests->getImages();

		$this->assertInstanceOf(ImageDocsResponseDto::class, $result);
		$this->assertEquals(2406957, $result->total);
		$this->assertCount(1, $result->docs);
		$this->assertEquals(40966, $result->docs[0]->movieId);
		$this->assertEquals('still', $result->docs[0]->type);
	}

	public function test_getImages_withFilters_returnsFilteredImages(): void {
		$imagesData = [
			'docs'  => [
				[
					'movieId'    => 7519616,
					'type'       => 'poster',
					'url'        => 'https://image.openmoviedb.com/kinopoisk-images/10703959/c4930044-6b5f-46be-840f-72faa4753edd/orig',
					'previewUrl' => 'https://image.openmoviedb.com/kinopoisk-images/10703959/c4930044-6b5f-46be-840f-72faa4753edd/360',
					'height'     => 1996,
					'width'      => 3000,
					'createdAt'  => '2025-07-06T01:29:08.095Z',
					'updatedAt'  => '2025-07-06T01:29:08.095Z',
					'id'         => '6869d164e782baeefc72b544',
				],
			],
			'total' => 1,
			'limit' => 10,
			'page'  => 1,
			'pages' => 1,
		];

		$response = new Response(200, [], json_encode($imagesData));
		$this->mockHandler->append($response);

		$filter = new MovieSearchFilter();
		$filter->addFilter('type', 'poster');

		$result = $this->imageRequests->getImages($filter);

		$this->assertInstanceOf(ImageDocsResponseDto::class, $result);
		$this->assertEquals(1, $result->total);
		$this->assertCount(1, $result->docs);
		$this->assertEquals(7519616, $result->docs[0]->movieId);
		$this->assertEquals('poster', $result->docs[0]->type);
	}

	public function test_getImagesByMovieId_withValidId_returnsImages(): void {
		$imagesData = [
			'docs'  => [
				[
					'movieId'    => 40966,
					'type'       => 'still',
					'url'        => 'https://image.openmoviedb.com/kinopoisk-images/10768063/32c11330-7a08-440a-aa0d-7eff41e572c3/orig',
					'previewUrl' => 'https://image.openmoviedb.com/kinopoisk-images/10768063/32c11330-7a08-440a-aa0d-7eff41e572c3/360',
					'height'     => 635,
					'width'      => 1118,
					'createdAt'  => '2025-07-06T13:00:21.475Z',
					'updatedAt'  => '2025-07-06T13:00:21.475Z',
					'id'         => '686a73652737c1f3f932cd46',
				],
			],
			'total' => 1,
			'limit' => 10,
			'page'  => 1,
			'pages' => 1,
		];

		$response = new Response(200, [], json_encode($imagesData));
		$this->mockHandler->append($response);

		$result = $this->imageRequests->getImagesByMovieId(40966);

		$this->assertInstanceOf(ImageDocsResponseDto::class, $result);
		$this->assertEquals(1, $result->total);
		$this->assertCount(1, $result->docs);
		$this->assertEquals(40966, $result->docs[0]->movieId);
	}

	public function test_getImagesByMovieId_withType_returnsFilteredImages(): void {
		$imagesData = [
			'docs'  => [
				[
					'movieId'    => 40966,
					'type'       => 'poster',
					'url'        => 'https://image.openmoviedb.com/kinopoisk-images/10768063/32c11330-7a08-440a-aa0d-7eff41e572c3/orig',
					'previewUrl' => 'https://image.openmoviedb.com/kinopoisk-images/10768063/32c11330-7a08-440a-aa0d-7eff41e572c3/360',
					'height'     => 635,
					'width'      => 1118,
					'createdAt'  => '2025-07-06T13:00:21.475Z',
					'updatedAt'  => '2025-07-06T13:00:21.475Z',
					'id'         => '686a73652737c1f3f932cd46',
				],
			],
			'total' => 1,
			'limit' => 10,
			'page'  => 1,
			'pages' => 1,
		];

		$response = new Response(200, [], json_encode($imagesData));
		$this->mockHandler->append($response);

		$result = $this->imageRequests->getImagesByMovieId(40966, 'poster');

		$this->assertInstanceOf(ImageDocsResponseDto::class, $result);
		$this->assertEquals(1, $result->total);
		$this->assertCount(1, $result->docs);
		$this->assertEquals('poster', $result->docs[0]->type);
	}

	public function test_getHighRatedPosters_returnsPosters(): void {
		$imagesData = [
			'docs'  => [
				[
					'movieId'    => 7519616,
					'type'       => 'poster',
					'url'        => 'https://image.openmoviedb.com/kinopoisk-images/10703959/c4930044-6b5f-46be-840f-72faa4753edd/orig',
					'previewUrl' => 'https://image.openmoviedb.com/kinopoisk-images/10703959/c4930044-6b5f-46be-840f-72faa4753edd/360',
					'height'     => 1996,
					'width'      => 3000,
					'createdAt'  => '2025-07-06T01:29:08.095Z',
					'updatedAt'  => '2025-07-06T01:29:08.095Z',
					'id'         => '6869d164e782baeefc72b544',
				],
			],
			'total' => 1,
			'limit' => 10,
			'page'  => 1,
			'pages' => 1,
		];

		$response = new Response(200, [], json_encode($imagesData));
		$this->mockHandler->append($response);

		$result = $this->imageRequests->getHighRatedPosters(7.0);

		$this->assertInstanceOf(ImageDocsResponseDto::class, $result);
		$this->assertEquals(1, $result->total);
		$this->assertCount(1, $result->docs);
		$this->assertEquals('poster', $result->docs[0]->type);
	}

	public function test_getImages_withInvalidLimit_throwsException(): void {
		$this->expectException(KinopoiskDevException::class);
		$this->expectExceptionMessage('Лимит не должен превышать 250');

		$this->imageRequests->getImages(NULL, 1, 251);
	}

	public function test_getImages_withInvalidPage_throwsException(): void {
		$this->expectException(KinopoiskDevException::class);
		$this->expectExceptionMessage('Номер страницы не должен быть меньше 1');

		$this->imageRequests->getImages(NULL, 0, 10);
	}

	public function test_getImages_withServerError_throwsException(): void {
		$errorResponse = new Response(500, [], json_encode(['error' => 'Internal server error']));
		$this->mockHandler->append($errorResponse);

		$this->expectException(KinopoiskDevException::class);
		$this->expectExceptionMessage('Ошибка HTTP запроса: Server error: `GET /v1.4/image?page.eq=1&limit.eq=10` resulted in a `500 Internal Server Error` response:');

		$this->imageRequests->getImages();
	}

	public function test_getImages_withUnauthorized_throwsException(): void {
		$errorResponse = new Response(401, [], json_encode(['error' => 'Unauthorized']));
		$this->mockHandler->append($errorResponse);

		$this->expectException(KinopoiskDevException::class);
		$this->expectExceptionMessage('Ошибка HTTP запроса: Client error: `GET /v1.4/image?page.eq=1&limit.eq=10` resulted in a `401 Unauthorized` response:');

		$this->imageRequests->getImages();
	}

	public function test_getImages_withForbidden_throwsException(): void {
		$errorResponse = new Response(403, [], json_encode(['error' => 'Forbidden']));
		$this->mockHandler->append($errorResponse);

		$this->expectException(KinopoiskDevException::class);
		$this->expectExceptionMessage('Ошибка HTTP запроса: Client error: `GET /v1.4/image?page.eq=1&limit.eq=10` resulted in a `403 Forbidden` response:');

		$this->imageRequests->getImages();
	}

	protected function setUp(): void {
		parent::setUp();

		$this->mockHandler  = new MockHandler();
		$this->handlerStack = HandlerStack::create($this->mockHandler);
		$this->httpClient   = new Client(['handler' => $this->handlerStack]);

		$this->imageRequests = new ImageRequests(
			apiToken  : 'MOCK123-TEST456-UNIT789-TOKEN01',
			httpClient: $this->httpClient,
		);
	}

	protected function tearDown(): void {
		parent::tearDown();
	}

}