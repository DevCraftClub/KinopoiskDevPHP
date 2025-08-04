<?php

declare(strict_types=1);

namespace KinopoiskDev\Tests\Unit\Enums;

use KinopoiskDev\Enums\PersonSex;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 * @group enums
 * @group person-sex
 */
class PersonSexTest extends TestCase {

	public function test_all_person_sexes_have_correct_values(): void {
		$this->assertEquals('male', PersonSex::MALE->value);
		$this->assertEquals('female', PersonSex::FEMALE->value);
	}

	public function test_person_sex_can_be_created_from_string(): void {
		$this->assertEquals(PersonSex::MALE, PersonSex::from('male'));
		$this->assertEquals(PersonSex::FEMALE, PersonSex::from('female'));
	}

	public function test_person_sex_can_be_compared(): void {
		$this->assertTrue(PersonSex::MALE === PersonSex::from('male'));
		$this->assertFalse(PersonSex::MALE === PersonSex::FEMALE);
	}

	public function test_all_cases_are_covered(): void {
		$cases = PersonSex::cases();
		$this->assertCount(2, $cases);

		$expectedValues = ['male', 'female'];
		$actualValues   = array_map(fn ($case) => $case->value, $cases);

		$this->assertEquals($expectedValues, $actualValues);
	}

}