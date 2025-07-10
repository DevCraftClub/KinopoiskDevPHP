<?php

namespace Tests\Filter;

use PHPUnit\Framework\TestCase;
use KinopoiskDev\Filter\MovieSearchFilter;
use KinopoiskDev\Filter\PersonSearchFilter;
use KinopoiskDev\Filter\ReviewSearchFilter;
use KinopoiskDev\Filter\SeasonSearchFilter;
use KinopoiskDev\Filter\StudioSearchFilter;
use KinopoiskDev\Filter\ImageSearchFilter;
use KinopoiskDev\Filter\KeywordSearchFilter;

/**
 * Тесты для метода notNullFields
 */
class NotNullFieldsTest extends TestCase
{
    /**
     * Тест notNullFields в MovieSearchFilter
     */
    public function testMovieSearchFilterNotNullFields(): void
    {
        $filter = new MovieSearchFilter();
        $filter->notNullFields(['poster.url', 'description', 'name']);
        
        $filters = $filter->getFilters();
        
        $this->assertArrayHasKey('poster.url.ne', $filters);
        $this->assertArrayHasKey('description.ne', $filters);
        $this->assertArrayHasKey('name.ne', $filters);
        
        $this->assertNull($filters['poster.url.ne']);
        $this->assertNull($filters['description.ne']);
        $this->assertNull($filters['name.ne']);
    }

    /**
     * Тест notNullFields в PersonSearchFilter
     */
    public function testPersonSearchFilterNotNullFields(): void
    {
        $filter = new PersonSearchFilter();
        $filter->notNullFields(['photo', 'description', 'name']);
        
        $filters = $filter->getFilters();
        
        $this->assertArrayHasKey('photo.ne', $filters);
        $this->assertArrayHasKey('description.ne', $filters);
        $this->assertArrayHasKey('name.ne', $filters);
        
        $this->assertNull($filters['photo.ne']);
        $this->assertNull($filters['description.ne']);
        $this->assertNull($filters['name.ne']);
    }

    /**
     * Тест notNullFields в ReviewSearchFilter
     */
    public function testReviewSearchFilterNotNullFields(): void
    {
        $filter = new ReviewSearchFilter();
        $filter->notNullFields(['review', 'title', 'author']);
        
        $filters = $filter->getFilters();
        
        $this->assertArrayHasKey('review.ne', $filters);
        $this->assertArrayHasKey('title.ne', $filters);
        $this->assertArrayHasKey('author.ne', $filters);
        
        $this->assertNull($filters['review.ne']);
        $this->assertNull($filters['title.ne']);
        $this->assertNull($filters['author.ne']);
    }

    /**
     * Тест notNullFields в SeasonSearchFilter
     */
    public function testSeasonSearchFilterNotNullFields(): void
    {
        $filter = new SeasonSearchFilter();
        $filter->notNullFields(['episodesCount', 'number']);
        
        $filters = $filter->getFilters();
        
        $this->assertArrayHasKey('episodesCount.ne', $filters);
        $this->assertArrayHasKey('number.ne', $filters);
        
        $this->assertNull($filters['episodesCount.ne']);
        $this->assertNull($filters['number.ne']);
    }

    /**
     * Тест notNullFields в StudioSearchFilter
     */
    public function testStudioSearchFilterNotNullFields(): void
    {
        $filter = new StudioSearchFilter();
        $filter->notNullFields(['logo.url', 'title']);
        
        $filters = $filter->getFilters();
        
        $this->assertArrayHasKey('logo.url.ne', $filters);
        $this->assertArrayHasKey('title.ne', $filters);
        
        $this->assertNull($filters['logo.url.ne']);
        $this->assertNull($filters['title.ne']);
    }

    /**
     * Тест notNullFields в ImageSearchFilter
     */
    public function testImageSearchFilterNotNullFields(): void
    {
        $filter = new ImageSearchFilter();
        $filter->notNullFields(['width', 'height', 'url']);
        
        $filters = $filter->getFilters();
        
        $this->assertArrayHasKey('width.ne', $filters);
        $this->assertArrayHasKey('height.ne', $filters);
        $this->assertArrayHasKey('url.ne', $filters);
        
        $this->assertNull($filters['width.ne']);
        $this->assertNull($filters['height.ne']);
        $this->assertNull($filters['url.ne']);
    }

