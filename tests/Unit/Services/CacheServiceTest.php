<?php

declare(strict_types=1);

namespace KinopoiskDev\Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use KinopoiskDev\Services\CacheService;
use KinopoiskDev\Contracts\CacheInterface;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;
use GuzzleHttp\Psr7\Response;

/**
 * @group unit
 * @group services
 * @group cache-service
 */
class CacheServiceTest extends TestCase
{
    private MockObject|CacheItemPoolInterface $cachePoolMock;
    private MockObject|CacheItemInterface $cacheItemMock;
    private CacheService $cacheService;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->cachePoolMock = $this->createMock(CacheItemPoolInterface::class);
        $this->cacheItemMock = $this->createMock(CacheItemInterface::class);
        $this->cacheService = new CacheService($this->cachePoolMock);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function test_constructor_createsInstance(): void
    {
        $this->assertInstanceOf(CacheService::class, $this->cacheService);
        $this->assertInstanceOf(CacheInterface::class, $this->cacheService);
    }

    public function test_get_withExistingItem_returnsValue(): void
    {
        $key = 'test_key';
        $expectedValue = 'test_value';
        
        $this->cachePoolMock->expects($this->once())
            ->method('getItem')
            ->with($key)
            ->willReturn($this->cacheItemMock);
        
        $this->cacheItemMock->expects($this->once())
            ->method('isHit')
            ->willReturn(true);
        
        $this->cacheItemMock->expects($this->once())
            ->method('get')
            ->willReturn($expectedValue);
        
        $result = $this->cacheService->get($key);
        
        $this->assertEquals($expectedValue, $result);
    }

    public function test_get_withNonExistingItem_returnsNull(): void
    {
        $key = 'non_existing_key';
        
        $this->cachePoolMock->expects($this->once())
            ->method('getItem')
            ->with($key)
            ->willReturn($this->cacheItemMock);
        
        $this->cacheItemMock->expects($this->once())
            ->method('isHit')
            ->willReturn(false);
        
        $result = $this->cacheService->get($key);
        
        $this->assertNull($result);
    }

    public function test_get_withCacheException_returnsNull(): void
    {
        $key = 'invalid_key';
        $exception = $this->createMock(InvalidArgumentException::class);
        $this->cachePoolMock->expects($this->once())
            ->method('getItem')
            ->with($key)
            ->willThrowException($exception);
        $result = $this->cacheService->get($key);
        $this->assertNull($result);
    }

    public function test_set_withValidData_setsValue(): void
    {
        $key = 'test_key';
        $value = 'test_value';
        $ttl = 3600;
        
        $this->cachePoolMock->expects($this->once())
            ->method('getItem')
            ->with($key)
            ->willReturn($this->cacheItemMock);
        
        $this->cacheItemMock->expects($this->once())
            ->method('set')
            ->with($value);
        
        $this->cacheItemMock->expects($this->once())
            ->method('expiresAfter')
            ->with($ttl);
        
        $this->cachePoolMock->expects($this->once())
            ->method('save')
            ->with($this->cacheItemMock)
            ->willReturn(true);
        
        $result = $this->cacheService->set($key, $value, $ttl);
        
        $this->assertTrue($result);
    }

    public function test_set_withDefaultTtl_setsValueWithDefaultTtl(): void
    {
        $key = 'test_key';
        $value = 'test_value';
        
        $this->cachePoolMock->expects($this->once())
            ->method('getItem')
            ->with($key)
            ->willReturn($this->cacheItemMock);
        
        $this->cacheItemMock->expects($this->once())
            ->method('set')
            ->with($value);
        
        $this->cacheItemMock->expects($this->once())
            ->method('expiresAfter')
            ->with(3600); // Default TTL
        
        $this->cachePoolMock->expects($this->once())
            ->method('save')
            ->with($this->cacheItemMock)
            ->willReturn(true);
        
        $result = $this->cacheService->set($key, $value);
        
        $this->assertTrue($result);
    }

    public function test_set_withCacheException_returnsFalse(): void
    {
        $key = 'invalid_key';
        $value = 'test_value';
        $exception = $this->createMock(InvalidArgumentException::class);
        $this->cachePoolMock->expects($this->once())
            ->method('getItem')
            ->with($key)
            ->willThrowException($exception);
        $result = $this->cacheService->set($key, $value);
        $this->assertFalse($result);
    }

