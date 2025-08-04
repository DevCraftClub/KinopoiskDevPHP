<?php

declare(strict_types=1);

namespace KinopoiskDev\Tests\Unit\Models;

use KinopoiskDev\Enums\MovieStatus;
use KinopoiskDev\Enums\MovieType;
use KinopoiskDev\Enums\RatingMpaa;
use KinopoiskDev\Exceptions\ValidationException;
use KinopoiskDev\Models\ExternalId;
use KinopoiskDev\Models\Movie;
use KinopoiskDev\Models\Rating;
use KinopoiskDev\Models\ShortImage;
use KinopoiskDev\Models\Votes;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 * @group models
 * @group movie
 */
class MovieTest extends TestCase {

	public function test_constructor_withValidData_createsInstance(): void {
		$movie = new Movie(
			id         : 123,
			name       : 'Test Movie',
			year       : 2023,
			description: 'Test description',
		);

		$this->assertInstanceOf(Movie::class, $movie);
		$this->assertEquals(123, $movie->id);
		$this->assertEquals('Test Movie', $movie->name);
		$this->assertEquals(2023, $movie->year);
		$this->assertEquals('Test description', $movie->description);
	}

	public function test_constructor_withNullValues_createsInstance(): void {
		$movie = new Movie();

		$this->assertInstanceOf(Movie::class, $movie);
		$this->assertNull($movie->id);
		$this->assertNull($movie->name);
		$this->assertNull($movie->year);
		$this->assertNull($movie->description);
	}

	public function test_fromArray_withValidData_createsInstance(): void {
		$data = [
			'id'          => 456,
			'name'        => 'From Array Movie',
			'year'        => 2022,
			'description' => 'Created from array',
			'type'        => 'movie',
			'status'      => 'completed',
			'ratingMpaa'  => 'g',
			'isSeries'    => FALSE,
		];

		$movie = Movie::fromArray($data);

		$this->assertInstanceOf(Movie::class, $movie);
		$this->assertEquals(456, $movie->id);
		$this->assertEquals('From Array Movie', $movie->name);
		$this->assertEquals(2022, $movie->year);
		$this->assertEquals('Created from array', $movie->description);
		$this->assertEquals(MovieType::MOVIE, $movie->type);
		$this->assertEquals(MovieStatus::COMPLETED, $movie->status);
		$this->assertEquals(RatingMpaa::G, $movie->ratingMpaa);
		$this->assertFalse($movie->isSeries);
	}

	public function test_fromArray_withComplexData_createsInstance(): void {
		$data = [
			'id'         => 789,
			'name'       => 'Complex Movie',
			'externalId' => [
				'imdb' => 'tt1234567',
				'tmdb' => 123456,
			],
			'names'      => [
				['name' => 'English Name', 'language' => 'en'],
				['name' => 'Russian Name', 'language' => 'ru'],
			],
			'rating'     => [
				'kp'   => 8.5,
				'imdb' => 8.2,
			],
			'votes'      => [
				'kp'   => 1000,
				'imdb' => 5000,
			],
			'genres'     => [
				['name' => 'Action'],
				['name' => 'Drama'],
			],
			'countries'  => [
				['name' => 'USA'],
				['name' => 'UK'],
			],
		];

		$movie = Movie::fromArray($data);

		$this->assertInstanceOf(Movie::class, $movie);
		$this->assertEquals(789, $movie->id);
		$this->assertEquals('Complex Movie', $movie->name);
		$this->assertInstanceOf(ExternalId::class, $movie->externalId);
		$this->assertCount(2, $movie->names);
		$this->assertInstanceOf(Rating::class, $movie->rating);
		$this->assertInstanceOf(Votes::class, $movie->votes);
		$this->assertCount(2, $movie->genres);
		$this->assertCount(2, $movie->countries);
	}

