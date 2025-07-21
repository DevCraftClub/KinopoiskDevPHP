<?php

declare(strict_types=1);

namespace KinopoiskDev\Tests\Unit\Http;

use KinopoiskDev\Exceptions\KinopoiskDevException;
use KinopoiskDev\Http\SeasonRequests;
use Symfony\Component\Yaml\Yaml;

/**
 * @group http
 * @group season-requests
 */
class SeasonRequestsTest extends BaseHttpTest {

    private SeasonRequests $seasonRequests;

    protected function setUp(): void {
        parent::setUp();
        $this->seasonRequests = new SeasonRequests(
            apiToken: $this->getApiToken(),
        );
    }

    public function test_searchSeasons_real(): void {
        $result = $this->seasonRequests->searchSeasons(null, 1, 10);
        $this->assertNotEmpty($result->docs);
        $this->assertIsArray($result->docs);
        $firstSeason = $result->docs[0];
        $this->assertNotEmpty($firstSeason->movieId);
        $this->assertNotEmpty($firstSeason->number);
        $this->assertNotEmpty($firstSeason->name);
    }

    public function test_getSeasonsForMovie_real(): void {
        $result = $this->seasonRequests->getSeasonsForMovie(464963);
        $this->assertNotNull($result);
        $this->assertNotEmpty($result->docs);
        $this->assertIsArray($result->docs);
        $firstSeason = $result->docs[0];
        $this->assertNotEmpty($firstSeason->movieId);
        $this->assertNotEmpty($firstSeason->number);
        $this->assertNotEmpty($firstSeason->name);
    }

    public function test_getSeasonByNumber_real(): void {
        $result = $this->seasonRequests->getSeasonByNumber(464963, 1);
		$this->assertNotNull($result);
        $this->assertNotEmpty($result->id);
        $this->assertNotEmpty($result->name);
    }

    public function test_searchSeasons_withInvalidLimit_throwsException(): void {
        $this->expectException(\KinopoiskDev\Exceptions\KinopoiskDevException::class);
        $this->expectExceptionMessage('Лимит не должен превышать 250');
        $this->seasonRequests->searchSeasons(null, 1, 251);
    }

    public function test_searchSeasons_withInvalidPage_throwsException(): void {
        $this->expectException(\KinopoiskDev\Exceptions\KinopoiskDevException::class);
        $this->expectExceptionMessage('Номер страницы не должен быть меньше 1');
        $this->seasonRequests->searchSeasons(null, 0, 10);
    }
}