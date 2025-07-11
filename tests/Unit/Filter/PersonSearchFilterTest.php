<?php

declare(strict_types=1);

namespace KinopoiskDev\Tests\Unit\Filter;

use KinopoiskDev\Filter\PersonSearchFilter;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 * @group filter
 * @group person-search-filter
 */
class PersonSearchFilterTest extends TestCase {

	private PersonSearchFilter $filter;

	public function test_age_filter(): void {
		$this->filter->age(30, 'gte');

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('age.gte', $filters);
		$this->assertEquals(30, $filters['age.gte']);
	}

	public function test_age_filter_with_default_operator(): void {
		$this->filter->age(25);

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('age.eq', $filters);
		$this->assertEquals(25, $filters['age.eq']);
	}

	public function test_sex_filter(): void {
		$this->filter->sex('male');

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('sex.eq', $filters);
		$this->assertEquals('male', $filters['sex.eq']);
	}

	public function test_birthPlace_filter(): void {
		$this->filter->birthPlace('Москва', 'regex');

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('birthPlace.value.regex', $filters);
		$this->assertEquals('Москва', $filters['birthPlace.value.regex']);
	}

	public function test_birthPlace_filter_with_default_operator(): void {
		$this->filter->birthPlace('Санкт-Петербург');

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('birthPlace.value.regex', $filters);
		$this->assertEquals('Санкт-Петербург', $filters['birthPlace.value.regex']);
	}

	public function test_death_filter(): void {
		$this->filter->death('2020-01-01', 'eq');

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('death.eq', $filters);
		$this->assertEquals('2020-01-01', $filters['death.eq']);
	}

	public function test_death_filter_with_default_operator(): void {
		$this->filter->death('2019-12-31');

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('death.eq', $filters);
		$this->assertEquals('2019-12-31', $filters['death.eq']);
	}

	public function test_birthday_filter(): void {
		$this->filter->birthday('1980-05-15', 'eq');

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('birthday.eq', $filters);
		$this->assertEquals('1980-05-15', $filters['birthday.eq']);
	}

	public function test_birthday_filter_with_default_operator(): void {
		$this->filter->birthday('1975-03-20');

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('birthday.eq', $filters);
		$this->assertEquals('1975-03-20', $filters['birthday.eq']);
	}

	public function test_countAwards_filter(): void {
		$this->filter->countAwards(5, 'gte');

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('countAwards.gte', $filters);
		$this->assertEquals(5, $filters['countAwards.gte']);
	}

	public function test_countAwards_filter_with_default_operator(): void {
		$this->filter->countAwards(10);

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('countAwards.gte', $filters);
		$this->assertEquals(10, $filters['countAwards.gte']);
	}

	public function test_profession_filter(): void {
		$this->filter->profession('актер', 'eq');

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('profession.eq', $filters);
		$this->assertEquals('актер', $filters['profession.eq']);
	}

	public function test_profession_filter_with_default_operator(): void {
		$this->filter->profession('режиссер');

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('profession.eq', $filters);
		$this->assertEquals('режиссер', $filters['profession.eq']);
	}

	public function test_onlyActors_filter(): void {
		$this->filter->onlyActors();

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('profession.eq', $filters);
		$this->assertEquals('актер', $filters['profession.eq']);
	}

	public function test_onlyDirectors_filter(): void {
		$this->filter->onlyDirectors();

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('profession.eq', $filters);
		$this->assertEquals('режиссер', $filters['profession.eq']);
	}

	public function test_onlyWriters_filter(): void {
		$this->filter->onlyWriters();

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('profession.eq', $filters);
		$this->assertEquals('сценарист', $filters['profession.eq']);
	}

	public function test_onlyAlive_filter(): void {
		$this->filter->onlyAlive();

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('death.eq', $filters);
		$this->assertNull($filters['death.eq']);
	}

	public function test_birthYear_filter_single_year(): void {
		$this->filter->birthYear(1980);

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('birthYear.eq', $filters);
		$this->assertEquals(1980, $filters['birthYear.eq']);
	}

	public function test_birthYear_filter_range(): void {
		$this->filter->birthYear(1970, 1980);

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('birthYear.eq', $filters);
		$this->assertEquals([1970, 1980], $filters['birthYear.eq']);
	}

	public function test_deathYear_filter(): void {
		$this->filter->deathYear(2020);

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('deathYear.eq', $filters);
		$this->assertEquals(2020, $filters['deathYear.eq']);
	}

	public function test_chaining_filters(): void {
		$this->filter
			->age(30, 'gte')
			->sex('male')
			->profession('актер')
			->birthYear(1980, 1990);

		$filters = $this->filter->getFilters();

		$this->assertArrayHasKey('age.gte', $filters);
		$this->assertEquals(30, $filters['age.gte']);

		$this->assertArrayHasKey('sex.eq', $filters);
		$this->assertEquals('male', $filters['sex.eq']);

		$this->assertArrayHasKey('profession.eq', $filters);
		$this->assertEquals('актер', $filters['profession.eq']);

		$this->assertArrayHasKey('birthYear.eq', $filters);
		$this->assertEquals([1980, 1990], $filters['birthYear.eq']);
	}

	public function test_reset_filters(): void {
		$this->filter->age(30)->sex('male');

		$this->assertNotEmpty($this->filter->getFilters());

		$this->filter->reset();

		$this->assertEmpty($this->filter->getFilters());
	}

	public function test_inherited_methods_from_movie_filter(): void {
		// Тестируем методы, унаследованные от MovieFilter
		$this->filter->name('Том Круз');

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('name.eq', $filters);
		$this->assertEquals('Том Круз', $filters['name.eq']);
	}

	public function test_not_null_fields(): void {
		$this->filter->notNullFields(['age', 'birthday']);

		$filters = $this->filter->getFilters();
		$this->assertArrayHasKey('age.ne', $filters);
		$this->assertArrayHasKey('birthday.ne', $filters);
	}

	protected function setUp(): void {
		$this->filter = new PersonSearchFilter();
	}

}