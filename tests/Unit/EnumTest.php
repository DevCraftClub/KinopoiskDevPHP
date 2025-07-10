<?php

namespace KinopoiskDev\Tests\Unit;

use KinopoiskDev\Enums\MovieType;
use KinopoiskDev\Enums\MovieStatus;
use KinopoiskDev\Enums\RatingMpaa;
use KinopoiskDev\Enums\PersonProfession;
use KinopoiskDev\Enums\PersonSex;
use KinopoiskDev\Models\Movie;
use KinopoiskDev\Models\Person;
use KinopoiskDev\Models\LinkedMovie;
use PHPUnit\Framework\TestCase;

class EnumTest extends TestCase
{
    public function testMovieEnums()
    {
        $movieData = [
            'id' => 1,
            'type' => 'movie',
            'status' => 'completed',
            'ratingMpaa' => 'pg13',
        ];
        
        $movie = Movie::fromArray($movieData);
        
        $this->assertInstanceOf(MovieType::class, $movie->type);
        $this->assertEquals(MovieType::MOVIE, $movie->type);
        $this->assertEquals('movie', $movie->type->value);
        
        $this->assertInstanceOf(MovieStatus::class, $movie->status);
        $this->assertEquals(MovieStatus::COMPLETED, $movie->status);
        $this->assertEquals('completed', $movie->status->value);
        
        $this->assertInstanceOf(RatingMpaa::class, $movie->ratingMpaa);
        $this->assertEquals(RatingMpaa::PG13, $movie->ratingMpaa);
        $this->assertEquals('pg13', $movie->ratingMpaa->value);
        
        $movieArray = $movie->toArray();
        
        $this->assertEquals('movie', $movieArray['type']);
        $this->assertEquals('completed', $movieArray['status']);
        $this->assertEquals('pg13', $movieArray['ratingMpaa']);
    }
    
    public function testPersonEnums()
    {
        $personData = [
            'id' => 1,
            'profession' => [PersonProfession::ACTOR],
            'sex' => 'male',
        ];
        
        $person = Person::fromArray($personData);
        
        $this->assertIsArray($person->profession);
        $this->assertCount(1, $person->profession);
        $this->assertContains('actor', $person->profession);
        
        $this->assertInstanceOf(PersonSex::class, $person->sex);
        $this->assertEquals(PersonSex::MALE, $person->sex);
        $this->assertEquals('male', $person->sex->value);
        
        $personArray = $person->toArray();
        
        $this->assertIsArray($personArray['profession']);
        $this->assertContains('actor', $personArray['profession']);
        $this->assertEquals('male', $personArray['sex']);
    }
    
    public function testLinkedMovieEnums()
    {
        $linkedMovieData = [
            'id' => 1,
            'type' => 'tv-series',
        ];
        
        $linkedMovie = LinkedMovie::fromArray($linkedMovieData);
        
        $this->assertInstanceOf(MovieType::class, $linkedMovie->type);
        $this->assertEquals(MovieType::TV_SERIES, $linkedMovie->type);
        $this->assertEquals('tv-series', $linkedMovie->type->value);
        
        $linkedMovieArray = $linkedMovie->toArray();
        
        $this->assertEquals('tv-series', $linkedMovieArray['type']);
    }
}