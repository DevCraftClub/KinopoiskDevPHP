<?php

declare(strict_types=1);

namespace Tests\Unit\Utils;

use PHPUnit\Framework\TestCase;
use KinopoiskDev\Utils\DataManager;
use KinopoiskDev\Models\ItemName;
use KinopoiskDev\Models\Name;
use KinopoiskDev\Models\PersonInMovie;
use KinopoiskDev\Exceptions\ValidationException;

/**
 * @group unit
 * @group utils
 * @group data-manager
 */
class DataManagerTest extends TestCase
{
    private DataManager $dataManager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dataManager = new DataManager();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function test_constructor_createsInstance(): void
    {
        $this->assertInstanceOf(DataManager::class, $this->dataManager);
    }

    public function test_parseObjectArray_withValidData_returnsObjects(): void
    {
        $data = [
            ['name' => 'Action', 'slug' => 'action'],
            ['name' => 'Drama', 'slug' => 'drama'],
            ['name' => 'Comedy', 'slug' => 'comedy']
        ];

        $result = DataManager::parseObjectArray($data, ItemName::class);

        $this->assertIsArray($result);
        $this->assertCount(3, $result);
        
        foreach ($result as $item) {
            $this->assertInstanceOf(ItemName::class, $item);
        }

        $this->assertEquals('Action', $result[0]->getName());
        $this->assertEquals('action', $result[0]->getSlug());
        $this->assertEquals('Drama', $result[1]->getName());
        $this->assertEquals('drama', $result[1]->getSlug());
        $this->assertEquals('Comedy', $result[2]->getName());
        $this->assertEquals('comedy', $result[2]->getSlug());
    }

