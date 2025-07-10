# Response классы для работы с ответами API

## Описание

Пространство имен `KinopoiskDev\Responses` содержит классы для структурированной работы с ответами API Kinopoisk.dev. Все классы представляют собой DTO (Data Transfer Objects) для типобезопасной работы с данными.

## Структура

### Базовые классы ответов

#### BaseResponseDto

Абстрактный базовый класс для всех ответов API.

```php
abstract class BaseResponseDto
{
    public function toArray(): array;
    public function toJson(): string;
}
```

#### BaseDocsResponseDto

Базовый класс для пагинированных ответов с документами.

```php
class BaseDocsResponseDto extends BaseResponseDto
{
    public array $docs;     // Массив документов
    public int $total;      // Общее количество
    public int $limit;      // Лимит на страницу
    public int $page;       // Текущая страница
    public int $pages;      // Всего страниц
}
```

#### ErrorResponseDto

Базовый класс для ошибок API.

```php
class ErrorResponseDto extends BaseResponseDto
{
    public string $error;      // Код ошибки
    public string $message;    // Сообщение об ошибке
    public int $statusCode;    // HTTP статус код
}
```

### Классы ответов API (папка Api/)

#### MovieDocsResponseDto

Ответ со списком фильмов.

```php
class MovieDocsResponseDto extends BaseDocsResponseDto
{
    /** @var Movie[] */
    public array $docs;
}
```

#### PersonDocsResponseDto

Ответ со списком персон.

```php
class PersonDocsResponseDto extends BaseDocsResponseDto
{
    /** @var Person[] */
    public array $docs;
}
```

#### ReviewDocsResponseDto

Ответ со списком отзывов.

```php
class ReviewDocsResponseDto extends BaseDocsResponseDto
{
    /** @var Review[] */
    public array $docs;
}
```

#### SeasonDocsResponseDto

Ответ со списком сезонов.

```php
class SeasonDocsResponseDto extends BaseDocsResponseDto
{
    /** @var Season[] */
    public array $docs;
}
```

#### ImageDocsResponseDto

Ответ со списком изображений.

```php
class ImageDocsResponseDto extends BaseDocsResponseDto
{
    /** @var Image[] */
    public array $docs;
}
```

#### StudioDocsResponseDto

Ответ со списком студий.

```php
class StudioDocsResponseDto extends BaseDocsResponseDto
{
    /** @var Studio[] */
    public array $docs;
}
```

#### KeywordDocsResponseDto

Ответ со списком ключевых слов.

```php
class KeywordDocsResponseDto extends BaseDocsResponseDto
{
    /** @var Keyword[] */
    public array $docs;
}
```

#### ListDocsResponseDto

Ответ со списками (коллекциями).

```php
class ListDocsResponseDto extends BaseDocsResponseDto
{
    /** @var Lists[] */
    public array $docs;
}
```

#### MovieAwardDocsResponseDto

Ответ с наградами фильмов.

```php
class MovieAwardDocsResponseDto extends BaseDocsResponseDto
{
    /** @var MovieAward[] */
    public array $docs;
}
```

#### PersonAwardDocsResponseDto

Ответ с наградами персон.

```php
class PersonAwardDocsResponseDto extends BaseDocsResponseDto
{
    /** @var PersonAward[] */
    public array $docs;
}
```

#### SearchMovieResponseDto и SearchPersonResponseDto

Специальные классы для результатов поиска.

#### PossibleValueDto

DTO для возможных значений полей.

```php
class PossibleValueDto
{
    public string $name;
    public string $slug;
}
```

### Классы ошибок (папка Errors/)

#### UnauthorizedErrorResponseDto

Ошибка 401 - неавторизован.

```php
class UnauthorizedErrorResponseDto extends ErrorResponseDto
{
    public string $error = 'UNAUTHORIZED';
    public string $message = 'Необходима авторизация';
    public int $statusCode = 401;
}
```

#### ForbiddenErrorResponseDto

Ошибка 403 - доступ запрещен.

```php
class ForbiddenErrorResponseDto extends ErrorResponseDto
{
    public string $error = 'FORBIDDEN';
    public string $message = 'Доступ запрещен';
    public int $statusCode = 403;
}
```

#### NotFoundErrorResponseDto

Ошибка 404 - не найдено.

```php
class NotFoundErrorResponseDto extends ErrorResponseDto
{
    public string $error = 'NOT_FOUND';
    public string $message = 'Ресурс не найден';
    public int $statusCode = 404;
}
```

## Примеры использования

### Работа с пагинированными ответами

