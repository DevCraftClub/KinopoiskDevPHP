<?php

declare(strict_types=1);

namespace KinopoiskDev\Tests\Unit\Http;

use KinopoiskDev\Exceptions\KinopoiskDevException;
use KinopoiskDev\Filter\MovieSearchFilter;
use KinopoiskDev\Http\MovieRequests;
use Symfony\Component\Yaml\Yaml;

/**
 * @group http
 * @group movie-requests
 */
class MovieRequestsTest extends BaseHttpTest {

    private MovieRequests $movieRequests;

    protected function setUp(): void {
        parent::setUp();
        $this->movieRequests = new MovieRequests(
            apiToken: $this->getApiToken(),
        );
    }

    public function test_getMovieById_real(): void {
        $result = $this->movieRequests->getMovieById(8124);
        $this->assertNotEmpty($result->id);
        $this->assertNotEmpty($result->name);
        $this->assertNotEmpty($result->type);
        $this->assertNotEmpty($result->year);
    }

    public function test_searchByName_real(): void {
        $result = $this->movieRequests->searchByName('матрица');
        $this->assertNotEmpty($result->docs);
        $this->assertIsArray($result->docs);
        $firstMovie = $result->docs[0];
        $this->assertNotEmpty($firstMovie->id);
        $this->assertNotEmpty($firstMovie->type);
        $this->assertNotEmpty($firstMovie->year);
    }

    public function test_searchMovies_withYear_real(): void {
        $filter = new \KinopoiskDev\Filter\MovieSearchFilter();
        $filter->year(1999);
        $result = $this->movieRequests->searchMovies($filter);
        $this->assertNotEmpty($result->docs);
        $this->assertIsArray($result->docs);
        foreach ($result->docs as $movie) {
            $this->assertNotEmpty($movie->id);
            $this->assertNotEmpty($movie->type);
            $this->assertNotEmpty($movie->year);
        }
    }

    public function test_getMoviesByCountry_real(): void {
        $filter = new \KinopoiskDev\Filter\MovieSearchFilter();
        $filter->countries('США');
        $result = $this->movieRequests->searchMovies($filter);
        $this->assertNotEmpty($result->docs);
        $this->assertIsArray($result->docs);
        foreach ($result->docs as $movie) {
            $this->assertNotEmpty($movie->id);
            $this->assertNotEmpty($movie->type);
            $this->assertNotEmpty($movie->year);
        }
    }

    public function test_getMoviesByGenre_real(): void {
        $filter = new \KinopoiskDev\Filter\MovieSearchFilter();
        $filter->genres('фантастика');
        $result = $this->movieRequests->searchMovies($filter);
        $this->assertNotNull($result->docs);
        $this->assertIsArray($result->docs);
        foreach ($result->docs as $movie) {
            $this->assertNotEmpty($movie->id);
            $this->assertNotEmpty($movie->type);
            $this->assertIsInt($movie->year);
        }
    }

    public function test_getRandomMovie_real(): void {
        $result = $this->movieRequests->getRandomMovie();
        $this->assertNotEmpty($result->id);
        $this->assertNotEmpty($result->name);
        $this->assertNotEmpty($result->type);
        $this->assertIsInt($result->year);
    }

    public function test_getMovieById_withInvalidId_throwsException(): void {
        $this->expectException(\KinopoiskDev\Exceptions\KinopoiskDevException::class);
        $this->movieRequests->getMovieById(999999999);
    }
}