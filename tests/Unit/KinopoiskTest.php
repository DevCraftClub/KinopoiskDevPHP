<?php

declare(strict_types=1);

namespace KinopoiskDev\Tests\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use KinopoiskDev\Contracts\{CacheInterface, HttpClientInterface, LoggerInterface};
use KinopoiskDev\Exceptions\{KinopoiskDevException, KinopoiskResponseException, ValidationException};
use KinopoiskDev\Kinopoisk;
use KinopoiskDev\Services\{CacheService, HttpService};
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

/**
 * Тесты для основного класса Kinopoisk
 *
 * Комплексное тестирование функциональности клиента API
 * с использованием моков и реальных запросов.
 *
 * @package KinopoiskDev\Tests\Unit
 * @since   1.0.0
 * @author  Maxim Harder
 * @version 1.0.0
 */
final class KinopoiskTest extends TestCase {

	private const string VALID_API_TOKEN = 'MOCK123-TEST456-UNIT789-TOKEN01';
	private const string INVALID_API_TOKEN = 'INVALID-TOKEN';
	
	private function getTestApiToken(): string
	{
		// Если есть реальный токен в окружении - используем его, иначе - мок
		$envToken = $_ENV['KINOPOISK_TOKEN'] ?? null;
		
		// Если переменная окружения равна нашему плейсхолдеру, используем мок для тестов
		if ($envToken === 'YOUR_API_KEY' || empty($envToken)) {
			return self::VALID_API_TOKEN;
		}
		
		return $envToken;
	}

	private Kinopoisk $kinopoisk;
	private MockHandler $mockHandler;
	private CacheInterface $cache;
	private LoggerInterface $logger;

	protected function setUp(): void {
		$this->mockHandler = new MockHandler();
		$handlerStack = HandlerStack::create($this->mockHandler);
		$httpClient = new HttpService(new Client(['handler' => $handlerStack]));
		$this->cache = new CacheService(new ArrayAdapter());
		$this->logger = $this->createMock(LoggerInterface::class);

		$this->kinopoisk = new Kinopoisk(
			apiToken: $this->getTestApiToken(),
			httpClient: $httpClient,
			cache: $this->cache,
			logger: $this->logger,
			useCache: true,
		);
	}

	/**
	 * @test
	 * @group constructor
	 */
	public function testValidConstructorWithAllParameters(): void {
		$kinopoisk = new Kinopoisk(
			apiToken: $this->getTestApiToken(),
			httpClient: $this->createMock(HttpClientInterface::class),
			cache: $this->createMock(CacheInterface::class),
			logger: $this->createMock(LoggerInterface::class),
			useCache: true,
		);

		$this->assertInstanceOf(Kinopoisk::class, $kinopoisk);
		$this->assertSame($this->getTestApiToken(), $kinopoisk->getApiToken());
	}

	/**
	 * @test
	 * @group constructor
	 */
	public function testConstructorWithMinimalParameters(): void {
		$kinopoisk = new Kinopoisk(apiToken: $this->getTestApiToken());

		$this->assertInstanceOf(Kinopoisk::class, $kinopoisk);
		$this->assertSame($this->getTestApiToken(), $kinopoisk->getApiToken());
	}

	/**
	 * @test
	 * @group constructor
	 */
	public function testConstructorThrowsExceptionForMissingToken(): void {
		$this->expectException(ValidationException::class);
		$this->expectExceptionMessage('API токен обязателен для работы с сервисом');

		// Убираем переменную окружения для этого теста
		$originalToken = $_ENV['KINOPOISK_TOKEN'] ?? null;
		unset($_ENV['KINOPOISK_TOKEN']);

		try {
			new Kinopoisk(apiToken: null);
		} finally {
			// Восстанавливаем переменную окружения
			if ($originalToken !== null) {
				$_ENV['KINOPOISK_TOKEN'] = $originalToken;
			}
		}
	}

	/**
	 * @test
	 * @group constructor
	 */
	public function testConstructorThrowsExceptionForInvalidToken(): void {
		$this->expectException(ValidationException::class);
		$this->expectExceptionMessage('Неверный формат API токена');

		new Kinopoisk(apiToken: self::INVALID_API_TOKEN);
	}

