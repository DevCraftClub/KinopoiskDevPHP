<?php

declare(strict_types=1);

namespace KinopoiskDev\Tests\Unit\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use KinopoiskDev\Exceptions\KinopoiskDevException;
use KinopoiskDev\Filter\PersonSearchFilter;
use KinopoiskDev\Http\PersonRequests;
use KinopoiskDev\Models\Person;
use KinopoiskDev\Responses\Api\PersonAwardDocsResponseDto;
use KinopoiskDev\Responses\Api\PersonDocsResponseDto;
use PHPUnit\Framework\TestCase;

/**
 * @group unit
 * @group http
 * @group person-requests
 */
class PersonRequestsTest extends TestCase {

	private MockHandler    $mockHandler;
	private HandlerStack   $handlerStack;
	private Client         $httpClient;
	private PersonRequests $personRequests;

	public function test_getPersonById_withValidId_returnsPerson(): void {
		$personData = [
			'id'         => 123,
			'name'       => 'John Doe',
			'enName'     => 'John Doe',
			'sex'        => 'MALE',
			'profession' => 'актер',
		];

		$response = new Response(200, [], json_encode($personData));
		$this->mockHandler->append($response);

		$person = $this->personRequests->getPersonById(123);

		$this->assertInstanceOf(Person::class, $person);
		$this->assertEquals(123, $person->id);
		$this->assertEquals('John Doe', $person->name);
		$this->assertEquals('John Doe', $person->enName);
	}

	public function test_getPersonById_withInvalidId_throwsException(): void {
		$errorResponse = new Response(404, [], json_encode(['error' => 'Person not found']));
		$this->mockHandler->append($errorResponse);

		$this->expectException(KinopoiskDevException::class);
		$this->expectExceptionMessage('Ошибка HTTP запроса: Client error: `GET /v1.4/person/999999` resulted in a `404 Not Found` response:');

		$this->personRequests->getPersonById(999999);
	}

	public function test_getRandomPerson_withoutFilters_returnsPerson(): void {
		$personsData = [
			'docs'  => [
				[
					'id'         => 456,
					'name'       => 'Jane Smith',
					'enName'     => 'Jane Smith',
					'sex'        => 'FEMALE',
					'profession' => 'режиссер',
				],
			],
			'total' => 1,
			'limit' => 1,
			'page'  => 1,
			'pages' => 1,
		];

		$response = new Response(200, [], json_encode($personsData));
		$this->mockHandler->append($response);

		$person = $this->personRequests->getRandomPerson();

		$this->assertInstanceOf(Person::class, $person);
		$this->assertEquals(456, $person->id);
		$this->assertEquals('Jane Smith', $person->name);
	}

	public function test_getRandomPerson_withFilters_returnsFilteredPerson(): void {
		$personsData = [
			'docs'  => [
				[
					'id'         => 789,
					'name'       => 'Actor Name',
					'enName'     => 'Actor Name',
					'sex'        => 'MALE',
					'profession' => 'актер',
				],
			],
			'total' => 1,
			'limit' => 1,
			'page'  => 1,
			'pages' => 1,
		];

		$response = new Response(200, [], json_encode($personsData));
		$this->mockHandler->append($response);

		$filter = new PersonSearchFilter();
		$filter->profession('актер');

		$person = $this->personRequests->getRandomPerson($filter);

		$this->assertInstanceOf(Person::class, $person);
		$this->assertEquals(789, $person->id);
	}

	public function test_searchPersons_withFilters_returnsPersons(): void {
		$personsData = [
			'docs'  => [
				[
					'id'         => 101,
					'name'       => 'Search Result',
					'enName'     => 'Search Result',
					'sex'        => 'MALE',
					'profession' => 'актер',
				],
			],
			'total' => 1,
			'limit' => 10,
			'page'  => 1,
			'pages' => 1,
		];

		$response = new Response(200, [], json_encode($personsData));
		$this->mockHandler->append($response);

		$filter = new PersonSearchFilter();
		$filter->name('Search');

		$result = $this->personRequests->searchPersons($filter, 1, 10);

		$this->assertInstanceOf(PersonDocsResponseDto::class, $result);
		$this->assertEquals(1, $result->total);
		$this->assertCount(1, $result->docs);
		$this->assertEquals('Search Result', $result->docs[0]->name);
	}

	public function test_searchPersonsByName_withValidQuery_returnsPersons(): void {
		$personsData = [
			'docs'  => [
				[
					'id'         => 202,
					'name'       => 'Name Search',
					'enName'     => 'Name Search',
					'sex'        => 'FEMALE',
					'profession' => 'режиссер',
				],
			],
			'total' => 1,
			'limit' => 10,
			'page'  => 1,
			'pages' => 1,
		];

		$response = new Response(200, [], json_encode($personsData));
		$this->mockHandler->append($response);

		// Добавляем мок для searchByName (который вызывает searchPersons)
		$searchResponse = new Response(200, [], json_encode($personsData));
		$this->mockHandler->append($searchResponse);

		$result = $this->personRequests->searchPersonsByName('Name Search');

		$this->assertInstanceOf(PersonDocsResponseDto::class, $result);
		$this->assertEquals(1, $result->total);
		$this->assertCount(1, $result->docs);
		$this->assertEquals('Name Search', $result->docs[0]->name);
	}

