<?php

declare(strict_types=1);

namespace KinopoiskDev\Tests\Unit\Enums;

use PHPUnit\Framework\TestCase;
use KinopoiskDev\Enums\MovieStatus;

/**
 * @group unit
 * @group enums
 * @group movie-status
 */
class MovieStatusTest extends TestCase
{
    public function test_all_movie_statuses_have_correct_values(): void
    {
        $this->assertEquals('filming', MovieStatus::FILMING->value);
        $this->assertEquals('pre-production', MovieStatus::PRE_PRODUCTION->value);
        $this->assertEquals('post-production', MovieStatus::POST_PRODUCTION->value);
        $this->assertEquals('completed', MovieStatus::COMPLETED->value);
        $this->assertEquals('announced', MovieStatus::ANNOUNCED->value);
    }

    public function test_movie_status_can_be_created_from_string(): void
    {
        $this->assertEquals(MovieStatus::FILMING, MovieStatus::from('filming'));
        $this->assertEquals(MovieStatus::PRE_PRODUCTION, MovieStatus::from('pre-production'));
        $this->assertEquals(MovieStatus::POST_PRODUCTION, MovieStatus::from('post-production'));
        $this->assertEquals(MovieStatus::COMPLETED, MovieStatus::from('completed'));
        $this->assertEquals(MovieStatus::ANNOUNCED, MovieStatus::from('announced'));
    }

    public function test_movie_status_can_be_compared(): void
    {
        $this->assertTrue(MovieStatus::FILMING === MovieStatus::from('filming'));
        $this->assertFalse(MovieStatus::FILMING === MovieStatus::PRE_PRODUCTION);
    }

    public function test_all_cases_are_covered(): void
    {
        $cases = MovieStatus::cases();
        $this->assertCount(5, $cases);
        
        $expectedValues = ['filming', 'pre-production', 'completed', 'announced', 'post-production'];
        $actualValues = array_map(fn($case) => $case->value, $cases);
        
        $this->assertEquals($expectedValues, $actualValues);
    }
} 