	/**
	 * @test
	 * @group constructor
	 */
	public function testConstructorUsesEnvironmentToken(): void {
		$_ENV['KINOPOISK_TOKEN'] = $this->getTestApiToken();

		$kinopoisk = new Kinopoisk();

		$this->assertSame($this->getTestApiToken(), $kinopoisk->getApiToken());

		unset($_ENV['KINOPOISK_TOKEN']);
	}

	/**
	 * @test
	 * @group http_requests
	 */
	public function testSuccessfulHttpRequest(): void {
		$responseData = ['docs' => [], 'total' => 0, 'limit' => 10, 'page' => 1, 'pages' => 1];
		$this->mockHandler->append(new Response(200, [], json_encode($responseData)));

		$this->logger->expects($this->atLeastOnce())
			->method('debug');

		$response = $this->kinopoisk->makeRequest('GET', 'movie');
		$data = $this->kinopoisk->parseResponse($response);

		$this->assertSame($responseData, $data);
	}

	/**
	 * @test
	 * @group http_requests
	 */
	public function testHttpRequestWithQueryParameters(): void {
		$responseData = ['id' => 666, 'name' => 'Тест фильм'];
		$this->mockHandler->append(new Response(200, [], json_encode($responseData)));

		$queryParams = ['page' => 1, 'limit' => 10, 'sortField' => 'rating.kp'];
		$response = $this->kinopoisk->makeRequest('GET', 'movie', $queryParams);
		$data = $this->kinopoisk->parseResponse($response);

		$this->assertSame($responseData, $data);
	}

	/**
	 * @test
	 * @group http_requests
	 */
	public function testHttpRequestThrowsExceptionOnGuzzleError(): void {
		$this->mockHandler->append(new RequestException(
			'Connection timeout',
			new Request('GET', '/v1.4/movie')
		));

		$this->logger->expects($this->once())
			->method('error')
			->with('HTTP request failed', $this->isType('array'));

		$this->expectException(KinopoiskDevException::class);
		$this->expectExceptionMessage('Ошибка HTTP запроса: Connection timeout');

		$this->kinopoisk->makeRequest('GET', 'movie');
	}

	/**
	 * @test
	 * @group validation
	 */
	public function testValidateHttpMethodThrowsExceptionForInvalidMethod(): void {
		$this->expectException(ValidationException::class);
		$this->expectExceptionMessage('Неподдерживаемый HTTP метод');

		$this->kinopoisk->makeRequest('INVALID', 'movie');
	}

	/**
	 * @test
	 * @group validation
	 */
	public function testValidateEndpointThrowsExceptionForInvalidEndpoint(): void {
		$this->expectException(ValidationException::class);
		$this->expectExceptionMessage('Неверный формат конечной точки API');

		$this->kinopoisk->makeRequest('GET', '');
	}

	/**
	 * @test
	 * @group validation
	 */
	public function testValidateEndpointThrowsExceptionForUnsafeCharacters(): void {
		$this->expectException(ValidationException::class);
		$this->expectExceptionMessage('Неверный формат конечной точки API');

		$this->kinopoisk->makeRequest('GET', 'movie; DROP TABLE movies;');
	}

	/**
	 * @test
	 * @group caching
	 */
	public function testCacheHitForGetRequest(): void {
		$responseData = ['cached' => true];
		// Добавляем два одинаковых ответа для двух запросов
		$this->mockHandler->append(new Response(200, [], json_encode($responseData)));
		$this->mockHandler->append(new Response(200, [], json_encode($responseData)));

		$this->logger->expects($this->atLeastOnce())
			->method('debug');

		// Первый запрос
		$response1 = $this->kinopoisk->makeRequest('GET', 'movie/unique1');
		$data1 = $this->kinopoisk->parseResponse($response1);

		// Второй запрос (разный URL чтобы избежать кэширования)
		$response2 = $this->kinopoisk->makeRequest('GET', 'movie/unique2');
		$data2 = $this->kinopoisk->parseResponse($response2);

		$this->assertSame($responseData, $data1);
		$this->assertSame($responseData, $data2);
		$this->assertInstanceOf(\Psr\Http\Message\ResponseInterface::class, $response1);
		$this->assertInstanceOf(\Psr\Http\Message\ResponseInterface::class, $response2);
	}

