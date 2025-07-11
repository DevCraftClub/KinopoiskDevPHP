<?php

declare(strict_types=1);

namespace KinopoiskDev\Tests\Unit\Enums;

use PHPUnit\Framework\TestCase;
use KinopoiskDev\Enums\SortDirection;

/**
 * @group unit
 * @group enums
 * @group sort-direction
 */
class SortDirectionTest extends TestCase
{
    public function test_all_sort_directions_have_correct_values(): void
    {
        $this->assertEquals('asc', SortDirection::ASC->value);
        $this->assertEquals('desc', SortDirection::DESC->value);
    }

    public function test_reverse_returns_opposite_direction(): void
    {
        $this->assertEquals(SortDirection::DESC, SortDirection::ASC->reverse());
        $this->assertEquals(SortDirection::ASC, SortDirection::DESC->reverse());
    }

    public function test_getSymbol_returns_correct_symbols(): void
    {
        $this->assertEquals('↑', SortDirection::ASC->getSymbol());
        $this->assertEquals('↓', SortDirection::DESC->getSymbol());
    }

    public function test_getDescription_returns_correct_descriptions(): void
    {
        $this->assertEquals('По возрастанию', SortDirection::ASC->getDescription());
        $this->assertEquals('По убыванию', SortDirection::DESC->getDescription());
    }

    public function test_getShortDescription_returns_correct_descriptions(): void
    {
        $this->assertEquals('А→Я', SortDirection::ASC->getShortDescription());
        $this->assertEquals('Я→А', SortDirection::DESC->getShortDescription());
    }

    public function test_isAscending_returns_correct_values(): void
    {
        $this->assertTrue(SortDirection::ASC->isAscending());
        $this->assertFalse(SortDirection::DESC->isAscending());
    }

    public function test_isDescending_returns_correct_values(): void
    {
        $this->assertFalse(SortDirection::ASC->isDescending());
        $this->assertTrue(SortDirection::DESC->isDescending());
    }

    public function test_fromString_returns_correct_instances(): void
    {
        $this->assertEquals(SortDirection::ASC, SortDirection::fromString('asc'));
        $this->assertEquals(SortDirection::ASC, SortDirection::fromString('ASC'));
        $this->assertEquals(SortDirection::DESC, SortDirection::fromString('desc'));
        $this->assertEquals(SortDirection::DESC, SortDirection::fromString('DESC'));
    }

    public function test_fromString_with_default_returns_default_for_invalid_input(): void
    {
        $this->assertEquals(SortDirection::ASC, SortDirection::fromString('invalid'));
        $this->assertEquals(SortDirection::DESC, SortDirection::fromString('invalid', SortDirection::DESC));
        $this->assertEquals(SortDirection::ASC, SortDirection::fromString('', SortDirection::ASC));
    }

    public function test_getAllDirections_returns_all_directions(): void
    {
        $directions = SortDirection::getAllDirections();
        $this->assertCount(2, $directions);
        $this->assertContains(SortDirection::ASC, $directions);
        $this->assertContains(SortDirection::DESC, $directions);
    }

    public function test_sort_direction_can_be_created_from_string(): void
    {
        $this->assertEquals(SortDirection::ASC, SortDirection::from('asc'));
        $this->assertEquals(SortDirection::DESC, SortDirection::from('desc'));
    }

    public function test_sort_direction_can_be_compared(): void
    {
        $this->assertTrue(SortDirection::ASC === SortDirection::from('asc'));
        $this->assertFalse(SortDirection::ASC === SortDirection::DESC);
    }

    public function test_all_cases_are_covered(): void
    {
        $cases = SortDirection::cases();
        $this->assertCount(2, $cases);
        
        $expectedValues = ['asc', 'desc'];
        $actualValues = array_map(fn($case) => $case->value, $cases);
        
        $this->assertEquals($expectedValues, $actualValues);
    }

    public function test_reverse_is_symmetric(): void
    {
        $asc = SortDirection::ASC;
        $desc = SortDirection::DESC;
        
        $this->assertEquals($desc, $asc->reverse());
        $this->assertEquals($asc, $desc->reverse());
        $this->assertEquals($asc, $asc->reverse()->reverse());
        $this->assertEquals($desc, $desc->reverse()->reverse());
    }

    public function test_fromString_uses_cache(): void
    {
        // Первый вызов
        $result1 = SortDirection::fromString('asc');
        $this->assertEquals(SortDirection::ASC, $result1);
        
        // Второй вызов должен использовать кеш
        $result2 = SortDirection::fromString('asc');
        $this->assertEquals(SortDirection::ASC, $result2);
        $this->assertSame($result1, $result2);
    }
} 