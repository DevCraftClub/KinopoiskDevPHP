<?php

declare(strict_types=1);

namespace KinopoiskDev\Tests\Unit\Enums;

use PHPUnit\Framework\TestCase;
use KinopoiskDev\Enums\RatingMpaa;

/**
 * @group unit
 * @group enums
 * @group rating-mpaa
 */
class RatingMpaaTest extends TestCase
{
    public function test_all_rating_mpaa_have_correct_values(): void
    {
        $this->assertEquals('G', RatingMpaa::G->value);
        $this->assertEquals('PG', RatingMpaa::PG->value);
        $this->assertEquals('PG-13', RatingMpaa::PG_13->value);
        $this->assertEquals('R', RatingMpaa::R->value);
        $this->assertEquals('NC-17', RatingMpaa::NC_17->value);
    }

    public function test_rating_mpaa_can_be_created_from_string(): void
    {
        $this->assertEquals(RatingMpaa::G, RatingMpaa::from('G'));
        $this->assertEquals(RatingMpaa::PG, RatingMpaa::from('PG'));
        $this->assertEquals(RatingMpaa::PG_13, RatingMpaa::from('PG-13'));
        $this->assertEquals(RatingMpaa::R, RatingMpaa::from('R'));
        $this->assertEquals(RatingMpaa::NC_17, RatingMpaa::from('NC-17'));
    }

    public function test_rating_mpaa_can_be_compared(): void
    {
        $this->assertTrue(RatingMpaa::G === RatingMpaa::from('G'));
        $this->assertFalse(RatingMpaa::G === RatingMpaa::PG);
    }

    public function test_all_cases_are_covered(): void
    {
        $cases = RatingMpaa::cases();
        $this->assertCount(5, $cases);
        
        $expectedValues = ['G', 'PG', 'PG-13', 'R', 'NC-17'];
        $actualValues = array_map(fn($case) => $case->value, $cases);
        
        $this->assertEquals($expectedValues, $actualValues);
    }
} 