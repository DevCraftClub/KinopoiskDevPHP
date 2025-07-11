<?php

declare(strict_types=1);

namespace KinopoiskDev\Tests\Unit\Filter;

use KinopoiskDev\Enums\SortDirection;
use KinopoiskDev\Enums\SortField;
use KinopoiskDev\Filter\SortCriteria;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 * @group filter
 * @group sort-criteria
 */
class SortCriteriaTest extends TestCase {

	public function test_constructor_withValidArguments_createsInstance(): void {
		$sortCriteria = new SortCriteria(SortField::RATING_KP, SortDirection::DESC);

		$this->assertInstanceOf(SortCriteria::class, $sortCriteria);
		$this->assertEquals(SortField::RATING_KP, $sortCriteria->field);
		$this->assertEquals(SortDirection::DESC, $sortCriteria->direction);
	}

	public function test_constructor_withCustomValues_setsValues(): void {
		$sortCriteria = new SortCriteria(SortField::YEAR, SortDirection::ASC);

		$this->assertEquals(SortField::YEAR, $sortCriteria->field);
		$this->assertEquals(SortDirection::ASC, $sortCriteria->direction);
	}

	public function test_create_withField_createsInstanceWithDefaultDirection(): void {
		$sortCriteria = SortCriteria::create(SortField::RATING_KP);

		$this->assertInstanceOf(SortCriteria::class, $sortCriteria);
		$this->assertEquals(SortField::RATING_KP, $sortCriteria->field);
		$this->assertEquals(SortField::RATING_KP->getDefaultDirection(), $sortCriteria->direction);
	}

	public function test_ascending_withField_createsInstanceWithAscDirection(): void {
		$sortCriteria = SortCriteria::ascending(SortField::NAME);

		$this->assertInstanceOf(SortCriteria::class, $sortCriteria);
		$this->assertEquals(SortField::NAME, $sortCriteria->field);
		$this->assertEquals(SortDirection::ASC, $sortCriteria->direction);
	}

	public function test_descending_withField_createsInstanceWithDescDirection(): void {
		$sortCriteria = SortCriteria::descending(SortField::VOTES_KP);

		$this->assertInstanceOf(SortCriteria::class, $sortCriteria);
		$this->assertEquals(SortField::VOTES_KP, $sortCriteria->field);
		$this->assertEquals(SortDirection::DESC, $sortCriteria->direction);
	}

	public function test_toArray_returnsArrayRepresentation(): void {
		$sortCriteria = new SortCriteria(SortField::RATING_KP, SortDirection::ASC);

		$result = $sortCriteria->toArray();

		$this->assertIsArray($result);
		$this->assertEquals(SortField::RATING_KP->value, $result['field']);
		$this->assertEquals(SortDirection::ASC->value, $result['direction']);
	}

	public function test_toArray_withDescDirection_returnsCorrectArray(): void {
		$sortCriteria = new SortCriteria(SortField::YEAR, SortDirection::DESC);

		$result = $sortCriteria->toArray();

		$this->assertEquals(SortField::YEAR->value, $result['field']);
		$this->assertEquals(SortDirection::DESC->value, $result['direction']);
	}

	public function test_fromArray_withValidData_createsInstance(): void {
		$data = [
			'field'     => SortField::RATING_KP,
			'direction' => SortDirection::ASC,
		];

		$sortCriteria = SortCriteria::fromArray($data);

		$this->assertInstanceOf(SortCriteria::class, $sortCriteria);
		$this->assertEquals(SortField::RATING_KP, $sortCriteria->field);
		$this->assertEquals(SortDirection::ASC, $sortCriteria->direction);
	}

	public function test_fromArray_withDescData_createsInstance(): void {
		$data = [
			'field'     => SortField::YEAR,
			'direction' => SortDirection::DESC,
		];

		$sortCriteria = SortCriteria::fromArray($data);

		$this->assertInstanceOf(SortCriteria::class, $sortCriteria);
		$this->assertEquals(SortField::YEAR, $sortCriteria->field);
		$this->assertEquals(SortDirection::DESC, $sortCriteria->direction);
	}

