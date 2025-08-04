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
		$this->assertArrayHasKey('id', $filters);
		$this->assertEquals(123, $filters['id']);
	}

	public function test_id_filter_single_with_default_operator(): void {
		$this->filter->id(456);

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('id', $filters);
		$this->assertEquals(456, $filters['id']);
	}

	public function test_id_filter_multiple(): void {
		$this->filter->id([123, 456, 789], 'eq');

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('id', $filters);
		$this->assertEquals([123, 456, 789], $filters['id']);
	}

	public function test_title_filter(): void {
		$this->filter->title('драма', 'eq');

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('title', $filters);
		$this->assertEquals('драма', $filters['title']);
	}

	public function test_title_filter_with_default_operator(): void {
		$this->filter->title('комедия');

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('title', $filters);
		$this->assertEquals('комедия', $filters['title']);
	}

	public function test_title_filter_with_regex(): void {
		$this->filter->title('боевик', 'regex');

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('title', $filters);
		$this->assertEquals('боевик', $filters['title']);
	}

	public function test_movieId_filter_single(): void {
		$this->filter->movieId(123);
		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('movieId', $filters);
		$this->assertEquals(123, $filters['movieId']);
	}

	public function test_movieId_filter_multiple(): void {
		$this->filter->movieId([123, 456, 789]);
		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('movieId', $filters);
		$this->assertEquals([123, 456, 789], $filters['movieId']);
	}

	public function test_createdAt_filter(): void {
		$this->filter->createdAt('2023-01-01T00:00:00.000Z');
		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('createdAt', $filters);
		$this->assertEquals('2023-01-01T00:00:00.000Z', $filters['createdAt']);
	}

	public function test_createdAt_filter_with_default_operator(): void {
		$this->filter->createdAt('2023-01-01T00:00:00.000Z');
		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('createdAt', $filters);
		$this->assertEquals('2023-01-01T00:00:00.000Z', $filters['createdAt']);
	}

	public function test_updatedAt_filter(): void {
		$this->filter->updatedAt('2023-01-01T00:00:00.000Z');
		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('updatedAt', $filters);
		$this->assertEquals('2023-01-01T00:00:00.000Z', $filters['updatedAt']);
	}

	public function test_updatedAt_filter_with_default_operator(): void {
		$this->filter->updatedAt('2023-01-01T00:00:00.000Z');
		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('updatedAt', $filters);
		$this->assertEquals('2023-01-01T00:00:00.000Z', $filters['updatedAt']);
	}

	public function test_search_filter(): void {
		$this->filter->search('драма');
		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('title', $filters);
		$this->assertEquals('драма', $filters['title']);
	}

	public function test_onlyPopular_filter(): void {
		$this->filter->onlyPopular(20);
		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('movieCount', $filters);
		$this->assertEquals(20, $filters['movieCount']);
	}

	public function test_onlyPopular_filter_with_default(): void {
		$this->filter->onlyPopular();
		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('movieCount', $filters);
		$this->assertEquals(10, $filters['movieCount']);
	}

	public function test_recentlyCreated_filter(): void {
		$this->filter->recentlyCreated(7);
		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('createdAt', $filters);
		$this->assertStringContainsString('T', $filters['createdAt']);
		$this->assertStringContainsString('Z', $filters['createdAt']);
	}

	public function test_recentlyCreated_filter_with_default(): void {
		$this->filter->recentlyCreated();
		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('createdAt', $filters);
		$this->assertStringContainsString('T', $filters['createdAt']);
		$this->assertStringContainsString('Z', $filters['createdAt']);
	}

	public function test_recentlyUpdated_filter(): void {
		$this->filter->recentlyUpdated(3);
		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('updatedAt', $filters);
		$this->assertStringContainsString('T', $filters['updatedAt']);
		$this->assertStringContainsString('Z', $filters['updatedAt']);
	}

	public function test_recentlyUpdated_filter_with_default(): void {
		$this->filter->recentlyUpdated();
		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('updatedAt', $filters);
		$this->assertStringContainsString('T', $filters['updatedAt']);
		$this->assertStringContainsString('Z', $filters['updatedAt']);
	}

	public function test_createdBetween_filter(): void {
		$this->filter->createdBetween('2023-01-01T00:00:00.000Z', '2023-12-31T23:59:59.999Z');
		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('createdAt', $filters);
		$this->assertEquals('2023-12-31T23:59:59.999Z', $filters['createdAt']);
	}

	public function test_updatedBetween_filter(): void {
		$this->filter->updatedBetween('2023-01-01T00:00:00.000Z', '2023-12-31T23:59:59.999Z');
		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('updatedAt', $filters);
		$this->assertEquals('2023-12-31T23:59:59.999Z', $filters['updatedAt']);
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
		$this->assertArrayHasKey('title', $filters);
		$this->assertArrayHasKey('movieCount', $filters);
	}

	public function test_sortById_filter(): void {
		$filter = new KeywordSearchFilter();
		$filter->sortById();

		$filters = $filter->getFilters();
		$this->assertArrayHasKey('sortField', $filters);
		$this->assertArrayHasKey('sortType', $filters);
		$this->assertEquals(['id'], $filters['sortField']);
		$this->assertEquals(['1'], $filters['sortType']); // ascending
	}

	public function test_sortById_filter_desc(): void {
		$filter = new KeywordSearchFilter();
		$filter->sortById('desc');

		$filters = $filter->getFilters();
		$this->assertArrayHasKey('sortField', $filters);
		$this->assertArrayHasKey('sortType', $filters);
		$this->assertEquals(['id'], $filters['sortField']);
		$this->assertEquals(['-1'], $filters['sortType']); // descending
	}

	public function test_sortByTitle_filter(): void {
		$filter = new KeywordSearchFilter();
		$filter->sortByTitle();

		$filters = $filter->getFilters();
		$this->assertArrayHasKey('sortField', $filters);
		$this->assertArrayHasKey('sortType', $filters);
		$this->assertEquals(['title'], $filters['sortField']);
		$this->assertEquals(['1'], $filters['sortType']); // ascending
	}

	public function test_sortByTitle_filter_desc(): void {
		$filter = new KeywordSearchFilter();
		$filter->sortByTitle('desc');

		$filters = $filter->getFilters();
		$this->assertArrayHasKey('sortField', $filters);
		$this->assertArrayHasKey('sortType', $filters);
		$this->assertEquals(['title'], $filters['sortField']);
		$this->assertEquals(['-1'], $filters['sortType']); // descending
	}

	public function test_sortByCreatedAt_filter(): void {
		$filter = new KeywordSearchFilter();
		$filter->sortByCreatedAt();

		$filters = $filter->getFilters();
		$this->assertArrayHasKey('sortField', $filters);
		$this->assertArrayHasKey('sortType', $filters);
		$this->assertEquals(['createdAt'], $filters['sortField']);
		$this->assertEquals(['-1'], $filters['sortType']); // default is desc
	}

	public function test_sortByCreatedAt_filter_asc(): void {
		$filter = new KeywordSearchFilter();
		$filter->sortByCreatedAt('asc');

		$filters = $filter->getFilters();
		$this->assertArrayHasKey('sortField', $filters);
		$this->assertArrayHasKey('sortType', $filters);
		$this->assertEquals(['createdAt'], $filters['sortField']);
		$this->assertEquals(['1'], $filters['sortType']); // ascending
	}

	public function test_sortByUpdatedAt_filter(): void {
		$filter = new KeywordSearchFilter();
		$filter->sortByUpdatedAt();

		$filters = $filter->getFilters();
		$this->assertArrayHasKey('sortField', $filters);
		$this->assertArrayHasKey('sortType', $filters);
		$this->assertEquals(['updatedAt'], $filters['sortField']);
		$this->assertEquals(['-1'], $filters['sortType']); // default is desc
	}

	public function test_sortByUpdatedAt_filter_asc(): void {
		$filter = new KeywordSearchFilter();
		$filter->sortByUpdatedAt('asc');

		$filters = $filter->getFilters();
		$this->assertArrayHasKey('sortField', $filters);
		$this->assertArrayHasKey('sortType', $filters);
		$this->assertEquals(['updatedAt'], $filters['sortField']);
		$this->assertEquals(['1'], $filters['sortType']); // ascending
	}

	public function test_sortByPopularity_filter(): void {
		$filter = new KeywordSearchFilter();
		$filter->sortByPopularity();
		$filters = $filter->getFilters();
		$this->assertArrayHasKey('sort', $filters);
		$this->assertStringContainsString('movieCount:desc', $filters['sort']);
	}

	public function test_sortByPopularity_filter_asc(): void {
		$filter = new KeywordSearchFilter();
		$filter->sortByPopularity('asc');
		$filters = $filter->getFilters();
		$this->assertArrayHasKey('sort', $filters);
		$this->assertStringContainsString('movieCount:asc', $filters['sort']);
	}

	public function test_sortByPopularity_filter_with_default(): void {
		$filter = new KeywordSearchFilter();
		$filter->sortByPopularity('desc');
		$filters = $filter->getFilters();
		$this->assertArrayHasKey('sort', $filters);
		$this->assertStringContainsString('movieCount:desc', $filters['sort']);
	}

	public function test_chaining_filters(): void {
		$filter = new KeywordSearchFilter();
		$filter
			->title('test')
			->sortByTitle()
			->id(123);
		$filters = $filter->getFilters();
		$this->assertArrayHasKey('title', $filters);
		$this->assertEquals('test', $filters['title']);
		$this->assertArrayHasKey('id', $filters);
		$this->assertEquals(123, $filters['id']);
		$this->assertArrayHasKey('sortField', $filters);
		$this->assertArrayHasKey('sortType', $filters);
		$this->assertEquals(['title'], $filters['sortField']);
		$this->assertEquals(['1'], $filters['sortType']); // ascending
	}

	public function test_clear_filters(): void {
		$this->filter->id(123)->title('драма');

		$this->assertNotEmpty($this->filter->getFilters());

		$this->filter->reset();

		$this->assertEmpty($this->filter->getFilters());
	}

	public function test_inherited_methods_from_movie_filter(): void {
		$this->filter->name('Ключевое слово');
		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('name', $filters);
		$this->assertEquals('Ключевое слово', $filters['name']);
	}

	protected function setUp(): void {
		$this->filter = new KeywordSearchFilter();
	}

}