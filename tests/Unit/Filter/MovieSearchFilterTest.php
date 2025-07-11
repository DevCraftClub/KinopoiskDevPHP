<?php

declare(strict_types=1);

namespace KinopoiskDev\Tests\Unit\Filter;

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
        $this->assertArrayHasKey('alternativeName.regex', $filters);
        $this->assertEquals('test movie', $filters['alternativeName.regex']);
    }

    public function test_searchByAllNames_addsFilter(): void
    {
        $this->filter->searchByAllNames('test movie');

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('names.name.regex', $filters);
        $this->assertEquals('test movie', $filters['names.name.regex']);
    }

    public function test_withMinVotes_addsFilter(): void
    {
        $this->filter->withMinVotes(1000, 'kp');

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('votes.kp.gte', $filters);
        $this->assertEquals(1000, $filters['votes.kp.gte']);
    }

    public function test_withMinVotes_withDefaultField_addsKpFilter(): void
    {
        $this->filter->withMinVotes(500);

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('votes.kp.gte', $filters);
        $this->assertEquals(500, $filters['votes.kp.gte']);
    }

    public function test_withVotesBetween_addsRangeFilter(): void
    {
        $this->filter->withVotesBetween(1000, 5000, 'imdb');

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('votes.imdb', $filters);
        $this->assertEquals('1000-5000', $filters['votes.imdb']);
    }

    public function test_withYearBetween_addsYearRangeFilter(): void
    {
        $this->filter->withYearBetween(2020, 2023);

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('year', $filters);
        $this->assertEquals('2020-2023', $filters['year']);
    }

    public function test_withAllGenres_addsAllFilter(): void
    {
        $this->filter->withAllGenres(['Action', 'Drama']);

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('genres.name.all', $filters);
        $this->assertEquals(['Action', 'Drama'], $filters['genres.name.all']);
    }

    public function test_withIncludedGenres_addsIncludeFilter(): void
    {
        $this->filter->withIncludedGenres(['Action', 'Drama']);

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('genres.name', $filters);
        $this->assertEquals(['+Action', '+Drama'], $filters['genres.name']);
    }

    public function test_withIncludedGenres_singleGenre_addsIncludeFilter(): void
    {
        $this->filter->withIncludedGenres('Action');

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('genres.name', $filters);
        $this->assertEquals(['+Action'], $filters['genres.name']);
    }

    public function test_withExcludedGenres_addsExcludeFilter(): void
    {
        $this->filter->withExcludedGenres(['Horror', 'Thriller']);

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('genres.name', $filters);
        $this->assertEquals(['!Horror', '!Thriller'], $filters['genres.name']);
    }

    public function test_withExcludedGenres_singleGenre_addsExcludeFilter(): void
    {
        $this->filter->withExcludedGenres('Horror');

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('genres.name', $filters);
        $this->assertEquals(['!Horror'], $filters['genres.name']);
    }

    public function test_withAllCountries_addsAllFilter(): void
    {
        $this->filter->withAllCountries(['USA', 'UK']);

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('countries.name.all', $filters);
        $this->assertEquals(['USA', 'UK'], $filters['countries.name.all']);
    }

    public function test_withIncludedCountries_addsIncludeFilter(): void
    {
        $this->filter->withIncludedCountries(['USA', 'UK']);

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('countries.name', $filters);
        $this->assertEquals(['+USA', '+UK'], $filters['countries.name']);
    }

    public function test_withExcludedCountries_addsExcludeFilter(): void
    {
        $this->filter->withExcludedCountries(['Russia', 'China']);

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('countries.name', $filters);
        $this->assertEquals(['!Russia', '!China'], $filters['countries.name']);
    }

    public function test_withActor_withString_addsActorFilter(): void
    {
        $this->filter->withActor('Tom Hanks');

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('persons.name.regex', $filters);
        $this->assertEquals('Tom Hanks', $filters['persons.name.regex']);
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
        $this->assertArrayHasKey('persons.name.regex', $filters);
        $this->assertEquals('Christopher Nolan', $filters['persons.name.regex']);
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
        $this->assertArrayHasKey('isSeries', $filters);
        $this->assertEquals(false, $filters['isSeries']);
    }

    public function test_onlySeries_addsSeriesFilter(): void
    {
        $this->filter->onlySeries();

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('isSeries', $filters);
        $this->assertEquals(true, $filters['isSeries']);
    }

    public function test_inTop250_addsTop250Filter(): void
    {
        $this->filter->inTop250();

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('top250.lte', $filters);
        $this->assertEquals(250, $filters['top250.lte']);
    }

    public function test_inTop10_addsTop10Filter(): void
    {
        $this->filter->inTop10();

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('top10.lte', $filters);
        $this->assertEquals(10, $filters['top10.lte']);
    }

    public function test_withPremiereRange_addsPremiereFilter(): void
    {
        $this->filter->withPremiereRange('01.01.2023', '31.12.2023');

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('premiere.world', $filters);
        $this->assertEquals('01.01.2023-31.12.2023', $filters['premiere.world']);
    }

    public function test_withPremiereRange_withDefaultCountry_addsWorldFilter(): void
    {
        $this->filter->withPremiereRange('01.01.2023', '31.12.2023');

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('premiere.world', $filters);
        $this->assertEquals('01.01.2023-31.12.2023', $filters['premiere.world']);
    }

    public function test_year_addsYearFilter(): void
    {
        $this->filter->year(2023);

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('year.eq', $filters);
        $this->assertEquals(2023, $filters['year.eq']);
    }

    public function test_rating_addsRatingFilter(): void
    {
        $this->filter->rating(7.5);

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('rating.kp.gte', $filters);
        $this->assertEquals(7.5, $filters['rating.kp.gte']);
    }

    public function test_ratingRange_addsRatingRangeFilter(): void
    {
        $this->filter->ratingRange(7.0, 9.0);

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('rating.kp', $filters);
        $this->assertEquals('7-9', $filters['rating.kp']);
    }

    public function test_movieLength_addsLengthFilter(): void
    {
        $this->filter->movieLength(120);

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('movieLength.eq', $filters);
        $this->assertEquals(120, $filters['movieLength.eq']);
    }

    public function test_status_addsStatusFilter(): void
    {
        $this->filter->status('completed');

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('status.eq', $filters);
        $this->assertEquals('completed', $filters['status.eq']);
    }

    public function test_name_addsNameFilter(): void
    {
        $this->filter->name('Test Movie');

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('name.eq', $filters);
        $this->assertEquals('Test Movie', $filters['name.eq']);
    }

    public function test_enName_addsEnNameFilter(): void
    {
        $this->filter->enName('Test Movie');

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('enName.eq', $filters);
        $this->assertEquals('Test Movie', $filters['enName.eq']);
    }

    public function test_description_addsDescriptionFilter(): void
    {
        $this->filter->description('Test description');

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('description.regex', $filters);
        $this->assertEquals('Test description', $filters['description.regex']);
    }

    public function test_slogan_addsSloganFilter(): void
    {
        $this->filter->slogan('Test slogan');

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('slogan.regex', $filters);
        $this->assertEquals('Test slogan', $filters['slogan.regex']);
    }

    public function test_ageRating_addsAgeRatingFilter(): void
    {
        $this->filter->ageRating(16);

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('ageRating.eq', $filters);
        $this->assertEquals(16, $filters['ageRating.eq']);
    }

    public function test_sortBy_addsSortCriteria(): void
    {
        $this->filter->sortBy(SortField::RATING_KP, SortDirection::DESC);

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('sort', $filters);
        $this->assertEquals('-rating.kp', $filters['sort']);
    }

    public function test_sortBy_withDefaultDirection_addsAscSort(): void
    {
        $this->filter->sortBy(SortField::RATING_KP);

        $filters = $this->filter->getFilters();
        $this->assertArrayHasKey('sort', $filters);
        $this->assertEquals('-rating.kp', $filters['sort']);
    }

    public function test_clear_removesAllFilters(): void
    {
        $this->filter->year(2023);
        $this->filter->rating(7.5);

        $this->filter->reset();

        $this->assertEquals([], $this->filter->getFilters());
    }

    public function test_removeFilter_removesSpecificFilter(): void
    {
        $this->filter->year(2023);
        $this->filter->rating(7.5);

        // Reset removes all filters, so we'll test that
        $this->filter->reset();

        $this->assertEquals([], $this->filter->getFilters());
    }

    public function test_fluentInterface_returnsSelf(): void
    {
        $result = $this->filter
            ->year(2023)
            ->rating(7.5)
            ->name('Test Movie');

        $this->assertSame($this->filter, $result);
    }

    public function test_complexFilterCombination_worksCorrectly(): void
    {
        $this->filter
            ->year(2023)
            ->rating(7.5)
            ->withIncludedGenres(['Action', 'Drama'])
            ->withExcludedCountries(['Russia'])
            ->sortBy(SortField::RATING_KP, SortDirection::DESC);

        $filters = $this->filter->getFilters();

        $this->assertArrayHasKey('year.eq', $filters);
        $this->assertEquals(2023, $filters['year.eq']);

        $this->assertArrayHasKey('rating.kp.gte', $filters);
        $this->assertEquals(7.5, $filters['rating.kp.gte']);

        $this->assertArrayHasKey('genres.name', $filters);
        $this->assertEquals(['+Action', '+Drama'], $filters['genres.name']);

        $this->assertArrayHasKey('countries.name', $filters);
        $this->assertEquals(['!Russia'], $filters['countries.name']);

        $this->assertArrayHasKey('sort', $filters);
        $this->assertEquals('-rating.kp', $filters['sort']);
    }
} 