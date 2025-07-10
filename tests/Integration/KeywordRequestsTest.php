<?php

declare(strict_types=1);

namespace KinopoiskDev\Tests\Integration;

use KinopoiskDev\Filter\KeywordSearchFilter;
use KinopoiskDev\Http\KeywordRequests;
use KinopoiskDev\Models\Keyword;
use KinopoiskDev\Responses\Api\KeywordDocsResponseDto;
use PHPUnit\Framework\TestCase;

/**
 * Интеграционные тесты для KeywordRequests
 *
 * Тестирование реальных запросов к API ключевых слов
 * с проверкой корректности обработки ответов.
 *
 * @package KinopoiskDev\Tests\Integration
 * @since   1.0.0
 * @author  Maxim Harder
 * @version 1.0.0
 * @group   integration
 * @group   keyword_requests
 */
final class KeywordRequestsTest extends TestCase {

	private const string API_TOKEN = 'YOUR_API_KEY';
	
	private function getTestApiToken(): string
	{
		return $_ENV['KINOPOISK_TOKEN'] ?? self::API_TOKEN;
	}
	
	private function shouldSkipIntegrationTests(): bool
	{
		// Пропускаем интеграционные тесты если:
		// 1. Явно установлена переменная SKIP_INTEGRATION_TESTS
		// 2. API ключ не настроен (равен плейсхолдеру)
		return $_ENV['SKIP_INTEGRATION_TESTS'] === 'true' || 
			   $this->getTestApiToken() === self::API_TOKEN;
	}

	private KeywordRequests $keywordRequests;

	protected function setUp(): void {
		if ($this->shouldSkipIntegrationTests()) {
			$this->markTestSkipped('Интеграционные тесты пропущены: не настроен реальный API ключ');
		}
		
		$this->keywordRequests = new KeywordRequests(
			apiToken: $this->getTestApiToken(),
			httpClient: null,
			useCache: false,
		);
	}

	/**
	 * @test
	 * @group keyword_search
	 */
	public function testSearchKeywordsBasicFilter(): void {
		$filter = new KeywordSearchFilter();
		$filter->onlyPopular(5);

		$response = $this->keywordRequests->searchKeywords($filter, page: 1, limit: 10);

		$this->assertInstanceOf(KeywordDocsResponseDto::class, $response);
		$this->assertIsArray($response->docs);
		$this->assertLessThanOrEqual(10, count($response->docs));

		foreach ($response->docs as $keyword) {
			$this->assertInstanceOf(Keyword::class, $keyword);
			$this->assertIsInt($keyword->getId());
			$this->assertIsString($keyword->getTitle());
			$this->assertNotEmpty($keyword->getTitle());
		}
	}

	/**
	 * @test
	 * @group keyword_search
	 */
	public function testSearchKeywordsByTitle(): void {
		$searchTitle = 'драма';
		$keywords = $this->keywordRequests->getKeywordsByTitle($searchTitle, page: 1, limit: 5);

		$this->assertInstanceOf(KeywordDocsResponseDto::class, $keywords);
		$this->assertGreaterThan(0, $keywords->total);

		$foundMatch = false;
		foreach ($keywords->docs as $keyword) {
			$this->assertInstanceOf(Keyword::class, $keyword);
			if (str_contains(mb_strtolower($keyword->getTitle()), mb_strtolower($searchTitle))) {
				$foundMatch = true;
				break;
			}
		}

		$this->assertTrue($foundMatch, 'Должно найтись хотя бы одно ключевое слово содержащее "драма"');
	}

	/**
	 * @test
	 * @group keyword_movie_relation
	 */
	public function testGetKeywordsForMovie(): void {
		$movieId = 666; // Брат (1997)
		$keywords = $this->keywordRequests->getKeywordsForMovie($movieId, page: 1, limit: 10);

		$this->assertInstanceOf(KeywordDocsResponseDto::class, $keywords);

		foreach ($keywords->docs as $keyword) {
			$this->assertInstanceOf(Keyword::class, $keyword);
			$this->assertTrue($keyword->isRelatedToMovie($movieId), 
				'Ключевое слово должно быть связано с указанным фильмом');
		}
	}

