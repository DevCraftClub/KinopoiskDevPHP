# MovieFilter

**Путь:** src/Utils/MovieFilter.php  
**Пространство имён:** KinopoiskDev\Utils  
**Использует:** [SortManager](SortManager.md)

---

## Описание

> Класс для создания фильтров при поиске фильмов
>
> Этот класс предоставляет методы для построения параметров фильтрации при поиске фильмов через API Kinopoisk.dev
>
> @link https://kinopoiskdev.readme.io/reference/moviecontroller_findmanybyqueryv1_4

---

## Свойства

### filters
```php
protected array $filters = []
```
**Описание:** Массив параметров фильтрации

---

## Основные методы

### id
```php
public function id(int|array $id, string $operator = 'eq'): self
```
**Описание:** Добавляет фильтр по ID фильма

**Параметры:**
- `int|array $id` — ID фильма или массив ID
- `string $operator` — Оператор сравнения (eq, ne, in, nin)

**Возвращает:** `$this` — для fluent interface

---

### addFilter
```php
public function addFilter(string $field, mixed $value, string $operator = 'eq'): self
```
**Описание:** Добавляет произвольный фильтр

**Параметры:**
- `string $field` — Поле для фильтрации
- `mixed $value` — Значение фильтра
- `string $operator` — Оператор сравнения

**Возвращает:** `$this` — для fluent interface

---

### externalId
```php
public function externalId(array $externalId): self
```
**Описание:** Добавляет фильтр по внешнему ID фильма

**Параметры:**
- `array $externalId` — Массив внешних ID (imdb, tmdb, kpHD)

**Возвращает:** `$this` — для fluent interface

---

### name
```php
public function name(string $name, string $operator = 'eq'): self
```
**Описание:** Добавляет фильтр по названию фильма

**Параметры:**
- `string $name` — Название фильма
- `string $operator` — Оператор сравнения (eq, ne, in, nin, regex)

**Возвращает:** `$this` — для fluent interface

---

### enName
```php
public function enName(string $enName, string $operator = 'eq'): self
```
**Описание:** Добавляет фильтр по английскому названию фильма

**Параметры:**
- `string $enName` — Английское название фильма
- `string $operator` — Оператор сравнения (eq, ne, in, nin, regex)

**Возвращает:** `$this` — для fluent interface

---

### alternativeName
```php
public function alternativeName(string $alternativeName, string $operator = 'eq'): self
```
**Описание:** Добавляет фильтр по альтернативному названию фильма

**Параметры:**
- `string $alternativeName` — Альтернативное название фильма
- `string $operator` — Оператор сравнения (eq, ne, in, nin, regex)

**Возвращает:** `$this` — для fluent interface

---

### names
```php
public function names(string|array $names, string $operator = 'eq'): self
```
**Описание:** Добавляет фильтр по всем названиям фильма

**Параметры:**
- `string|array $names` — Название или массив названий
- `string $operator` — Оператор сравнения (eq, ne, in, nin, regex)

**Возвращает:** `$this` — для fluent interface

---

### description
```php
public function description(string $description, string $operator = 'regex'): self
```
**Описание:** Добавляет фильтр по описанию фильма

**Параметры:**
- `string $description` — Описание фильма
- `string $operator` — Оператор сравнения (eq, ne, regex)

**Возвращает:** `$this` — для fluent interface

---

### shortDescription
```php
public function shortDescription(string $shortDescription, string $operator = 'regex'): self
```
**Описание:** Добавляет фильтр по краткому описанию фильма

**Параметры:**
- `string $shortDescription` — Краткое описание фильма
- `string $operator` — Оператор сравнения (eq, ne, regex)

**Возвращает:** `$this` — для fluent interface

---

### slogan
```php
public function slogan(string $slogan, string $operator = 'regex'): self
```
**Описание:** Добавляет фильтр по слогану фильма

**Параметры:**
- `string $slogan` — Слоган фильма
- `string $operator` — Оператор сравнения (eq, ne, regex)

**Возвращает:** `$this` — для fluent interface

---

### type
```php
public function type(string $type, string $operator = 'eq'): self
```
**Описание:** Добавляет фильтр по типу фильма

**Параметры:**
- `string $type` — Тип фильма (movie, tv-series, cartoon, anime, animated-series, tv-show)
- `string $operator` — Оператор сравнения

**Возвращает:** `$this` — для fluent interface

---

### typeNumber
```php
public function typeNumber(int $typeNumber, string $operator = 'eq'): self
```
**Описание:** Добавляет фильтр по номеру типа фильма

**Параметры:**
- `int $typeNumber` — Номер типа (1-6)
- `string $operator` — Оператор сравнения

**Возвращает:** `$this` — для fluent interface

---

### isSeries
```php
public function isSeries(bool $isSeries): self
```
**Описание:** Добавляет фильтр по признаку сериала

**Параметры:**
- `bool $isSeries` — true для сериалов, false для фильмов

**Возвращает:** `$this` — для fluent interface

---

### status
```php
public function status(string $status, string $operator = 'eq'): self
```
**Описание:** Добавляет фильтр по статусу фильма

**Параметры:**
- `string $status` — Статус (announce, filming, production, post-production, completed, canceled)
- `string $operator` — Оператор сравнения

**Возвращает:** `$this` — для fluent interface

---

### year
```php
public function year(int $year, string $operator = 'eq'): self
```
**Описание:** Добавляет фильтр по году выпуска

**Параметры:**
- `int $year` — Год выпуска
- `string $operator` — Оператор сравнения

**Возвращает:** `$this` — для fluent interface

---

### yearRange
```php
public function yearRange(int $fromYear, int $toYear): self
```
**Описание:** Добавляет фильтр по диапазону годов

