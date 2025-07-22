<?php

declare(strict_types=1);

namespace KinopoiskDev\Tests\Unit\Http;

use KinopoiskDev\Enums\ListCategory;
use KinopoiskDev\Http\ListRequests;

/**
 * @group http
 * @group list-requests
 */
class ListRequestsTest extends BaseHttpTest {

	private ListRequests $listRequests;

	public function test_getAllLists_real(): void {
		$result = $this->listRequests->getAllLists();
		$this->assertNotNull($result);
		// Если docs пустой или null, это тоже нормально
		if ($result->docs !== NULL && !empty($result->docs)) {
			$this->assertIsArray($result->docs);
			$firstList = $result->docs[0];
			$this->assertNotEmpty($firstList->name);
		}
	}

	public function test_getListBySlug_real(): void {
		$result = $this->listRequests->getListBySlug('top250');
		$this->assertNotEmpty($result->name);
		$this->assertEquals('top250', $result->slug);
	}

	public function test_getListsByCategory_real(): void {
		$result = $this->listRequests->getListsByCategory(ListCategory::MOVIE);
		$this->assertNotNull($result);
		$this->assertNotEmpty($result->docs);
		$this->assertIsArray($result->docs);

		$firstList = $result->docs[0];
		$this->assertNotEmpty($firstList->name);
		$this->assertNotEmpty($firstList->category);
		$this->assertNotEmpty($firstList->slug);
	}

	public function test_getAllLists_withInvalidLimit_throwsException(): void {
		$this->expectException(\KinopoiskDev\Exceptions\KinopoiskDevException::class);
		$this->expectExceptionMessage('Лимит не должен превышать 250');
		$this->listRequests->getAllLists(NULL, 1, 251);
	}

	public function test_getAllLists_withInvalidPage_throwsException(): void {
		$this->expectException(\KinopoiskDev\Exceptions\KinopoiskDevException::class);
		$this->expectExceptionMessage('Номер страницы не должен быть меньше 1');
		$this->listRequests->getAllLists(NULL, 0, 10);
	}

	protected function setUp(): void {
		parent::setUp();
		$this->listRequests = new ListRequests(
			apiToken: $this->getApiToken(),
		);
	}

}