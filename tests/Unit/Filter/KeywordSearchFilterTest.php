<?php

declare(strict_types=1);

namespace KinopoiskDev\Tests\Unit\Filter;

use KinopoiskDev\Filter\KeywordSearchFilter;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 * @group filter
 * @group keyword-search-filter
 */
class KeywordSearchFilterTest extends TestCase {

	private KeywordSearchFilter $filter;

	public function test_id_filter_single(): void {
		$this->filter->id(123, 'eq');

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('id.eq', $filters);
		$this->assertEquals(123, $filters['id.eq']);
	}

	public function test_id_filter_single_with_default_operator(): void {
		$this->filter->id(456);

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('id.eq', $filters);
		$this->assertEquals(456, $filters['id.eq']);
	}

	public function test_id_filter_multiple(): void {
		$this->filter->id([123, 456, 789], 'eq');

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('id.eq', $filters);
		$this->assertEquals([123, 456, 789], $filters['id.eq']);
	}

	public function test_title_filter(): void {
		$this->filter->title('драма', 'eq');

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('title.eq', $filters);
		$this->assertEquals('драма', $filters['title.eq']);
	}

	public function test_title_filter_with_default_operator(): void {
		$this->filter->title('комедия');

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('title.eq', $filters);
		$this->assertEquals('комедия', $filters['title.eq']);
	}

	public function test_title_filter_with_regex(): void {
		$this->filter->title('боевик', 'regex');

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('title.regex', $filters);
		$this->assertEquals('боевик', $filters['title.regex']);
	}

	public function test_movieId_filter_single(): void {
		$this->filter->movieId(123);

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('movieId.eq', $filters);
		$this->assertEquals(123, $filters['movieId.eq']);
	}

	public function test_movieId_filter_multiple(): void {
		$this->filter->movieId([123, 456, 789]);

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('movieId.eq', $filters);
		$this->assertEquals([123, 456, 789], $filters['movieId.eq']);
	}

	public function test_createdAt_filter(): void {
		$this->filter->createdAt('2023-01-01T00:00:00.000Z', 'eq');

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('createdAt.eq', $filters);
		$this->assertEquals('2023-01-01T00:00:00.000Z', $filters['createdAt.eq']);
	}

	public function test_createdAt_filter_with_default_operator(): void {
		$this->filter->createdAt('2023-01-01T00:00:00.000Z');

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('createdAt.eq', $filters);
		$this->assertEquals('2023-01-01T00:00:00.000Z', $filters['createdAt.eq']);
	}

	public function test_updatedAt_filter(): void {
		$this->filter->updatedAt('2023-01-01T00:00:00.000Z', 'eq');

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('updatedAt.eq', $filters);
		$this->assertEquals('2023-01-01T00:00:00.000Z', $filters['updatedAt.eq']);
	}

	public function test_updatedAt_filter_with_default_operator(): void {
		$this->filter->updatedAt('2023-01-01T00:00:00.000Z');

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('updatedAt.eq', $filters);
		$this->assertEquals('2023-01-01T00:00:00.000Z', $filters['updatedAt.eq']);
	}

	public function test_search_filter(): void {
		$this->filter->search('драма');

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('title.regex', $filters);
		$this->assertEquals('драма', $filters['title.regex']);
	}

	public function test_onlyPopular_filter(): void {
		$this->filter->onlyPopular(20);

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('movieCount.gte', $filters);
		$this->assertEquals(20, $filters['movieCount.gte']);
	}

	public function test_onlyPopular_filter_with_default(): void {
		$this->filter->onlyPopular();

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('movieCount.gte', $filters);
		$this->assertEquals(10, $filters['movieCount.gte']);
	}

	public function test_recentlyCreated_filter(): void {
		$this->filter->recentlyCreated(7);

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('createdAt.gte', $filters);
		$this->assertStringContainsString('T', $filters['createdAt.gte']);
		$this->assertStringContainsString('Z', $filters['createdAt.gte']);
	}

	public function test_recentlyCreated_filter_with_default(): void {
		$this->filter->recentlyCreated();

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('createdAt.gte', $filters);
		$this->assertStringContainsString('T', $filters['createdAt.gte']);
		$this->assertStringContainsString('Z', $filters['createdAt.gte']);
	}

	public function test_recentlyUpdated_filter(): void {
		$this->filter->recentlyUpdated(3);

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('updatedAt.gte', $filters);
		$this->assertStringContainsString('T', $filters['updatedAt.gte']);
		$this->assertStringContainsString('Z', $filters['updatedAt.gte']);
	}

	public function test_recentlyUpdated_filter_with_default(): void {
		$this->filter->recentlyUpdated();

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('updatedAt.gte', $filters);
		$this->assertStringContainsString('T', $filters['updatedAt.gte']);
		$this->assertStringContainsString('Z', $filters['updatedAt.gte']);
	}

	public function test_createdBetween_filter(): void {
		$this->filter->createdBetween('2023-01-01T00:00:00.000Z', '2023-12-31T23:59:59.999Z');

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('createdAt.gte', $filters);
		$this->assertEquals('2023-01-01T00:00:00.000Z', $filters['createdAt.gte']);

		$this->assertArrayHasKey('createdAt.lte', $filters);
		$this->assertEquals('2023-12-31T23:59:59.999Z', $filters['createdAt.lte']);
	}

	public function test_updatedBetween_filter(): void {
		$this->filter->updatedBetween('2023-01-01T00:00:00.000Z', '2023-12-31T23:59:59.999Z');

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('updatedAt.gte', $filters);
		$this->assertEquals('2023-01-01T00:00:00.000Z', $filters['updatedAt.gte']);

		$this->assertArrayHasKey('updatedAt.lte', $filters);
		$this->assertEquals('2023-12-31T23:59:59.999Z', $filters['updatedAt.lte']);
	}