	public function test_fromArray_withMissingField_returnsNull(): void {
		$data = ['direction' => SortDirection::ASC];

		$sortCriteria = SortCriteria::fromArray($data);

		$this->assertNull($sortCriteria);
	}

	public function test_fromArray_withMissingDirection_usesFieldDefault(): void {
		$data = ['field' => SortField::RATING_KP];

		$sortCriteria = SortCriteria::fromArray($data);

		$this->assertInstanceOf(SortCriteria::class, $sortCriteria);
		$this->assertEquals(SortField::RATING_KP, $sortCriteria->field);
		$this->assertEquals(SortField::RATING_KP->getDefaultDirection(), $sortCriteria->direction);
	}

	public function test_fromStrings_withValidStrings_createsInstance(): void {
		$sortCriteria = SortCriteria::fromStrings('rating.kp', 'desc');

		$this->assertInstanceOf(SortCriteria::class, $sortCriteria);
		$this->assertEquals(SortField::RATING_KP, $sortCriteria->field);
		$this->assertEquals(SortDirection::DESC, $sortCriteria->direction);
	}

	public function test_fromStrings_withInvalidField_returnsNull(): void {
		$sortCriteria = SortCriteria::fromStrings('invalid_field', 'asc');

		$this->assertNull($sortCriteria);
	}

	public function test_fromStrings_withoutDirection_usesFieldDefault(): void {
		$sortCriteria = SortCriteria::fromStrings('rating.kp');

		$this->assertInstanceOf(SortCriteria::class, $sortCriteria);
		$this->assertEquals(SortField::RATING_KP, $sortCriteria->field);
		$this->assertEquals(SortField::RATING_KP->getDefaultDirection(), $sortCriteria->direction);
	}

	public function test_toApiString_withAscDirection_returnsCorrectString(): void {
		$sortCriteria = new SortCriteria(SortField::RATING_KP, SortDirection::ASC);

		$result = $sortCriteria->toApiString();

		$this->assertEquals('rating.kp', $result);
	}

	public function test_toApiString_withDescDirection_returnsCorrectString(): void {
		$sortCriteria = new SortCriteria(SortField::RATING_KP, SortDirection::DESC);

		$result = $sortCriteria->toApiString();

		$this->assertEquals('-rating.kp', $result);
	}

	public function test_reverse_returnsOppositeDirection(): void {
		$sortCriteria = new SortCriteria(SortField::RATING_KP, SortDirection::ASC);

		$reversed = $sortCriteria->reverse();

		$this->assertInstanceOf(SortCriteria::class, $reversed);
		$this->assertEquals(SortField::RATING_KP, $reversed->field);
		$this->assertEquals(SortDirection::DESC, $reversed->direction);
		// Original should remain unchanged
		$this->assertEquals(SortDirection::ASC, $sortCriteria->direction);
	}

	public function test_hasSameField_withSameField_returnsTrue(): void {
		$criteria1 = new SortCriteria(SortField::RATING_KP, SortDirection::ASC);
		$criteria2 = new SortCriteria(SortField::RATING_KP, SortDirection::DESC);

		$this->assertTrue($criteria1->hasSameField($criteria2));
	}

	public function test_hasSameField_withDifferentField_returnsFalse(): void {
		$criteria1 = new SortCriteria(SortField::RATING_KP, SortDirection::ASC);
		$criteria2 = new SortCriteria(SortField::YEAR, SortDirection::ASC);

		$this->assertFalse($criteria1->hasSameField($criteria2));
	}

	public function test_equals_withSameFieldAndDirection_returnsTrue(): void {
		$criteria1 = new SortCriteria(SortField::RATING_KP, SortDirection::ASC);
		$criteria2 = new SortCriteria(SortField::RATING_KP, SortDirection::ASC);

		$this->assertTrue($criteria1->equals($criteria2));
	}