	public function test_getPersonsByProfession_withValidProfession_returnsPersons(): void {
		$personsData = [
			'docs'  => [
				[
					'id'         => 303,
					'name'       => 'Actor One',
					'enName'     => 'Actor One',
					'sex'        => 'MALE',
					'profession' => 'актер',
				],
				[
					'id'         => 304,
					'name'       => 'Actor Two',
					'enName'     => 'Actor Two',
					'sex'        => 'FEMALE',
					'profession' => 'актер',
				],
			],
			'total' => 2,
			'limit' => 10,
			'page'  => 1,
			'pages' => 1,
		];

		$response = new Response(200, [], json_encode($personsData));
		$this->mockHandler->append($response);

		$result = $this->personRequests->getPersonsByProfession('актер');

		$this->assertInstanceOf(PersonDocsResponseDto::class, $result);
		$this->assertEquals(2, $result->total);
		$this->assertCount(2, $result->docs);
	}

	public function test_getPersonsByProfession_withMultipleProfessions_returnsPersons(): void {
		$personsData = [
			'docs'  => [
				[
					'id'         => 405,
					'name'       => 'Multi Person',
					'enName'     => 'Multi Person',
					'sex'        => 'MALE',
					'profession' => 'актер',
				],
			],
			'total' => 1,
			'limit' => 10,
			'page'  => 1,
			'pages' => 1,
		];

		$response = new Response(200, [], json_encode($personsData));
		$this->mockHandler->append($response);

		$result = $this->personRequests->getPersonsByProfession('актер');

		$this->assertInstanceOf(PersonDocsResponseDto::class, $result);
		$this->assertEquals(1, $result->total);
	}

	public function test_getPersonAwards_withoutFilters_returnsAwards(): void {
		$awardsData = [
			'docs'  => [
				[
					'id'         => '6869df05e782baeefc75c3e0',
					'nomination' => [
						'award' => [
							'title' => 'Премия Гильдии актеров',
							'year'  => 2012,
						],
						'title' => 'Лучший каскадерский состав',
					],
					'winning'    => FALSE,
					'personId'   => 1172655,
					'movie'      => [
						'rating' => [
							'kp'                 => 7.658,
							'imdb'               => 7.2,
							'filmCritics'        => 6.8,
							'russianFilmCritics' => 7.0,
							'await'              => 8.0,
						],
					],
					'createdAt'  => '2025-07-06T02:27:17.627Z',
					'updatedAt'  => '2025-07-06T02:27:17.627Z',
				],
			],
			'total' => 1,
			'limit' => 10,
			'page'  => 1,
			'pages' => 1,
		];

		$response = new Response(200, [], json_encode($awardsData));
		$this->mockHandler->append($response);

		$result = $this->personRequests->getPersonAwards();

		$this->assertInstanceOf(PersonAwardDocsResponseDto::class, $result);
		$this->assertEquals(1, $result->total);
		$this->assertCount(1, $result->docs);
		$this->assertEquals(1172655, $result->docs[0]->personId);
	}

	public function test_getPersonAwards_withFilters_returnsFilteredAwards(): void {
		$awardsData = [
			'docs'  => [
				[
					'id'         => '6869df05e782baeefc75c3e1',
					'nomination' => [
						'award' => [
							'title' => 'Премия Гильдии актеров',
							'year'  => 2013,
						],
						'title' => 'Лучший дублер',
					],
					'winning'    => TRUE,
					'personId'   => 1172656,
					'movie'      => [
						'rating' => [
							'kp'                 => 8.0,
							'imdb'               => 7.5,
							'filmCritics'        => 7.0,
							'russianFilmCritics' => 7.2,
							'await'              => 8.1,
						],
					],
					'createdAt'  => '2025-07-06T02:27:17.627Z',
					'updatedAt'  => '2025-07-06T02:27:17.627Z',
				],
			],
			'total' => 1,
			'limit' => 10,
			'page'  => 1,
			'pages' => 1,
		];

		$response = new Response(200, [], json_encode($awardsData));
		$this->mockHandler->append($response);

		$filter = new PersonSearchFilter();
		$filter->name('Filtered');

		$result = $this->personRequests->getPersonAwards($filter);

		$this->assertInstanceOf(PersonAwardDocsResponseDto::class, $result);
		$this->assertEquals(1, $result->total);
		$this->assertCount(1, $result->docs);
		$this->assertEquals(1172656, $result->docs[0]->personId);
	}

