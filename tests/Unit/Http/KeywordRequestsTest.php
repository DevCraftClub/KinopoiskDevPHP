<?php

declare(strict_types=1);

namespace KinopoiskDev\Tests\Unit\Http;

use KinopoiskDev\Exceptions\KinopoiskDevException;
use KinopoiskDev\Http\KeywordRequests;
use Symfony\Component\Yaml\Yaml;

/**
 * @group http
 * @group keyword-requests
 */
class KeywordRequestsTest extends BaseHttpTest {

    private KeywordRequests $keywordRequests;

    protected function setUp(): void {
        parent::setUp();
        $this->keywordRequests = new KeywordRequests(
            apiToken: $this->getApiToken(),
        );
    }

    public function test_searchKeywords_real(): void {
        $result = $this->keywordRequests->searchKeywords(null, 1, 10);
        $this->assertNotEmpty($result->docs);
        $this->assertIsArray($result->docs);
        $firstKeyword = $result->docs[0];
        $this->assertNotEmpty($firstKeyword->id);
        $this->assertNotEmpty($firstKeyword->title);
    }

    public function test_getKeywordById_real(): void {
        $result = $this->keywordRequests->getKeywordById(1);
        $this->assertNotEmpty($result->id);
        $this->assertNotEmpty($result->title);
        $this->assertIsInt($result->id);
        $this->assertIsString($result->title);
    }

    public function test_getKeywordsForMovie_real(): void {
        $result = $this->keywordRequests->getKeywordsForMovie(301, 1, 10);
        $this->assertNotEmpty($result->docs);
        $this->assertIsArray($result->docs);
        $firstKeyword = $result->docs[0];
        $this->assertNotEmpty($firstKeyword->id);
        $this->assertNotEmpty($firstKeyword->title);
    }

    public function test_getKeywordsByTitle_real(): void {
        $result = $this->keywordRequests->getKeywordsByTitle('Побег');
        $this->assertNotEmpty($result->docs);
        $this->assertIsArray($result->docs);
        $firstKeyword = $result->docs[0];
        $this->assertNotEmpty($firstKeyword->id);
        $this->assertNotEmpty($firstKeyword->title);
    }

    public function test_getPopularKeywords_real(): void {
        $result = $this->keywordRequests->getPopularKeywords();
        $this->assertNotEmpty($result->docs);
        $this->assertIsArray($result->docs);
        $firstKeyword = $result->docs[0];
        $this->assertNotEmpty($firstKeyword->id);
        $this->assertNotEmpty($firstKeyword->title);
    }

    public function test_searchKeywords_withInvalidLimit_throwsException(): void {
        $this->expectException(\KinopoiskDev\Exceptions\KinopoiskDevException::class);
        $this->expectExceptionMessage('Лимит не должен превышать 250');
        $this->keywordRequests->searchKeywords(null, 1, 251);
    }

    public function test_searchKeywords_withInvalidPage_throwsException(): void {
        $this->expectException(\KinopoiskDev\Exceptions\KinopoiskDevException::class);
        $this->expectExceptionMessage('Номер страницы не должен быть меньше 1');
        $this->keywordRequests->searchKeywords(null, 0, 10);
    }
}