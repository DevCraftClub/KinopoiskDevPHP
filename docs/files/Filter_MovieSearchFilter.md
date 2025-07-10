# MovieSearchFilter

**Путь:** src/Filter/MovieSearchFilter.php  
**Пространство имён:** KinopoiskDev\Filter  
**Родительский класс:** [MovieFilter](../Utils/MovieFilter.md)  
**Использует:** [FilterTrait](../Utils/FilterTrait.md)

---

## Описание

> Класс для создания фильтров при поиске фильмов
>
> Этот класс расширяет базовый MovieFilter и предоставляет дополнительные методы для поиска фильмов
>
> @link https://kinopoiskdev.readme.io/reference/moviecontroller_findmanybyqueryv1_4

---

## Методы

### searchByAlternativeName
```php
public function searchByAlternativeName(string $query): self
```
**Описание:** Добавляет фильтр для поиска по альтернативному названию с использованием регулярного выражения

**Параметры:**
- `string $query` — Поисковый запрос

**Возвращает:** `$this` — для fluent interface

---

### searchByAllNames
```php
public function searchByAllNames(string $query): self
```
**Описание:** Добавляет фильтр для поиска по всем названиям фильма

**Параметры:**
- `string $query` — Поисковый запрос

**Возвращает:** `$this` — для fluent interface

---

### withMinVotes
```php
public function withMinVotes(int $minVotes, string $field = 'kp'): self
```
**Описание:** Добавляет фильтр для поиска фильмов с количеством голосов выше указанного

**Параметры:**
- `int $minVotes` — Минимальное количество голосов
- `string $field` — Поле голосов (kp, imdb, tmdb, filmCritics, russianFilmCritics, await)

**Возвращает:** `$this` — для fluent interface

---

### withVotesBetween
```php
public function withVotesBetween(int $minVotes, int $maxVotes, string $field = 'kp'): self
```
**Описание:** Добавляет фильтр для поиска фильмов в диапазоне голосов

**Параметры:**
- `int $minVotes` — Минимальное количество голосов
- `int $maxVotes` — Максимальное количество голосов
- `string $field` — Поле голосов (kp, imdb, tmdb, filmCritics, russianFilmCritics, await)

**Возвращает:** `$this` — для fluent interface

---

### withYearBetween
```php
public function withYearBetween(int $fromYear, int $toYear): self
```
**Описание:** Добавляет фильтр для поиска фильмов в диапазоне годов

**Параметры:**
- `int $fromYear` — Начальный год
- `int $toYear` — Конечный год

**Возвращает:** `$this` — для fluent interface

---

### withAllGenres
```php
public function withAllGenres(array $genres): self
```
**Описание:** Добавляет фильтр для поиска фильмов по нескольким жанрам (И)

**Параметры:**
- `array $genres` — Массив жанров

**Возвращает:** `$this` — для fluent interface

---

### withIncludedGenres
```php
public function withIncludedGenres(string|array $genres): self
```
**Описание:** Добавляет фильтр для включения жанров (оператор +)

**Параметры:**
- `string|array $genres` — Жанр или массив жанров для включения

**Возвращает:** `$this` — для fluent interface

---

### withExcludedGenres
```php
public function withExcludedGenres(string|array $genres): self
```
**Описание:** Добавляет фильтр для исключения жанров (оператор !)

**Параметры:**
- `string|array $genres` — Жанр или массив жанров для исключения

**Возвращает:** `$this` — для fluent interface

---

### withAllCountries
```php
public function withAllCountries(array $countries): self
```
**Описание:** Добавляет фильтр для поиска фильмов по нескольким странам (И)

**Параметры:**
- `array $countries` — Массив стран

**Возвращает:** `$this` — для fluent interface

---

### withIncludedCountries
```php
public function withIncludedCountries(string|array $countries): self
```
**Описание:** Добавляет фильтр для включения стран (оператор +)

**Параметры:**
- `string|array $countries` — Страна или массив стран для включения

**Возвращает:** `$this` — для fluent interface

---

### withExcludedCountries
```php
public function withExcludedCountries(string|array $countries): self
```
**Описание:** Добавляет фильтр для исключения стран (оператор !)

**Параметры:**
- `string|array $countries` — Страна или массив стран для исключения

**Возвращает:** `$this` — для fluent interface

---

### withActor
```php
public function withActor(string|int $actor): self
```
**Описание:** Добавляет фильтр для поиска фильмов с участием указанного актера

**Параметры:**
- `string|int $actor` — Имя актера или его ID

**Возвращает:** `$this` — для fluent interface

---

### withDirector
```php
public function withDirector(string|int $director): self
```
**Описание:** Добавляет фильтр для поиска фильмов указанного режиссера

**Параметры:**
- `string|int $director` — Имя режиссера или его ID

**Возвращает:** `$this` — для fluent interface

---

### onlyMovies
```php
public function onlyMovies(): self
```
**Описание:** Добавляет фильтр для поиска только фильмов (не сериалов)

**Возвращает:** `$this` — для fluent interface

---

### onlySeries
```php
public function onlySeries(): self
```
**Описание:** Добавляет фильтр для поиска только сериалов

**Возвращает:** `$this` — для fluent interface

---

### inTop250
```php
public function inTop250(): self
```
**Описание:** Добавляет фильтр для поиска фильмов из топ-250

**Возвращает:** `$this` — для fluent interface

---

### inTop10
```php
public function inTop10(): self
```
**Описание:** Добавляет фильтр для поиска фильмов из топ-10

**Возвращает:** `$this` — для fluent interface

---

### withPremiereRange
```php
public function withPremiereRange(string $fromDate, string $toDate, string $country = 'world'): self
```
**Описание:** Добавляет фильтр по диапазону дат премьеры

**Параметры:**
- `string $fromDate` — Начальная дата в формате dd.mm.yyyy
- `string $toDate` — Конечная дата в формате dd.mm.yyyy
- `string $country` — Страна премьеры (russia, world, usa, ...)

**Возвращает:** `$this` — для fluent interface

---

## Примеры использования

```php
use KinopoiskDev\Filter\MovieSearchFilter;

$filter = new MovieSearchFilter();

// Поиск российских драм 2020-2024 с рейтингом выше 7.0
$filter->withIncludedCountries('Россия')
       ->withIncludedGenres('драма')
       ->withYearBetween(2020, 2024)
       ->withRatingBetween(7.0, 10.0)
       ->onlyMovies()
       ->sortByKinopoiskRating();

// Поиск фильмов с участием конкретного актера
$filter->withActor('Леонардо ДиКаприо')
       ->withMinVotes(10000)
       ->sortByYear();
```

---

## Связи
- **Родительский класс:** [MovieFilter](../Utils/MovieFilter.md)
- **Использует:** [FilterTrait](../Utils/FilterTrait.md)
- **Используется в:** [MovieRequests](../Http/MovieRequests.md), [PersonRequests](../Http/PersonRequests.md)