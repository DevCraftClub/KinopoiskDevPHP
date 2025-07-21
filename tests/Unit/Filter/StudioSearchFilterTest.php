<?php

declare(strict_types=1);

namespace KinopoiskDev\Tests\Unit\Filter;

use KinopoiskDev\Enums\StudioType;
use KinopoiskDev\Filter\StudioSearchFilter;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 * @group filter
 * @group studio-search-filter
 */
class StudioSearchFilterTest extends TestCase {

	private StudioSearchFilter $filter;

	public function test_movieId_filter_single_id(): void {
		$this->filter->movieId(123);
		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('movies.id', $filters);
		$this->assertEquals(123, $filters['movies.id']);
	}

	public function test_movieId_filter_multiple_ids(): void {
		$this->filter->movieId([123, 456, 789]);
		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('movies.id', $filters);
		$this->assertEquals([123, 456, 789], $filters['movies.id']);
	}

	public function test_studioType_filter_string(): void {
		$this->filter->studioType('Производство');
		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('type', $filters);
		$this->assertEquals('Производство', $filters['type']);
	}

	public function test_studioType_filter_enum(): void {
		$this->filter->studioType(StudioType::PRODUCTION);
		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('type', $filters);
		$this->assertEquals('Производство', $filters['type']);
	}

	public function test_studioType_filter_array_strings(): void {
		$this->filter->studioType(['Производство', 'Спецэффекты']);
		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('type', $filters);
		$this->assertEquals(['Производство', 'Спецэффекты'], $filters['type']);
	}

	public function test_studioType_filter_array_enums(): void {
		$this->filter->studioType([StudioType::PRODUCTION, StudioType::SPECIAL_EFFECTS]);
		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('type', $filters);
		$this->assertEquals(['Производство', 'Спецэффекты'], $filters['type']);
	}

	public function test_subType_filter_single(): void {
		$this->filter->subType('кинокомпания');
		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('subType', $filters);
		$this->assertEquals('кинокомпания', $filters['subType']);
	}

	public function test_subType_filter_multiple(): void {
		$this->filter->subType(['кинокомпания', 'студия']);
		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('subType', $filters);
		$this->assertEquals(['кинокомпания', 'студия'], $filters['subType']);
	}

	public function test_title_filter_single(): void {
		$this->filter->title('Warner Bros.');
		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('title', $filters);
		$this->assertEquals('Warner Bros.', $filters['title']);
	}

	public function test_title_filter_multiple(): void {
		$this->filter->title(['Warner Bros.', 'Disney']);
		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('title', $filters);
		$this->assertEquals(['Warner Bros.', 'Disney'], $filters['title']);
	}

	public function test_productionStudios_filter(): void {
		$this->filter->productionStudios();
		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('type', $filters);
		$this->assertEquals('Производство', $filters['type']);
	}

	public function test_specialEffectsStudios_filter(): void {
		$this->filter->specialEffectsStudios();
		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('type', $filters);
		$this->assertEquals('Спецэффекты', $filters['type']);
	}

	public function test_distributionCompanies_filter(): void {
		$this->filter->distributionCompanies();
		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('type', $filters);
		$this->assertEquals('Прокат', $filters['type']);
	}

	public function test_dubbingStudios_filter(): void {
		$this->filter->dubbingStudios();
		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('type', $filters);
		$this->assertEquals('Студия дубляжа', $filters['type']);
	}

	public function test_excludeTypes_filter_single_string(): void {
		$filter = new StudioSearchFilter();
		$filter->excludeTypes('production');
		$filters = $filter->getFilters();
		$this->assertArrayHasKey('type', $filters);
		$this->assertIsArray($filters['type']);
		$this->assertContains('!production', $filters['type']);
	}

	public function test_excludeTypes_filter_single_enum(): void {
		$filter = new StudioSearchFilter();
		$filter->excludeTypes(StudioType::PRODUCTION);
		$filters = $filter->getFilters();
		$this->assertArrayHasKey('type', $filters);
		$this->assertIsArray($filters['type']);
		$this->assertContains('!Производство', $filters['type']);
	}