    public function test_set_withSaveFailure_returnsFalse(): void
    {
        $key = 'test_key';
        $value = 'test_value';
        
        $this->cachePoolMock->expects($this->once())
            ->method('getItem')
            ->with($key)
            ->willReturn($this->cacheItemMock);
        
        $this->cacheItemMock->expects($this->once())
            ->method('set')
            ->with($value);
        
        $this->cacheItemMock->expects($this->once())
            ->method('expiresAfter')
            ->with(3600);
        
        $this->cachePoolMock->expects($this->once())
            ->method('save')
            ->with($this->cacheItemMock)
            ->willReturn(false);
        
        $result = $this->cacheService->set($key, $value);
        
        $this->assertFalse($result);
    }

    public function test_delete_withExistingItem_deletesValue(): void
    {
        $key = 'test_key';
        
        $this->cachePoolMock->expects($this->once())
            ->method('deleteItem')
            ->with($key)
            ->willReturn(true);
        
        $result = $this->cacheService->delete($key);
        
        $this->assertTrue($result);
    }

    public function test_delete_withNonExistingItem_returnsTrue(): void
    {
        $key = 'non_existing_key';
        
        $this->cachePoolMock->expects($this->once())
            ->method('deleteItem')
            ->with($key)
            ->willReturn(true);
        
        $result = $this->cacheService->delete($key);
        
        $this->assertTrue($result);
    }

    public function test_delete_withCacheException_returnsFalse(): void
    {
        $key = 'invalid_key';
        $exception = $this->createMock(InvalidArgumentException::class);
        $this->cachePoolMock->expects($this->once())
            ->method('deleteItem')
            ->with($key)
            ->willThrowException($exception);
        $result = $this->cacheService->delete($key);
        $this->assertFalse($result);
    }

    public function test_clear_clearsAllItems(): void
    {
        $this->cachePoolMock->expects($this->once())
            ->method('clear')
            ->willReturn(true);
        
        $result = $this->cacheService->clear();
        
        $this->assertTrue($result);
    }

    public function test_clear_withCacheException_returnsFalse(): void
    {
        $exception = $this->createMock(InvalidArgumentException::class);
        $this->cachePoolMock->expects($this->once())
            ->method('clear')
            ->willThrowException($exception);
        $result = $this->cacheService->clear();
        $this->assertFalse($result);
    }

    public function test_has_withExistingItem_returnsTrue(): void
    {
        $key = 'existing_key';
        $this->cachePoolMock->expects($this->once())
            ->method('hasItem')
            ->with($key)
            ->willReturn(true);
        $result = $this->cacheService->has($key);
        $this->assertTrue($result);
    }

    public function test_has_withNonExistingItem_returnsFalse(): void
    {
        $key = 'non_existing_key';
        $this->cachePoolMock->expects($this->once())
            ->method('hasItem')
            ->with($key)
            ->willReturn(false);
        $result = $this->cacheService->has($key);
        $this->assertFalse($result);
    }

    public function test_has_withCacheException_returnsFalse(): void
    {
        $key = 'invalid_key';
        $exception = $this->createMock(InvalidArgumentException::class);
        $this->cachePoolMock->expects($this->once())
            ->method('hasItem')
            ->with($key)
            ->willThrowException($exception);
        $result = $this->cacheService->has($key);
        $this->assertFalse($result);
    }

    public function test_getMultiple_withValidKeys_returnsValues(): void
    {
        $keys = ['key1', 'key2', 'key3'];
        $expectedValues = [
            'key1' => 'value1',
            'key2' => 'value2',
            'key3' => null
        ];
        
        $this->cachePoolMock->expects($this->once())
            ->method('getItems')
            ->with($keys)
            ->willReturn([
                'key1' => $this->createCacheItemMock('key1', 'value1', true),
                'key2' => $this->createCacheItemMock('key2', 'value2', true),
                'key3' => $this->createCacheItemMock('key3', null, false)
            ]);
        
        $result = $this->cacheService->getMultiple($keys);
        
        $this->assertEquals($expectedValues, $result);
    }

    public function test_getMultiple_withEmptyKeys_returnsEmptyArray(): void
    {
        $result = $this->cacheService->getMultiple([]);
        
        $this->assertEquals([], $result);
    }

    public function test_getMultiple_withCacheException_returnsEmptyArray(): void
    {
        $key = 'invalid_key';
        $exception = $this->createMock(InvalidArgumentException::class);
        $this->cachePoolMock->expects($this->once())
            ->method('getItems')
            ->willThrowException($exception);
        $result = $this->cacheService->getMultiple([$key]);
        $this->assertEquals([$key => null], $result);
    }

