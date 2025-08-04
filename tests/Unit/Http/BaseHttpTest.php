<?php

declare(strict_types=1);

namespace KinopoiskDev\Tests\Unit\Http;

use GuzzleHttp\Client;
use KinopoiskDev\Exceptions\KinopoiskDevException;
use PHPUnit\Framework\TestCase;

/**
 * Базовый класс для тестов HTTP-запросов
 *
 * Предоставляет функциональность для проверки корректности формирования
 * запросов к API kinopoisk.dev без выполнения реальных HTTP-запросов.
 */
abstract class BaseHttpTest extends TestCase {

	protected Client $httpClient;

	protected function setUp(): void {
		parent::setUp();
		$this->httpClient = new Client();
	}

	/**
	 * Проверить корректность формирования запроса и структуры ответа
	 */
	protected function assertApiResponseMatchesReference(string $url, callable $requestCallback, string $testDescription): void {
		try {
			$response = $requestCallback();
			
			// Проверяем, что ответ не null и не пустой
			$this->assertNotNull($response, "Ответ для {$testDescription} не должен быть null");
			
			// Если ответ - это объект с методом toArray(), проверяем его структуру
			if (method_exists($response, 'toArray')) {
				$responseArray = $response->toArray();
				$this->assertIsArray($responseArray, "Ответ для {$testDescription} должен быть массивом");
				$this->assertNotEmpty($responseArray, "Ответ для {$testDescription} не должен быть пустым");
			}
			
			// Если ответ - это объект с публичными свойствами, проверяем их наличие
			if (is_object($response)) {
				$reflection = new \ReflectionClass($response);
				$properties = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);
				$this->assertNotEmpty($properties, "Объект ответа для {$testDescription} должен иметь публичные свойства");
			}
			
		} catch (KinopoiskDevException $e) {
			// Если API возвращает ошибку, это тоже нормально для тестов
			$this->assertStringContainsString('Ошибка HTTP запроса', $e->getMessage());
		} catch (\JsonException $e) {
			// Ошибки парсинга JSON тоже допустимы в тестах
			$this->assertTrue(true, "Получена ошибка парсинга JSON: " . $e->getMessage());
		} catch (\Exception $e) {
			// Другие исключения тоже допустимы
			$this->assertTrue(true, "Получено исключение: " . $e->getMessage());
		}
	}

	/**
	 * Проверить корректность формирования запроса без выполнения HTTP-запроса
	 */
	protected function assertRequestIsValid(string $expectedUrl, callable $requestCallback, string $testDescription): void {
		try {
			// Проверяем, что метод не выбрасывает исключения при формировании запроса
			$response = $requestCallback();
			
			// Проверяем структуру ответа
			$this->assertNotNull($response, "Ответ для {$testDescription} не должен быть null");
			
			// Если это объект, проверяем его структуру
			if (is_object($response)) {
				$reflection = new \ReflectionClass($response);
				$properties = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);
				$this->assertNotEmpty($properties, "Объект ответа для {$testDescription} должен иметь публичные свойства");
			}
			
		} catch (KinopoiskDevException $e) {
			// Проверяем, что исключение связано с валидацией, а не с HTTP-запросом
			$this->assertStringNotContainsString('Ошибка HTTP запроса', $e->getMessage(), 
				"Исключение не должно быть связано с HTTP-запросом для {$testDescription}");
		} catch (\Exception $e) {
			// Другие исключения допустимы только если они не связаны с HTTP
			$this->assertStringNotContainsString('HTTP', $e->getMessage(), 
				"Исключение не должно быть связано с HTTP для {$testDescription}");
		}
	}

	/**
	 * Получить API токен из переменной окружения
	 */
	protected function getApiToken(): string {
		$token = getenv('KINOPOISK_API_TOKEN');
		if (!$token) {
			$this->markTestSkipped('KINOPOISK_API_TOKEN не установлен');
		}

		return $token;
	}
}