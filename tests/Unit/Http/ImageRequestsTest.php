<?php

declare(strict_types=1);

namespace KinopoiskDev\Tests\Unit\Http;

use KinopoiskDev\Exceptions\KinopoiskDevException;
use KinopoiskDev\Http\ImageRequests;
use Symfony\Component\Yaml\Yaml;

/**
 * @group http
 * @group image-requests
 */
class ImageRequestsTest extends BaseHttpTest {

    private ImageRequests $imageRequests;

    protected function setUp(): void {
        parent::setUp();
        $this->imageRequests = new ImageRequests(
            apiToken: $this->getApiToken(),
        );
    }

    public function test_getImages_real(): void {
        $result = $this->imageRequests->getImages(null, 1, 10);
        $this->assertNotEmpty($result->docs);
        $this->assertIsArray($result->docs);
        $firstImage = $result->docs[0];
        $this->assertNotEmpty($firstImage->movieId);
        $this->assertNotEmpty($firstImage->type);
        $this->assertNotEmpty($firstImage->height);
        $this->assertNotEmpty($firstImage->width);
    }

    public function test_getImagesByMovieId_real(): void {
        $result = $this->imageRequests->getImagesByMovieId(301, '', 1, 10);
        $this->assertNotEmpty($result->docs);
        $this->assertIsArray($result->docs);
        $firstImage = $result->docs[0];
        $this->assertNotEmpty($firstImage->movieId);
        $this->assertNotEmpty($firstImage->type);
        $this->assertNotEmpty($firstImage->height);
        $this->assertNotEmpty($firstImage->width);
    }

    public function test_getImagesByMovieId_withType_real(): void {
        $result = $this->imageRequests->getImagesByMovieId(301, 'cover', 1, 10);
        $this->assertNotEmpty($result->docs);
        $this->assertIsArray($result->docs);
        $firstImage = $result->docs[0];
        $this->assertNotEmpty($firstImage->movieId);
        $this->assertNotEmpty($firstImage->type);
        $this->assertNotEmpty($firstImage->height);
        $this->assertNotEmpty($firstImage->width);
    }

    public function test_getHighRatedPosters_real(): void {
        $result = $this->imageRequests->getHighRatedPosters(7.0, 1, 10);
        $this->assertNotEmpty($result->docs);
        $this->assertIsArray($result->docs);
        $firstImage = $result->docs[0];
        $this->assertNotEmpty($firstImage->movieId);
        $this->assertNotEmpty($firstImage->type);
        $this->assertNotEmpty($firstImage->height);
        $this->assertNotEmpty($firstImage->width);
    }

    public function test_getImages_withInvalidLimit_throwsException(): void {
        $this->expectException(\KinopoiskDev\Exceptions\KinopoiskDevException::class);
        $this->expectExceptionMessage('Лимит не должен превышать 250');
        $this->imageRequests->getImages(null, 1, 251);
    }

    public function test_getImages_withInvalidPage_throwsException(): void {
        $this->expectException(\KinopoiskDev\Exceptions\KinopoiskDevException::class);
        $this->expectExceptionMessage('Номер страницы не должен быть меньше 1');
        $this->imageRequests->getImages(null, 0, 10);
    }
}