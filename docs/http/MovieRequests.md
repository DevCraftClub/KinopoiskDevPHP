# Класс MovieRequests

## Описание

`MovieRequests` - класс для API-запросов, связанных с фильмами. Расширяет базовый класс `Kinopoisk` и предоставляет специализированные методы для всех конечных точек фильмов API Kinopoisk.dev.

## Пространство имен

```php
namespace KinopoiskDev\Http;
```

## Объявление класса

```php
class MovieRequests extends Kinopoisk
```

## Методы

### getMovieById()

Получает фильм по его ID.

```php
public function getMovieById(int $movieId): Movie
```

**Параметры:**
- `$movieId` - Уникальный ID фильма

**Возвращает:** `Movie` - Фильм со всеми доступными данными

**Исключения:**
- `KinopoiskDevException` - При ошибках API или проблемах с сетью
- `\JsonException` - При ошибках парсинга JSON

**API:** `/v1.4/movie/{id}`

### getRandomMovie()

Получает случайный фильм.

```php
public function getRandomMovie(?MovieSearchFilter $filters = NULL): Movie
```

**Параметры:**
- `$filters` - Опциональные фильтры для случайного фильма

**Возвращает:** `Movie` - Случайный фильм, соответствующий фильтрам

**API:** `/v1.4/movie/random`

### getPossibleValuesByField()

Получает возможные значения для определенного поля.

```php
public function getPossibleValuesByField(string $field): array
```

**Параметры:**
- `$field` - Поле, для которого нужно получить возможные значения

**Возвращает:** `array` - Массив с возможными значениями для поля

**Поддерживаемые поля:**
- `genres.name`
- `countries.name`
- `type`
- `typeNumber`
- `status`

**API:** `/v1/movie/possible-values-by-field`

### getMovieAwards()

Получает награды фильмов с возможностью фильтрации и пагинации.

```php
public function getMovieAwards(
    ?MovieSearchFilter $filters = NULL, 
    int $page = 1, 
    int $limit = 10
): MovieAwardDocsResponseDto
```

**Параметры:**
- `$filters` - Объект фильтрации для поиска наград
- `$page` - Номер страницы (по умолчанию 1)
- `$limit` - Количество результатов на странице (по умолчанию 10, максимум 250)

**Возвращает:** `MovieAwardDocsResponseDto` - Объект ответа с наградами и метаданными пагинации

**API:** `/v1.4/movie/awards`

### searchByName()

Ищет фильмы только по названию (упрощенный поиск).

```php
public function searchByName(
    string $query, 
    int $page = 1, 
    int $limit = 10
): SearchMovieResponseDto
```

**Параметры:**
- `$query` - Поисковый запрос
- `$page` - Номер страницы
- `$limit` - Результатов на странице

**Возвращает:** `SearchMovieResponseDto` - Результаты поиска

**API:** `/v1.4/movie/search`

### searchMovies()

Ищет фильмы по различным критериям.

```php
public function searchMovies(
    ?MovieSearchFilter $filters = NULL, 
    int $page = 1, 
    int $limit = 10
): MovieDocsResponseDto
```

**Параметры:**
- `$filters` - Объект фильтра для поиска
- `$page` - Номер страницы (по умолчанию 1)
- `$limit` - Количество результатов на странице (по умолчанию 10, макс 250)

**Возвращает:** `MovieDocsResponseDto` - Результаты поиска с пагинацией

**API:** `/v1.4/movie`

### getLatestMovies()

Получает новейшие фильмы.

```php
public function getLatestMovies(
    ?int $year = NULL, 
    int $page = 1, 
    int $limit = 10
): MovieDocsResponseDto
```

**Параметры:**
- `$year` - Год (по умолчанию текущий год)
- `$page` - Номер страницы
- `$limit` - Результатов на странице

**Возвращает:** `MovieDocsResponseDto` - Новейшие фильмы

### getMoviesByGenre()

Получает фильмы по жанру.

```php
public function getMoviesByGenre(
    string|array $genres, 
    int $page = 1, 
    int $limit = 10
): MovieDocsResponseDto
```