	/**
	 * @test
	 * @group keyword_by_id
	 */
	public function testGetKeywordById(): void {
		// Сначала получаем любое ключевое слово
		$filter = new KeywordSearchFilter();
		$filter->onlyPopular(10);
		$response = $this->keywordRequests->searchKeywords($filter, page: 1, limit: 1);

		$this->assertGreaterThan(0, $response->total);
		$firstKeyword = $response->docs[0];

		// Теперь получаем его по ID
		$keyword = $this->keywordRequests->getKeywordById($firstKeyword->getId());

		$this->assertInstanceOf(Keyword::class, $keyword);
		$this->assertSame($firstKeyword->getId(), $keyword->getId());
		$this->assertSame($firstKeyword->getTitle(), $keyword->getTitle());
	}

	/**
	 * @test
	 * @group keyword_by_id
	 */
	public function testGetKeywordByIdNonExistent(): void {
		$nonExistentId = 99999999;
		$keyword = $this->keywordRequests->getKeywordById($nonExistentId);

		$this->assertNull($keyword);
	}

	/**
	 * @test
	 * @group keyword_popular
	 */
	public function testGetPopularKeywords(): void {
		$keywords = $this->keywordRequests->getPopularKeywords(page: 1, limit: 15);

		$this->assertInstanceOf(KeywordDocsResponseDto::class, $keywords);
		$this->assertGreaterThan(0, $keywords->total);

		// Проверяем, что ключевые слова отсортированы по популярности
		$previousMovieCount = PHP_INT_MAX;
		foreach ($keywords->docs as $keyword) {
			$this->assertInstanceOf(Keyword::class, $keyword);
			
			$currentMovieCount = $keyword->getMoviesCount();
			$this->assertLessThanOrEqual($previousMovieCount, $currentMovieCount,
				'Ключевые слова должны быть отсортированы по убыванию популярности');
			$previousMovieCount = $currentMovieCount;
		}
	}

	/**
	 * @test
	 * @group keyword_filter_advanced
	 */
	public function testAdvancedKeywordFiltering(): void {
		$filter = new KeywordSearchFilter();
		$filter->search('коме')
			   ->onlyPopular(3)
			   ->sortByTitle('asc');

		$response = $this->keywordRequests->searchKeywords($filter, page: 1, limit: 10);

		$this->assertInstanceOf(KeywordDocsResponseDto::class, $response);

		foreach ($response->docs as $keyword) {
			$this->assertInstanceOf(Keyword::class, $keyword);
			$this->assertGreaterThanOrEqual(3, $keyword->getMoviesCount(),
				'Ключевое слово должно быть связано минимум с 3 фильмами');
			$this->assertStringContainsStringIgnoringCase('коме', $keyword->getTitle(),
				'Название должно содержать поисковый запрос');
		}
	}

	/**
	 * @test
	 * @group keyword_filtering
	 */
	public function testRecentlyCreatedKeywords(): void {
		$filter = new KeywordSearchFilter();
		$filter->recentlyCreated(365) // За последний год
			   ->sortByCreatedAt('desc');

		$response = $this->keywordRequests->searchKeywords($filter, page: 1, limit: 10);

		$this->assertInstanceOf(KeywordDocsResponseDto::class, $response);

		foreach ($response->docs as $keyword) {
			$this->assertInstanceOf(Keyword::class, $keyword);
			$this->assertTrue($keyword->isRecentlyCreated(365),
				'Ключевое слово должно быть создано в указанный период');
		}
	}

	/**
	 * @test
	 * @group keyword_filtering
	 */
	public function testRecentlyUpdatedKeywords(): void {
		$filter = new KeywordSearchFilter();
		$filter->recentlyUpdated(180) // За последние 6 месяцев
			   ->sortByUpdatedAt('desc');

		$response = $this->keywordRequests->searchKeywords($filter, page: 1, limit: 10);

		$this->assertInstanceOf(KeywordDocsResponseDto::class, $response);

		foreach ($response->docs as $keyword) {
			$this->assertInstanceOf(Keyword::class, $keyword);
			$this->assertTrue($keyword->isRecentlyUpdated(180),
				'Ключевое слово должно быть обновлено в указанный период');
		}
	}

