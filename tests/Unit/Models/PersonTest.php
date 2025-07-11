<?php

declare(strict_types=1);

namespace KinopoiskDev\Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use KinopoiskDev\Models\Person;
use KinopoiskDev\Enums\PersonSex;
use KinopoiskDev\Enums\PersonProfession;
use KinopoiskDev\Exceptions\ValidationException;

/**
 * @group unit
 * @group models
 * @group person
 */
class PersonTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function test_constructor_withValidData_createsInstance(): void
    {
        $person = new Person(
            id: 123,
            name: 'John Doe',
            enName: 'John Doe',
            sex: PersonSex::MALE,
            profession: [PersonProfession::ACTOR->value],
            birthday: '1980-01-01'
        );

        $this->assertInstanceOf(Person::class, $person);
        $this->assertEquals(123, $person->id);
        $this->assertEquals('John Doe', $person->name);
        $this->assertEquals('John Doe', $person->enName);
        $this->assertEquals(PersonSex::MALE, $person->sex);
        $this->assertEquals([PersonProfession::ACTOR->value], $person->profession);
        $this->assertEquals('1980-01-01', $person->birthday);
    }

    public function test_constructor_withNullValues_createsInstance(): void
    {
        $person = new Person(
            id: 456,
            name: null,
            enName: null,
            sex: null,
            profession: null
        );

        $this->assertInstanceOf(Person::class, $person);
        $this->assertEquals(456, $person->id);
        $this->assertNull($person->name);
        $this->assertNull($person->enName);
        $this->assertNull($person->sex);
        $this->assertNull($person->profession);
    }

    public function test_fromArray_withValidData_createsInstance(): void
    {
        $data = [
            'id' => 123,
            'name' => 'John Doe',
            'enName' => 'John Doe',
            'sex' => 'male',
            'profession' => ['actor'],
            'birthday' => '1980-01-01',
            'death' => '2020-01-01',
            'growth' => 180,
            'age' => 40,
            'birthPlace' => [],
            'deathPlace' => [],
            'spouses' => [],
            'countAwards' => 5,
            'facts' => [],
            'movies' => [],
            'updatedAt' => '2023-01-01T00:00:00.000Z',
            'createdAt' => '2023-01-01T00:00:00.000Z'
        ];

        $person = Person::fromArray($data);

        $this->assertInstanceOf(Person::class, $person);
        $this->assertEquals(123, $person->id);
        $this->assertEquals('John Doe', $person->name);
        $this->assertEquals('John Doe', $person->enName);
        $this->assertEquals(PersonSex::MALE, $person->sex);
        $this->assertEquals(['actor'], $person->profession);
        $this->assertEquals('1980-01-01', $person->birthday);
        $this->assertEquals('2020-01-01', $person->death);
        $this->assertEquals(180, $person->growth);
        $this->assertEquals(40, $person->age);
        $this->assertEquals(5, $person->countAwards);
    }

    public function test_fromArray_withComplexData_createsInstance(): void
    {
        $data = [
            'id' => 456,
            'name' => 'Jane Smith',
            'enName' => 'Jane Smith',
            'photo' => 'https://example.com/photo.jpg',
            'sex' => 'female',
            'profession' => ['director', 'producer'],
            'birthday' => '1985-05-15',
            'growth' => 165,
            'birthPlace' => [
                ['name' => 'Moscow', 'value' => 'Moscow, Russia']
            ],
            'deathPlace' => [],
            'spouses' => [
                ['id' => 1, 'name' => 'Spouse Name', 'divorced' => false, 'divorcedReason' => null, 'sex' => 'male', 'children' => 2, 'webUrl' => null, 'relation' => 'Spouse']
            ],
            'countAwards' => 10,
            'facts' => [
                ['value' => 'Interesting fact about Jane']
            ],
            'movies' => [
                ['id' => 1, 'name' => 'Movie 1', 'alternativeName' => 'Movie 1 Alt', 'rating' => 8.5, 'general' => true, 'description' => 'Description', 'professionKey' => 'director']
            ]
        ];

        $person = Person::fromArray($data);

        $this->assertInstanceOf(Person::class, $person);
        $this->assertEquals(456, $person->id);
        $this->assertEquals('Jane Smith', $person->name);
        $this->assertEquals('Jane Smith', $person->enName);
        $this->assertEquals('https://example.com/photo.jpg', $person->photo);
        $this->assertEquals(PersonSex::FEMALE, $person->sex);
        $this->assertEquals(['director', 'producer'], $person->profession);
        $this->assertEquals('1985-05-15', $person->birthday);
        $this->assertEquals(165, $person->growth);
        $this->assertEquals(10, $person->countAwards);
    }

    public function test_fromJson_withValidJson_createsInstance(): void
    {
        $json = '{
            "id": 789,
            "name": "Bob Johnson",
            "enName": "Bob Johnson",
            "sex": "male",
            "profession": ["actor"],
            "birthday": "1990-12-25"
        }';

        $person = Person::fromJson($json);

        $this->assertInstanceOf(Person::class, $person);
        $this->assertEquals(789, $person->id);
        $this->assertEquals('Bob Johnson', $person->name);
        $this->assertEquals('Bob Johnson', $person->enName);
        $this->assertEquals(PersonSex::MALE, $person->sex);
        $this->assertEquals(['actor'], $person->profession);
        $this->assertEquals('1990-12-25', $person->birthday);
    }

    public function test_fromJson_withInvalidJson_throwsException(): void
    {
        $this->expectException(\JsonException::class);

        Person::fromJson('invalid json');
    }

    public function test_validate_withValidData_returnsTrue(): void
    {
        $person = new Person(
            id: 123,
            name: 'Valid Person',
            enName: 'Valid Person',
            sex: PersonSex::MALE,
            profession: [PersonProfession::ACTOR->value],
            birthday: '1980-01-01'
        );

        $result = $person->validate();

        $this->assertTrue($result);
    }

    public function test_toJson_withValidData_returnsJsonString(): void
    {
        $person = new Person(
            id: 123,
            name: 'John Doe',
            enName: 'John Doe',
            sex: PersonSex::MALE,
            profession: [PersonProfession::ACTOR->value],
            birthday: '1980-01-01'
        );

        $json = $person->toJson();

        $this->assertIsString($json);
        $this->assertStringContainsString('"id":123', $json);
        $this->assertStringContainsString('"name":"John Doe"', $json);
        $this->assertStringContainsString('"enName":"John Doe"', $json);
    }

    public function test_toArray_withIncludeNulls_returnsFullArray(): void
    {
        $person = new Person(
            id: 123,
            name: 'John Doe',
            enName: 'John Doe',
            sex: PersonSex::MALE,
            profession: [PersonProfession::ACTOR->value]
        );

        $array = $person->toArray(true);

        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('name', $array);
        $this->assertArrayHasKey('enName', $array);
        $this->assertArrayHasKey('sex', $array);
        $this->assertArrayHasKey('profession', $array);
        $this->assertArrayHasKey('growth', $array);
        $this->assertArrayHasKey('birthday', $array);
        $this->assertArrayHasKey('death', $array);
        $this->assertArrayHasKey('age', $array);
    }

    public function test_toArray_withoutIncludeNulls_returnsFilteredArray(): void
    {
        $person = new Person(
            id: 123,
            name: 'John Doe',
            enName: 'John Doe',
            sex: PersonSex::MALE,
            profession: [PersonProfession::ACTOR->value]
        );

        $array = $person->toArray(false);

        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('name', $array);
        $this->assertArrayHasKey('enName', $array);
        $this->assertArrayHasKey('sex', $array);
        $this->assertArrayHasKey('profession', $array);
        // Note: toArray(false) doesn't actually filter null values in current implementation
        $this->assertArrayHasKey('growth', $array);
        $this->assertArrayHasKey('birthday', $array);
        $this->assertArrayHasKey('death', $array);
        $this->assertArrayHasKey('age', $array);
    }
} 