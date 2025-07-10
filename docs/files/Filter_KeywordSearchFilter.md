# KeywordSearchFilter

**Путь:** src/Filter/KeywordSearchFilter.php  
**Пространство имён:** KinopoiskDev\Filter  
**Родительский класс:** [MovieFilter](../Utils/MovieFilter.md)  
**Использует:** [FilterTrait](../Utils/FilterTrait.md), [SortDirection](../Enums/SortDirection.md), [SortField](../Enums/SortField.md)

---

## Описание

> Фильтр для поиска ключевых слов
>
> Класс предоставляет методы для создания фильтров поиска ключевых слов по различным критериям: ID, названию, связанным фильмам, датам и т.д. Используется в KeywordRequests для формирования параметров запроса к API.
>
> @package KinopoiskDev\Filter  
> @since 1.0.0  
> @author Maxim Harder  
> @version 1.0.0
>
> @see \KinopoiskDev\Http\KeywordRequests Для использования фильтра  
> @link https://api.kinopoisk.dev/documentation-yaml Документация API

---

## Методы

### id
```php
public function id(int|array $id, string $operator = 'eq'): self
```
**Описание:** Добавляет фильтр по ID ключевого слова

**Параметры:**
- `int|array $id` — ID ключевого слова или массив ID
- `string $operator` — Оператор сравнения (eq, ne, in, nin)

**Возвращает:** `$this` — для fluent interface

---

### title
```php
public function title(string $title, string $operator = 'eq'): self
```
**Описание:** Добавляет фильтр по названию ключевого слова

**Параметры:**
- `string $title` — Название ключевого слова
- `string $operator` — Оператор сравнения (eq, ne, regex)

**Возвращает:** `$this` — для fluent interface

---

### movieId
```php
public function movieId(int|array $movieId): self
```
**Описание:** Добавляет фильтр по ID фильма

> Находит все ключевые слова, связанные с указанным фильмом.

**Параметры:**
- `int|array $movieId` — ID фильма или массив ID фильмов

**Возвращает:** `$this` — для fluent interface

---

### createdAt
```php
public function createdAt(string $createdAt, string $operator = 'eq'): self
```
**Описание:** Добавляет фильтр по дате создания

**Параметры:**
- `string $createdAt` — Дата создания в ISO формате
- `string $operator` — Оператор сравнения (eq, ne, gt, gte, lt, lte)

**Возвращает:** `$this` — для fluent interface

---

### updatedAt
```php
public function updatedAt(string $updatedAt, string $operator = 'eq'): self
```
**Описание:** Добавляет фильтр по дате обновления

**Параметры:**
- `string $updatedAt` — Дата обновления в ISO формате
- `string $operator` — Оператор сравнения (eq, ne, gt, gte, lt, lte)

**Возвращает:** `$this` — для fluent interface

---

### search
```php
public function search(string $query): self
```
**Описание:** Поиск ключевых слов по названию с использованием регулярных выражений

**Параметры:**
- `string $query` — Поисковый запрос

**Возвращает:** `$this` — для fluent interface

---

### onlyPopular
```php
public function onlyPopular(int $minMovieCount = 10): self
```
**Описание:** Фильтр для популярных ключевых слов (связанных с большим количеством фильмов)

> Возвращает ключевые слова, которые встречаются в 10 и более фильмах.

**Параметры:**
- `int $minMovieCount` — Минимальное количество связанных фильмов

**Возвращает:** `$this` — для fluent interface

---

### recentlyCreated
```php
public function recentlyCreated(int $daysAgo = 30): self
```
**Описание:** Фильтр для недавно созданных ключевых слов

**Параметры:**
- `int $daysAgo` — Количество дней назад от текущей даты

**Возвращает:** `$this` — для fluent interface

---

### recentlyUpdated
```php
public function recentlyUpdated(int $daysAgo = 7): self
```
**Описание:** Фильтр для недавно обновленных ключевых слов

