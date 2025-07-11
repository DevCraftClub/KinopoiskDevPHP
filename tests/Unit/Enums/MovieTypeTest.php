<?php

declare(strict_types=1);

namespace KinopoiskDev\Tests\Unit\Enums;

use PHPUnit\Framework\TestCase;
use KinopoiskDev\Enums\MovieType;

/**
 * @group unit
 * @group enums
 * @group movie-type
 */
class MovieTypeTest extends TestCase
{
    public function test_all_movie_types_have_correct_values(): void
    {
        $this->assertEquals('movie', MovieType::MOVIE->value);
        $this->assertEquals('tv-series', MovieType::TV_SERIES->value);
        $this->assertEquals('cartoon', MovieType::CARTOON->value);
        $this->assertEquals('anime', MovieType::ANIME->value);
        $this->assertEquals('animated-series', MovieType::ANIMATED_SERIES->value);
        $this->assertEquals('tv-show', MovieType::TV_SHOW->value);
    }

    public function test_getLabel_returns_correct_russian_labels(): void
    {
        $this->assertEquals('Фильм', MovieType::MOVIE->getLabel());
        $this->assertEquals('Сериал', MovieType::TV_SERIES->getLabel());
        $this->assertEquals('Мультфильм', MovieType::CARTOON->getLabel());
        $this->assertEquals('Аниме', MovieType::ANIME->getLabel());
        $this->assertEquals('Анимационный сериал', MovieType::ANIMATED_SERIES->getLabel());
        $this->assertEquals('ТВ-шоу', MovieType::TV_SHOW->getLabel());
    }

    public function test_movie_type_can_be_created_from_string(): void
    {
        $this->assertEquals(MovieType::MOVIE, MovieType::from('movie'));
        $this->assertEquals(MovieType::TV_SERIES, MovieType::from('tv-series'));
        $this->assertEquals(MovieType::CARTOON, MovieType::from('cartoon'));
        $this->assertEquals(MovieType::ANIME, MovieType::from('anime'));
        $this->assertEquals(MovieType::ANIMATED_SERIES, MovieType::from('animated-series'));
        $this->assertEquals(MovieType::TV_SHOW, MovieType::from('tv-show'));
    }

    public function test_movie_type_can_be_compared(): void
    {
        $this->assertTrue(MovieType::MOVIE === MovieType::from('movie'));
        $this->assertFalse(MovieType::MOVIE === MovieType::TV_SERIES);
    }

    public function test_movie_type_can_be_used_in_switch(): void
    {
        $type = MovieType::MOVIE;
        
        $result = match ($type) {
            MovieType::MOVIE => 'is_movie',
            MovieType::TV_SERIES => 'is_series',
            default => 'unknown'
        };
        
        $this->assertEquals('is_movie', $result);
    }

    public function test_all_cases_are_covered(): void
    {
        $cases = MovieType::cases();
        $this->assertCount(6, $cases);
        
        $expectedValues = ['movie', 'tv-series', 'cartoon', 'anime', 'animated-series', 'tv-show'];
        $actualValues = array_map(fn($case) => $case->value, $cases);
        
        $this->assertEquals($expectedValues, $actualValues);
    }
} 