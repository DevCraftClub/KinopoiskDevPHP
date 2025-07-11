<?php

declare(strict_types=1);

namespace KinopoiskDev\Tests\Unit\Filter;

use KinopoiskDev\Filter\ImageSearchFilter;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 * @group filter
 * @group image-search-filter
 */
class ImageSearchFilterTest extends TestCase {

	private ImageSearchFilter $filter;

	public function test_language_filter(): void {
		$this->filter->language('ru');

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('language.eq', $filters);
		$this->assertEquals('ru', $filters['language.eq']);
	}

	public function test_onlyPosters_filter(): void {
		$this->filter->onlyPosters();

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('type.eq', $filters);
		$this->assertEquals('poster', $filters['type.eq']);
	}

	public function test_onlyStills_filter(): void {
		$this->filter->onlyStills();

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('type.eq', $filters);
		$this->assertEquals('still', $filters['type.eq']);
	}

	public function test_onlyShooting_filter(): void {
		$this->filter->onlyShooting();

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('type.eq', $filters);
		$this->assertEquals('shooting', $filters['type.eq']);
	}

	public function test_onlyScreenshots_filter(): void {
		$this->filter->onlyScreenshots();

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('type.eq', $filters);
		$this->assertEquals('screenshot', $filters['type.eq']);
	}

	public function test_onlyHighRes_filter(): void {
		$this->filter->onlyHighRes();

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('width.gte', $filters);
		$this->assertEquals(1920, $filters['width.gte']);

		$this->assertArrayHasKey('height.gte', $filters);
		$this->assertEquals(1080, $filters['height.gte']);
	}

	public function test_minResolution_filter(): void {
		$this->filter->minResolution(1280, 720);

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('width.gte', $filters);
		$this->assertEquals(1280, $filters['width.gte']);

		$this->assertArrayHasKey('height.gte', $filters);
		$this->assertEquals(720, $filters['height.gte']);
	}

	public function test_width_filter(): void {
		$this->filter->width(1920, 'eq');

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('width.eq', $filters);
		$this->assertEquals(1920, $filters['width.eq']);
	}

	public function test_width_filter_with_default_operator(): void {
		$this->filter->width(1280);

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('width.eq', $filters);
		$this->assertEquals(1280, $filters['width.eq']);
	}

	public function test_width_filter_with_different_operator(): void {
		$this->filter->width(800, 'gte');

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('width.gte', $filters);
		$this->assertEquals(800, $filters['width.gte']);
	}

	public function test_height_filter(): void {
		$this->filter->height(1080, 'eq');

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('height.eq', $filters);
		$this->assertEquals(1080, $filters['height.eq']);
	}

	public function test_height_filter_with_default_operator(): void {
		$this->filter->height(720);

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('height.eq', $filters);
		$this->assertEquals(720, $filters['height.eq']);
	}

	public function test_height_filter_with_different_operator(): void {
		$this->filter->height(600, 'lte');

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('height.lte', $filters);
		$this->assertEquals(600, $filters['height.lte']);
	}

	public function test_chaining_filters(): void {
		$this->filter
			->language('en')
			->onlyPosters()
			->width(1920, 'gte')
			->height(1080, 'gte');

		$filters = $this->filter->getFilters();

		$this->assertArrayHasKey('language.eq', $filters);
		$this->assertEquals('en', $filters['language.eq']);

		$this->assertArrayHasKey('type.eq', $filters);
		$this->assertEquals('poster', $filters['type.eq']);

		$this->assertArrayHasKey('width.gte', $filters);
		$this->assertEquals(1920, $filters['width.gte']);

		$this->assertArrayHasKey('height.gte', $filters);
		$this->assertEquals(1080, $filters['height.gte']);
	}

	public function test_clear_filters(): void {
		$this->filter->language('ru')->onlyPosters();

		$this->assertNotEmpty($this->filter->getFilters());

		$this->filter->reset();

		$this->assertEmpty($this->filter->getFilters());
	}

	public function test_inherited_methods_from_movie_filter(): void {
		// Тестируем методы, унаследованные от MovieFilter
		$this->filter->name('Постер');

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('name.eq', $filters);
		$this->assertEquals('Постер', $filters['name.eq']);
	}

	public function test_not_null_fields(): void {
		$this->filter->notNullFields(['width', 'height', 'language']);

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('width.ne', $filters);
		$this->assertArrayHasKey('height.ne', $filters);
		$this->assertArrayHasKey('language.ne', $filters);
	}

	protected function setUp(): void {
		$this->filter = new ImageSearchFilter();
	}

}