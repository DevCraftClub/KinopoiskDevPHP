<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use KinopoiskDev\Models\Person;
use KinopoiskDev\Models\ExternalId;
use KinopoiskDev\Models\Name;
use KinopoiskDev\Models\BirthPlace;
use KinopoiskDev\Models\DeathPlace;
use KinopoiskDev\Models\Spouses;
use KinopoiskDev\Models\PersonAward;
use KinopoiskDev\Models\PersonInMovie;
use KinopoiskDev\Models\FactInPerson;
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
            profession: PersonProfession::ACTOR
        );

        $this->assertInstanceOf(Person::class, $person);
        $this->assertEquals(123, $person->getId());
        $this->assertEquals('John Doe', $person->getName());
        $this->assertEquals('John Doe', $person->getEnName());
        $this->assertEquals(PersonSex::MALE, $person->getSex());
        $this->assertEquals(PersonProfession::ACTOR, $person->getProfession());
    }

    public function test_constructor_withNullValues_createsInstance(): void
    {
        $person = new Person();

        $this->assertInstanceOf(Person::class, $person);
        $this->assertNull($person->getId());
        $this->assertNull($person->getName());
        $this->assertNull($person->getEnName());
        $this->assertNull($person->getSex());
        $this->assertNull($person->getProfession());
    }

    public function test_fromArray_withValidData_createsInstance(): void
    {
        $data = [
            'id' => 456,
            'name' => 'Jane Smith',
            'enName' => 'Jane Smith',
            'sex' => 'FEMALE',
            'profession' => 'режиссер',
            'birthDate' => '1980-01-01',
            'deathDate' => null,
            'birthPlace' => ['value' => 'New York'],
            'deathPlace' => null
        ];

        $person = Person::fromArray($data);

        $this->assertInstanceOf(Person::class, $person);
        $this->assertEquals(456, $person->getId());
        $this->assertEquals('Jane Smith', $person->getName());
        $this->assertEquals('Jane Smith', $person->getEnName());
        $this->assertEquals(PersonSex::FEMALE, $person->getSex());
        $this->assertEquals(PersonProfession::DIRECTOR, $person->getProfession());
        $this->assertEquals('1980-01-01', $person->getBirthDate());
        $this->assertNull($person->getDeathDate());
    }

    public function test_fromArray_withComplexData_createsInstance(): void
    {
        $data = [
            'id' => 789,
            'name' => 'Complex Person',
            'enName' => 'Complex Person',
            'sex' => 'MALE',
            'profession' => 'актер',
            'externalId' => [
                'imdb' => 'nm1234567',
                'tmdb' => 123456
            ],
            'names' => [
                ['name' => 'English Name', 'language' => 'en'],
                ['name' => 'Russian Name', 'language' => 'ru']
            ],
            'birthPlace' => [
                'value' => 'Los Angeles',
                'country' => 'USA'
            ],
            'deathPlace' => [
                'value' => 'Hollywood',
                'country' => 'USA'
            ],
            'spouses' => [
                [
                    'id' => 1,
                    'name' => 'Spouse Name',
                    'divorced' => false,
                    'divorcedReason' => null,
                    'sex' => 'FEMALE',
                    'children' => 2
                ]
            ],
            'awards' => [
                [
                    'id' => 1,
                    'name' => 'Oscar',
                    'nomination' => 'Best Actor',
                    'winning' => true,
                    'year' => 2020
                ]
            ],
            'movies' => [
                [
                    'id' => 1,
                    'name' => 'Movie Title',
                    'alternativeName' => 'Alternative Title',
                    'rating' => 8.5,
                    'general' => true,
                    'description' => 'Lead role'
                ]
            ],
            'facts' => [
                [
                    'value' => 'Interesting fact about person',
                    'type' => 'fact'
                ]
            ]
        ];

        $person = Person::fromArray($data);

        $this->assertInstanceOf(Person::class, $person);
        $this->assertEquals(789, $person->getId());
        $this->assertEquals('Complex Person', $person->getName());
        $this->assertInstanceOf(ExternalId::class, $person->getExternalId());
        $this->assertCount(2, $person->getNames());
        $this->assertInstanceOf(BirthPlace::class, $person->getBirthPlace());
        $this->assertInstanceOf(DeathPlace::class, $person->getDeathPlace());
        $this->assertCount(1, $person->getSpouses());
        $this->assertCount(1, $person->getAwards());
        $this->assertCount(1, $person->getMovies());
        $this->assertCount(1, $person->getFacts());
    }

    public function test_fromJson_withValidJson_createsInstance(): void
    {
        $json = json_encode([
            'id' => 101,
            'name' => 'JSON Person',
            'enName' => 'JSON Person',
            'sex' => 'MALE',
            'profession' => 'актер'
        ]);

        $person = Person::fromJson($json);

        $this->assertInstanceOf(Person::class, $person);
        $this->assertEquals(101, $person->getId());
        $this->assertEquals('JSON Person', $person->getName());
        $this->assertEquals('JSON Person', $person->getEnName());
        $this->assertEquals(PersonSex::MALE, $person->getSex());
        $this->assertEquals(PersonProfession::ACTOR, $person->getProfession());
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
            profession: PersonProfession::ACTOR,
            birthDate: '1980-01-01'
        );

        $result = $person->validate();

        $this->assertTrue($result);
    }

    public function test_validate_withInvalidBirthDate_throwsException(): void
    {
        $person = new Person(
            id: 123,
            name: 'Invalid Person',
            birthDate: 'invalid-date'
        );

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Некорректный формат даты рождения');

        $person->validate();
    }

    public function test_validate_withInvalidDeathDate_throwsException(): void
    {
        $person = new Person(
            id: 123,
            name: 'Invalid Person',
            birthDate: '1980-01-01',
            deathDate: 'invalid-date'
        );

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Некорректный формат даты смерти');

        $person->validate();
    }

    public function test_validate_withDeathBeforeBirth_throwsException(): void
    {
        $person = new Person(
            id: 123,
            name: 'Invalid Person',
            birthDate: '1980-01-01',
            deathDate: '1970-01-01'
        );

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Дата смерти не может быть раньше даты рождения');

        $person->validate();
    }

    public function test_getAge_withValidBirthDate_returnsAge(): void
    {
        $birthDate = date('Y-m-d', strtotime('-30 years'));
        $person = new Person(birthDate: $birthDate);

        $age = $person->getAge();

        $this->assertEquals(30, $age);
    }

    public function test_getAge_withoutBirthDate_returnsNull(): void
    {
        $person = new Person();

        $age = $person->getAge();

        $this->assertNull($age);
    }

    public function test_getAge_withDeathDate_returnsAgeAtDeath(): void
    {
        $birthDate = '1980-01-01';
        $deathDate = '2020-01-01';
        $person = new Person(birthDate: $birthDate, deathDate: $deathDate);

        $age = $person->getAge();

        $this->assertEquals(40, $age);
    }

    public function test_isAlive_withoutDeathDate_returnsTrue(): void
    {
        $person = new Person(birthDate: '1980-01-01');

        $isAlive = $person->isAlive();

        $this->assertTrue($isAlive);
    }

    public function test_isAlive_withDeathDate_returnsFalse(): void
    {
        $person = new Person(
            birthDate: '1980-01-01',
            deathDate: '2020-01-01'
        );

        $isAlive = $person->isAlive();

        $this->assertFalse($isAlive);
    }

    public function test_getFullName_returnsFullName(): void
    {
        $person = new Person(
            name: 'John',
            enName: 'John Doe'
        );

        $fullName = $person->getFullName();

        $this->assertEquals('John (John Doe)', $fullName);
    }

    public function test_getFullName_withoutEnName_returnsName(): void
    {
        $person = new Person(name: 'John');

        $fullName = $person->getFullName();

        $this->assertEquals('John', $fullName);
    }

    public function test_getFullName_withoutName_returnsEnName(): void
    {
        $person = new Person(enName: 'John Doe');

        $fullName = $person->getFullName();

        $this->assertEquals('John Doe', $fullName);
    }

    public function test_getImdbUrl_withExternalId_returnsUrl(): void
    {
        $externalId = $this->createMock(ExternalId::class);
        $externalId->method('getImdb')->willReturn('nm1234567');

        $person = new Person(externalId: $externalId);

        $url = $person->getImdbUrl();

        $this->assertEquals('https://www.imdb.com/name/nm1234567/', $url);
    }

    public function test_getImdbUrl_withoutExternalId_returnsNull(): void
    {
        $person = new Person();

        $url = $person->getImdbUrl();

        $this->assertNull($url);
    }

    public function test_getTmdbUrl_withExternalId_returnsUrl(): void
    {
        $externalId = $this->createMock(ExternalId::class);
        $externalId->method('getTmdb')->willReturn(123456);

        $person = new Person(externalId: $externalId);

        $url = $person->getTmdbUrl();

        $this->assertEquals('https://www.themoviedb.org/person/123456', $url);
    }

    public function test_getTmdbUrl_withoutExternalId_returnsNull(): void
    {
        $person = new Person();

        $url = $person->getTmdbUrl();

        $this->assertNull($url);
    }

    public function test_getMoviesCount_returnsCount(): void
    {
        $movies = [
            $this->createMock(PersonInMovie::class),
            $this->createMock(PersonInMovie::class),
            $this->createMock(PersonInMovie::class)
        ];

        $person = new Person(movies: $movies);

        $count = $person->getMoviesCount();

        $this->assertEquals(3, $count);
    }

    public function test_getMoviesCount_withoutMovies_returnsZero(): void
    {
        $person = new Person();

        $count = $person->getMoviesCount();

        $this->assertEquals(0, $count);
    }

    public function test_getAwardsCount_returnsCount(): void
    {
        $awards = [
            $this->createMock(PersonAward::class),
            $this->createMock(PersonAward::class)
        ];

        $person = new Person(awards: $awards);

        $count = $person->getAwardsCount();

        $this->assertEquals(2, $count);
    }

    public function test_getAwardsCount_withoutAwards_returnsZero(): void
    {
        $person = new Person();

        $count = $person->getAwardsCount();

        $this->assertEquals(0, $count);
    }

    public function test_getWinningAwardsCount_returnsCount(): void
    {
        $award1 = $this->createMock(PersonAward::class);
        $award1->method('isWinning')->willReturn(true);
        
        $award2 = $this->createMock(PersonAward::class);
        $award2->method('isWinning')->willReturn(false);
        
        $award3 = $this->createMock(PersonAward::class);
        $award3->method('isWinning')->willReturn(true);

        $person = new Person(awards: [$award1, $award2, $award3]);

        $count = $person->getWinningAwardsCount();

        $this->assertEquals(2, $count);
    }

    public function test_getWinningAwardsCount_withoutAwards_returnsZero(): void
    {
        $person = new Person();

        $count = $person->getWinningAwardsCount();

        $this->assertEquals(0, $count);
    }

    public function test_toJson_withValidData_returnsJsonString(): void
    {
        $person = new Person(
            id: 123,
            name: 'JSON Test Person',
            enName: 'JSON Test Person',
            sex: PersonSex::MALE,
            profession: PersonProfession::ACTOR
        );

        $json = $person->toJson();
        $data = json_decode($json, true);

        $this->assertIsString($json);
        $this->assertEquals(123, $data['id']);
        $this->assertEquals('JSON Test Person', $data['name']);
        $this->assertEquals('JSON Test Person', $data['enName']);
        $this->assertEquals('MALE', $data['sex']);
        $this->assertEquals('актер', $data['profession']);
    }

    public function test_toArray_withIncludeNulls_returnsFullArray(): void
    {
        $person = new Person(
            id: 123,
            name: 'Array Test Person',
            enName: 'Array Test Person'
        );

        $array = $person->toArray(true);

        $this->assertIsArray($array);
        $this->assertEquals(123, $array['id']);
        $this->assertEquals('Array Test Person', $array['name']);
        $this->assertEquals('Array Test Person', $array['enName']);
        $this->assertArrayHasKey('sex', $array);
        $this->assertNull($array['sex']);
    }

    public function test_toArray_withoutIncludeNulls_returnsFilteredArray(): void
    {
        $person = new Person(
            id: 123,
            name: 'Array Test Person',
            enName: 'Array Test Person'
        );

        $array = $person->toArray(false);

        $this->assertIsArray($array);
        $this->assertEquals(123, $array['id']);
        $this->assertEquals('Array Test Person', $array['name']);
        $this->assertEquals('Array Test Person', $array['enName']);
        $this->assertArrayNotHasKey('sex', $array);
    }

    /**
     * @dataProvider validDateProvider
     */
    public function test_validate_withValidDates_returnsTrue(string $birthDate, ?string $deathDate): void
    {
        $person = new Person(
            id: 123,
            name: 'Valid Date Person',
            birthDate: $birthDate,
            deathDate: $deathDate
        );

        $result = $person->validate();

        $this->assertTrue($result);
    }

    public function validDateProvider(): array
    {
        return [
            'valid_birth_only' => ['1980-01-01', null],
            'valid_birth_and_death' => ['1980-01-01', '2020-01-01'],
            'valid_birth_same_year' => ['1980-01-01', '1980-12-31'],
            'valid_birth_leap_year' => ['1980-02-29', '2020-02-29'],
        ];
    }

    /**
     * @dataProvider invalidDateProvider
     */
    public function test_validate_withInvalidDates_throwsException(string $birthDate, ?string $deathDate, string $expectedMessage): void
    {
        $person = new Person(
            id: 123,
            name: 'Invalid Date Person',
            birthDate: $birthDate,
            deathDate: $deathDate
        );

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage($expectedMessage);

        $person->validate();
    }

    public function invalidDateProvider(): array
    {
        return [
            'invalid_birth_date' => ['invalid-date', null, 'Некорректный формат даты рождения'],
            'invalid_death_date' => ['1980-01-01', 'invalid-date', 'Некорректный формат даты смерти'],
            'death_before_birth' => ['1980-01-01', '1970-01-01', 'Дата смерти не может быть раньше даты рождения'],
            'invalid_birth_month' => ['1980-13-01', null, 'Некорректный формат даты рождения'],
            'invalid_birth_day' => ['1980-01-32', null, 'Некорректный формат даты рождения'],
        ];
    }
} 