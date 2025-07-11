<?php

declare(strict_types=1);

namespace Tests\Unit\Filter;

use PHPUnit\Framework\TestCase;
use KinopoiskDev\Filter\MovieSearchFilter;
use KinopoiskDev\Filter\SortCriteria;
use KinopoiskDev\Enums\SortField;
use KinopoiskDev\Enums\SortDirection;

/**
 * @group unit
 * @group filter
 * @group movie-search-filter
 */
class MovieSearchFilterTest extends TestCase
{
    private MovieSearchFilter $filter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->filter = new MovieSearchFilter();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function test_constructor_createsEmptyFilter(): void
    {
        $this->assertInstanceOf(MovieSearchFilter::class, $this->filter);
        $this->assertEquals([], $this->filter->getFilters());
    }

    public function test_searchByAlternativeName_addsFilter(): void
    {
        $this->filter->searchByAlternativeName('test movie');

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('alternativeName', $filters);
        $this->assertEquals('test movie', $filters['alternativeName']);
    }

    public function test_searchByAllNames_addsFilter(): void
    {
        $this->filter->searchByAllNames('test movie');

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('names.name', $filters);
        $this->assertEquals('test movie', $filters['names.name']);
    }

    public function test_withMinVotes_addsFilter(): void
    {
        $this->filter->withMinVotes(1000, 'kp');

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('votes.kp', $filters);
        $this->assertEquals('gte:1000', $filters['votes.kp']);
    }

    public function test_withMinVotes_withDefaultField_addsKpFilter(): void
    {
        $this->filter->withMinVotes(500);

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('votes.kp', $filters);
        $this->assertEquals('gte:500', $filters['votes.kp']);
    }

    public function test_withVotesBetween_addsRangeFilter(): void
    {
        $this->filter->withVotesBetween(1000, 5000, 'imdb');

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('votes.imdb', $filters);
        $this->assertEquals('gte:1000,lte:5000', $filters['votes.imdb']);
    }

    public function test_withYearBetween_addsYearRangeFilter(): void
    {
        $this->filter->withYearBetween(2020, 2023);

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('year', $filters);
        $this->assertEquals('gte:2020,lte:2023', $filters['year']);
    }

    public function test_withAllGenres_addsAllFilter(): void
    {
        $this->filter->withAllGenres(['Action', 'Drama']);

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('genres.name', $filters);
        $this->assertEquals('all:Action,Drama', $filters['genres.name']);
    }

    public function test_withIncludedGenres_addsIncludeFilter(): void
    {
        $this->filter->withIncludedGenres(['Action', 'Drama']);

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('genres.name', $filters);
        $this->assertEquals('+Action,+Drama', $filters['genres.name']);
    }

    public function test_withIncludedGenres_singleGenre_addsIncludeFilter(): void
    {
        $this->filter->withIncludedGenres('Action');

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('genres.name', $filters);
        $this->assertEquals('+Action', $filters['genres.name']);
    }

    public function test_withExcludedGenres_addsExcludeFilter(): void
    {
        $this->filter->withExcludedGenres(['Horror', 'Thriller']);

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('genres.name', $filters);
        $this->assertEquals('!Horror,!Thriller', $filters['genres.name']);
    }

    public function test_withExcludedGenres_singleGenre_addsExcludeFilter(): void
    {
        $this->filter->withExcludedGenres('Horror');

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('genres.name', $filters);
        $this->assertEquals('!Horror', $filters['genres.name']);
    }

    public function test_withAllCountries_addsAllFilter(): void
    {
        $this->filter->withAllCountries(['USA', 'UK']);

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('countries.name', $filters);
        $this->assertEquals('all:USA,UK', $filters['countries.name']);
    }

    public function test_withIncludedCountries_addsIncludeFilter(): void
    {
        $this->filter->withIncludedCountries(['USA', 'UK']);

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('countries.name', $filters);
        $this->assertEquals('+USA,+UK', $filters['countries.name']);
    }

    public function test_withExcludedCountries_addsExcludeFilter(): void
    {
        $this->filter->withExcludedCountries(['Russia', 'China']);

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('countries.name', $filters);
        $this->assertEquals('!Russia,!China', $filters['countries.name']);
    }

    public function test_withActor_withString_addsActorFilter(): void
    {
        $this->filter->withActor('Tom Hanks');

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('persons.name', $filters);
        $this->assertEquals('regex:Tom Hanks', $filters['persons.name']);
        $this->assertArrayHasKey('persons.profession', $filters);
        $this->assertEquals('актер', $filters['persons.profession']);
    }

