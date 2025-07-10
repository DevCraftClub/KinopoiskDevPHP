# Интерфейс CacheInterface

## Описание

`CacheInterface` - интерфейс для сервиса кэширования, определяющий контракт для работы с различными системами кэширования в приложении. Поддерживает базовые операции CRUD для кэша.

## Пространство имен

```php
namespace KinopoiskDev\Contracts;
```

## Объявление интерфейса

```php
interface CacheInterface
```

## Методы

### get()

Получает значение из кэша по ключу.

```php
public function get(string $key): mixed
```

**Параметры:**
- `$key` - Ключ кэша

**Возвращает:** `mixed|null` - Значение из кэша или null если не найдено

### set()

Сохраняет значение в кэш.

```php
public function set(string $key, mixed $value, int $ttl = 3600): bool
```

**Параметры:**
- `$key` - Ключ кэша
- `$value` - Значение для сохранения
- `$ttl` - Время жизни в секундах (по умолчанию 3600 - 1 час)

**Возвращает:** `bool` - True при успешном сохранении

### delete()

Удаляет значение из кэша.

```php
public function delete(string $key): bool
```

**Параметры:**
- `$key` - Ключ кэша

**Возвращает:** `bool` - True при успешном удалении

### has()

Проверяет наличие ключа в кэше.

```php
public function has(string $key): bool
```

**Параметры:**
- `$key` - Ключ кэша

**Возвращает:** `bool` - True если ключ существует

### clear()

Очищает весь кэш.

```php
public function clear(): bool
```

**Возвращает:** `bool` - True при успешной очистке

### getMultiple()

Получает множественные значения по ключам.

```php
public function getMultiple(array $keys): array
```

**Параметры:**
- `$keys` - Массив ключей

**Возвращает:** `array` - Ассоциативный массив ключ => значение

### setMultiple()

Сохраняет множественные значения.

```php
public function setMultiple(array $values, int $ttl = 3600): bool
```

**Параметры:**
- `$values` - Ассоциативный массив ключ => значение
- `$ttl` - Время жизни в секундах (по умолчанию 3600)

**Возвращает:** `bool` - True при успешном сохранении

## Примеры использования

### Реализация интерфейса с Redis

```php
use KinopoiskDev\Contracts\CacheInterface;
use Redis;

class RedisCache implements CacheInterface
{
    private Redis $redis;
    
    public function __construct(Redis $redis)
    {
        $this->redis = $redis;
    }
    
    public function get(string $key): mixed
    {
        $value = $this->redis->get($key);
        return $value !== false ? unserialize($value) : null;
    }
    
    public function set(string $key, mixed $value, int $ttl = 3600): bool
    {
        return $this->redis->setex($key, $ttl, serialize($value));
    }
    
    public function delete(string $key): bool
    {
        return $this->redis->del($key) > 0;
    }
    
    public function has(string $key): bool
    {
        return $this->redis->exists($key) > 0;
    }
    
    public function clear(): bool
    {
        return $this->redis->flushDB();
    }
    
    public function getMultiple(array $keys): array
    {
        $values = $this->redis->mget($keys);
        $result = [];
        
        foreach ($keys as $index => $key) {
            $result[$key] = $values[$index] !== false 
                ? unserialize($values[$index]) 
                : null;
        }
        
        return $result;
    }
    
    public function setMultiple(array $values, int $ttl = 3600): bool
    {
        $serialized = [];
        foreach ($values as $key => $value) {
            $serialized[$key] = serialize($value);
        }
        
        $this->redis->mset($serialized);
        
        foreach (array_keys($values) as $key) {
            $this->redis->expire($key, $ttl);
        }
        
        return true;
    }
}
```

### Реализация с файловым кэшем