**Параметры:**
- `int $fromYear` — Начальный год
- `int $toYear` — Конечный год

**Возвращает:** `$this` — для fluent interface

---

### rating
```php
public function rating(float|array $rating, string $field = 'kp', string $operator = 'gte'): self
```
**Описание:** Добавляет фильтр по рейтингу

**Параметры:**
- `float|array $rating` — Рейтинг или массив рейтингов
- `string $field` — Поле рейтинга (kp, imdb, tmdb, filmCritics, russianFilmCritics, await)
- `string $operator` — Оператор сравнения

**Возвращает:** `$this` — для fluent interface

---

### ratingRange
```php
public function ratingRange(float $minRating, float $maxRating, string $field = 'kp'): self
```
**Описание:** Добавляет фильтр по диапазону рейтингов

**Параметры:**
- `float $minRating` — Минимальный рейтинг
- `float $maxRating` — Максимальный рейтинг
- `string $field` — Поле рейтинга

**Возвращает:** `$this` — для fluent interface

---

### votes
```php
public function votes(int|array $votes, string $field = 'kp', string $operator = 'gte'): self
```
**Описание:** Добавляет фильтр по количеству голосов

**Параметры:**
- `int|array $votes` — Количество голосов или массив
- `string $field` — Поле голосов (kp, imdb, tmdb, filmCritics, russianFilmCritics, await)
- `string $operator` — Оператор сравнения

**Возвращает:** `$this` — для fluent interface

---

### votesRange
```php
public function votesRange(int $minVotes, int $maxVotes, string $field = 'kp'): self
```
**Описание:** Добавляет фильтр по диапазону голосов

**Параметры:**
- `int $minVotes` — Минимальное количество голосов
- `int $maxVotes` — Максимальное количество голосов
- `string $field` — Поле голосов

**Возвращает:** `$this` — для fluent interface

---

### genres
```php
public function genres(string|array $genres, string $operator = 'in'): self
```
**Описание:** Добавляет фильтр по жанрам

**Параметры:**
- `string|array $genres` — Жанр или массив жанров
- `string $operator` — Оператор сравнения

**Возвращает:** `$this` — для fluent interface

---

### includeGenres
```php
public function includeGenres(string|array $genres): self
```
**Описание:** Добавляет фильтр для включения жанров (оператор +)

**Параметры:**
- `string|array $genres` — Жанр или массив жанров для включения

**Возвращает:** `$this` — для fluent interface

---

### excludeGenres
```php
public function excludeGenres(string|array $genres): self
```
**Описание:** Добавляет фильтр для исключения жанров (оператор !)

**Параметры:**
- `string|array $genres` — Жанр или массив жанров для исключения

**Возвращает:** `$this` — для fluent interface

---

### countries
```php
public function countries(string|array $countries, string $operator = 'in'): self
```
**Описание:** Добавляет фильтр по странам

**Параметры:**
- `string|array $countries` — Страна или массив стран
- `string $operator` — Оператор сравнения

**Возвращает:** `$this` — для fluent interface

---

### includeCountries
```php
public function includeCountries(string|array $countries): self
```
**Описание:** Добавляет фильтр для включения стран (оператор +)

**Параметры:**
- `string|array $countries` — Страна или массив стран для включения

**Возвращает:** `$this` — для fluent interface

---

### excludeCountries
```php
public function excludeCountries(string|array $countries): self
```
**Описание:** Добавляет фильтр для исключения стран (оператор !)

**Параметры:**
- `string|array $countries` — Страна или массив стран для исключения

**Возвращает:** `$this` — для fluent interface

---

### movieLength
```php
public function movieLength(int $movieLength, string $operator = 'eq'): self
```
**Описание:** Добавляет фильтр по длительности фильма

**Параметры:**
- `int $movieLength` — Длительность в минутах
- `string $operator` — Оператор сравнения

**Возвращает:** `$this` — для fluent interface

---

### top250
```php
public function top250(int $top250, string $operator = 'eq'): self
```
**Описание:** Добавляет фильтр по позиции в топ-250

**Параметры:**
- `int $top250` — Позиция в топ-250
- `string $operator` — Оператор сравнения

**Возвращает:** `$this` — для fluent interface

---

### getFilters
```php
public function getFilters(): array
```
**Описание:** Возвращает массив всех фильтров

**Возвращает:** `array` — Массив фильтров

---

### reset
```php
public function reset(): self
```
**Описание:** Сбрасывает все фильтры

**Возвращает:** `$this` — для fluent interface

---

## Примеры использования

```php
use KinopoiskDev\Utils\MovieFilter;

$filter = new MovieFilter();

// Поиск фильмов 2020-2024 с рейтингом выше 7.0
$filter->yearRange(2020, 2024)
       ->rating(7.0, 'kp', 'gte')
       ->onlyMovies()
       ->sortByRating();

// Поиск российских драм
$filter->includeCountries('Россия')
       ->includeGenres('драма')
       ->sortByYear();

// Поиск фильмов из топ-250
$filter->top250(1, 'gte')
       ->top250(100, 'lte')
       ->sortByTop250();

// Поиск по внешнему ID
$filter->externalId(['imdb' => 'tt0111161'])
       ->sortByRating();
```

---

## Связи
- **Использует:** [SortManager](SortManager.md)
- **Наследуется:** [MovieSearchFilter](../Filter/MovieSearchFilter.md), [PersonSearchFilter](../Filter/PersonSearchFilter.md), [ReviewSearchFilter](../Filter/ReviewSearchFilter.md), [SeasonSearchFilter](../Filter/SeasonSearchFilter.md), [StudioSearchFilter](../Filter/StudioSearchFilter.md), [ImageSearchFilter](../Filter/ImageSearchFilter.md), [KeywordSearchFilter](../Filter/KeywordSearchFilter.md)