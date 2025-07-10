# HTTP классы для работы с API

## Описание

Пространство имен `KinopoiskDev\Http` содержит классы для выполнения запросов к различным конечным точкам API Kinopoisk.dev. Все классы наследуют базовый класс `Kinopoisk` и предоставляют специализированные методы для работы с конкретными типами данных.

## Классы

### MovieRequests

[Полная документация](MovieRequests.md)

Класс для работы с фильмами. Основные методы:
- `getMovieById()` - Получение фильма по ID
- `searchMovies()` - Поиск фильмов
- `getRandomMovie()` - Случайный фильм
- `getMovieAwards()` - Награды фильмов

### PersonRequests

Класс для работы с персонами (актеры, режиссеры и т.д.). Основные методы:
- `getPersonById()` - Получение персоны по ID
- `searchPersons()` - Поиск персон
- `getPersonAwards()` - Награды персон
- `getPersonsByProfession()` - Персоны по профессии

### ImageRequests

Класс для работы с изображениями. Основные методы:
- `getImages()` - Получение изображений с фильтрацией
- `getImagesByMovieId()` - Изображения конкретного фильма
- `getImagesByType()` - Изображения по типу

### ReviewRequests

Класс для работы с отзывами. Основные методы:
- `getReviews()` - Получение отзывов
- `getReviewsByMovieId()` - Отзывы к конкретному фильму
- `getReviewsByAuthor()` - Отзывы конкретного автора

### SeasonRequests

Класс для работы с сезонами сериалов. Основные методы:
- `getSeasons()` - Получение сезонов
- `getSeasonsByMovieId()` - Сезоны конкретного сериала
- `getEpisodes()` - Эпизоды сезона

### StudioRequests

Класс для работы со студиями. Основные методы:
- `getStudios()` - Получение студий
- `getStudioById()` - Студия по ID
- `getMoviesByStudio()` - Фильмы студии

### KeywordRequests

Класс для работы с ключевыми словами. Основные методы:
- `getKeywords()` - Получение ключевых слов
- `getKeywordById()` - Ключевое слово по ID
- `searchKeywords()` - Поиск ключевых слов

### ListRequests

Класс для работы со списками (коллекциями). Основные методы:
- `getLists()` - Получение списков
- `getListById()` - Список по ID
- `getListsByCategory()` - Списки по категории

## Общие принципы использования

### Инициализация

```php
use KinopoiskDev\Http\{MovieRequests, PersonRequests, ImageRequests};

// Создание экземпляров
$movieApi = new MovieRequests('YOUR_API_TOKEN');
$personApi = new PersonRequests('YOUR_API_TOKEN');
$imageApi = new ImageRequests('YOUR_API_TOKEN');
```

### Пагинация

Все методы поиска поддерживают пагинацию:

```php
// Параметры пагинации
$page = 1;     // Номер страницы
$limit = 50;   // Количество результатов (макс. 250)

$results = $movieApi->searchMovies($filter, $page, $limit);
echo "Страница {$results->page} из {$results->pages}";
echo "Всего найдено: {$results->total}";
```

### Фильтрация

Каждый класс поддерживает соответствующий фильтр:

```php
use KinopoiskDev\Filter\{MovieSearchFilter, PersonSearchFilter, ImageSearchFilter};

// Фильтр для фильмов
$movieFilter = new MovieSearchFilter();
$movieFilter->withIncludedGenres(['драма'])->year(2023);

// Фильтр для персон
$personFilter = new PersonSearchFilter();
$personFilter->withProfession('актер')->withName('Иванов');

// Фильтр для изображений
$imageFilter = new ImageSearchFilter();
$imageFilter->withMovieId(123456)->withType('poster');
```

### Обработка ошибок

```php
use KinopoiskDev\Exceptions\{KinopoiskDevException, KinopoiskResponseException};

try {
    $movie = $movieApi->getMovieById(123);
} catch (KinopoiskResponseException $e) {
    // Ошибки API (401, 403, 404)
    echo "API Error: " . $e->getMessage();
} catch (KinopoiskDevException $e) {
    // Общие ошибки
    echo "Error: " . $e->getMessage();
} catch (\JsonException $e) {
    // Ошибки парсинга JSON
    echo "JSON Error: " . $e->getMessage();
}
```

## Примеры комплексного использования

### Получение полной информации о фильме

```php
use KinopoiskDev\Http\{MovieRequests, PersonRequests, ImageRequests, ReviewRequests};

$movieId = 123456;

// Инициализация API классов
$movieApi = new MovieRequests($token);
$personApi = new PersonRequests($token);
$imageApi = new ImageRequests($token);
$reviewApi = new ReviewRequests($token);

// Получение основной информации
$movie = $movieApi->getMovieById($movieId);

// Получение изображений
$images = $imageApi->getImagesByMovieId($movieId);

// Получение отзывов
$reviews = $reviewApi->getReviewsByMovieId($movieId, 1, 10);

// Получение информации о персонах
$persons = [];
foreach ($movie->persons as $person) {
    if ($person->id) {
        $persons[] = $personApi->getPersonById($person->id);
    }
}
```

### Поиск с множественными критериями

```php
use KinopoiskDev\Http\MovieRequests;
use KinopoiskDev\Filter\MovieSearchFilter;

$movieApi = new MovieRequests($token);

// Создание сложного фильтра
$filter = new MovieSearchFilter();
$filter
    ->withIncludedGenres(['фантастика', 'боевик'])
    ->withExcludedGenres(['ужасы'])
    ->withIncludedCountries(['США', 'Канада'])
    ->withYearBetween(2020, 2024)
    ->withKinopoiskRatingBetween(7.0, 10.0)
    ->withAgeLimits([0, 6, 12])
    ->sortByKinopoiskRating();

// Поиск с обработкой всех страниц
$allMovies = [];
$page = 1;

do {
    $results = $movieApi->searchMovies($filter, $page, 250);
    $allMovies = array_merge($allMovies, $results->docs);
    $page++;
} while ($page <= $results->pages);

echo "Найдено фильмов: " . count($allMovies);
```

### Работа с кэшированием

```php
use KinopoiskDev\Http\MovieRequests;

class CachedMovieApi extends MovieRequests
{
    private array $cache = [];
    
    public function getMovieById(int $movieId): Movie
    {
        $cacheKey = "movie_{$movieId}";
        
        if (!isset($this->cache[$cacheKey])) {
            $this->cache[$cacheKey] = parent::getMovieById($movieId);
        }
        
        return $this->cache[$cacheKey];
    }
}
```

## Лимиты API

- Максимум 250 результатов на страницу
- Ограничения по количеству запросов в зависимости от тарифа
- Некоторые поля могут быть недоступны на бесплатном тарифе

## Связанные компоненты

- [Kinopoisk](../Kinopoisk.md) - Базовый класс
- [Фильтры](../filter/) - Классы фильтрации
- [Модели](../models/) - Модели данных
- [Ответы](../responses/) - Классы ответов API

## Требования

- PHP 8.3+
- Валидный API токен Kinopoisk.dev
- Guzzle HTTP Client