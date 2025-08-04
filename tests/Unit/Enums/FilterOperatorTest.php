<?php

declare(strict_types=1);

namespace KinopoiskDev\Tests\Unit\Enums;

use KinopoiskDev\Enums\FilterOperator;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 * @group enums
 * @group filter-operator
 */
class FilterOperatorTest extends TestCase {

	public function test_all_filter_operators_have_correct_values(): void {
		$this->assertEquals('eq', FilterOperator::EQUALS->value);
		$this->assertEquals('ne', FilterOperator::NOT_EQUALS->value);
		$this->assertEquals('gt', FilterOperator::GREATER_THAN->value);
		$this->assertEquals('gte', FilterOperator::GREATER_THAN_EQUALS->value);
		$this->assertEquals('lt', FilterOperator::LESS_THAN->value);
		$this->assertEquals('lte', FilterOperator::LESS_THAN_EQUALS->value);
		$this->assertEquals('in', FilterOperator::IN->value);
		$this->assertEquals('nin', FilterOperator::NOT_IN->value);
		$this->assertEquals('all', FilterOperator::ALL->value);
		$this->assertEquals('regex', FilterOperator::REGEX->value);
		$this->assertEquals('range', FilterOperator::RANGE->value);
		$this->assertEquals('include', FilterOperator::INCLUDE->value);
		$this->assertEquals('exclude', FilterOperator::EXCLUDE->value);
	}

	public function test_getDefaultForFieldType_returns_correct_operators(): void {
		$this->assertEquals(FilterOperator::IN, FilterOperator::getDefaultForFieldType('array'));
		$this->assertEquals(FilterOperator::REGEX, FilterOperator::getDefaultForFieldType('text'));
		$this->assertEquals(FilterOperator::EQUALS, FilterOperator::getDefaultForFieldType('number'));
		$this->assertEquals(FilterOperator::EQUALS, FilterOperator::getDefaultForFieldType('boolean'));
		$this->assertEquals(FilterOperator::EQUALS, FilterOperator::getDefaultForFieldType('date'));
		$this->assertEquals(FilterOperator::EQUALS, FilterOperator::getDefaultForFieldType('unknown'));
	}

	public function test_getPrefix_returns_correct_prefixes(): void {
		$this->assertEquals('+', FilterOperator::INCLUDE->getPrefix());
		$this->assertEquals('!', FilterOperator::EXCLUDE->getPrefix());
		$this->assertNull(FilterOperator::EQUALS->getPrefix());
		$this->assertNull(FilterOperator::NOT_EQUALS->getPrefix());
		$this->assertNull(FilterOperator::GREATER_THAN->getPrefix());
		$this->assertNull(FilterOperator::LESS_THAN->getPrefix());
		$this->assertNull(FilterOperator::IN->getPrefix());
		$this->assertNull(FilterOperator::NOT_IN->getPrefix());
		$this->assertNull(FilterOperator::ALL->getPrefix());
		$this->assertNull(FilterOperator::REGEX->getPrefix());
		$this->assertNull(FilterOperator::RANGE->getPrefix());
	}

	public function test_isRangeOperator_returns_correct_values(): void {
		$this->assertTrue(FilterOperator::RANGE->isRangeOperator());
		$this->assertFalse(FilterOperator::EQUALS->isRangeOperator());
		$this->assertFalse(FilterOperator::NOT_EQUALS->isRangeOperator());
		$this->assertFalse(FilterOperator::GREATER_THAN->isRangeOperator());
		$this->assertFalse(FilterOperator::LESS_THAN->isRangeOperator());
		$this->assertFalse(FilterOperator::IN->isRangeOperator());
		$this->assertFalse(FilterOperator::NOT_IN->isRangeOperator());
		$this->assertFalse(FilterOperator::ALL->isRangeOperator());
		$this->assertFalse(FilterOperator::REGEX->isRangeOperator());
		$this->assertFalse(FilterOperator::INCLUDE->isRangeOperator());
		$this->assertFalse(FilterOperator::EXCLUDE->isRangeOperator());
	}

	public function test_isIncludeExcludeOperator_returns_correct_values(): void {
		$this->assertTrue(FilterOperator::INCLUDE->isIncludeExcludeOperator());
		$this->assertTrue(FilterOperator::EXCLUDE->isIncludeExcludeOperator());
		$this->assertFalse(FilterOperator::EQUALS->isIncludeExcludeOperator());
		$this->assertFalse(FilterOperator::NOT_EQUALS->isIncludeExcludeOperator());
		$this->assertFalse(FilterOperator::GREATER_THAN->isIncludeExcludeOperator());
		$this->assertFalse(FilterOperator::LESS_THAN->isIncludeExcludeOperator());
		$this->assertFalse(FilterOperator::IN->isIncludeExcludeOperator());
		$this->assertFalse(FilterOperator::NOT_IN->isIncludeExcludeOperator());
		$this->assertFalse(FilterOperator::ALL->isIncludeExcludeOperator());
		$this->assertFalse(FilterOperator::REGEX->isIncludeExcludeOperator());
		$this->assertFalse(FilterOperator::RANGE->isIncludeExcludeOperator());
	}

	public function test_filter_operator_can_be_created_from_string(): void {
		$this->assertEquals(FilterOperator::EQUALS, FilterOperator::from('eq'));
		$this->assertEquals(FilterOperator::NOT_EQUALS, FilterOperator::from('ne'));
		$this->assertEquals(FilterOperator::GREATER_THAN, FilterOperator::from('gt'));
		$this->assertEquals(FilterOperator::GREATER_THAN_EQUALS, FilterOperator::from('gte'));
		$this->assertEquals(FilterOperator::LESS_THAN, FilterOperator::from('lt'));
		$this->assertEquals(FilterOperator::LESS_THAN_EQUALS, FilterOperator::from('lte'));
		$this->assertEquals(FilterOperator::IN, FilterOperator::from('in'));
		$this->assertEquals(FilterOperator::NOT_IN, FilterOperator::from('nin'));
		$this->assertEquals(FilterOperator::ALL, FilterOperator::from('all'));
		$this->assertEquals(FilterOperator::REGEX, FilterOperator::from('regex'));
		$this->assertEquals(FilterOperator::RANGE, FilterOperator::from('range'));
		$this->assertEquals(FilterOperator::INCLUDE, FilterOperator::from('include'));
		$this->assertEquals(FilterOperator::EXCLUDE, FilterOperator::from('exclude'));
	}

	public function test_all_cases_are_covered(): void {
		$cases = FilterOperator::cases();
		$this->assertCount(13, $cases);

		$expectedValues = ['eq', 'ne', 'gt', 'gte', 'lt', 'lte', 'in', 'nin', 'all', 'regex', 'range', 'include', 'exclude'];
		$actualValues   = array_map(fn ($case) => $case->value, $cases);

		$this->assertEquals($expectedValues, $actualValues);
	}

	public function test_getDefaultForFieldType_uses_cache(): void {
		// Первый вызов
		$result1 = FilterOperator::getDefaultForFieldType('array');
		$this->assertEquals(FilterOperator::IN, $result1);

		// Второй вызов должен использовать кеш
		$result2 = FilterOperator::getDefaultForFieldType('array');
		$this->assertEquals(FilterOperator::IN, $result2);
		$this->assertSame($result1, $result2);
	}

}