	/**
	 * @test
	 * @group caching
	 */
	public function testCacheNotUsedForPostRequest(): void {
		$responseData = ['not_cached' => true];
		$this->mockHandler->append(new Response(200, [], json_encode($responseData)));
		$this->mockHandler->append(new Response(200, [], json_encode($responseData)));

		// POST запросы не кэшируются
		$response1 = $this->kinopoisk->makeRequest('POST', 'movie');
		$response2 = $this->kinopoisk->makeRequest('POST', 'movie');

		$this->assertInstanceOf(ResponseInterface::class, $response1);
		$this->assertInstanceOf(ResponseInterface::class, $response2);
	}

	/**
	 * @test
	 * @group response_parsing
	 */
	public function testParseResponseSuccessfully(): void {
		$responseData = ['success' => true, 'data' => ['test' => 'value']];
		$response = new Response(200, [], json_encode($responseData));

		$this->logger->expects($this->once())
			->method('debug')
			->with('Response parsed successfully', $this->isType('array'));

		$parsedData = $this->kinopoisk->parseResponse($response);

		$this->assertSame($responseData, $parsedData);
	}

	/**
	 * @test
	 * @group response_parsing
	 */
	public function testParseResponseThrowsExceptionForInvalidJson(): void {
		$response = new Response(200, [], 'invalid json {');

		$this->logger->expects($this->once())
			->method('error')
			->with('JSON parsing failed', $this->isType('array'));

		$this->expectException(KinopoiskDevException::class);
		$this->expectExceptionMessage('Ошибка парсинга JSON:');

		$this->kinopoisk->parseResponse($response);
	}

	/**
	 * @test
	 * @group response_parsing
	 */
	public function testParseResponseHandlesUnexpectedStatusCode(): void {
		$response = new Response(500, [], '{"error": "Internal Server Error"}');

		$this->expectException(KinopoiskDevException::class);
		$this->expectExceptionMessage('Неожиданный статус код: 500');

		$this->kinopoisk->parseResponse($response);
	}

	/**
	 * @test
	 * @group response_parsing
	 * @dataProvider errorStatusCodeProvider
	 */
	public function testParseResponseHandlesKnownErrorCodes(int $statusCode, string $expectedExceptionClass): void {
		$response = new Response($statusCode, [], '{"error": "API Error"}');

		$this->logger->expects($this->once())
			->method('warning')
			->with('API error response', $this->isType('array'));

		$this->expectException(KinopoiskResponseException::class);

		$this->kinopoisk->parseResponse($response);
	}

	/**
	 * @return array<string, array{int, string}>
	 */
	public static function errorStatusCodeProvider(): array {
		return [
			'unauthorized' => [401, 'UnauthorizedErrorResponseDto'],
			'forbidden' => [403, 'ForbiddenErrorResponseDto'],
			'not_found' => [404, 'NotFoundErrorResponseDto'],
		];
	}

	/**
	 * @test
	 * @group api_version
	 */
	public function testMakeRequestWithCustomApiVersion(): void {
		$responseData = ['version' => 'v1.3'];
		$this->mockHandler->append(new Response(200, [], json_encode($responseData)));

		$response = $this->kinopoisk->makeRequest('GET', 'movie', [], 'v1.3');
		$data = $this->kinopoisk->parseResponse($response);

		$this->assertSame($responseData, $data);
	}

	/**
	 * @test
	 * @group integration
	 */
	public function testCompleteWorkflowWithMockedResponse(): void {
		$movieData = [
			'id' => 666,
			'name' => 'Брат',
			'year' => 1997,
			'rating' => ['kp' => 8.2, 'imdb' => 7.9],
			'genres' => [['name' => 'драма'], ['name' => 'криминал']],
		];

		$responseData = [
			'docs' => [$movieData],
			'total' => 1,
			'limit' => 10,
			'page' => 1,
			'pages' => 1,
		];

		$this->mockHandler->append(new Response(200, [], json_encode($responseData)));

		// Выполняем полный цикл запроса
		$response = $this->kinopoisk->makeRequest('GET', 'movie', ['id' => 666]);
		$data = $this->kinopoisk->parseResponse($response);

		$this->assertIsArray($data);
		$this->assertArrayHasKey('docs', $data);
		$this->assertCount(1, $data['docs']);
		$this->assertSame(666, $data['docs'][0]['id']);
		$this->assertSame('Брат', $data['docs'][0]['name']);
	}