**Параметры:**
- `int $daysAgo` — Количество дней назад от текущей даты

**Возвращает:** `$this` — для fluent interface

---

### createdBetween
```php
public function createdBetween(string $startDate, string $endDate): self
```
**Описание:** Фильтр по диапазону дат создания

**Параметры:**
- `string $startDate` — Начальная дата в ISO формате
- `string $endDate` — Конечная дата в ISO формате

**Возвращает:** `$this` — для fluent interface

---

### updatedBetween
```php
public function updatedBetween(string $startDate, string $endDate): self
```
**Описание:** Фильтр по диапазону дат обновления

**Параметры:**
- `string $startDate` — Начальная дата в ISO формате
- `string $endDate` — Конечная дата в ISO формате

**Возвращает:** `$this` — для fluent interface

---

### selectFields
```php
public function selectFields(array $fields): self
```
**Описание:** Выбор определенных полей для возвращения

**Параметры:**
- `array $fields` — Массив названий полей

**Возвращает:** `$this` — для fluent interface

---

### notNullFields
```php
public function notNullFields(array $fields): self
```
**Описание:** Исключение записей с пустыми значениями в указанных полях

**Параметры:**
- `array $fields` — Массив названий полей

**Возвращает:** `$this` — для fluent interface

---

### sortById
```php
public function sortById(string $direction = 'asc'): self
```
**Описание:** Сортировка по ID

**Параметры:**
- `string $direction` — Направление сортировки ('asc' или 'desc')

**Возвращает:** `$this` — для fluent interface

---

### sortByTitle
```php
public function sortByTitle(string $direction = 'asc'): self
```
**Описание:** Сортировка по названию

**Параметры:**
- `string $direction` — Направление сортировки ('asc' или 'desc')

**Возвращает:** `$this` — для fluent interface

---

### sortByCreatedAt
```php
public function sortByCreatedAt(string $direction = 'desc'): self
```
**Описание:** Сортировка по дате создания

**Параметры:**
- `string $direction` — Направление сортировки ('asc' или 'desc')

**Возвращает:** `$this` — для fluent interface

---

### sortByUpdatedAt
```php
public function sortByUpdatedAt(string $direction = 'desc'): self
```
**Описание:** Сортировка по дате обновления

**Параметры:**
- `string $direction` — Направление сортировки ('asc' или 'desc')

**Возвращает:** `$this` — для fluent interface

---

### sortByPopularity
```php
public function sortByPopularity(string $direction = 'desc'): self
```
**Описание:** Сортировка по популярности (количеству связанных фильмов)

**Параметры:**
- `string $direction` — Направление сортировки ('asc' или 'desc')

**Возвращает:** `$this` — для fluent interface

---

## Примеры использования

```php
use KinopoiskDev\Filter\KeywordSearchFilter;

$filter = new KeywordSearchFilter();

// Поиск популярных ключевых слов
$filter->onlyPopular(20)
       ->sortByPopularity();

// Поиск ключевых слов по названию
$filter->search('драма')
       ->sortByTitle();

// Поиск ключевых слов для конкретного фильма
$filter->movieId(12345)
       ->sortByTitle();

// Поиск недавно созданных ключевых слов
$filter->recentlyCreated(7)
       ->sortByCreatedAt();

// Поиск в диапазоне дат
$filter->createdBetween('2024-01-01T00:00:00.000Z', '2024-12-31T23:59:59.999Z')
       ->sortByCreatedAt();

// Выбор определенных полей
$filter->selectFields(['id', 'title', 'movieCount'])
       ->onlyPopular()
       ->sortByPopularity();
```

---

## Связи
- **Родительский класс:** [MovieFilter](../Utils/MovieFilter.md)
- **Использует:** [FilterTrait](../Utils/FilterTrait.md), [SortDirection](../Enums/SortDirection.md), [SortField](../Enums/SortField.md)
- **Используется в:** [KeywordRequests](../Http/KeywordRequests.md)