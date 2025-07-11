<?php

declare(strict_types=1);

namespace KinopoiskDev\Tests\Unit\Utils;

use KinopoiskDev\Models\ItemName;
use KinopoiskDev\Utils\DataManager;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 * @group utils
 * @group data-manager
 */
class DataManagerTest extends TestCase {

	public function test_constructor_createsInstance(): void {
		// DataManager is abstract, so we can't test constructor
		$this->assertTrue(TRUE);
	}

	public function test_parseObjectArray_withValidData_returnsObjects(): void {
		$data   = [
			["name" => "Test 1"],
			["name" => "Test 2"],
		];
		$result = DataManager::parseObjectArray(["items" => $data], "items", \KinopoiskDev\Models\ItemName::class);
		$this->assertCount(2, $result);
		$this->assertEquals("Test 1", $result[0]->name);
		$this->assertEquals("Test 2", $result[1]->name);
	}

	public function test_parseObjectArray_withEmptyArray_returnsEmptyArray(): void {
		$data   = ['items' => []];
		$result = DataManager::parseObjectArray($data, 'items', ItemName::class);

		$this->assertIsArray($result);
		$this->assertEmpty($result);
	}

	public function test_parseObjectArray_withNullData_returnsEmptyArray(): void {
		$data   = ['items' => NULL];
		$result = DataManager::parseObjectArray($data, 'items', ItemName::class);

		$this->assertIsArray($result);
		$this->assertEmpty($result);
	}

	public function test_parseObjectArray_withInvalidClass_throwsException(): void {
		$data = ['items' => [['name' => 'Test']]];

		$this->expectException(\KinopoiskDev\Exceptions\KinopoiskDevException::class);

		DataManager::parseObjectArray($data, 'items', 'NonExistentClass');
	}

	public function test_parseObjectArray_withComplexData_returnsObjects(): void {
		$data   = [
			["name" => "Alpha"],
			["name" => "Beta"],
		];
		$result = DataManager::parseObjectArray(["names" => $data], "names", \KinopoiskDev\Models\Name::class);
		$this->assertCount(2, $result);
		$this->assertEquals("Alpha", $result[0]->name);
		$this->assertEquals("Beta", $result[1]->name);
	}

	public function test_parseObjectArray_withPersonData_returnsObjects(): void {
		$data   = [
			["id" => 1, "name" => "Ivan"],
			["id" => 2, "name" => "Petr"],
		];
		$result = DataManager::parseObjectArray(["persons" => $data], "persons", \KinopoiskDev\Models\PersonInMovie::class);
		$this->assertCount(2, $result);
		$this->assertEquals(1, $result[0]->id);
		$this->assertEquals("Ivan", $result[0]->name);
		$this->assertEquals(2, $result[1]->id);
		$this->assertEquals("Petr", $result[1]->name);
	}

	public function test_parseObjectArray_withMissingData_handlesNullValues(): void {
		$data = [
			["name" => "Test"],
			[],
		];
		$this->expectException(\TypeError::class);
		DataManager::parseObjectArray(["items" => $data], "items", \KinopoiskDev\Models\ItemName::class);
	}

	public function test_parseObjectArray_withInvalidDataStructure_handlesGracefully(): void {
		$data = [
			["name" => "Test 1"],
			["invalid" => "Test 2"],
			[],
			NULL,
		];
		$this->expectException(\TypeError::class);
		DataManager::parseObjectArray(["items" => $data], "items", \KinopoiskDev\Models\ItemName::class);
	}

	public function test_parseObjectArray_withNestedData_returnsObjects(): void {
		$data   = [
			["id" => 1, "name" => "Ivan", "roles" => [["role" => "actor"]]],
			["id" => 2, "name" => "Petr", "roles" => [["role" => "director"]]],
		];
		$result = DataManager::parseObjectArray(["persons" => $data], "persons", \KinopoiskDev\Models\PersonInMovie::class);
		$this->assertCount(2, $result);
		$this->assertEquals(1, $result[0]->id);
		$this->assertEquals("Ivan", $result[0]->name);
		$this->assertEquals(2, $result[1]->id);
		$this->assertEquals("Petr", $result[1]->name);
	}

	public function test_parseObjectArray_withEmptyObjectData_returnsObjects(): void {
		$data   = [
			["name" => "Test"],
		];
		$result = DataManager::parseObjectArray(["items" => $data], "items", \KinopoiskDev\Models\ItemName::class);
		$this->assertCount(1, $result);
		$this->assertEquals("Test", $result[0]->name);
	}

	public function test_parseObjectArray_withLargeDataset_handlesEfficiently(): void {
		$data = [];
		for ($i = 0; $i < 1000; $i++) {
			$data[] = ["name" => "Item $i"];
		}
		$result = DataManager::parseObjectArray(["items" => $data], "items", \KinopoiskDev\Models\ItemName::class);
		$this->assertCount(1000, $result);
		$this->assertEquals("Item 0", $result[0]->name);
		$this->assertEquals("Item 999", $result[999]->name);
	}

	public function test_parseObjectArray_withSpecialCharacters_handlesCorrectly(): void {
		$data   = [
			["name" => "Тест"],
			["name" => "特殊字符"],
		];
		$result = DataManager::parseObjectArray(["items" => $data], "items", \KinopoiskDev\Models\ItemName::class);
		$this->assertCount(2, $result);
		$this->assertEquals("Тест", $result[0]->name);
		$this->assertEquals("特殊字符", $result[1]->name);
	}

	public function test_parseObjectArray_withUnicodeCharacters_handlesCorrectly(): void {
		$data   = [
			["name" => "😀"],
			["name" => "🚀"],
		];
		$result = DataManager::parseObjectArray(["items" => $data], "items", \KinopoiskDev\Models\ItemName::class);
		$this->assertCount(2, $result);
		$this->assertEquals("😀", $result[0]->name);
		$this->assertEquals("🚀", $result[1]->name);
	}

	public function test_parseObjectArray_withMixedDataTypes_handlesCorrectly(): void {
		$data = [
			["name" => "Test"],
			["name" => 123],
			["name" => TRUE],
		];
		$this->expectException(\TypeError::class);
		DataManager::parseObjectArray(["items" => $data], "items", \KinopoiskDev\Models\ItemName::class);
	}

	protected function setUp(): void {
		parent::setUp();
	}

	protected function tearDown(): void {
		parent::tearDown();
	}

}