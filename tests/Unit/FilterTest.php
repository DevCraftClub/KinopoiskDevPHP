<?php

declare(strict_types=1);

namespace KinopoiskDev\Tests\Unit;

use KinopoiskDev\Filter\KeywordSearchFilter;
use PHPUnit\Framework\TestCase;

/**
 * Test class for filter functionality
 */
class FilterTest extends TestCase {

    /**
     * Test notNullFields method creates correct filters
     */
    public function testNotNullFields(): void {
        $filter = new KeywordSearchFilter();
        $filter->notNullFields(['title', 'movieId']);
        
        $filters = $filter->getFilters();
        
        $this->assertArrayHasKey('title.ne', $filters);
        $this->assertArrayHasKey('movieId.ne', $filters);
        $this->assertNull($filters['title.ne']);
        $this->assertNull($filters['movieId.ne']);
    }

    /**
     * Test combined filters including year.gte
     */
    public function testCombinedNotNullFields(): void {
        $filter = new KeywordSearchFilter();
        
        // Create combined filters like the failing test might expect
        $filter->year(2020, 'gte')
               ->notNullFields(['title', 'movieId'])
               ->onlyPopular(5);
        
        $filters = $filter->getFilters();
        
        // Check that year.gte exists (this was the failing assertion)
        $this->assertArrayHasKey('year.gte', $filters);
        $this->assertEquals(2020, $filters['year.gte']);
        
        // Check notNullFields
        $this->assertArrayHasKey('title.ne', $filters);
        $this->assertArrayHasKey('movieId.ne', $filters);
        
        // Check onlyPopular
        $this->assertArrayHasKey('movieCount.gte', $filters);
        $this->assertEquals(5, $filters['movieCount.gte']);
    }

    /**
     * Test year filter method inheritance
     */
    public function testYearFilterInheritance(): void {
        $filter = new KeywordSearchFilter();
        
        // Test that year method is available from MovieFilter
        $result = $filter->year(2023, 'gte');
        $this->assertInstanceOf(KeywordSearchFilter::class, $result);
        
        $filters = $filter->getFilters();
        $this->assertArrayHasKey('year.gte', $filters);
        $this->assertEquals(2023, $filters['year.gte']);
    }

    /**
     * Test various operators with year filter
     */
    public function testYearFilterOperators(): void {
        $testCases = [
            ['eq', 2023, 'year.eq'],
            ['ne', 2023, 'year.ne'],
            ['gt', 2023, 'year.gt'],
            ['gte', 2023, 'year.gte'],
            ['lt', 2023, 'year.lt'],
            ['lte', 2023, 'year.lte'],
        ];

        foreach ($testCases as [$operator, $value, $expectedKey]) {
            $filter = new KeywordSearchFilter();
            $filter->year($value, $operator);
            
            $filters = $filter->getFilters();
            $this->assertArrayHasKey($expectedKey, $filters, "Failed for operator: $operator");
            $this->assertEquals($value, $filters[$expectedKey]);
        }
    }

    /**
     * Test filter chaining and method availability
     */
    public function testFilterChaining(): void {
        $filter = new KeywordSearchFilter();
        
        $result = $filter
            ->id(123)
            ->title('test')
            ->movieId(456)
            ->year(2023, 'gte')
            ->notNullFields(['title', 'movies'])
            ->selectFields(['id', 'title'])
            ->sortByTitle('asc');
        
        $this->assertInstanceOf(KeywordSearchFilter::class, $result);
        
        $filters = $filter->getFilters();
        
        // Verify all expected keys exist
        $expectedKeys = [
            'id.eq',
            'title.eq', 
            'movieId.eq',
            'year.gte',
            'title.ne',
            'movies.ne',
            'selectFields'
        ];
        
        foreach ($expectedKeys as $key) {
            $this->assertArrayHasKey($key, $filters, "Missing key: $key");
        }
    }

    /**
     * Test that would reproduce the original error
     */
    public function testReproduceOriginalError(): void {
        $filter = new KeywordSearchFilter();
        
        // This should create both year.gte and other filters
        $filter->year(2020, 'gte')
               ->notNullFields(['title', 'description']);
        
        $filters = $filter->getFilters();
        
        // This is the assertion that was failing according to the user
        $this->assertArrayHasKey('year.gte', $filters, 'year.gte key should exist in combined filters');
        
        // Additional checks
        $this->assertArrayHasKey('title.ne', $filters);
        $this->assertArrayHasKey('description.ne', $filters);
        
        // Verify values
        $this->assertEquals(2020, $filters['year.gte']);
        $this->assertNull($filters['title.ne']);
        $this->assertNull($filters['description.ne']);
    }
}