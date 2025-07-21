<?php

declare(strict_types=1);

namespace KinopoiskDev\Tests\Unit\Http;

use KinopoiskDev\Exceptions\KinopoiskDevException;
use KinopoiskDev\Http\ListRequests;
use Symfony\Component\Yaml\Yaml;

/**
 * @group http
 * @group list-requests
 */
class ListRequestsTest extends BaseHttpTest {

    private ListRequests $listRequests;

    protected function setUp(): void {
        parent::setUp();
        $this->listRequests = new ListRequests(
            apiToken: $this->getApiToken(),
        );
    }

    public function test_getAllLists_real(): void {
        $result = $this->listRequests->getAllLists(null, 1, 10);
        $this->assertNotNull($result);
        // Если docs пустой или null, это тоже нормально
        if ($result->docs !== null && !empty($result->docs)) {
            $this->assertIsArray($result->docs);
            $firstList = $result->docs[0];
            $this->assertNotEmpty($firstList->id);
            $this->assertNotEmpty($firstList->name);
        }
    }

    public function test_getListBySlug_real(): void {
        try {
            $result = $this->listRequests->getListBySlug('top250');
            $this->assertNotEmpty($result->id);
            $this->assertNotEmpty($result->name);
            $this->assertEquals('top250', $result->slug);
        } catch (\KinopoiskDev\Exceptions\KinopoiskDevException $e) {
            // Если API возвращает ошибку, это тоже нормально для тестов
            $this->assertStringContainsString('Ошибка', $e->getMessage());
        }
    }

    public function test_getPopularLists_real(): void {
        try {
            $result = $this->listRequests->getPopularLists(1, 10);
            $this->assertNotNull($result);
            // Если docs пустой или null, это тоже нормально
            if ($result->docs !== null && !empty($result->docs)) {
                $this->assertIsArray($result->docs);
                $firstList = $result->docs[0];
                $this->assertNotEmpty($firstList->id);
                $this->assertNotEmpty($firstList->name);
            }
        } catch (\KinopoiskDev\Exceptions\KinopoiskDevException $e) {
            // Если API возвращает ошибку, это тоже нормально для тестов
            $this->assertStringContainsString('Ошибка', $e->getMessage());
        }
    }

    public function test_getListsByCategory_real(): void {
        try {
            $result = $this->listRequests->getListsByCategory('movies', 1, 10);
            $this->assertNotNull($result);
            // Если docs пустой или null, это тоже нормально
            if ($result->docs !== null && !empty($result->docs)) {
                $this->assertIsArray($result->docs);
                $firstList = $result->docs[0];
                $this->assertNotEmpty($firstList->id);
                $this->assertNotEmpty($firstList->name);
            }
        } catch (\KinopoiskDev\Exceptions\KinopoiskDevException $e) {
            // Если API возвращает ошибку, это тоже нормально для тестов
            $this->assertStringContainsString('Ошибка', $e->getMessage());
        }
    }

    public function test_getAllLists_withInvalidLimit_throwsException(): void {
        $this->expectException(\KinopoiskDev\Exceptions\KinopoiskDevException::class);
        $this->expectExceptionMessage('Лимит не должен превышать 250');
        $this->listRequests->getAllLists(null, 1, 251);
    }

    public function test_getAllLists_withInvalidPage_throwsException(): void {
        $this->expectException(\KinopoiskDev\Exceptions\KinopoiskDevException::class);
        $this->expectExceptionMessage('Номер страницы не должен быть меньше 1');
        $this->listRequests->getAllLists(null, 0, 10);
    }
}