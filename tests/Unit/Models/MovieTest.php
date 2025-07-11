<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use KinopoiskDev\Models\Movie;
use KinopoiskDev\Models\ExternalId;
use KinopoiskDev\Models\Name;
use KinopoiskDev\Models\Rating;
use KinopoiskDev\Models\Votes;
use KinopoiskDev\Models\ShortImage;
use KinopoiskDev\Models\ItemName;
use KinopoiskDev\Models\PersonInMovie;
use KinopoiskDev\Models\FactInMovie;
use KinopoiskDev\Models\CurrencyValue;
use KinopoiskDev\Models\Fees;
use KinopoiskDev\Models\Premiere;
use KinopoiskDev\Models\Watchability;
use KinopoiskDev\Models\Audience;
use KinopoiskDev\Models\Lists;
use KinopoiskDev\Models\Networks;
use KinopoiskDev\Enums\MovieType;
use KinopoiskDev\Enums\MovieStatus;
use KinopoiskDev\Enums\RatingMpaa;
use KinopoiskDev\Exceptions\ValidationException;

/**
 * @group unit
 * @group models
 * @group movie
 */
class MovieTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function test_constructor_withValidData_createsInstance(): void
    {
        $movie = new Movie(
            id: 123,
            name: 'Test Movie',
            year: 2023,
            description: 'Test description'
        );

        $this->assertInstanceOf(Movie::class, $movie);
        $this->assertEquals(123, $movie->getId());
        $this->assertEquals('Test Movie', $movie->getName());
        $this->assertEquals(2023, $movie->getYear());
        $this->assertEquals('Test description', $movie->getDescription());
    }

    public function test_constructor_withNullValues_createsInstance(): void
    {
        $movie = new Movie();

        $this->assertInstanceOf(Movie::class, $movie);
        $this->assertNull($movie->getId());
        $this->assertNull($movie->getName());
        $this->assertNull($movie->getYear());
        $this->assertNull($movie->getDescription());
    }

    public function test_fromArray_withValidData_createsInstance(): void
    {
        $data = [
            'id' => 456,
            'name' => 'From Array Movie',
            'year' => 2022,
            'description' => 'Created from array',
            'type' => 'movie',
            'status' => 'completed',
            'ratingMpaa' => 'G',
            'isSeries' => false
        ];

        $movie = Movie::fromArray($data);

        $this->assertInstanceOf(Movie::class, $movie);
        $this->assertEquals(456, $movie->getId());
        $this->assertEquals('From Array Movie', $movie->getName());
        $this->assertEquals(2022, $movie->getYear());
        $this->assertEquals('Created from array', $movie->getDescription());
        $this->assertEquals(MovieType::MOVIE, $movie->getType());
        $this->assertEquals(MovieStatus::COMPLETED, $movie->getStatus());
        $this->assertEquals(RatingMpaa::G, $movie->getRatingMpaa());
        $this->assertFalse($movie->getIsSeries());
    }

    public function test_fromArray_withComplexData_createsInstance(): void
    {
        $data = [
            'id' => 789,
            'name' => 'Complex Movie',
            'externalId' => [
                'imdb' => 'tt1234567',
                'tmdb' => 123456
            ],
            'names' => [
                ['name' => 'English Name', 'language' => 'en'],
                ['name' => 'Russian Name', 'language' => 'ru']
            ],
            'rating' => [
                'kp' => 8.5,
                'imdb' => 8.2
            ],
            'votes' => [
                'kp' => 1000,
                'imdb' => 5000
            ],
            'genres' => [
                ['name' => 'Action'],
                ['name' => 'Drama']
            ],
            'countries' => [
                ['name' => 'USA'],
                ['name' => 'UK']
            ]
        ];

        $movie = Movie::fromArray($data);

        $this->assertInstanceOf(Movie::class, $movie);
        $this->assertEquals(789, $movie->getId());
        $this->assertEquals('Complex Movie', $movie->getName());
        $this->assertInstanceOf(ExternalId::class, $movie->getExternalId());
        $this->assertCount(2, $movie->getNames());
        $this->assertInstanceOf(Rating::class, $movie->getRating());
        $this->assertInstanceOf(Votes::class, $movie->getVotes());
        $this->assertCount(2, $movie->getGenres());
        $this->assertCount(2, $movie->getCountries());
    }

    public function test_fromJson_withValidJson_createsInstance(): void
    {
        $json = json_encode([
            'id' => 101,
            'name' => 'JSON Movie',
            'year' => 2021
        ]);

        $movie = Movie::fromJson($json);

        $this->assertInstanceOf(Movie::class, $movie);
        $this->assertEquals(101, $movie->getId());
        $this->assertEquals('JSON Movie', $movie->getName());
        $this->assertEquals(2021, $movie->getYear());
    }

    public function test_fromJson_withInvalidJson_throwsException(): void
    {
        $this->expectException(\JsonException::class);

        Movie::fromJson('invalid json');
    }

    public function test_validate_withValidData_returnsTrue(): void
    {
        $movie = new Movie(
            id: 123,
            name: 'Valid Movie',
            year: 2023
        );

        $result = $movie->validate();

        $this->assertTrue($result);
    }

    public function test_validate_withInvalidYear_throwsException(): void
    {
        $movie = new Movie(
            id: 123,
            name: 'Invalid Year Movie',
            year: 1800 // Too early
        );

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Год должен быть в диапазоне от 1888 до 2030');

        $movie->validate();
    }

    public function test_validate_withFutureYear_throwsException(): void
    {
        $movie = new Movie(
            id: 123,
            name: 'Future Movie',
            year: 2035 // Too late
        );

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Год должен быть в диапазоне от 1888 до 2030');

        $movie->validate();
    }

    public function test_getKinopoiskRating_withRating_returnsValue(): void
    {
        $rating = $this->createMock(Rating::class);
        $rating->method('getKp')->willReturn(8.5);

        $movie = new Movie(rating: $rating);

        $result = $movie->getKinopoiskRating();

        $this->assertEquals(8.5, $result);
    }

    public function test_getKinopoiskRating_withoutRating_returnsNull(): void
    {
        $movie = new Movie();

        $result = $movie->getKinopoiskRating();

        $this->assertNull($result);
    }

    public function test_getImdbRating_withRating_returnsValue(): void
    {
        $rating = $this->createMock(Rating::class);
        $rating->method('getImdb')->willReturn(8.2);

        $movie = new Movie(rating: $rating);

        $result = $movie->getImdbRating();

        $this->assertEquals(8.2, $result);
    }

    public function test_getImdbRating_withoutRating_returnsNull(): void
    {
        $movie = new Movie();

        $result = $movie->getImdbRating();

        $this->assertNull($result);
    }

    public function test_getPosterUrl_withPoster_returnsUrl(): void
    {
        $poster = $this->createMock(ShortImage::class);
        $poster->method('getUrl')->willReturn('https://example.com/poster.jpg');

        $movie = new Movie(poster: $poster);

        $result = $movie->getPosterUrl();

        $this->assertEquals('https://example.com/poster.jpg', $result);
    }

    public function test_getPosterUrl_withoutPoster_returnsNull(): void
    {
        $movie = new Movie();

        $result = $movie->getPosterUrl();

        $this->assertNull($result);
    }

    public function test_getGenreNames_withGenres_returnsNames(): void
    {
        $genres = [
            new ItemName(name: 'Action'),
            new ItemName(name: 'Drama'),
            new ItemName(name: 'Comedy')
        ];

        $movie = new Movie(genres: $genres);

        $result = $movie->getGenreNames();

        $this->assertEquals(['Action', 'Drama', 'Comedy'], $result);
    }

    public function test_getGenreNames_withoutGenres_returnsEmptyArray(): void
    {
        $movie = new Movie();

        $result = $movie->getGenreNames();

        $this->assertEquals([], $result);
    }

    public function test_getCountryNames_withCountries_returnsNames(): void
    {
        $countries = [
            new ItemName(name: 'USA'),
            new ItemName(name: 'UK'),
            new ItemName(name: 'France')
        ];

        $movie = new Movie(countries: $countries);

        $result = $movie->getCountryNames();

        $this->assertEquals(['USA', 'UK', 'France'], $result);
    }

    public function test_getCountryNames_withoutCountries_returnsEmptyArray(): void
    {
        $movie = new Movie();

        $result = $movie->getCountryNames();

        $this->assertEquals([], $result);
    }

    public function test_getImdbUrl_withExternalId_returnsUrl(): void
    {
        $externalId = $this->createMock(ExternalId::class);
        $externalId->method('getImdb')->willReturn('tt1234567');

        $movie = new Movie(externalId: $externalId);

        $result = $movie->getImdbUrl();

        $this->assertEquals('https://www.imdb.com/title/tt1234567/', $result);
    }

    public function test_getImdbUrl_withoutExternalId_returnsNull(): void
    {
        $movie = new Movie();

        $result = $movie->getImdbUrl();

        $this->assertNull($result);
    }

    public function test_getTmdbUrl_withExternalId_returnsUrl(): void
    {
        $externalId = $this->createMock(ExternalId::class);
        $externalId->method('getTmdb')->willReturn(123456);

        $movie = new Movie(externalId: $externalId);

        $result = $movie->getTmdbUrl();

        $this->assertEquals('https://www.themoviedb.org/movie/123456', $result);
    }

    public function test_getTmdbUrl_withoutExternalId_returnsNull(): void
    {
        $movie = new Movie();

        $result = $movie->getTmdbUrl();

        $this->assertNull($result);
    }

    public function test_toJson_withValidData_returnsJsonString(): void
    {
        $movie = new Movie(
            id: 123,
            name: 'JSON Test Movie',
            year: 2023
        );

        $json = $movie->toJson();
        $data = json_decode($json, true);

        $this->assertIsString($json);
        $this->assertEquals(123, $data['id']);
        $this->assertEquals('JSON Test Movie', $data['name']);
        $this->assertEquals(2023, $data['year']);
    }

    public function test_toArray_withIncludeNulls_returnsFullArray(): void
    {
        $movie = new Movie(
            id: 123,
            name: 'Array Test Movie',
            year: 2023
        );

        $array = $movie->toArray(true);

        $this->assertIsArray($array);
        $this->assertEquals(123, $array['id']);
        $this->assertEquals('Array Test Movie', $array['name']);
        $this->assertEquals(2023, $array['year']);
        $this->assertArrayHasKey('description', $array);
        $this->assertNull($array['description']);
    }

    public function test_toArray_withoutIncludeNulls_returnsFilteredArray(): void
    {
        $movie = new Movie(
            id: 123,
            name: 'Array Test Movie',
            year: 2023
        );

        $array = $movie->toArray(false);

        $this->assertIsArray($array);
        $this->assertEquals(123, $array['id']);
        $this->assertEquals('Array Test Movie', $array['name']);
        $this->assertEquals(2023, $array['year']);
        $this->assertArrayNotHasKey('description', $array);
    }

    /**
     * @dataProvider validYearProvider
     */
    public function test_validate_withValidYears_returnsTrue(int $year): void
    {
        $movie = new Movie(id: 123, name: 'Valid Year Movie', year: $year);

        $result = $movie->validate();

        $this->assertTrue($result);
    }

    public function validYearProvider(): array
    {
        return [
            'min_year' => [1888],
            'max_year' => [2030],
            'middle_year' => [2023],
            'old_year' => [1950],
            'recent_year' => [2020],
        ];
    }

    /**
     * @dataProvider invalidYearProvider
     */
    public function test_validate_withInvalidYears_throwsException(int $year): void
    {
        $movie = new Movie(id: 123, name: 'Invalid Year Movie', year: $year);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Год должен быть в диапазоне от 1888 до 2030');

        $movie->validate();
    }

    public function invalidYearProvider(): array
    {
        return [
            'too_early' => [1887],
            'too_late' => [2031],
            'negative' => [-1],
            'zero' => [0],
        ];
    }
} 