**Параметры:**
- `$genres` - Жанр(ы)
- `$page` - Номер страницы
- `$limit` - Результатов на странице

**Возвращает:** `MovieDocsResponseDto` - Фильмы указанного жанра

### getMoviesByCountry()

Получает фильмы по стране.

```php
public function getMoviesByCountry(
    string|array $countries, 
    int $page = 1, 
    int $limit = 10
): MovieDocsResponseDto
```

**Параметры:**
- `$countries` - Страна/Страны
- `$page` - Номер страницы
- `$limit` - Результатов на странице

**Возвращает:** `MovieDocsResponseDto` - Фильмы из указанной страны

### getMoviesByYearRange()

Получает фильмы по диапазону лет.

```php
public function getMoviesByYearRange(
    int $fromYear, 
    int $toYear, 
    int $page = 1, 
    int $limit = 10
): MovieDocsResponseDto
```

**Параметры:**
- `$fromYear` - Начальный год
- `$toYear` - Конечный год
- `$page` - Номер страницы
- `$limit` - Результатов на странице

**Возвращает:** `MovieDocsResponseDto` - Фильмы из указанного периода

## Примеры использования

### Получение фильма по ID

```php
use KinopoiskDev\Http\MovieRequests;

$movieRequests = new MovieRequests('YOUR_API_TOKEN');

try {
    // Получение фильма по ID
    $movie = $movieRequests->getMovieById(123456);
    
    echo "Название: " . $movie->name . "\n";
    echo "Год: " . $movie->year . "\n";
    echo "Рейтинг: " . $movie->rating->kp . "\n";
} catch (KinopoiskDevException $e) {
    echo "Ошибка: " . $e->getMessage();
}
```

### Поиск фильмов по названию

```php
use KinopoiskDev\Http\MovieRequests;

$movieRequests = new MovieRequests('YOUR_API_TOKEN');

// Простой поиск по названию
$results = $movieRequests->searchByName('Матрица', 1, 20);

foreach ($results->docs as $movie) {
    echo "{$movie->name} ({$movie->year})\n";
}

echo "Найдено: {$results->total} фильмов\n";
echo "Страница {$results->page} из {$results->pages}\n";
```

### Расширенный поиск с фильтрами

```php
use KinopoiskDev\Http\MovieRequests;
use KinopoiskDev\Filter\MovieSearchFilter;

$movieRequests = new MovieRequests('YOUR_API_TOKEN');

// Создание фильтра
$filter = new MovieSearchFilter();
$filter
    ->withIncludedGenres(['фантастика', 'боевик'])
    ->withYearBetween(2020, 2024)
    ->withKinopoiskRatingBetween(7.0, 10.0)
    ->withIncludedCountries(['США', 'Великобритания'])
    ->sortByKinopoiskRating();

// Поиск с фильтрами
$results = $movieRequests->searchMovies($filter, 1, 50);

foreach ($results->docs as $movie) {
    echo "{$movie->name} - Рейтинг: {$movie->rating->kp}\n";
}
```

### Получение случайного фильма

```php
use KinopoiskDev\Http\MovieRequests;
use KinopoiskDev\Filter\MovieSearchFilter;

$movieRequests = new MovieRequests('YOUR_API_TOKEN');

// Случайный фильм без фильтров
$randomMovie = $movieRequests->getRandomMovie();

// Случайный фильм с фильтрами
$filter = new MovieSearchFilter();
$filter
    ->withIncludedGenres(['комедия'])
    ->withImdbRatingBetween(7.0, 10.0);

$randomComedy = $movieRequests->getRandomMovie($filter);
```

### Получение возможных значений

```php
use KinopoiskDev\Http\MovieRequests;
use KinopoiskDev\Enums\FilterField;

$movieRequests = new MovieRequests('YOUR_API_TOKEN');

// Получение всех жанров
$genres = $movieRequests->getPossibleValuesByField(FilterField::GENRES);
foreach ($genres as $genre) {
    echo "Жанр: {$genre['name']} (Slug: {$genre['slug']})\n";
}

// Получение всех стран
$countries = $movieRequests->getPossibleValuesByField(FilterField::COUNTRIES);
foreach ($countries as $country) {
    echo "Страна: {$country['name']}\n";
}
```