    public function test_setMultiple_withValidData_setsValues(): void
    {
        $values = ['key1' => 'value1', 'key2' => 'value2'];
        $ttl = 3600;
        $itemMocks = [];
        foreach ($values as $key => $value) {
            $itemMock = $this->createMock(CacheItemInterface::class);
            $itemMock->expects($this->once())->method('set')->with($value);
            $itemMock->expects($this->once())->method('expiresAfter')->with($ttl);
            $itemMocks[$key] = $itemMock;
        }
        $this->cachePoolMock->expects($this->exactly(count($values)))
            ->method('getItem')
            ->with($this->logicalOr('key1', 'key2'))
            ->willReturnCallback(function($key) use ($itemMocks) {
                return $itemMocks[$key];
            });
        $this->cachePoolMock->expects($this->exactly(count($values)))
            ->method('saveDeferred')
            ->with($this->logicalOr($itemMocks['key1'], $itemMocks['key2']))
            ->willReturn(true);
        $this->cachePoolMock->expects($this->once())
            ->method('commit')
            ->willReturn(true);
        $result = $this->cacheService->setMultiple($values, $ttl);
        $this->assertTrue($result);
    }

    public function test_setMultiple_withEmptyValues_returnsTrue(): void
    {
        $result = $this->cacheService->setMultiple([]);
        
        $this->assertTrue($result);
    }

    public function test_setMultiple_withCacheException_returnsFalse(): void
    {
        $key = 'invalid_key';
        $value = 'test_value';
        $exception = $this->createMock(InvalidArgumentException::class);
        $this->cachePoolMock->expects($this->once())
            ->method('getItem')
            ->with($key)
            ->willThrowException($exception);
        $result = $this->cacheService->setMultiple([$key => $value]);
        $this->assertFalse($result);
    }

    public function test_deleteMultiple_withValidKeys_deletesValues(): void
    {
        $keys = ['key1', 'key2'];
        $this->cachePoolMock->expects($this->exactly(count($keys)))
            ->method('deleteItem')
            ->with($this->logicalOr('key1', 'key2'))
            ->willReturn(true);
        $result = $this->cacheService->deleteMultiple($keys);
        $this->assertTrue($result);
    }

    public function test_deleteMultiple_withEmptyKeys_returnsTrue(): void
    {
        $result = $this->cacheService->deleteMultiple([]);
        
        $this->assertTrue($result);
    }

    public function test_deleteMultiple_withCacheException_returnsFalse(): void
    {
        $key = 'invalid_key';
        $exception = $this->createMock(InvalidArgumentException::class);
        $this->cachePoolMock->expects($this->once())
            ->method('deleteItem')
            ->with($key)
            ->willThrowException($exception);
        $result = $this->cacheService->deleteMultiple([$key]);
        $this->assertFalse($result);
    }

    public function test_set_withResponseObject_setsSerializedResponse(): void
    {
        $key = 'response_key';
        $response = new Response(200, ['Content-Type' => 'application/json'], '{"success": true}');
        $ttl = 3600;
        
        $this->cachePoolMock->expects($this->once())
            ->method('getItem')
            ->with($key)
            ->willReturn($this->cacheItemMock);
        
        $this->cacheItemMock->expects($this->once())
            ->method('set')
            ->with($this->callback(function ($value) {
                return $value instanceof Response;
            }));
        
        $this->cacheItemMock->expects($this->once())
            ->method('expiresAfter')
            ->with($ttl);
        
        $this->cachePoolMock->expects($this->once())
            ->method('save')
            ->with($this->cacheItemMock)
            ->willReturn(true);
        
        $result = $this->cacheService->set($key, $response, $ttl);
        
        $this->assertTrue($result);
    }

    public function test_get_withResponseObject_returnsResponse(): void
    {
        $key = 'response_key';
        $response = new Response(200, ['Content-Type' => 'application/json'], '{"success": true}');
        
        $this->cachePoolMock->expects($this->once())
            ->method('getItem')
            ->with($key)
            ->willReturn($this->cacheItemMock);
        
        $this->cacheItemMock->expects($this->once())
            ->method('isHit')
            ->willReturn(true);
        
        $this->cacheItemMock->expects($this->once())
            ->method('get')
            ->willReturn($response);
        
        $result = $this->cacheService->get($key);
        
        $this->assertInstanceOf(Response::class, $result);
        $this->assertEquals(200, $result->getStatusCode());
        $this->assertEquals('{"success": true}', (string) $result->getBody());
    }

    private function createCacheItemMock(string $key, mixed $value, bool $isHit): MockObject|CacheItemInterface
    {
        $mock = $this->createMock(CacheItemInterface::class);
        $mock->method('getKey')->willReturn($key);
        $mock->method('get')->willReturn($value);
        $mock->method('isHit')->willReturn($isHit);
        $mock->method('set')->willReturnSelf();
        $mock->method('expiresAfter')->willReturnSelf();
        
        return $mock;
    }
} 