	public function test_selectFields_filter(): void {
		$this->filter->selectFields(['id', 'title', 'movieCount']);

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('selectFields', $filters);
		$this->assertEquals('id title movieCount', $filters['selectFields']);
	}

	public function test_notNullFields_filter(): void {
		$this->filter->notNullFields(['title', 'movieCount']);

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('title.ne', $filters);
		$this->assertNull($filters['title.ne']);

		$this->assertArrayHasKey('movieCount.ne', $filters);
		$this->assertNull($filters['movieCount.ne']);
	}

	public function test_sortById_filter(): void {
		$filter = new KeywordSearchFilter();
		$filter->sortById();

		$filters = $filter->getFilters();
		$this->assertArrayHasKey('sort', $filters);
		$this->assertStringContainsString('id', $filters['sort']);
		$this->assertStringNotContainsString('-', $filters['sort']); // ascending
	}

	public function test_sortById_filter_desc(): void {
		$filter = new KeywordSearchFilter();
		$filter->sortById('desc');

		$filters = $filter->getFilters();
		$this->assertArrayHasKey('sort', $filters);
		$this->assertStringContainsString('-id', $filters['sort']); // descending
	}

	public function test_sortByTitle_filter(): void {
		$filter = new KeywordSearchFilter();
		$filter->sortByTitle();

		$filters = $filter->getFilters();
		$this->assertArrayHasKey('sort', $filters);
		$this->assertStringContainsString('title', $filters['sort']);
		$this->assertStringNotContainsString('-', $filters['sort']); // ascending
	}

	public function test_sortByTitle_filter_desc(): void {
		$filter = new KeywordSearchFilter();
		$filter->sortByTitle('desc');

		$filters = $filter->getFilters();
		$this->assertArrayHasKey('sort', $filters);
		$this->assertStringContainsString('-title', $filters['sort']); // descending
	}

	public function test_sortByCreatedAt_filter(): void {
		$filter = new KeywordSearchFilter();
		$filter->sortByCreatedAt();

		$filters = $filter->getFilters();
		$this->assertArrayHasKey('sort', $filters);
		$this->assertStringContainsString('-createdAt', $filters['sort']); // default is desc
	}

	public function test_sortByCreatedAt_filter_asc(): void {
		$filter = new KeywordSearchFilter();
		$filter->sortByCreatedAt('asc');

		$filters = $filter->getFilters();
		$this->assertArrayHasKey('sort', $filters);
		$this->assertStringContainsString('createdAt', $filters['sort']);
		$this->assertStringNotContainsString('-', $filters['sort']); // ascending
	}

	public function test_sortByUpdatedAt_filter(): void {
		$filter = new KeywordSearchFilter();
		$filter->sortByUpdatedAt();

		$filters = $filter->getFilters();
		$this->assertArrayHasKey('sort', $filters);
		$this->assertStringContainsString('-updatedAt', $filters['sort']); // default is desc
	}

	public function test_sortByUpdatedAt_filter_asc(): void {
		$filter = new KeywordSearchFilter();
		$filter->sortByUpdatedAt('asc');

		$filters = $filter->getFilters();
		$this->assertArrayHasKey('sort', $filters);
		$this->assertStringContainsString('updatedAt', $filters['sort']);
		$this->assertStringNotContainsString('-', $filters['sort']); // ascending
	}

	public function test_sortByPopularity_filter(): void {
		$filter = new KeywordSearchFilter();
		$filter->sortByPopularity();

		$filters = $filter->getFilters();
		$this->assertArrayHasKey('sort.eq', $filters);
		$this->assertStringContainsString('movieCount:desc', $filters['sort.eq']); // default is desc
	}

	public function test_sortByPopularity_filter_asc(): void {
		$filter = new KeywordSearchFilter();
		$filter->sortByPopularity('asc');

		$filters = $filter->getFilters();
		$this->assertArrayHasKey('sort.eq', $filters);
		$this->assertStringContainsString('movieCount:asc', $filters['sort.eq']);
	}

	public function test_sortByPopularity_filter_with_default(): void {
		$filter = new KeywordSearchFilter();
		$filter->sortByPopularity('desc');

		$filters = $filter->getFilters();
		$this->assertArrayHasKey('sort.eq', $filters);
		$this->assertStringContainsString('movieCount:desc', $filters['sort.eq']);
	}

	public function test_chaining_filters(): void {
		$filter = new KeywordSearchFilter();
		$filter
			->title('test')
			->sortByTitle()
			->id(123);

		$filters = $filter->getFilters();
		$this->assertArrayHasKey('title.eq', $filters);
		$this->assertEquals('test', $filters['title.eq']);
		$this->assertArrayHasKey('id.eq', $filters);
		$this->assertEquals(123, $filters['id.eq']);
		$this->assertArrayHasKey('sort', $filters);
		$this->assertStringContainsString('title', $filters['sort']);
		$this->assertStringNotContainsString('-', $filters['sort']); // ascending
	}

	public function test_clear_filters(): void {
		$this->filter->id(123)->title('драма');

		$this->assertNotEmpty($this->filter->getFilters());

		$this->filter->reset();

		$this->assertEmpty($this->filter->getFilters());
	}

	public function test_inherited_methods_from_movie_filter(): void {
		// Тестируем методы, унаследованные от MovieFilter
		$this->filter->name('Ключевое слово');

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('name.eq', $filters);
		$this->assertEquals('Ключевое слово', $filters['name.eq']);
	}

	protected function setUp(): void {
		$this->filter = new KeywordSearchFilter();
	}

}