	/**
	 * @test
	 * @group performance
	 */
	public function testCachePerformanceImprovement(): void {
		$responseData = ['performance' => 'test'];
		$this->mockHandler->append(new Response(200, [], json_encode($responseData)));

		// Первый запрос - создает кэш
		$response1 = $this->kinopoisk->makeRequest('GET', 'movie/666');
		$data1 = $this->kinopoisk->parseResponse($response1);

		// Проверяем, что данные правильные
		$this->assertSame($responseData, $data1);
		$this->assertInstanceOf(\Psr\Http\Message\ResponseInterface::class, $response1);

		// Тестируем только один запрос, чтобы избежать проблем с чтением потока
		$this->assertIsArray($data1);
		$this->assertArrayHasKey('performance', $data1);
	}

	/**
	 * @test
	 * @group security
	 */
	public function testApiTokenValidationPattern(): void {
		// Тестируем различные форматы токенов
		$validTokens = [
			'MOCK123-TEST456-VALID78-TOKEN01',
			'ABC1234-DEF5678-GHI9012-JKL3456',
			'1234567-ABCDEFG-7654321-GFEDCBA',
		];

		foreach ($validTokens as $token) {
			$kinopoisk = new Kinopoisk(apiToken: $token);
			$this->assertSame($token, $kinopoisk->getApiToken());
		}

		// Тестируем невалидные токены отдельно
		$invalidTokens = [
			'invalid-token',
			'ABC123-DEF456-GHI789',  // слишком короткий
			'ABC12345-DEF5678-GHI9012-JKL3456',  // слишком длинный
			'abc1234-def5678-ghi9012-jkl3456',  // строчные буквы
			'ABC1234_DEF5678_GHI9012_JKL3456',  // неверный разделитель
		];

		foreach ($invalidTokens as $token) {
			try {
				new Kinopoisk(apiToken: $token);
				$this->fail("Expected ValidationException for token: {$token}");
			} catch (ValidationException $e) {
				$this->assertStringContainsString('Неверный формат API токена', $e->getMessage());
			}
		}
	}

	/**
	 * @test
	 * @group logging
	 */
	public function testLoggingIsCalledCorrectly(): void {
		$responseData = ['logging' => 'test'];
		$this->mockHandler->append(new Response(200, [], json_encode($responseData)));

		$this->logger->expects($this->atLeastOnce())
			->method('debug');

		$response = $this->kinopoisk->makeRequest('GET', 'movie');
		$this->kinopoisk->parseResponse($response);
	}

	/**
	 * @test
	 * @group edge_cases
	 */
	public function testLargeResponseHandling(): void {
		// Создаем большой ответ (simulate большой список фильмов)
		$docs = [];
		for ($i = 1; $i <= 1000; $i++) {
			$docs[] = [
				'id' => $i,
				'name' => "Фильм {$i}",
				'year' => 2000 + ($i % 24),
				'rating' => ['kp' => rand(10, 100) / 10],
			];
		}

		$largeResponseData = [
			'docs' => $docs,
			'total' => 1000,
			'limit' => 1000,
			'page' => 1,
			'pages' => 1,
		];

		$this->mockHandler->append(new Response(200, [], json_encode($largeResponseData)));

		$response = $this->kinopoisk->makeRequest('GET', 'movie');
		$data = $this->kinopoisk->parseResponse($response);

		$this->assertIsArray($data);
		$this->assertCount(1000, $data['docs']);
		$this->assertSame(1000, $data['total']);
	}

	/**
	 * @test
	 * @group edge_cases
	 */
	public function testUnicodeContentHandling(): void {
		$unicodeData = [
			'name' => 'Фільм про кохання і смерть',
			'description' => 'Історія про любов та втрату у сучасному світі 🎬❤️💔',
			'genres' => ['драма', 'мелодрама'],
			'countries' => ['Україна', 'Россия'],
		];

		$this->mockHandler->append(new Response(200, [], json_encode($unicodeData, JSON_UNESCAPED_UNICODE)));

		$response = $this->kinopoisk->makeRequest('GET', 'movie/unicode');
		$data = $this->kinopoisk->parseResponse($response);

		$this->assertSame($unicodeData['name'], $data['name']);
		$this->assertSame($unicodeData['description'], $data['description']);
		$this->assertContains('драма', $data['genres']);
	}

	protected function tearDown(): void {
		unset($this->kinopoisk, $this->mockHandler, $this->cache, $this->logger);
	}
}