    public function test_withActor_withId_addsActorFilter(): void
    {
        $this->filter->withActor(123);

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('persons.id', $filters);
        $this->assertEquals(123, $filters['persons.id']);
        $this->assertArrayHasKey('persons.profession', $filters);
        $this->assertEquals('актер', $filters['persons.profession']);
    }

    public function test_withDirector_withString_addsDirectorFilter(): void
    {
        $this->filter->withDirector('Christopher Nolan');

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('persons.name', $filters);
        $this->assertEquals('regex:Christopher Nolan', $filters['persons.name']);
        $this->assertArrayHasKey('persons.profession', $filters);
        $this->assertEquals('режиссер', $filters['persons.profession']);
    }

    public function test_withDirector_withId_addsDirectorFilter(): void
    {
        $this->filter->withDirector(456);

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('persons.id', $filters);
        $this->assertEquals(456, $filters['persons.id']);
        $this->assertArrayHasKey('persons.profession', $filters);
        $this->assertEquals('режиссер', $filters['persons.profession']);
    }

    public function test_onlyMovies_addsMovieFilter(): void
    {
        $this->filter->onlyMovies();

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('type', $filters);
        $this->assertEquals('movie', $filters['type']);
    }

    public function test_onlySeries_addsSeriesFilter(): void
    {
        $this->filter->onlySeries();

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('type', $filters);
        $this->assertEquals('tv-series', $filters['type']);
    }

    public function test_inTop250_addsTop250Filter(): void
    {
        $this->filter->inTop250();

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('top250', $filters);
        $this->assertEquals('gte:1', $filters['top250']);
    }

    public function test_inTop10_addsTop10Filter(): void
    {
        $this->filter->inTop10();

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('top10', $filters);
        $this->assertEquals('gte:1', $filters['top10']);
    }

    public function test_withPremiereRange_addsPremiereFilter(): void
    {
        $this->filter->withPremiereRange('2023-01-01', '2023-12-31', 'world');

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('premiere.world', $filters);
        $this->assertEquals('gte:2023-01-01,lte:2023-12-31', $filters['premiere.world']);
    }

    public function test_withPremiereRange_withDefaultCountry_addsWorldFilter(): void
    {
        $this->filter->withPremiereRange('2023-01-01', '2023-12-31');

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('premiere.world', $filters);
        $this->assertEquals('gte:2023-01-01,lte:2023-12-31', $filters['premiere.world']);
    }

    public function test_year_addsYearFilter(): void
    {
        $this->filter->year(2023);

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('year', $filters);
        $this->assertEquals(2023, $filters['year']);
    }

    public function test_rating_addsRatingFilter(): void
    {
        $this->filter->rating(8.5);

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('rating.kp', $filters);
        $this->assertEquals('gte:8.5', $filters['rating.kp']);
    }

    public function test_ratingRange_addsRatingRangeFilter(): void
    {
        $this->filter->ratingRange(7.0, 9.0);

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('rating.kp', $filters);
        $this->assertEquals('gte:7.0,lte:9.0', $filters['rating.kp']);
    }

    public function test_movieLength_addsLengthFilter(): void
    {
        $this->filter->movieLength(120);

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('movieLength', $filters);
        $this->assertEquals('gte:120', $filters['movieLength']);
    }

    public function test_movieLengthRange_addsLengthRangeFilter(): void
    {
        $this->filter->movieLengthRange(90, 150);

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('movieLength', $filters);
        $this->assertEquals('gte:90,lte:150', $filters['movieLength']);
    }

    public function test_status_addsStatusFilter(): void
    {
        $this->filter->status('completed');

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('status', $filters);
        $this->assertEquals('completed', $filters['status']);
    }

    public function test_name_addsNameFilter(): void
    {
        $this->filter->name('Test Movie');

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('name', $filters);
        $this->assertEquals('regex:Test Movie', $filters['name']);
    }

    public function test_enName_addsEnNameFilter(): void
    {
        $this->filter->enName('Test Movie');

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('enName', $filters);
        $this->assertEquals('regex:Test Movie', $filters['enName']);
    }

    public function test_description_addsDescriptionFilter(): void
    {
        $this->filter->description('action movie');

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('description', $filters);
        $this->assertEquals('regex:action movie', $filters['description']);
    }

    public function test_slogan_addsSloganFilter(): void
    {
        $this->filter->slogan('Just do it');

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('slogan', $filters);
        $this->assertEquals('regex:Just do it', $filters['slogan']);
    }

    public function test_ageRating_addsAgeRatingFilter(): void
    {
        $this->filter->ageRating(18);

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('ageRating', $filters);
        $this->assertEquals('gte:18', $filters['ageRating']);
    }