	public function test_fromJson_withValidJson_createsInstance(): void {
		$json = json_encode([
			'id'   => 101,
			'name' => 'JSON Movie',
			'year' => 2021,
		]);

		$movie = Movie::fromJson($json);

		$this->assertInstanceOf(Movie::class, $movie);
		$this->assertEquals(101, $movie->id);
		$this->assertEquals('JSON Movie', $movie->name);
		$this->assertEquals(2021, $movie->year);
	}

	public function test_fromJson_withInvalidJson_throwsException(): void {
		$this->expectException(\JsonException::class);

		Movie::fromJson('invalid json');
	}

	public function test_validate_withValidData_returnsTrue(): void {
		$movie = new Movie(
			id  : 123,
			name: 'Valid Movie',
			year: 2023,
		);

		$result = $movie->validate();

		$this->assertTrue($result);
	}

	public function test_validate_withInvalidYear_throwsException(): void {
		$movie = new Movie(
			id  : 123,
			name: 'Invalid Year Movie',
			year: 1800, // Too early
		);

		$this->expectException(ValidationException::class);
		$this->expectExceptionMessage('Год должен быть в диапазоне от 1888 до 2030');

		$movie->validate();
	}

	public function test_validate_withFutureYear_throwsException(): void {
		$movie = new Movie(
			id  : 123,
			name: 'Future Movie',
			year: 2035, // Too late
		);

		$this->expectException(ValidationException::class);
		$this->expectExceptionMessage('Год должен быть в диапазоне от 1888 до 2030');

		$movie->validate();
	}

	public function test_getKinopoiskRating_withRating_returnsValue(): void {
		$rating = new Rating(kp: 8.5);

		$movie = new Movie(rating: $rating);

		$result = $movie->getKinopoiskRating();

		$this->assertEquals(8.5, $result);
	}

	public function test_getKinopoiskRating_withoutRating_returnsNull(): void {
		$movie = new Movie();

		$result = $movie->getKinopoiskRating();

		$this->assertNull($result);
	}

	public function test_getImdbRating_withRating_returnsValue(): void {
		$rating = new Rating(imdb: 8.2);

		$movie = new Movie(rating: $rating);

		$result = $movie->getImdbRating();

		$this->assertEquals(8.2, $result);
	}

	public function test_getImdbRating_withoutRating_returnsNull(): void {
		$movie = new Movie();

		$result = $movie->getImdbRating();

		$this->assertNull($result);
	}

	public function test_getPosterUrl_withPoster_returnsUrl(): void {
		$poster = new ShortImage(url: 'https://example.com/poster.jpg');

		$movie = new Movie(poster: $poster);

		$result = $movie->getPosterUrl();

		$this->assertEquals('https://example.com/poster.jpg', $result);
	}

	public function test_getPosterUrl_withoutPoster_returnsNull(): void {
		$movie = new Movie();

		$result = $movie->getPosterUrl();

		$this->assertNull($result);
	}

	public function test_getGenreNames_withGenres_returnsNames(): void {
		$movie = new Movie(
			genres: [
				new \KinopoiskDev\Models\ItemName('Action'),
				new \KinopoiskDev\Models\ItemName('Drama'),
			],
		);

		$result = $movie->getGenreNames();

		$this->assertEquals(['Action', 'Drama'], $result);
	}

	public function test_getGenreNames_withoutGenres_returnsEmptyArray(): void {
		$movie = new Movie();

		$result = $movie->getGenreNames();

		$this->assertEquals([], $result);
	}

	public function test_getCountryNames_withCountries_returnsNames(): void {
		$movie = new Movie(
			countries: [
				new \KinopoiskDev\Models\ItemName('USA'),
				new \KinopoiskDev\Models\ItemName('UK'),
			],
		);

		$result = $movie->getCountryNames();

		$this->assertEquals(['USA', 'UK'], $result);
	}

	public function test_getCountryNames_withoutCountries_returnsEmptyArray(): void {
		$movie = new Movie();

		$result = $movie->getCountryNames();

		$this->assertEquals([], $result);
	}

