# KinoPoisk.dev PHP Client - API Documentation

![PHP Version](https://img.shields.io/badge/PHP-%3E%3D8.3-blue)
![License](https://img.shields.io/badge/license-MIT-green)
![API Version](https://img.shields.io/badge/API-v1.4-orange)

## Содержание

1. [Введение](#введение)
2. [Архитектура проекта](#архитектура-проекта)
3. [Основные компоненты](#основные-компоненты)
4. [API Reference](#api-reference)
5. [Модели данных](#модели-данных)
6. [Фильтры и поиск](#фильтры-и-поиск)
7. [Обработка ошибок](#обработка-ошибок)
8. [Примеры использования](#примеры-использования)

## Введение

**KinoPoisk.dev PHP Client** — это полнофункциональная библиотека для работы с неофициальным API [KinoPoisk.dev](https://kinopoisk.dev) на PHP 8.3+. Библиотека предоставляет объектно-ориентированный интерфейс для доступа ко всем возможностям API с полной поддержкой типов и детальной документацией.

### Основные возможности
- ✅ Полное покрытие API v1.4
- ✅ Строгая типизация PHP 8.3+
- ✅ Система кэширования
- ✅ Мощные фильтры с fluent interface
- ✅ Обработка ошибок
- ✅ PSR-совместимость

## Архитектура проекта

```
src/
├── Kinopoisk.php              # Главный класс клиента
├── Http/                      # HTTP клиенты для различных сущностей
├── Models/                    # Модели данных
├── Filter/                    # Фильтры для поиска и сортировки
├── Exceptions/                # Исключения
├── Contracts/                 # Интерфейсы
├── Services/                  # Сервисы (кэш, HTTP, валидация)
├── Enums/                     # Перечисления
├── Responses/                 # DTO для ответов API
├── Utils/                     # Утилиты
└── Attributes/                # Атрибуты
```

## Основные компоненты

### Главный класс - Kinopoisk

Базовый класс для работы с API, предоставляющий основную функциональность HTTP запросов, кэширования и обработки ошибок.

#### Конструктор

```php
public function __construct(
    ?string $apiToken = null,
    ?HttpClientInterface $httpClient = null,
    ?CacheInterface $cache = null,
    ?LoggerInterface $logger = null,
    private readonly bool $useCache = false,
)
```

**Параметры:**
- `$apiToken` - Токен API Kinopoisk.dev
- `$httpClient` - HTTP клиент (по умолчанию Guzzle)
- `$cache` - Реализация кэша
- `$logger` - Логгер для отладки
- `$useCache` - Включить кэширование

#### Основные методы

**makeRequest()**
```php
public function makeRequest(
    string $method,
    string $endpoint,
    array $queryParams = [],
    ?string $apiVersion = null,
): ResponseInterface
```

Выполняет HTTP запрос к API с поддержкой кэширования.

**parseResponse()**
```php
public function parseResponse(ResponseInterface $response): array
```

Обрабатывает ответ от API с валидацией статус кодов.

## API Reference

### HTTP Клиенты

#### MovieRequests

Основной класс для работы с фильмами и сериалами.

```php
namespace KinopoiskDev\Http;

/**
 * Класс для API-запросов, связанных с фильмами
 *
 * @package KinopoiskDev\Http
 * @since   1.0.0
 * @version 1.0.0
 */
class MovieRequests extends Kinopoisk
```

##### Методы

**getMovieById()**
```php
/**
 * Получает фильм по его ID
 *
 * @param int $movieId Уникальный ID фильма
 * @return Movie Фильм со всеми доступными данными
 * @throws KinopoiskDevException При ошибках API
 */
public function getMovieById(int $movieId): Movie
```

**getRandomMovie()**
```php
/**
 * Получает случайный фильм
 *
 * @param MovieSearchFilter|null $filters Опциональные фильтры
 * @return Movie Случайный фильм
 * @throws KinopoiskDevException При ошибках API
 */
public function getRandomMovie(?MovieSearchFilter $filters = null): Movie
```

**searchMovies()**
```php
/**
 * Ищет фильмы по различным критериям
 *
 * @param MovieSearchFilter|null $filters Объект фильтра
 * @param int $page Номер страницы (по умолчанию: 1)
 * @param int $limit Количество результатов (по умолчанию: 10, макс: 250)
 * @return MovieDocsResponseDto Результаты поиска с пагинацией
 */
public function searchMovies(?MovieSearchFilter $filters = null, int $page = 1, int $limit = 10): MovieDocsResponseDto
```

**getMovieAwards()**
```php
/**
 * Получает награды фильмов с возможностью фильтрации
 *
 * @param MovieSearchFilter|null $filters Объект фильтрации наград
 * @param int $page Номер страницы (по умолчанию 1)
 * @param int $limit Количество результатов (по умолчанию 10, макс 250)
 * @return MovieAwardDocsResponseDto Объект ответа с наградами и метаданными
 */
public function getMovieAwards(?MovieSearchFilter $filters = null, int $page = 1, int $limit = 10): MovieAwardDocsResponseDto
```

**getPossibleValuesByField()**
```php
/**
 * Получает возможные значения для определенного поля
 *
 * @param string $field Поле (genres, countries, type, typeNumber, status)
 * @return array Массив с возможными значениями
 */
public function getPossibleValuesByField(string $field): array
```

##### Удобные методы

```php
// Получить новейшие фильмы
public function getLatestMovies(?int $year = null, int $page = 1, int $limit = 10): MovieDocsResponseDto

// Фильмы по жанру
public function getMoviesByGenre(string|array $genres, int $page = 1, int $limit = 10): MovieDocsResponseDto

// Фильмы по стране
public function getMoviesByCountry(string|array $countries, int $page = 1, int $limit = 10): MovieDocsResponseDto

// Фильмы по диапазону лет
public function getMoviesByYearRange(int $fromYear, int $toYear, int $page = 1, int $limit = 10): MovieDocsResponseDto
```

#### PersonRequests

Класс для работы с персонами (актеры, режиссеры и т.д.).

```php
namespace KinopoiskDev\Http;

class PersonRequests extends Kinopoisk
```

#### ImageRequests

Класс для работы с изображениями.

```php
namespace KinopoiskDev\Http;

class ImageRequests extends Kinopoisk
```

#### ListRequests

Класс для работы с коллекциями фильмов.

```php
namespace KinopoiskDev\Http;

class ListRequests extends Kinopoisk
```

#### KeywordRequests

Класс для работы с ключевыми словами.

```php
namespace KinopoiskDev\Http;

class KeywordRequests extends Kinopoisk
```

#### StudioRequests

Класс для работы со студиями.

```php
namespace KinopoiskDev\Http;

class StudioRequests extends Kinopoisk
```

#### ReviewRequests

Класс для работы с рецензиями.

```php
namespace KinopoiskDev\Http;

class ReviewRequests extends Kinopoisk
```

#### SeasonRequests

Класс для работы с сезонами сериалов.

```php
namespace KinopoiskDev\Http;

class SeasonRequests extends Kinopoisk
```

## Модели данных

### Movie

Основная модель для представления фильма или сериала.

```php
namespace KinopoiskDev\Models;

/**
 * Класс для представления фильма/сериала из API Kinopoisk.dev
 *
 * @package KinopoiskDev\Models
 * @since   1.0.0
 * @version 1.0.0
 */
readonly class Movie implements BaseModel
```

#### Основные свойства

```php
public function __construct(
    public ?int $id = null,                    // Уникальный идентификатор
    public ?ExternalId $externalId = null,     // Внешние идентификаторы
    public ?string $name = null,               // Название на русском
    public ?string $alternativeName = null,    // Альтернативное название
    public ?string $enName = null,             // Название на английском
    public ?array $names = [],                 // Все названия
    public ?MovieType $type = null,            // Тип (фильм, сериал)
    public ?int $year = null,                  // Год выпуска
    public ?string $description = null,        // Полное описание
    public ?string $shortDescription = null,   // Краткое описание
    public ?Rating $rating = null,             // Рейтинги
    public ?Votes $votes = null,               // Количество голосов
    public ?ShortImage $poster = null,         // Постер
    public array $genres = [],                 // Жанры
    public array $countries = [],              // Страны
    public array $persons = [],                // Участники
    public ?int $movieLength = null,           // Длительность
    public bool $isSeries = false,             // Является ли сериалом
    // ... другие свойства
)
```

#### Методы

**fromArray()**
```php
/**
 * Создает объект Movie из массива данных API
 *
 * @param array $data Массив данных о фильме от API
 * @return Movie Новый экземпляр класса Movie
 */
public static function fromArray(array $data): Movie
```

**toArray()**
```php
/**
 * Преобразует объект в массив данных
 *
 * @return array Массив с данными о фильме
 */
public function toArray(): array
```

##### Удобные методы

```php
// Получить рейтинг Кинопоиска
public function getKinopoiskRating(): ?float

// Получить рейтинг IMDB
public function getImdbRating(): ?float

// Получить URL постера
public function getPosterUrl(): ?string

// Получить названия жанров
public function getGenreNames(): array

// Получить названия стран
public function getCountryNames(): array

// Получить URL страницы в IMDB
public function getImdbUrl(): ?string

// Получить URL страницы в TMDB
public function getTmdbUrl(): ?string
```

### Другие модели

#### Person
```php
namespace KinopoiskDev\Models;

readonly class Person implements BaseModel
```

Модель для представления персоны (актер, режиссер и т.д.).

#### Image
```php
namespace KinopoiskDev\Models;

readonly class Image implements BaseModel
```

Модель для изображений.

#### Rating
```php
namespace KinopoiskDev\Models;

readonly class Rating implements BaseModel
```

Модель для рейтингов (Кинопоиск, IMDB, TMDB и т.д.).

#### Votes
```php
namespace KinopoiskDev\Models;

readonly class Votes implements BaseModel
```

Модель для количества голосов.

## Фильтры и поиск

### MovieSearchFilter

Основной класс для создания фильтров при поиске фильмов.

```php
namespace KinopoiskDev\Filter;

/**
 * Класс для создания фильтров при поиске фильмов
 */
class MovieSearchFilter extends MovieFilter
```

#### Методы фильтрации

**Поиск по названию**
```php
public function searchByName(string $query): self
public function searchByAlternativeName(string $query): self
public function searchByAllNames(string $query): self
```

**Фильтрация по годам**
```php
public function year(int $year): self
public function withYearBetween(int $fromYear, int $toYear): self
```

**Фильтрация по рейтингам**
```php
public function withRatingBetween(float $minRating, float $maxRating, string $field = 'kp'): self
public function withMinRating(float $minRating, string $field = 'kp'): self
```

**Фильтрация по голосам**
```php
public function withVotesBetween(int $minVotes, int $maxVotes, string $field = 'kp'): self
public function withMinVotes(int $minVotes, string $field = 'kp'): self
```

**Фильтрация по жанрам**
```php
public function withIncludedGenres(string|array $genres): self
public function withExcludedGenres(string|array $genres): self
public function withAllGenres(array $genres): self
```

**Фильтрация по странам**
```php
public function withIncludedCountries(string|array $countries): self
public function withExcludedCountries(string|array $countries): self
public function withAllCountries(array $countries): self
```

**Фильтрация по типу контента**
```php
public function onlyMovies(): self
public function onlySeries(): self
public function onlyAnimated(): self
```

**Фильтрация по участникам**
```php
public function withActor(string|int $actor): self
public function withDirector(string|int $director): self
```

**Фильтрация по топам**
```php
public function inTop250(): self
public function inTop10(): self
```

**Сортировка**
```php
public function sortByKinopoiskRating(string $direction = 'desc'): self
public function sortByImdbRating(string $direction = 'desc'): self
public function sortByYear(string $direction = 'desc'): self
public function sortByCreated(string $direction = 'desc'): self
```

### KeywordSearchFilter

Класс для фильтрации ключевых слов.

```php
namespace KinopoiskDev\Filter;

class KeywordSearchFilter
```

### PersonSearchFilter

Класс для фильтрации персон.

```php
namespace KinopoiskDev\Filter;

class PersonSearchFilter
```

## Обработка ошибок

### Исключения

#### KinopoiskDevException

Базовое исключение для ошибок библиотеки.

```php
namespace KinopoiskDev\Exceptions;

/**
 * Базовое исключение для ошибок Kinopoisk.dev клиента
 */
class KinopoiskDevException extends Exception
{
    public function __construct(
        string $message = '',
        int $code = 0,
        ?Exception $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}
```

#### KinopoiskResponseException

Исключение для ошибок ответов API.

```php
namespace KinopoiskDev\Exceptions;

class KinopoiskResponseException extends KinopoiskDevException
```

#### ValidationException

Исключение для ошибок валидации.

```php
namespace KinopoiskDev\Exceptions;

class ValidationException extends KinopoiskDevException
```

### Коды ошибок HTTP

```php
namespace KinopoiskDev\Enums;

enum HttpStatusCode: int
{
    case OK = 200;
    case UNAUTHORIZED = 401;
    case FORBIDDEN = 403;
    case NOT_FOUND = 404;
    case INTERNAL_SERVER_ERROR = 500;
}
```

## Интерфейсы и контракты

### CacheInterface

Интерфейс для работы с кэшированием.

```php
namespace KinopoiskDev\Contracts;

/**
 * Интерфейс для сервиса кэширования
 *
 * @package KinopoiskDev\Contracts
 * @since   1.0.0
 * @version 1.0.0
 */
interface CacheInterface
{
    /**
     * Получает значение из кэша по ключу
     */
    public function get(string $key): mixed;

    /**
     * Сохраняет значение в кэш
     */
    public function set(string $key, mixed $value, int $ttl = 3600): bool;

    /**
     * Удаляет значение из кэша
     */
    public function delete(string $key): bool;

    /**
     * Проверяет наличие ключа в кэше
     */
    public function has(string $key): bool;

    /**
     * Очищает весь кэш
     */
    public function clear(): bool;

    /**
     * Получает множественные значения по ключам
     */
    public function getMultiple(array $keys): array;

    /**
     * Сохраняет множественные значения
     */
    public function setMultiple(array $values, int $ttl = 3600): bool;
}
```

### HttpClientInterface

Интерфейс для HTTP клиента.

```php
namespace KinopoiskDev\Contracts;

interface HttpClientInterface
{
    public function send(RequestInterface $request): ResponseInterface;
}
```

### LoggerInterface

Интерфейс для логирования.

```php
namespace KinopoiskDev\Contracts;

interface LoggerInterface
{
    public function debug(string $message, array $context = []): void;
    public function info(string $message, array $context = []): void;
    public function warning(string $message, array $context = []): void;
    public function error(string $message, array $context = []): void;
}
```

## Сервисы

### ValidationService

Сервис для валидации данных.

```php
namespace KinopoiskDev\Services;

class ValidationService
```

### CacheService

Реализация кэширования на основе Symfony Cache.

```php
namespace KinopoiskDev\Services;

class CacheService implements CacheInterface
```

### HttpService

Обертка над Guzzle HTTP клиентом.

```php
namespace KinopoiskDev\Services;

class HttpService implements HttpClientInterface
```

## Перечисления (Enums)

### MovieType

```php
namespace KinopoiskDev\Enums;

enum MovieType: string
{
    case MOVIE = 'movie';
    case TV_SERIES = 'tv-series';
    case CARTOON = 'cartoon';
    case ANIME = 'anime';
    case ANIMATED_SERIES = 'animated-series';
    case TV_SHOW = 'tv-show';
}
```

### MovieStatus

```php
namespace KinopoiskDev\Enums;

enum MovieStatus: string
{
    case FILMING = 'filming';
    case PRE_PRODUCTION = 'pre-production';
    case COMPLETED = 'completed';
    case ANNOUNCED = 'announced';
    case POST_PRODUCTION = 'post-production';
}
```

### PersonProfession

```php
namespace KinopoiskDev\Enums;

enum PersonProfession: string
{
    case ACTOR = 'актер';
    case DIRECTOR = 'режиссер';
    case PRODUCER = 'продюсер';
    case WRITER = 'сценарист';
    case OPERATOR = 'оператор';
    case COMPOSER = 'композитор';
    // ... другие профессии
}
```

## Примеры использования

### Базовое использование

```php
<?php

require_once 'vendor/autoload.php';

use KinopoiskDev\Http\MovieRequests;
use KinopoiskDev\Filter\MovieSearchFilter;

// Инициализация клиента
$apiToken = 'YOUR_API_TOKEN';
$movieClient = new MovieRequests($apiToken);

try {
    // Получение фильма по ID
    $movie = $movieClient->getMovieById(666);
    echo "Фильм: {$movie->name} ({$movie->year})\n";
    echo "Рейтинг КП: {$movie->getKinopoiskRating()}\n";
    
} catch (KinopoiskDevException $e) {
    echo "Ошибка: " . $e->getMessage() . "\n";
}
```

### Сложные фильтры

```php
// Поиск лучших российских драм 2020-2024
$filter = new MovieSearchFilter();
$filter->withIncludedCountries('Россия')
       ->withIncludedGenres('драма')
       ->withYearBetween(2020, 2024)
       ->withRatingBetween(7.0, 10.0)
       ->withVotesBetween(1000, null)
       ->onlyMovies()
       ->sortByKinopoiskRating();

$results = $movieClient->searchMovies($filter, 1, 20);

foreach ($results->docs as $movie) {
    echo sprintf(
        "%s (%d) - %.1f\n",
        $movie->name,
        $movie->year,
        $movie->getKinopoiskRating()
    );
}
```

### Работа с кэшированием

```php
// Включение кэширования
$movieClient = new MovieRequests($apiToken, null, null, null, true);

// Первый запрос - из API
$movie1 = $movieClient->getMovieById(666);

// Второй запрос - из кэша
$movie2 = $movieClient->getMovieById(666);
```

---

## Заключение

Данная документация покрывает основные возможности библиотеки KinoPoisk.dev PHP Client. Для получения актуальной информации об API рекомендуется обращаться к официальной документации [KinoPoisk.dev](https://kinopoisk.dev) и репозиторию проекта.

Библиотека активно развивается и поддерживается. При возникновении вопросов или обнаружении ошибок рекомендуется создавать issues в репозитории проекта.