    public function test_ageRatingRange_addsAgeRatingRangeFilter(): void
    {
        $this->filter->ageRatingRange(12, 18);

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('ageRating', $filters);
        $this->assertEquals('gte:12,lte:18', $filters['ageRating']);
    }

    public function test_budget_addsBudgetFilter(): void
    {
        $this->filter->budget(1000000);

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('budget.value', $filters);
        $this->assertEquals('gte:1000000', $filters['budget.value']);
    }

    public function test_budgetRange_addsBudgetRangeFilter(): void
    {
        $this->filter->budgetRange(1000000, 10000000);

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('budget.value', $filters);
        $this->assertEquals('gte:1000000,lte:10000000', $filters['budget.value']);
    }

    public function test_fees_addsFeesFilter(): void
    {
        $this->filter->fees(5000000);

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('fees.world.value', $filters);
        $this->assertEquals('gte:5000000', $filters['fees.world.value']);
    }

    public function test_feesRange_addsFeesRangeFilter(): void
    {
        $this->filter->feesRange(1000000, 50000000);

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('fees.world.value', $filters);
        $this->assertEquals('gte:1000000,lte:50000000', $filters['fees.world.value']);
    }

    public function test_sortBy_addsSortCriteria(): void
    {
        $this->filter->sortBy(SortField::RATING, SortDirection::DESC);

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('sortField', $filters);
        $this->assertEquals('rating.kp', $filters['sortField']);
        $this->assertArrayHasKey('sortType', $filters);
        $this->assertEquals(-1, $filters['sortType']);
    }

    public function test_sortBy_withDefaultDirection_addsAscSort(): void
    {
        $this->filter->sortBy(SortField::YEAR);

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('sortField', $filters);
        $this->assertEquals('year', $filters['sortField']);
        $this->assertArrayHasKey('sortType', $filters);
        $this->assertEquals(1, $filters['sortType']);
    }

    public function test_limit_addsLimitFilter(): void
    {
        $this->filter->limit(20);

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('limit', $filters);
        $this->assertEquals(20, $filters['limit']);
    }

    public function test_page_addsPageFilter(): void
    {
        $this->filter->page(2);

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('page', $filters);
        $this->assertEquals(2, $filters['page']);
    }

    public function test_clear_removesAllFilters(): void
    {
        $this->filter->year(2023)->rating(8.0)->genre('Action');
        
        $this->assertNotEmpty($this->filter->getFilters());
        
        $this->filter->clear();
        
        $this->assertEquals([], $this->filter->getFilters());
    }

    public function test_removeFilter_removesSpecificFilter(): void
    {
        $this->filter->year(2023)->rating(8.0);
        
        $this->assertArrayHasKey('year', $this->filter->getFilters());
        $this->assertArrayHasKey('rating.kp', $this->filter->getFilters());
        
        $this->filter->removeFilter('year');
        
        $filters = $this->filter->getFilters();
        $this->assertArrayNotHasKey('year', $filters);
        $this->assertArrayHasKey('rating.kp', $filters);
    }

    public function test_fluentInterface_returnsSelf(): void
    {
        $result = $this->filter
            ->year(2023)
            ->rating(8.0)
            ->genre('Action')
            ->country('USA');

        $this->assertSame($this->filter, $result);
    }

    public function test_complexFilterCombination_worksCorrectly(): void
    {
        $this->filter
            ->year(2023)
            ->rating(8.0)
            ->withIncludedGenres(['Action', 'Drama'])
            ->withExcludedGenres(['Horror'])
            ->withActor('Tom Hanks')
            ->onlyMovies()
            ->inTop250()
            ->sortBy(SortField::RATING, SortDirection::DESC)
            ->limit(20)
            ->page(1);

        $filters = $this->filter->getFilters();
        
        $this->assertEquals(2023, $filters['year']);
        $this->assertEquals('gte:8.0', $filters['rating.kp']);
        $this->assertEquals('+Action,+Drama', $filters['genres.name']);
        $this->assertEquals('!Horror', $filters['genres.name']);
        $this->assertEquals('regex:Tom Hanks', $filters['persons.name']);
        $this->assertEquals('актер', $filters['persons.profession']);
        $this->assertEquals('movie', $filters['type']);
        $this->assertEquals('gte:1', $filters['top250']);
        $this->assertEquals('rating.kp', $filters['sortField']);
        $this->assertEquals(-1, $filters['sortType']);
        $this->assertEquals(20, $filters['limit']);
        $this->assertEquals(1, $filters['page']);
    }
} 