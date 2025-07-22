<?php

declare(strict_types=1);

namespace KinopoiskDev\Tests\Unit\Http;

use KinopoiskDev\Exceptions\KinopoiskDevException;
use KinopoiskDev\Http\PersonRequests;
use Symfony\Component\Yaml\Yaml;

/**
 * @group http
 * @group person-requests
 */
class PersonRequestsTest extends BaseHttpTest {

    private PersonRequests $personRequests;

    protected function setUp(): void {
        parent::setUp();
        $this->personRequests = new PersonRequests(
            apiToken: $this->getApiToken(),
        );
    }

    public function test_getPersonById_real(): void {
        $result = $this->personRequests->getPersonById(29855);
		$this->assertNotNull($result);
        $this->assertNotEmpty($result->id);
        $this->assertNotEmpty($result->name);
        $this->assertNotNull($result->sex);
    }

    public function test_searchByName_real(): void {
        $result = $this->personRequests->searchByName('Киану');
        $this->assertNotEmpty($result->docs);
        $this->assertIsArray($result->docs);
        $firstPerson = $result->docs[0];
        $this->assertNotEmpty($firstPerson->id);
        $this->assertNotEmpty($firstPerson->name);
    }

    public function test_getRandomPerson_real(): void {
        $result = $this->personRequests->getRandomPerson();
        $this->assertNotEmpty($result->id);
        $this->assertNotEmpty($result->name);
        $this->assertNotEmpty($result->sex);
        $this->assertNotEmpty($result->age);
    }

    public function test_getPersonById_withInvalidId_throwsException(): void {
        $this->expectException(\KinopoiskDev\Exceptions\KinopoiskDevException::class);
        $this->personRequests->getPersonById(999999999);
    }
}