	/**
	 * @test
	 * @group keyword_sorting
	 */
	public function testKeywordSortingById(): void {
		$filter = new KeywordSearchFilter();
		$filter->onlyPopular(5)
			   ->sortById('asc');

		$response = $this->keywordRequests->searchKeywords($filter, page: 1, limit: 10);

		$this->assertInstanceOf(KeywordDocsResponseDto::class, $response);
		$this->assertGreaterThan(1, count($response->docs));

		$previousId = 0;
		foreach ($response->docs as $keyword) {
			$this->assertInstanceOf(Keyword::class, $keyword);
			$this->assertGreaterThan($previousId, $keyword->getId(),
				'ID должны быть отсортированы по возрастанию');
			$previousId = $keyword->getId();
		}
	}

	/**
	 * @test
	 * @group keyword_sorting
	 */
	public function testKeywordSortingByTitle(): void {
		$filter = new KeywordSearchFilter();
		$filter->onlyPopular(5)
			   ->sortByTitle('asc');

		$response = $this->keywordRequests->searchKeywords($filter, page: 1, limit: 10);

		$this->assertInstanceOf(KeywordDocsResponseDto::class, $response);
		$this->assertGreaterThan(1, count($response->docs));

		$previousTitle = '';
		foreach ($response->docs as $keyword) {
			$this->assertInstanceOf(Keyword::class, $keyword);
			$currentTitle = $keyword->getTitle();
			$this->assertGreaterThanOrEqual(0, strcmp($currentTitle, $previousTitle),
				'Названия должны быть отсортированы по алфавиту');
			$previousTitle = $currentTitle;
		}
	}

	/**
	 * @test
	 * @group keyword_field_selection
	 */
	public function testSelectSpecificFields(): void {
		$filter = new KeywordSearchFilter();
		$filter->onlyPopular(10)
			   ->selectFields(['id', 'title']);

		$response = $this->keywordRequests->searchKeywords($filter, page: 1, limit: 5);

		$this->assertInstanceOf(KeywordDocsResponseDto::class, $response);

		foreach ($response->docs as $keyword) {
			$this->assertInstanceOf(Keyword::class, $keyword);
			$this->assertIsInt($keyword->getId());
			$this->assertIsString($keyword->getTitle());
			// При выборе полей другие поля могут быть null
		}
	}

	/**
	 * @test
	 * @group keyword_pagination
	 */
	public function testKeywordPagination(): void {
		$filter = new KeywordSearchFilter();
		$filter->onlyPopular(5);

		// Первая страница
		$page1 = $this->keywordRequests->searchKeywords($filter, page: 1, limit: 5);
		// Вторая страница
		$page2 = $this->keywordRequests->searchKeywords($filter, page: 2, limit: 5);

		$this->assertInstanceOf(KeywordDocsResponseDto::class, $page1);
		$this->assertInstanceOf(KeywordDocsResponseDto::class, $page2);

		$this->assertSame(1, $page1->page);
		$this->assertSame(2, $page2->page);
		$this->assertSame($page1->total, $page2->total);

		// Проверяем, что ключевые слова на разных страницах разные
		$page1Ids = array_map(fn($keyword) => $keyword->getId(), $page1->docs);
		$page2Ids = array_map(fn($keyword) => $keyword->getId(), $page2->docs);

		$this->assertEmpty(array_intersect($page1Ids, $page2Ids),
			'Ключевые слова на разных страницах не должны пересекаться');
	}

