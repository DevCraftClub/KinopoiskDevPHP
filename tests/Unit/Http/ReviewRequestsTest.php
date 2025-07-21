<?php

declare(strict_types=1);

namespace KinopoiskDev\Tests\Unit\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use KinopoiskDev\Exceptions\KinopoiskDevException;
use KinopoiskDev\Filter\ReviewSearchFilter;
use KinopoiskDev\Http\ReviewRequests;
use KinopoiskDev\Responses\Api\ReviewDocsResponseDto;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;

/**
 * @group unit
 * @group http
 * @group review-requests
 */
class ReviewRequestsTest extends TestCase {

	private ReviewRequests $reviewRequests;

	public function test_searchReviews_withoutFilters_returnsReviews(): void {
		$result = $this->reviewRequests->searchReviews(null, 1, 10);
		$this->assertNotEmpty($result->docs);
		$this->assertIsArray($result->docs);
		$firstReview = $result->docs[0];
		$this->assertNotEmpty($firstReview->id);
		$this->assertNotEmpty($firstReview->type);
		$this->assertNotEmpty($firstReview->author);
	}

	public function test_searchReviews_withFilters_returnsFilteredReviews(): void {
		$filter = new \KinopoiskDev\Filter\ReviewSearchFilter();
		$filter->type('Позитивный');
		$result = $this->reviewRequests->searchReviews($filter, 1, 10);
		$this->assertNotEmpty($result->docs);
		$this->assertIsArray($result->docs);
		$firstReview = $result->docs[0];
		$this->assertNotEmpty($firstReview->id);
		$this->assertNotEmpty($firstReview->type);
		$this->assertNotEmpty($firstReview->author);
	}

	public function test_getPositiveReviews_returnsPositiveReviews(): void {
		$result = $this->reviewRequests->getPositiveReviews(1, 10);
		$this->assertNotEmpty($result->docs);
		$this->assertIsArray($result->docs);
		$firstReview = $result->docs[0];
		$this->assertNotEmpty($firstReview->id);
		$this->assertNotEmpty($firstReview->type);
		$this->assertNotEmpty($firstReview->author);
	}

	public function test_getNegativeReviews_returnsNegativeReviews(): void {
		$result = $this->reviewRequests->getNegativeReviews(1, 10);
		$this->assertNotEmpty($result->docs);
		$this->assertIsArray($result->docs);
		$firstReview = $result->docs[0];
		$this->assertNotEmpty($firstReview->id);
		$this->assertNotEmpty($firstReview->type);
		$this->assertNotEmpty($firstReview->author);
	}

	public function test_searchReviews_withMovieIdFilter_returnsMovieReviews(): void {
		$filter = new \KinopoiskDev\Filter\ReviewSearchFilter();
		$filter->movieId(4295935);
		$result = $this->reviewRequests->searchReviews($filter, 1, 10);
		$this->assertNotEmpty($result->docs);
		$this->assertIsArray($result->docs);
		$firstReview = $result->docs[0];
		$this->assertNotEmpty($firstReview->id);
		$this->assertNotEmpty($firstReview->type);
		$this->assertNotEmpty($firstReview->author);
	}

	public function test_searchReviews_withAuthorFilter_returnsAuthorReviews(): void {
		$filter = new \KinopoiskDev\Filter\ReviewSearchFilter();
		$filter->author('Kotinets');
		$result = $this->reviewRequests->searchReviews($filter, 1, 10);
		$this->assertNotEmpty($result->docs);
		$this->assertIsArray($result->docs);
		$firstReview = $result->docs[0];
		$this->assertNotEmpty($firstReview->id);
		$this->assertNotEmpty($firstReview->type);
		$this->assertNotEmpty($firstReview->author);
	}

	public function test_searchReviews_withInvalidLimit_throwsException(): void {
		$this->expectException(\KinopoiskDev\Exceptions\KinopoiskDevException::class);
		$this->expectExceptionMessage('Лимит не должен превышать 250');
		$this->reviewRequests->searchReviews(null, 1, 251);
	}

	public function test_searchReviews_withInvalidPage_throwsException(): void {
		$this->expectException(\KinopoiskDev\Exceptions\KinopoiskDevException::class);
		$this->expectExceptionMessage('Номер страницы не должен быть меньше 1');
		$this->reviewRequests->searchReviews(null, 0, 10);
	}

	protected function setUp(): void {
		parent::setUp();
		$apiToken = getenv('KINOPOISK_API_TOKEN');
		if (!$apiToken) {
			$this->markTestSkipped('KINOPOISK_API_TOKEN не установлен');
		}
		$this->reviewRequests = new ReviewRequests(apiToken: $apiToken);
	}

}