	public function test_getImdbUrl_withExternalId_returnsUrl(): void {
		$externalId = new ExternalId(imdb: 'tt1234567');

		$movie = new Movie(externalId: $externalId);

		$result = $movie->getImdbUrl();

		$this->assertEquals('https://www.imdb.com/title/tt1234567/', $result);
	}

	public function test_getImdbUrl_withoutExternalId_returnsNull(): void {
		$movie = new Movie();

		$result = $movie->getImdbUrl();

		$this->assertNull($result);
	}

	public function test_getTmdbUrl_withExternalId_returnsUrl(): void {
		$externalId = new ExternalId(tmdb: 123456);

		$movie = new Movie(externalId: $externalId);

		$result = $movie->getTmdbUrl();

		$this->assertEquals('https://www.themoviedb.org/movie/123456', $result);
	}

	public function test_getTmdbUrl_withoutExternalId_returnsNull(): void {
		$movie = new Movie();

		$result = $movie->getTmdbUrl();

		$this->assertNull($result);
	}

	public function test_toJson_withValidData_returnsJsonString(): void {
		$movie = new Movie(
			id  : 123,
			name: 'Test Movie',
			year: 2023,
		);

		$json = $movie->toJson();

		$this->assertIsString($json);
		$this->assertStringContainsString('"id":123', $json);
		$this->assertStringContainsString('"name":"Test Movie"', $json);
		$this->assertStringContainsString('"year":2023', $json);
	}

	public function test_toArray_withIncludeNulls_returnsFullArray(): void {
		$movie = new Movie(
			id  : 123,
			name: 'Test Movie',
			year: 2023,
		);

		$array = $movie->toArray(TRUE);

		$this->assertArrayHasKey('id', $array);
		$this->assertArrayHasKey('name', $array);
		$this->assertArrayHasKey('year', $array);
		$this->assertArrayHasKey('description', $array);
		$this->assertArrayHasKey('type', $array);
		$this->assertArrayHasKey('status', $array);
	}

	public function test_toArray_withoutIncludeNulls_returnsFilteredArray(): void {
		$movie = new Movie(
			id  : 123,
			name: 'Test Movie',
			year: 2023,
		);

		$array = $movie->toArray(FALSE);

		$this->assertArrayHasKey('id', $array);
		$this->assertArrayHasKey('name', $array);
		$this->assertArrayHasKey('year', $array);
		// Null values should be filtered out
		$this->assertArrayNotHasKey('description', $array);
		$this->assertArrayNotHasKey('type', $array);
		$this->assertArrayNotHasKey('status', $array);
	}

	/**
	 * @dataProvider validYearProvider
	 */
	public function test_validate_withValidYears_returnsTrue(): void {
		$year = $this->validYearProvider()[array_rand($this->validYearProvider())][0];
		$movie = new Movie(id: 123, year: $year);

		$result = $movie->validate();

		$this->assertTrue($result);
	}

	public function validYearProvider(): array {
		return [
			'minimum_year' => [1888],
			'current_year' => [2023],
			'maximum_year' => [2030],
			'middle_year'  => [2000],
		];
	}

	/**
	 * @dataProvider invalidYearProvider
	 */
	public function test_validate_withInvalidYears_throwsException(): void {
		$year = $this->invalidYearProvider()[array_rand($this->invalidYearProvider())][0];
		$movie = new Movie(id: 123, year: $year);

		$this->expectException(ValidationException::class);
		$this->expectExceptionMessage('Год должен быть в диапазоне от 1888 до 2030');

		$movie->validate();
	}

	public function invalidYearProvider(): array {
		return [
			'too_early' => [1887],
			'too_late'  => [2031],
			'negative'  => [-1],
			'zero'      => [0],
		];
	}

	protected function setUp(): void {
		parent::setUp();
	}

	protected function tearDown(): void {
		parent::tearDown();
	}

}