    /**
     * Тест notNullFields в KeywordSearchFilter
     */
    public function testKeywordSearchFilterNotNullFields(): void
    {
        $filter = new KeywordSearchFilter();
        $filter->notNullFields(['title', 'movies']);
        
        $filters = $filter->getFilters();
        
        $this->assertArrayHasKey('title.ne', $filters);
        $this->assertArrayHasKey('movies.ne', $filters);
        
        $this->assertNull($filters['title.ne']);
        $this->assertNull($filters['movies.ne']);
    }

    /**
     * Тест комбинированного использования notNullFields с другими фильтрами
     */
    public function testCombinedNotNullFields(): void
    {
        $filter = new MovieSearchFilter();
        $filter->withYearBetween(2020, 2024)
               ->withRatingBetween(7.0, 10.0)
               ->notNullFields(['poster.url', 'description', 'name'])
               ->sortByKinopoiskRating();
        
        $filters = $filter->getFilters();
        
        // Проверяем, что notNullFields работает вместе с другими фильтрами
        $this->assertArrayHasKey('poster.url.ne', $filters);
        $this->assertArrayHasKey('description.ne', $filters);
        $this->assertArrayHasKey('name.ne', $filters);
        
        // Проверяем, что другие фильтры тоже присутствуют
        $this->assertArrayHasKey('year.gte', $filters);
        $this->assertArrayHasKey('year.lte', $filters);
        $this->assertArrayHasKey('rating.kp.gte', $filters);
        $this->assertArrayHasKey('rating.kp.lte', $filters);
    }

    /**
     * Тест fluent interface для notNullFields
     */
    public function testNotNullFieldsFluentInterface(): void
    {
        $filter = new MovieSearchFilter();
        $result = $filter->notNullFields(['poster.url', 'description']);
        
        $this->assertSame($filter, $result);
        $this->assertInstanceOf(MovieSearchFilter::class, $result);
    }

    /**
     * Тест сброса фильтров после notNullFields
     */
    public function testResetAfterNotNullFields(): void
    {
        $filter = new MovieSearchFilter();
        $filter->notNullFields(['poster.url', 'description']);
        
        // Проверяем, что фильтры добавлены
        $filters = $filter->getFilters();
        $this->assertArrayHasKey('poster.url.ne', $filters);
        $this->assertArrayHasKey('description.ne', $filters);
        
        // Сбрасываем фильтры
        $filter->reset();
        
        // Проверяем, что фильтры удалены
        $filters = $filter->getFilters();
        $this->assertArrayNotHasKey('poster.url.ne', $filters);
        $this->assertArrayNotHasKey('description.ne', $filters);
    }

    /**
     * Тест с пустым массивом полей
     */
    public function testNotNullFieldsWithEmptyArray(): void
    {
        $filter = new MovieSearchFilter();
        $filter->notNullFields([]);
        
        $filters = $filter->getFilters();
        
        // Проверяем, что фильтры не добавлены при пустом массиве
        $this->assertEmpty($filters);
    }

    /**
     * Тест с одним полем
     */
    public function testNotNullFieldsWithSingleField(): void
    {
        $filter = new MovieSearchFilter();
        $filter->notNullFields(['poster.url']);
        
        $filters = $filter->getFilters();
        
        $this->assertArrayHasKey('poster.url.ne', $filters);
        $this->assertNull($filters['poster.url.ne']);
        $this->assertCount(1, $filters);
    }

    /**
     * Тест с множественными полями
     */
    public function testNotNullFieldsWithMultipleFields(): void
    {
        $filter = new MovieSearchFilter();
        $fields = [
            'poster.url',
            'backdrop.url', 
            'description',
            'name',
            'rating.kp',
            'votes.kp',
            'year',
            'genres.name'
        ];
        
        $filter->notNullFields($fields);
        
        $filters = $filter->getFilters();
        
        foreach ($fields as $field) {
            $this->assertArrayHasKey($field . '.ne', $filters);
            $this->assertNull($filters[$field . '.ne']);
        }
        
        $this->assertCount(count($fields), $filters);
    }
}