	public function test_excludeTypes_filter_multiple(): void {
		$this->filter->excludeTypes(['Производство', 'Спецэффекты']);
		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('type', $filters);
		$this->assertEquals(['!Производство', '!Спецэффекты'], $filters['type']);
	}

	public function test_participatedInAllMovies_filter(): void {
		$this->filter->participatedInAllMovies([123, 456, 789]);
		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('movies.id', $filters);
		$this->assertEquals('+789', $filters['movies.id']);
		$this->assertStringStartsWith('+', $filters['movies.id']);
	}

	public function test_sortByTitle_filter(): void {
		$filter = new StudioSearchFilter();
		$filter->sortByTitle();
		$filters = $filter->getFilters();
		$this->assertArrayHasKey('sortField', $filters);
		$this->assertArrayHasKey('sortType', $filters);
		$this->assertEquals(['title'], $filters['sortField']);
		$this->assertEquals(['1'], $filters['sortType']); // ascending
	}

	public function test_sortByTitle_filter_desc(): void {
		$filter = new StudioSearchFilter();
		$filter->sortByTitle('desc');
		$filters = $filter->getFilters();
		$this->assertArrayHasKey('sortField', $filters);
		$this->assertArrayHasKey('sortType', $filters);
		$this->assertEquals(['title'], $filters['sortField']);
		$this->assertEquals(['-1'], $filters['sortType']); // descending
	}

	public function test_sortByType_filter(): void {
		$filter = new StudioSearchFilter();
		$filter->sortByType();
		$filters = $filter->getFilters();
		$this->assertArrayHasKey('sortField', $filters);
		$this->assertArrayHasKey('sortType', $filters);
		$this->assertEquals(['type'], $filters['sortField']);
		$this->assertEquals(['1'], $filters['sortType']); // ascending
	}

	public function test_sortByType_filter_desc(): void {
		$filter = new StudioSearchFilter();
		$filter->sortByType('desc');
		$filters = $filter->getFilters();
		$this->assertArrayHasKey('sortField', $filters);
		$this->assertArrayHasKey('sortType', $filters);
		$this->assertEquals(['type'], $filters['sortField']);
		$this->assertEquals(['-1'], $filters['sortType']); // descending
	}

	public function test_country_filter(): void {
		$this->filter->country('США');
		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('country', $filters);
		$this->assertEquals('США', $filters['country']);
	}

	public function test_chaining_filters(): void {
		$filter = new StudioSearchFilter();
		$filter
			->title('test')
			->sortByTitle()
			->studioType(StudioType::PRODUCTION);
		$filters = $filter->getFilters();
		$this->assertArrayHasKey('title', $filters);
		$this->assertEquals('test', $filters['title']);
		$this->assertArrayHasKey('type', $filters);
		$this->assertEquals('Производство', $filters['type']);
		$this->assertArrayHasKey('sortField', $filters);
		$this->assertArrayHasKey('sortType', $filters);
		$this->assertEquals(['title'], $filters['sortField']);
		$this->assertEquals(['1'], $filters['sortType']); // ascending
	}

	public function test_clear_filters(): void {
		$this->filter->movieId(123)->studioType(StudioType::PRODUCTION);
		$this->assertNotEmpty($this->filter->getFilters());
		$this->filter->reset();
		$this->assertEmpty($this->filter->getFilters());
	}

	public function test_inherited_methods_from_movie_filter(): void {
		$this->filter->name('Warner Bros.');
		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('name', $filters);
		$this->assertEquals('Warner Bros.', $filters['name']);
	}

	public function test_not_null_fields(): void {
		$this->filter->notNullFields(['title', 'type']);
		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('title', $filters);
		$this->assertArrayHasKey('type', $filters);
	}

	protected function setUp(): void {
		$this->filter = new StudioSearchFilter();
	}

}