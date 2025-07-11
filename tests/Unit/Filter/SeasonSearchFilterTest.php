<?php

declare(strict_types=1);

namespace KinopoiskDev\Tests\Unit\Filter;

use KinopoiskDev\Filter\SeasonSearchFilter;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 * @group filter
 * @group season-search-filter
 */
class SeasonSearchFilterTest extends TestCase {

	private SeasonSearchFilter $filter;

	public function test_number_filter(): void {
		$this->filter->number(1, 'eq');

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('number.eq', $filters);
		$this->assertEquals(1, $filters['number.eq']);
	}

	public function test_number_filter_with_default_operator(): void {
		$this->filter->number(2);

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('number.eq', $filters);
		$this->assertEquals(2, $filters['number.eq']);
	}

	public function test_number_filter_with_different_operator(): void {
		$this->filter->number(5, 'gte');

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('number.gte', $filters);
		$this->assertEquals(5, $filters['number.gte']);
	}

	public function test_episodesCount_filter(): void {
		$this->filter->episodesCount(10, 'eq');

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('episodesCount.eq', $filters);
		$this->assertEquals(10, $filters['episodesCount.eq']);
	}

	public function test_episodesCount_filter_with_default_operator(): void {
		$this->filter->episodesCount(20);

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('episodesCount.eq', $filters);
		$this->assertEquals(20, $filters['episodesCount.eq']);
	}

	public function test_episodesCount_filter_with_different_operator(): void {
		$this->filter->episodesCount(15, 'lte');

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('episodesCount.lte', $filters);
		$this->assertEquals(15, $filters['episodesCount.lte']);
	}

	public function test_chaining_filters(): void {
		$this->filter
			->number(1, 'eq')
			->episodesCount(10, 'gte');

		$filters = $this->filter->getFilters();

		$this->assertArrayHasKey('number.eq', $filters);
		$this->assertEquals(1, $filters['number.eq']);

		$this->assertArrayHasKey('episodesCount.gte', $filters);
		$this->assertEquals(10, $filters['episodesCount.gte']);
	}

	public function test_clear_filters(): void {
		$this->filter->number(1)->episodesCount(10);

		$this->assertNotEmpty($this->filter->getFilters());

		$this->filter->reset();

		$this->assertEmpty($this->filter->getFilters());
	}

	public function test_inherited_methods_from_movie_filter(): void {
		// Тестируем методы, унаследованные от MovieFilter
		$this->filter->name('Первый сезон');

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('name.eq', $filters);
		$this->assertEquals('Первый сезон', $filters['name.eq']);
	}

	public function test_not_null_fields(): void {
		$this->filter->notNullFields(['number', 'episodesCount']);

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('number.ne', $filters);
		$this->assertArrayHasKey('episodesCount.ne', $filters);
	}

	protected function setUp(): void {
		$this->filter = new SeasonSearchFilter();
	}

}