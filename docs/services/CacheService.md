# Класс CacheService

## Описание

`CacheService` - сервис для работы с кэшем, реализующий интерфейс `CacheInterface` с использованием PSR-6 Cache. Обеспечивает типобезопасную работу с различными драйверами кэша.

## Пространство имен

```php
namespace KinopoiskDev\Services;
```

## Объявление класса

```php
final readonly class CacheService implements CacheInterface
```

## Интерфейсы

- [CacheInterface](../contracts/CacheInterface.md) - Контракт для кэширования

## Методы

### __construct()

Конструктор сервиса.

```php
public function __construct(
    private CacheItemPoolInterface $cache,
)
```

**Параметры:**
- `$cache` - PSR-6 кэш адаптер

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
- `$ttl` - Время жизни в секундах (по умолчанию 3600)

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
- `$ttl` - Время жизни в секундах

**Возвращает:** `bool` - True при успешном сохранении

## Приватные методы

### normalizeKey()

Нормализует ключ кэша для соответствия PSR-6.

```php
private function normalizeKey(string $key): string
```

**Параметры:**
- `$key` - Исходный ключ

**Возвращает:** `string` - Нормализованный ключ (только буквы, цифры, _, ., -)

## Примеры использования

### Базовое использование

```php
use KinopoiskDev\Services\CacheService;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

// Создание с файловым адаптером
$adapter = new FilesystemAdapter('kinopoisk', 3600, '/var/cache');
$cacheService = new CacheService($adapter);

// Сохранение данных
$cacheService->set('movie_123', [
    'title' => 'Матрица',
    'year' => 1999,
    'rating' => 8.7
], 7200); // Кэш на 2 часа

// Получение данных
$movie = $cacheService->get('movie_123');
if ($movie !== null) {
    echo $movie['title']; // Матрица
}

// Проверка существования
if ($cacheService->has('movie_123')) {
    echo "Фильм в кэше";
}

// Удаление
$cacheService->delete('movie_123');
```

### С Redis адаптером

```php
use KinopoiskDev\Services\CacheService;
use Symfony\Component\Cache\Adapter\RedisAdapter;

// Создание Redis соединения
$redis = RedisAdapter::createConnection('redis://localhost:6379');

// Создание адаптера
$adapter = new RedisAdapter($redis, 'kinopoisk', 3600);
$cacheService = new CacheService($adapter);

// Использование
$cacheService->set('user:session:abc123', [
    'user_id' => 42,
    'name' => 'John Doe',
    'permissions' => ['read', 'write']
], 1800); // 30 минут
```

### Множественные операции

```php
use KinopoiskDev\Services\CacheService;
use Symfony\Component\Cache\Adapter\MemcachedAdapter;

// Создание Memcached клиента
$client = MemcachedAdapter::createConnection('memcached://localhost:11211');
$adapter = new MemcachedAdapter($client, 'app', 3600);
$cacheService = new CacheService($adapter);

// Сохранение нескольких значений
$movies = [
    'movie_123' => ['title' => 'Матрица', 'year' => 1999],
    'movie_456' => ['title' => 'Начало', 'year' => 2010],
    'movie_789' => ['title' => 'Интерстеллар', 'year' => 2014]
];

$cacheService->setMultiple($movies, 3600);

// Получение нескольких значений
$keys = ['movie_123', 'movie_456', 'movie_789'];
$cachedMovies = $cacheService->getMultiple($keys);

foreach ($cachedMovies as $key => $movie) {
    echo "{$key}: {$movie['title']} ({$movie['year']})\n";
}
```

### Кэширование результатов API

```php
use KinopoiskDev\Services\CacheService;

class MovieApiService
{
    public function __construct(
        private CacheService $cache,
        private ApiClient $api
    ) {}
    
    public function getMovie(int $id): ?array
    {
        $cacheKey = "api_movie_{$id}";
        
        // Проверяем кэш
        $cached = $this->cache->get($cacheKey);
        if ($cached !== null) {
            return $cached;
        }
        
        // Получаем из API
        try {
            $movie = $this->api->fetchMovie($id);
            
            // Сохраняем в кэш
            $this->cache->set($cacheKey, $movie, 3600);
            
            return $movie;
        } catch (\Exception $e) {
            return null;
        }
    }
    
    public function invalidateMovie(int $id): void
    {
        $this->cache->delete("api_movie_{$id}");
    }
}
```

### С тегированием (Symfony Cache)

```php
use KinopoiskDev\Services\CacheService;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

// Создание тегированного адаптера
$filesystemAdapter = new FilesystemAdapter();
$adapter = new TagAwareAdapter($filesystemAdapter);
$cacheService = new CacheService($adapter);

// Расширенный сервис с поддержкой тегов
class TaggedCacheService extends CacheService
{
    public function setWithTags(string $key, mixed $value, array $tags, int $ttl = 3600): bool
    {
        $item = $this->cache->getItem($this->normalizeKey($key));
        $item->set($value);
        $item->expiresAfter($ttl);
        $item->tag($tags);
        
        return $this->cache->save($item);
    }
    
    public function invalidateTags(array $tags): bool
    {
        return $this->cache->invalidateTags($tags);
    }
}
```

### Обработка ошибок

```php
use KinopoiskDev\Services\CacheService;
use Psr\Log\LoggerInterface;

class SafeCacheService
{
    public function __construct(
        private CacheService $cache,
        private LoggerInterface $logger
    ) {}
    
    public function get(string $key): mixed
    {
        try {
            return $this->cache->get($key);
        } catch (\Exception $e) {
            $this->logger->error('Cache get failed', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
    
    public function set(string $key, mixed $value, int $ttl = 3600): bool
    {
        try {
            return $this->cache->set($key, $value, $ttl);
        } catch (\Exception $e) {
            $this->logger->error('Cache set failed', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}
```

## Особенности

1. **Final readonly класс** - Не может быть расширен, свойства неизменяемы
2. **PSR-6 совместимость** - Работает с любым PSR-6 адаптером
3. **Нормализация ключей** - Автоматически преобразует ключи для PSR-6
4. **Безопасность** - Перехватывает InvalidArgumentException
5. **Batch операции** - Поддержка множественных операций

## Связанные классы

- [CacheInterface](../contracts/CacheInterface.md) - Интерфейс, который реализует сервис
- [Kinopoisk](../Kinopoisk.md) - Использует сервис для кэширования
- `Psr\Cache\CacheItemPoolInterface` - PSR-6 интерфейс
- `Symfony\Component\Cache\Adapter\*` - Различные адаптеры кэша

## Требования

- PHP 8.3+
- PSR-6 Cache interfaces
- Symfony Cache Component (рекомендуется)