<?php

declare(strict_types=1);

namespace KinopoiskDev\Tests\Unit\Enums;

use KinopoiskDev\Enums\RatingMpaa;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 * @group enums
 * @group rating-mpaa
 */
class RatingMpaaTest extends TestCase {

	public function test_all_rating_mpaa_have_correct_values(): void {
		$this->assertEquals('g', RatingMpaa::G->value);
		$this->assertEquals('pg', RatingMpaa::PG->value);
		$this->assertEquals('pg13', RatingMpaa::PG13->value);
		$this->assertEquals('r', RatingMpaa::R->value);
		$this->assertEquals('nc17', RatingMpaa::NC17->value);
	}

	public function test_rating_mpaa_can_be_created_from_string(): void {
		$this->assertEquals(RatingMpaa::G, RatingMpaa::from('g'));
		$this->assertEquals(RatingMpaa::PG, RatingMpaa::from('pg'));
		$this->assertEquals(RatingMpaa::PG13, RatingMpaa::from('pg13'));
		$this->assertEquals(RatingMpaa::R, RatingMpaa::from('r'));
		$this->assertEquals(RatingMpaa::NC17, RatingMpaa::from('nc17'));
	}

	public function test_rating_mpaa_can_be_compared(): void {
		$this->assertTrue(RatingMpaa::G === RatingMpaa::from('g'));
		$this->assertFalse(RatingMpaa::G === RatingMpaa::PG);
	}

	public function test_all_cases_are_covered(): void {
		$cases = RatingMpaa::cases();
		$this->assertCount(5, $cases);

		$expectedValues = ['g', 'pg', 'pg13', 'r', 'nc17'];
		$actualValues   = array_map(fn ($case) => $case->value, $cases);

		$this->assertEquals($expectedValues, $actualValues);
	}

}