	public function test_getPersonsBySex_withValidSex_returnsPersons(): void {
		$personsData = [
			'docs'  => [
				[
					'id'         => 506,
					'name'       => 'Male Person',
					'enName'     => 'Male Person',
					'sex'        => 'MALE',
					'profession' => 'актер',
				],
			],
			'total' => 1,
			'limit' => 10,
			'page'  => 1,
			'pages' => 1,
		];

		$response = new Response(200, [], json_encode($personsData));
		$this->mockHandler->append($response);

		$result = $this->personRequests->getPersonsBySex('MALE');

		$this->assertInstanceOf(PersonDocsResponseDto::class, $result);
		$this->assertEquals(1, $result->total);
		$this->assertCount(1, $result->docs);
	}

	public function test_getPersonsByBirthYear_withValidYear_returnsPersons(): void {
		$personsData = [
			'docs'  => [
				[
					'id'         => 607,
					'name'       => '1980 Person',
					'enName'     => '1980 Person',
					'sex'        => 'FEMALE',
					'profession' => 'актер',
					'birthPlace' => ['value' => 'New York'],
				],
			],
			'total' => 1,
			'limit' => 10,
			'page'  => 1,
			'pages' => 1,
		];

		$response = new Response(200, [], json_encode($personsData));
		$this->mockHandler->append($response);

		$result = $this->personRequests->getPersonsByBirthYear(1980);

		$this->assertInstanceOf(PersonDocsResponseDto::class, $result);
		$this->assertEquals(1, $result->total);
	}

	public function test_getPersonsByBirthYearRange_withValidRange_returnsPersons(): void {
		$personsData = [
			'docs'  => [
				[
					'id'         => 708,
					'name'       => 'Range Person',
					'enName'     => 'Range Person',
					'sex'        => 'MALE',
					'profession' => 'режиссер',
				],
			],
			'total' => 1,
			'limit' => 10,
			'page'  => 1,
			'pages' => 1,
		];

		$response = new Response(200, [], json_encode($personsData));
		$this->mockHandler->append($response);

		$result = $this->personRequests->getPersonsByBirthYearRange(1980, 1990);

		$this->assertInstanceOf(PersonDocsResponseDto::class, $result);
		$this->assertEquals(1, $result->total);
	}

	public function test_getPersonsByDeathYear_withValidYear_returnsPersons(): void {
		$personsData = [
			'docs'  => [
				[
					'id'         => 809,
					'name'       => 'Deceased Person',
					'enName'     => 'Deceased Person',
					'sex'        => 'MALE',
					'profession' => 'актер',
					'death'      => '2020-01-01',
				],
			],
			'total' => 1,
			'limit' => 10,
			'page'  => 1,
			'pages' => 1,
		];

		$response = new Response(200, [], json_encode($personsData));
		$this->mockHandler->append($response);

		$result = $this->personRequests->getPersonsByDeathYear(2020);

		$this->assertInstanceOf(PersonDocsResponseDto::class, $result);
		$this->assertEquals(1, $result->total);
	}

	public function test_makeRequest_withNetworkError_throwsException(): void {
		$request   = new Request('GET', 'http://example.com');
		$exception = new RequestException('Network error', $request);

		$this->mockHandler->append($exception);

		$this->expectException(KinopoiskDevException::class);
		$this->expectExceptionMessage('Ошибка HTTP запроса: Network error');

		$this->personRequests->getPersonById(123);
	}

	public function test_makeRequest_withServerError_throwsException(): void {
		$errorResponse = new Response(500, [], json_encode(['error' => 'Internal server error']));
		$this->mockHandler->append($errorResponse);

		$this->expectException(KinopoiskDevException::class);
		$this->expectExceptionMessage('Ошибка HTTP запроса: Server error: `GET /v1.4/person/1` resulted in a `500 Internal Server Error` response:');

		$this->personRequests->getPersonById(1);
	}

	public function test_makeRequest_withUnauthorized_throwsException(): void {
		$errorResponse = new Response(401, [], json_encode(['error' => 'Unauthorized']));
		$this->mockHandler->append($errorResponse);

		$this->expectException(KinopoiskDevException::class);
		$this->expectExceptionMessage('Ошибка HTTP запроса: Client error: `GET /v1.4/person/1` resulted in a `401 Unauthorized` response:');

		$this->personRequests->getPersonById(1);
	}

	public function test_makeRequest_withForbidden_throwsException(): void {
		$errorResponse = new Response(403, [], json_encode(['error' => 'Forbidden']));
		$this->mockHandler->append($errorResponse);

		$this->expectException(KinopoiskDevException::class);
		$this->expectExceptionMessage('Ошибка HTTP запроса: Client error: `GET /v1.4/person/1` resulted in a `403 Forbidden` response:');

		$this->personRequests->getPersonById(1);
	}

	protected function setUp(): void {
		parent::setUp();

		$this->mockHandler  = new MockHandler();
		$this->handlerStack = HandlerStack::create($this->mockHandler);
		$this->httpClient   = new Client(['handler' => $this->handlerStack]);

		$this->personRequests = new PersonRequests(
			apiToken  : 'MOCK123-TEST456-UNIT789-TOKEN01',
			httpClient: $this->httpClient,
		);
	}

	protected function tearDown(): void {
		parent::tearDown();
	}

}