```php
use KinopoiskDev\Responses\Api\MovieDocsResponseDto;
use KinopoiskDev\Http\MovieRequests;

$movieApi = new MovieRequests($token);
$response = $movieApi->searchMovies($filter, 1, 50);

// Доступ к данным
echo "Найдено фильмов: {$response->total}\n";
echo "Страница {$response->page} из {$response->pages}\n";

foreach ($response->docs as $movie) {
    echo "{$movie->name} ({$movie->year})\n";
}

// Преобразование в массив
$array = $response->toArray();

// Преобразование в JSON
$json = $response->toJson();
```

### Обработка ошибок

```php
use KinopoiskDev\Exceptions\KinopoiskResponseException;
use KinopoiskDev\Responses\Errors\{
    UnauthorizedErrorResponseDto,
    ForbiddenErrorResponseDto,
    NotFoundErrorResponseDto
};

try {
    $movie = $movieApi->getMovieById(123);
} catch (KinopoiskResponseException $e) {
    $errorClass = $e->getErrorClass();
    
    switch ($errorClass) {
        case UnauthorizedErrorResponseDto::class:
            echo "Ошибка авторизации: проверьте API токен";
            break;
        case ForbiddenErrorResponseDto::class:
            echo "Доступ запрещен: недостаточно прав";
            break;
        case NotFoundErrorResponseDto::class:
            echo "Фильм не найден";
            break;
    }
}
```

### Создание собственных ответов

```php
use KinopoiskDev\Responses\BaseDocsResponseDto;
use KinopoiskDev\Models\Movie;

class CustomMovieResponse extends BaseDocsResponseDto
{
    /** @var Movie[] */
    public array $docs;
    public array $metadata;
    
    public function __construct(array $movies, array $metadata = [])
    {
        $this->docs = $movies;
        $this->metadata = $metadata;
        $this->total = count($movies);
        $this->limit = 50;
        $this->page = 1;
        $this->pages = 1;
    }
    
    public function getTopRated(int $limit = 10): array
    {
        $sorted = $this->docs;
        usort($sorted, fn($a, $b) => $b->rating->kp <=> $a->rating->kp);
        
        return array_slice($sorted, 0, $limit);
    }
}
```

### Работа с возможными значениями

```php
use KinopoiskDev\Responses\Api\PossibleValueDto;
use KinopoiskDev\Http\MovieRequests;

$movieApi = new MovieRequests($token);
$genres = $movieApi->getPossibleValuesByField('genres.name');

foreach ($genres as $genreData) {
    $genre = PossibleValueDto::fromArray($genreData);
    echo "Жанр: {$genre->name} (slug: {$genre->slug})\n";
}
```

### Пагинация результатов

```php
use KinopoiskDev\Http\MovieRequests;
use KinopoiskDev\Filter\MovieSearchFilter;

$movieApi = new MovieRequests($token);
$filter = new MovieSearchFilter();
$filter->withIncludedGenres(['драма']);

$allMovies = [];
$page = 1;
$limit = 250; // Максимальный лимит

do {
    $response = $movieApi->searchMovies($filter, $page, $limit);
    $allMovies = array_merge($allMovies, $response->docs);
    
    echo "Загружено {$response->limit} фильмов со страницы {$page}/{$response->pages}\n";
    
    $page++;
} while ($page <= $response->pages);

echo "Всего загружено: " . count($allMovies) . " фильмов\n";
```

### Кэширование ответов

```php
use KinopoiskDev\Responses\Api\MovieDocsResponseDto;

class CachedMovieService
{
    private array $cache = [];
    
    public function cacheResponse(string $key, MovieDocsResponseDto $response): void
    {
        $this->cache[$key] = [
            'data' => $response,
            'timestamp' => time()
        ];
    }
    
    public function getCachedResponse(string $key, int $ttl = 3600): ?MovieDocsResponseDto
    {
        if (!isset($this->cache[$key])) {
            return null;
        }
        
        $cached = $this->cache[$key];
        
        if (time() - $cached['timestamp'] > $ttl) {
            unset($this->cache[$key]);
            return null;
        }
        
        return $cached['data'];
    }
}
```

## Особенности

1. **Типизация** - Все свойства строго типизированы
2. **Неизменяемость** - Большинство классов readonly
3. **Сериализация** - Поддержка преобразования в массив и JSON
4. **Наследование** - Общая функциональность в базовых классах
5. **PSR совместимость** - Следование стандартам PHP

## Связанные компоненты

- [Http классы](../http/) - Возвращают response объекты
- [Модели](../models/) - Используются в ответах
- [Исключения](../exceptions/) - Для обработки ошибок
- [BaseModel](../models/BaseModel.md) - Базовый класс моделей

## Требования

- PHP 8.3+
- JSON расширение
- Модели данных