	public function test_equals_withDifferentDirection_returnsFalse(): void {
		$criteria1 = new SortCriteria(SortField::RATING_KP, SortDirection::ASC);
		$criteria2 = new SortCriteria(SortField::RATING_KP, SortDirection::DESC);

		$this->assertFalse($criteria1->equals($criteria2));
	}

	public function test_toShortString_returnsCorrectFormat(): void {
		$sortCriteria = new SortCriteria(SortField::RATING_KP, SortDirection::ASC);

		$result = $sortCriteria->toShortString();

		$this->assertStringContainsString(SortField::RATING_KP->getDescription(), $result);
		$this->assertStringContainsString(SortDirection::ASC->getSymbol(), $result);
	}

	public function test_toString_returnsCorrectFormat(): void {
		$sortCriteria = new SortCriteria(SortField::RATING_KP, SortDirection::ASC);

		$result = (string) $sortCriteria;

		$this->assertStringContainsString(SortField::RATING_KP->getDescription(), $result);
		$this->assertStringContainsString(SortDirection::ASC->getDescription(), $result);
	}

	public function test_isRatingSort_withRatingField_returnsTrue(): void {
		$sortCriteria = new SortCriteria(SortField::RATING_KP, SortDirection::ASC);

		$this->assertTrue($sortCriteria->isRatingSort());
	}

	public function test_isRatingSort_withNonRatingField_returnsFalse(): void {
		$sortCriteria = new SortCriteria(SortField::YEAR, SortDirection::ASC);

		$this->assertFalse($sortCriteria->isRatingSort());
	}

	public function test_isVotesSort_withVotesField_returnsTrue(): void {
		$sortCriteria = new SortCriteria(SortField::VOTES_KP, SortDirection::ASC);

		$this->assertTrue($sortCriteria->isVotesSort());
	}

	public function test_isVotesSort_withNonVotesField_returnsFalse(): void {
		$sortCriteria = new SortCriteria(SortField::RATING_KP, SortDirection::ASC);

		$this->assertFalse($sortCriteria->isVotesSort());
	}

	public function test_isDateSort_withDateField_returnsTrue(): void {
		$sortCriteria = new SortCriteria(SortField::PREMIERE_WORLD, SortDirection::ASC);

		$this->assertTrue($sortCriteria->isDateSort());
	}

	public function test_isDateSort_withNonDateField_returnsFalse(): void {
		$sortCriteria = new SortCriteria(SortField::RATING_KP, SortDirection::ASC);

		$this->assertFalse($sortCriteria->isDateSort());
	}

	public function test_getFieldDataType_returnsCorrectType(): void {
		$sortCriteria = new SortCriteria(SortField::RATING_KP, SortDirection::ASC);

		$dataType = $sortCriteria->getFieldDataType();

		$this->assertEquals(SortField::RATING_KP->getDataType(), $dataType);
	}

	public function test_fluentInterface_withStaticMethods_returnsSelf(): void {
		$sortCriteria = SortCriteria::create(SortField::RATING_KP);

		$this->assertInstanceOf(SortCriteria::class, $sortCriteria);
	}

	public function test_allSortFields_workCorrectly(): void {
		$fields = [
			SortField::RATING_KP,
			SortField::YEAR,
			SortField::NAME,
			SortField::VOTES_KP,
			SortField::MOVIE_LENGTH,
			SortField::PREMIERE_WORLD,
			SortField::TOP_10,
			SortField::TOP_250,
			SortField::CREATED_AT,
			SortField::UPDATED_AT,
		];

		foreach ($fields as $field) {
			$sortCriteria = new SortCriteria($field, SortDirection::ASC);
			$this->assertEquals($field, $sortCriteria->field);
			$this->assertEquals(SortDirection::ASC, $sortCriteria->direction);
		}
	}

	public function test_allSortDirections_workCorrectly(): void {
		$directions = [SortDirection::ASC, SortDirection::DESC];
		$field      = SortField::RATING_KP;

		foreach ($directions as $direction) {
			$sortCriteria = new SortCriteria($field, $direction);
			$this->assertEquals($field, $sortCriteria->field);
			$this->assertEquals($direction, $sortCriteria->direction);
		}
	}

}