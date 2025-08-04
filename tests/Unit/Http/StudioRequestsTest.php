<?php

declare(strict_types=1);

namespace KinopoiskDev\Tests\Unit\Http;

use KinopoiskDev\Exceptions\KinopoiskDevException;
use KinopoiskDev\Http\StudioRequests;
use Symfony\Component\Yaml\Yaml;

/**
 * @group http
 * @group studio-requests
 */
class StudioRequestsTest extends BaseHttpTest {

    private StudioRequests $studioRequests;

    protected function setUp(): void {
        parent::setUp();
        $this->studioRequests = new StudioRequests(
            apiToken: $this->getApiToken(),
        );
    }

    public function test_searchStudios_real(): void {
        $result = $this->studioRequests->searchStudios(null, 1, 10);
        $this->assertNotEmpty($result->docs);
        $this->assertIsArray($result->docs);
        $firstStudio = $result->docs[0];
        $this->assertNotEmpty($firstStudio->id);
        $this->assertNotEmpty($firstStudio->title);
        $this->assertNotEmpty($firstStudio->type);
    }

    public function test_getStudioById_real(): void {
        $result = $this->studioRequests->getStudioById(1);
        $this->assertNotEmpty($result->id);
        $this->assertNotEmpty($result->title);
        $this->assertNotEmpty($result->type);
    }

    public function test_getStudiosForMovie_real(): void {
        $result = $this->studioRequests->getStudiosForMovie(301, 1, 10);
        $this->assertNotEmpty($result->docs);
        $this->assertIsArray($result->docs);
        $firstStudio = $result->docs[0];
        $this->assertNotEmpty($firstStudio->id);
        $this->assertNotEmpty($firstStudio->title);
        $this->assertNotEmpty($firstStudio->type);
    }

    public function test_getProductionStudios_real(): void {
        $result = $this->studioRequests->getProductionStudios(1, 10);
        $this->assertNotEmpty($result->docs);
        $this->assertIsArray($result->docs);
        foreach ($result->docs as $studio) {
            $this->assertNotEmpty($studio->id);
            $this->assertNotEmpty($studio->title);
            $this->assertNotEmpty($studio->type);
        }
    }

    public function test_getDubbingStudios_real(): void {
        $result = $this->studioRequests->getDubbingStudios(1, 10);
        $this->assertNotEmpty($result->docs);
        $this->assertIsArray($result->docs);
        foreach ($result->docs as $studio) {
            $this->assertNotEmpty($studio->id);
            $this->assertNotEmpty($studio->title);
            $this->assertNotEmpty($studio->type);
        }
    }

    public function test_getStudiosByTitle_real(): void {
        $result = $this->studioRequests->getStudiosByTitle('Warner', 1, 10);
        $this->assertNotEmpty($result->docs);
        $this->assertIsArray($result->docs);
        foreach ($result->docs as $studio) {
            $this->assertNotEmpty($studio->id);
			$this->assertNotEmpty($studio->title);
			$this->assertIsArray($studio->movies);
        }
    }

    public function test_searchStudios_withInvalidLimit_throwsException(): void {
        $this->expectException(\KinopoiskDev\Exceptions\KinopoiskDevException::class);
        $this->expectExceptionMessage('Лимит не должен превышать 250');
        $this->studioRequests->searchStudios(null, 1, 251);
    }

    public function test_searchStudios_withInvalidPage_throwsException(): void {
        $this->expectException(\KinopoiskDev\Exceptions\KinopoiskDevException::class);
        $this->expectExceptionMessage('Номер страницы не должен быть меньше 1');
        $this->studioRequests->searchStudios(null, 0, 10);
    }
}