    public function test_parseObjectArray_withEmptyArray_returnsEmptyArray(): void
    {
        $result = DataManager::parseObjectArray([], ItemName::class);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function test_parseObjectArray_withNullData_returnsEmptyArray(): void
    {
        $result = DataManager::parseObjectArray(null, ItemName::class);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function test_parseObjectArray_withInvalidClass_throwsException(): void
    {
        $data = [['name' => 'Test']];

        $this->expectException(\Error::class);
        
        DataManager::parseObjectArray($data, 'NonExistentClass');
    }

    public function test_parseObjectArray_withComplexData_returnsObjects(): void
    {
        $data = [
            [
                'name' => 'English Name',
                'language' => 'en',
                'type' => 'original'
            ],
            [
                'name' => 'Russian Name',
                'language' => 'ru',
                'type' => 'translation'
            ]
        ];

        $result = DataManager::parseObjectArray($data, Name::class);

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        
        foreach ($result as $item) {
            $this->assertInstanceOf(Name::class, $item);
        }

        $this->assertEquals('English Name', $result[0]->getName());
        $this->assertEquals('en', $result[0]->getLanguage());
        $this->assertEquals('original', $result[0]->getType());
        $this->assertEquals('Russian Name', $result[1]->getName());
        $this->assertEquals('ru', $result[1]->getLanguage());
        $this->assertEquals('translation', $result[1]->getType());
    }

    public function test_parseObjectArray_withPersonData_returnsObjects(): void
    {
        $data = [
            [
                'id' => 123,
                'name' => 'John Doe',
                'profession' => 'актер',
                'enProfession' => 'actor'
            ],
            [
                'id' => 456,
                'name' => 'Jane Smith',
                'profession' => 'режиссер',
                'enProfession' => 'director'
            ]
        ];

        $result = DataManager::parseObjectArray($data, PersonInMovie::class);

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        
        foreach ($result as $item) {
            $this->assertInstanceOf(PersonInMovie::class, $item);
        }

        $this->assertEquals(123, $result[0]->getId());
        $this->assertEquals('John Doe', $result[0]->getName());
        $this->assertEquals('актер', $result[0]->getProfession());
        $this->assertEquals('actor', $result[0]->getEnProfession());
        $this->assertEquals(456, $result[1]->getId());
        $this->assertEquals('Jane Smith', $result[1]->getName());
        $this->assertEquals('режиссер', $result[1]->getProfession());
        $this->assertEquals('director', $result[1]->getEnProfession());
    }

    public function test_parseObjectArray_withMissingData_handlesNullValues(): void
    {
        $data = [
            ['name' => 'Action'],
            ['name' => 'Drama', 'slug' => 'drama'],
            ['slug' => 'comedy'] // Missing name
        ];

        $result = DataManager::parseObjectArray($data, ItemName::class);

        $this->assertIsArray($result);
        $this->assertCount(3, $result);
        
        $this->assertEquals('Action', $result[0]->getName());
        $this->assertNull($result[0]->getSlug());
        $this->assertEquals('Drama', $result[1]->getName());
        $this->assertEquals('drama', $result[1]->getSlug());
        $this->assertNull($result[2]->getName());
        $this->assertEquals('comedy', $result[2]->getSlug());
    }

    public function test_parseObjectArray_withInvalidDataStructure_handlesGracefully(): void
    {
        $data = [
            'not_an_array', // Invalid structure
            ['name' => 'Valid Item'],
            null, // Null item
            ['name' => 'Another Valid Item']
        ];

        $result = DataManager::parseObjectArray($data, ItemName::class);

        $this->assertIsArray($result);
        $this->assertCount(2, $result); // Only valid items
        
        $this->assertEquals('Valid Item', $result[0]->getName());
        $this->assertEquals('Another Valid Item', $result[1]->getName());
    }

    public function test_parseObjectArray_withNestedData_returnsObjects(): void
    {
        $data = [
            [
                'id' => 1,
                'name' => 'Person 1',
                'profession' => 'актер',
                'enProfession' => 'actor',
                'description' => 'Lead actor',
                'enName' => 'Person One'
            ],
            [
                'id' => 2,
                'name' => 'Person 2',
                'profession' => 'режиссер',
                'enProfession' => 'director',
                'description' => 'Main director',
                'enName' => 'Person Two'
            ]
        ];

        $result = DataManager::parseObjectArray($data, PersonInMovie::class);

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        
        $this->assertEquals(1, $result[0]->getId());
        $this->assertEquals('Person 1', $result[0]->getName());
        $this->assertEquals('актер', $result[0]->getProfession());
        $this->assertEquals('actor', $result[0]->getEnProfession());
        $this->assertEquals('Lead actor', $result[0]->getDescription());
        $this->assertEquals('Person One', $result[0]->getEnName());
        
        $this->assertEquals(2, $result[1]->getId());
        $this->assertEquals('Person 2', $result[1]->getName());
        $this->assertEquals('режиссер', $result[1]->getProfession());
        $this->assertEquals('director', $result[1]->getEnProfession());
        $this->assertEquals('Main director', $result[1]->getDescription());
        $this->assertEquals('Person Two', $result[1]->getEnName());
    }

    public function test_parseObjectArray_withEmptyObjectData_returnsObjects(): void
    {
        $data = [
            [],
            ['name' => 'Test'],
            []
        ];

        $result = DataManager::parseObjectArray($data, ItemName::class);

        $this->assertIsArray($result);
        $this->assertCount(3, $result);
        
        foreach ($result as $item) {
            $this->assertInstanceOf(ItemName::class, $item);
        }

        $this->assertNull($result[0]->getName());
        $this->assertEquals('Test', $result[1]->getName());
        $this->assertNull($result[2]->getName());
    }

    public function test_parseObjectArray_withLargeDataset_handlesEfficiently(): void
    {
        $data = [];
        for ($i = 1; $i <= 100; $i++) {
            $data[] = [
                'name' => "Item {$i}",
                'slug' => "item-{$i}"
            ];
        }

        $result = DataManager::parseObjectArray($data, ItemName::class);

        $this->assertIsArray($result);
        $this->assertCount(100, $result);
        
        foreach ($result as $index => $item) {
            $this->assertInstanceOf(ItemName::class, $item);
            $this->assertEquals("Item " . ($index + 1), $item->getName());
            $this->assertEquals("item-" . ($index + 1), $item->getSlug());
        }
    }

    public function test_parseObjectArray_withSpecialCharacters_handlesCorrectly(): void
    {
        $data = [
            ['name' => 'Action & Adventure', 'slug' => 'action-adventure'],
            ['name' => 'Sci-Fi', 'slug' => 'sci-fi'],
            ['name' => 'Drama (Contemporary)', 'slug' => 'drama-contemporary'],
            ['name' => 'Comedy, Romance', 'slug' => 'comedy-romance']
        ];

        $result = DataManager::parseObjectArray($data, ItemName::class);

        $this->assertIsArray($result);
        $this->assertCount(4, $result);
        
        $this->assertEquals('Action & Adventure', $result[0]->getName());
        $this->assertEquals('action-adventure', $result[0]->getSlug());
        $this->assertEquals('Sci-Fi', $result[1]->getName());
        $this->assertEquals('sci-fi', $result[1]->getSlug());
        $this->assertEquals('Drama (Contemporary)', $result[2]->getName());
        $this->assertEquals('drama-contemporary', $result[2]->getSlug());
        $this->assertEquals('Comedy, Romance', $result[3]->getName());
        $this->assertEquals('comedy-romance', $result[3]->getSlug());
    }

    public function test_parseObjectArray_withUnicodeCharacters_handlesCorrectly(): void
    {
        $data = [
            ['name' => 'Боевик', 'slug' => 'boevik'],
            ['name' => 'Драма', 'slug' => 'drama'],
            ['name' => 'Комедия', 'slug' => 'komediya'],
            ['name' => 'Ужасы', 'slug' => 'uzhasy']
        ];

        $result = DataManager::parseObjectArray($data, ItemName::class);

        $this->assertIsArray($result);
        $this->assertCount(4, $result);
        
        $this->assertEquals('Боевик', $result[0]->getName());
        $this->assertEquals('boevik', $result[0]->getSlug());
        $this->assertEquals('Драма', $result[1]->getName());
        $this->assertEquals('drama', $result[1]->getSlug());
        $this->assertEquals('Комедия', $result[2]->getName());
        $this->assertEquals('komediya', $result[2]->getSlug());
        $this->assertEquals('Ужасы', $result[3]->getName());
        $this->assertEquals('uzhasy', $result[3]->getSlug());
    }

    public function test_parseObjectArray_withMixedDataTypes_handlesCorrectly(): void
    {
        $data = [
            ['name' => 'String Item', 'slug' => 'string-item'],
            ['name' => 123, 'slug' => 'numeric-name'], // Numeric name
            ['name' => true, 'slug' => 'boolean-name'], // Boolean name
            ['name' => null, 'slug' => 'null-name'] // Null name
        ];

        $result = DataManager::parseObjectArray($data, ItemName::class);

        $this->assertIsArray($result);
        $this->assertCount(4, $result);
        
        $this->assertEquals('String Item', $result[0]->getName());
        $this->assertEquals('string-item', $result[0]->getSlug());
        $this->assertEquals(123, $result[1]->getName());
        $this->assertEquals('numeric-name', $result[1]->getSlug());
        $this->assertEquals(true, $result[2]->getName());
        $this->assertEquals('boolean-name', $result[2]->getSlug());
        $this->assertNull($result[3]->getName());
        $this->assertEquals('null-name', $result[3]->getSlug());
    }
} 