```php
use KinopoiskDev\Contracts\CacheInterface;

class FileCache implements CacheInterface
{
    private string $cacheDir;
    
    public function __construct(string $cacheDir = '/tmp/cache')
    {
        $this->cacheDir = $cacheDir;
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0777, true);
        }
    }
    
    public function get(string $key): mixed
    {
        $file = $this->getFilePath($key);
        
        if (!file_exists($file)) {
            return null;
        }
        
        $data = unserialize(file_get_contents($file));
        
        if ($data['expires'] < time()) {
            unlink($file);
            return null;
        }
        
        return $data['value'];
    }
    
    public function set(string $key, mixed $value, int $ttl = 3600): bool
    {
        $data = [
            'value' => $value,
            'expires' => time() + $ttl
        ];
        
        return file_put_contents(
            $this->getFilePath($key), 
            serialize($data)
        ) !== false;
    }
    
    public function delete(string $key): bool
    {
        $file = $this->getFilePath($key);
        return file_exists($file) ? unlink($file) : true;
    }
    
    public function has(string $key): bool
    {
        return $this->get($key) !== null;
    }
    
    public function clear(): bool
    {
        $files = glob($this->cacheDir . '/*.cache');
        foreach ($files as $file) {
            unlink($file);
        }
        return true;
    }
    
    public function getMultiple(array $keys): array
    {
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $this->get($key);
        }
        return $result;
    }
    
    public function setMultiple(array $values, int $ttl = 3600): bool
    {
        foreach ($values as $key => $value) {
            if (!$this->set($key, $value, $ttl)) {
                return false;
            }
        }
        return true;
    }
    
    private function getFilePath(string $key): string
    {
        return $this->cacheDir . '/' . md5($key) . '.cache';
    }
}
```

### Использование в сервисе

```php
use KinopoiskDev\Contracts\CacheInterface;

class ApiService
{
    public function __construct(
        private readonly CacheInterface $cache
    ) {}
    
    public function getMovieData(int $movieId): ?array
    {
        $cacheKey = 'movie_' . $movieId;
        
        // Проверяем кэш
        $data = $this->cache->get($cacheKey);
        if ($data !== null) {
            return $data;
        }
        
        // Получаем данные из API
        $data = $this->fetchFromApi($movieId);
        
        // Сохраняем в кэш на 1 час
        $this->cache->set($cacheKey, $data, 3600);
        
        return $data;
    }
    
    public function invalidateMovie(int $movieId): void
    {
        $this->cache->delete('movie_' . $movieId);
    }
}
```

### Декоратор для логирования

```php
use KinopoiskDev\Contracts\CacheInterface;
use Psr\Log\LoggerInterface;

class LoggingCache implements CacheInterface
{
    public function __construct(
        private readonly CacheInterface $cache,
        private readonly LoggerInterface $logger
    ) {}
    
    public function get(string $key): mixed
    {
        $value = $this->cache->get($key);
        $hit = $value !== null;
        
        $this->logger->debug('Cache get', [
            'key' => $key,
            'hit' => $hit
        ]);
        
        return $value;
    }
    
    public function set(string $key, mixed $value, int $ttl = 3600): bool
    {
        $result = $this->cache->set($key, $value, $ttl);
        
        $this->logger->debug('Cache set', [
            'key' => $key,
            'ttl' => $ttl,
            'success' => $result
        ]);
        
        return $result;
    }
    
    // Остальные методы с логированием...
}
```

## Особенности

1. **Гибкость** - Поддержка любой системы кэширования (Redis, Memcached, файлы и т.д.)
2. **Простота** - Минимальный набор методов для базовых операций
3. **Типизация** - Поддержка mixed типа для значений
4. **TTL** - Встроенная поддержка времени жизни кэша
5. **Batch операции** - Методы для работы с множественными значениями

## Связанные классы

- [CacheService](../services/CacheService.md) - Базовая реализация интерфейса
- [Kinopoisk](../Kinopoisk.md) - Использует интерфейс для кэширования запросов
- `Symfony\Component\Cache\Adapter\FilesystemAdapter` - Адаптер для файлового кэша

## Требования

- PHP 8.3+
- Опционально: Redis, Memcached или другая система кэширования