<?php

declare(strict_types=1);

namespace KinopoiskDev\Tests\Unit;

use KinopoiskDev\Filter\KeywordSearchFilter;
use PHPUnit\Framework\TestCase;

/**
 * Тестовый класс для функциональности фильтров
 */
class FilterTest extends TestCase {

    /**
     * Тест метода notNullFields создает правильные фильтры
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
     * Тест комбинированных фильтров включая year.gte
     */
    public function testCombinedNotNullFields(): void {
        $filter = new KeywordSearchFilter();
        
        // Создаем комбинированные фильтры как может ожидать падающий тест
        $filter->year(2020, 'gte')
               ->notNullFields(['title', 'movieId'])
               ->onlyPopular(5);
        
        $filters = $filter->getFilters();
        
        // Проверяем, что year.gte существует (это была падающая проверка)
        $this->assertArrayHasKey('year.gte', $filters);
        $this->assertEquals(2020, $filters['year.gte']);
        
        // Проверяем notNullFields
        $this->assertArrayHasKey('title.ne', $filters);
        $this->assertArrayHasKey('movieId.ne', $filters);
        
        // Проверяем onlyPopular
        $this->assertArrayHasKey('movieCount.gte', $filters);
        $this->assertEquals(5, $filters['movieCount.gte']);
    }

    /**
     * Тест наследования метода фильтра по году
     */
    public function testYearFilterInheritance(): void {
        $filter = new KeywordSearchFilter();
        
        // Тестируем, что метод year доступен из MovieFilter
        $result = $filter->year(2023, 'gte');
        $this->assertInstanceOf(KeywordSearchFilter::class, $result);
        
        $filters = $filter->getFilters();
        $this->assertArrayHasKey('year.gte', $filters);
        $this->assertEquals(2023, $filters['year.gte']);
    }

    /**
     * Тест различных операторов с фильтром по году
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
            $this->assertArrayHasKey($expectedKey, $filters, "Ошибка для оператора: $operator");
            $this->assertEquals($value, $filters[$expectedKey]);
        }
    }

    /**
     * Тест цепочки фильтров и доступности методов
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
        
        // Проверяем, что все ожидаемые ключи существуют
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
            $this->assertArrayHasKey($key, $filters, "Отсутствует ключ: $key");
        }
    }

    /**
     * Тест, который воспроизводит оригинальную ошибку
     */
    public function testReproduceOriginalError(): void {
        $filter = new KeywordSearchFilter();
        
        // Это должно создать как year.gte, так и другие фильтры
        $filter->year(2020, 'gte')
               ->notNullFields(['title', 'description']);
        
        $filters = $filter->getFilters();
        
        // Это проверка, которая падала согласно пользователю
        $this->assertArrayHasKey('year.gte', $filters, 'ключ year.gte должен существовать в комбинированных фильтрах');
        
        // Дополнительные проверки
        $this->assertArrayHasKey('title.ne', $filters);
        $this->assertArrayHasKey('description.ne', $filters);
        
        // Проверяем значения
        $this->assertEquals(2020, $filters['year.gte']);
        $this->assertNull($filters['title.ne']);
        $this->assertNull($filters['description.ne']);
    }
}