### Получение наград фильмов

```php
use KinopoiskDev\Http\MovieRequests;
use KinopoiskDev\Filter\MovieSearchFilter;

$movieRequests = new MovieRequests('YOUR_API_TOKEN');

// Награды всех фильмов
$awards = $movieRequests->getMovieAwards();

// Награды с фильтрацией
$filter = new MovieSearchFilter();
$filter->withName('Оппенгеймер');

$movieAwards = $movieRequests->getMovieAwards($filter);

foreach ($movieAwards->docs as $award) {
    echo "Фильм: {$award->movie->name}\n";
    echo "Награда: {$award->nomination->award->title}\n";
    echo "Номинация: {$award->nomination->title}\n";
    echo "Год: {$award->nomination->award->year}\n\n";
}
```

### Получение фильмов по жанрам и странам

```php
use KinopoiskDev\Http\MovieRequests;

$movieRequests = new MovieRequests('YOUR_API_TOKEN');

// Фильмы по жанру
$sciFiMovies = $movieRequests->getMoviesByGenre('фантастика', 1, 20);

// Фильмы по нескольким жанрам
$actionComedies = $movieRequests->getMoviesByGenre(['боевик', 'комедия'], 1, 20);

// Фильмы по стране
$russianMovies = $movieRequests->getMoviesByCountry('Россия', 1, 20);

// Фильмы по нескольким странам
$euroMovies = $movieRequests->getMoviesByCountry(['Франция', 'Италия', 'Испания'], 1, 20);
```

### Получение новейших фильмов

```php
use KinopoiskDev\Http\MovieRequests;

$movieRequests = new MovieRequests('YOUR_API_TOKEN');

// Новейшие фильмы текущего года
$latestMovies = $movieRequests->getLatestMovies();

// Новейшие фильмы 2023 года
$movies2023 = $movieRequests->getLatestMovies(2023, 1, 50);

// Фильмы за период
$periodMovies = $movieRequests->getMoviesByYearRange(2020, 2024, 1, 100);
```

## Обработка ошибок

```php
use KinopoiskDev\Http\MovieRequests;
use KinopoiskDev\Exceptions\{KinopoiskDevException, KinopoiskResponseException};

$movieRequests = new MovieRequests('YOUR_API_TOKEN');

try {
    $movie = $movieRequests->getMovieById(999999999);
} catch (KinopoiskResponseException $e) {
    // Ошибки API (404, 401, etc.)
    echo "Ошибка API: " . $e->getMessage();
} catch (KinopoiskDevException $e) {
    // Общие ошибки
    echo "Ошибка: " . $e->getMessage();
} catch (\JsonException $e) {
    // Ошибки парсинга JSON
    echo "Ошибка JSON: " . $e->getMessage();
}
```

## Особенности

1. **Расширение базового класса** - Наследует функциональность от `Kinopoisk`
2. **Специализированные методы** - Упрощенные методы для частых операций
3. **Автоматическая фильтрация** - Встроенная поддержка `MovieSearchFilter`
4. **Пагинация** - Все методы поиска поддерживают постраничную навигацию
5. **Типизированные ответы** - Возвращает специальные DTO объекты

## Связанные классы

- [Kinopoisk](../Kinopoisk.md) - Базовый класс
- [MovieSearchFilter](../filter/MovieSearchFilter.md) - Фильтр поиска фильмов
- [Movie](../models/Movie.md) - Модель фильма
- [MovieAward](../models/MovieAward.md) - Модель награды фильма
- [MovieDocsResponseDto](../responses/api/MovieDocsResponseDto.md) - Ответ со списком фильмов
- [MovieAwardDocsResponseDto](../responses/api/MovieAwardDocsResponseDto.md) - Ответ с наградами
- [SearchMovieResponseDto](../responses/api/SearchMovieResponseDto.md) - Ответ поиска

## Требования

- PHP 8.3+
- Валидный API токен Kinopoisk.dev