	/**
	 * @test
	 * @group keyword_dto_analysis
	 */
	public function testKeywordDocsResponseDtoAnalysis(): void {
		$filter = new KeywordSearchFilter();
		$filter->onlyPopular(10);

		$response = $this->keywordRequests->searchKeywords($filter, page: 1, limit: 20);

		$this->assertInstanceOf(KeywordDocsResponseDto::class, $response);

		// Тестируем методы анализа DTO
		$keywordTitles = $response->getKeywordTitles();
		$this->assertIsArray($keywordTitles);
		$this->assertCount(count($response->docs), $keywordTitles);

		$popularKeywords = $response->getPopularKeywords(threshold: 15);
		$this->assertIsArray($popularKeywords);
		foreach ($popularKeywords as $keyword) {
			$this->assertInstanceOf(Keyword::class, $keyword);
			$this->assertGreaterThanOrEqual(15, $keyword->getMoviesCount());
		}

		$groups = $response->groupByPopularity();
		$this->assertIsArray($groups);
		$this->assertArrayHasKey('very_popular', $groups);
		$this->assertArrayHasKey('popular', $groups);
		$this->assertArrayHasKey('moderate', $groups);
		$this->assertArrayHasKey('rare', $groups);

		$stats = $response->getStatistics();
		$this->assertIsArray($stats);
		$this->assertArrayHasKey('total_keywords', $stats);
		$this->assertArrayHasKey('keywords_with_movies', $stats);
		$this->assertArrayHasKey('popular_keywords', $stats);
		$this->assertArrayHasKey('average_movies_per_keyword', $stats);
	}

	/**
	 * @test
	 * @group keyword_search_by_text
	 */
	public function testSearchKeywordsByText(): void {
		$filter = new KeywordSearchFilter();
		$filter->onlyPopular(5);

		$response = $this->keywordRequests->searchKeywords($filter, page: 1, limit: 20);

		$this->assertInstanceOf(KeywordDocsResponseDto::class, $response);

		// Тестируем поиск по тексту в результатах
		$dramaKeywords = $response->searchByTitle('драма');
		$this->assertIsArray($dramaKeywords);

		foreach ($dramaKeywords as $keyword) {
			$this->assertInstanceOf(Keyword::class, $keyword);
			$this->assertStringContainsStringIgnoringCase('драма', $keyword->getTitle());
		}
	}

	/**
	 * @test
	 * @group keyword_data_integrity
	 */
	public function testKeywordDataIntegrity(): void {
		$filter = new KeywordSearchFilter();
		$filter->onlyPopular(10);

		$response = $this->keywordRequests->searchKeywords($filter, page: 1, limit: 5);

		$this->assertInstanceOf(KeywordDocsResponseDto::class, $response);

		foreach ($response->docs as $keyword) {
			$this->assertInstanceOf(Keyword::class, $keyword);

			// Проверяем основные поля
			$this->assertIsInt($keyword->getId());
			$this->assertGreaterThan(0, $keyword->getId());

			$this->assertIsString($keyword->getTitle());
			$this->assertNotEmpty($keyword->getTitle());

			// Проверяем методы модели
			$this->assertIsInt($keyword->getMoviesCount());
			$this->assertGreaterThanOrEqual(0, $keyword->getMoviesCount());

			$this->assertIsBool($keyword->isPopular());

			$movieIds = $keyword->getMovieIds();
			$this->assertIsArray($movieIds);

			$summary = $keyword->getSummary();
			$this->assertIsArray($summary);
			$this->assertArrayHasKey('id', $summary);
			$this->assertArrayHasKey('title', $summary);
			$this->assertArrayHasKey('movies_count', $summary);
		}
	}

	/**
	 * @test
	 * @group keyword_empty_results
	 */
	public function testEmptyKeywordResults(): void {
		$filter = new KeywordSearchFilter();
		$filter->search('absolutely_nonexistent_keyword_12345');

		$response = $this->keywordRequests->searchKeywords($filter, page: 1, limit: 10);

		$this->assertInstanceOf(KeywordDocsResponseDto::class, $response);
		$this->assertSame(0, $response->total);
		$this->assertEmpty($response->docs);
	}

	/**
	 * @test
	 * @group performance
	 */
	public function testKeywordRequestPerformance(): void {
		$filter = new KeywordSearchFilter();
		$filter->onlyPopular(1);

		$startTime = microtime(true);
		$response = $this->keywordRequests->searchKeywords($filter, page: 1, limit: 100);
		$endTime = microtime(true);

		$this->assertInstanceOf(KeywordDocsResponseDto::class, $response);
		$this->assertLessThan(5.0, $endTime - $startTime, 
			'Запрос ключевых слов должен выполняться за разумное время');
	}

	protected function tearDown(): void {
		unset($this->keywordRequests);
	}
}