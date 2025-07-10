# FilterTrait

**Путь:** src/Utils/FilterTrait.php  
**Пространство имён:** KinopoiskDev\Utils

---

## Описание

> Трейт для общих методов фильтрации
>
> Этот трейт предоставляет общие методы фильтрации, которые могут использоваться в различных классах фильтров. Он следует принципу DRY (Don't Repeat Yourself), централизуя общую логику фильтрации.
>
> @package KinopoiskDev\Utils  
> @since 1.0.0  
> @author Maxim Harder  
> @version 1.0.0

---

## Методы

### movieId
```php
public function movieId(int $movieId): self
```
**Описание:** Добавляет фильтр по ID фильма

**Параметры:**
- `int $movieId` — ID фильма

**Возвращает:** `$this` — для fluent interface

---

### name
```php
public function name(string $name, string $operator = 'eq'): self
```
**Описание:** Добавляет фильтр по названию

**Параметры:**
- `string $name` — Название
- `string $operator` — Оператор сравнения

**Возвращает:** `$this` — для fluent interface

---

### enName
```php
public function enName(string $enName, string $operator = 'eq'): self
```
**Описание:** Добавляет фильтр по английскому названию

**Параметры:**
- `string $enName` — Английское название
- `string $operator` — Оператор сравнения

**Возвращает:** `$this` — для fluent interface

---

### type
```php
public function type(string $type, string $operator = 'eq'): self
```
**Описание:** Добавляет фильтр по типу

**Параметры:**
- `string $type` — Тип
- `string $operator` — Оператор сравнения

**Возвращает:** `$this` — для fluent interface

---

### searchByName
```php
public function searchByName(string $query): self
```
**Описание:** Добавляет поисковый фильтр по названию с использованием регулярных выражений

**Параметры:**
- `string $query` — Поисковый запрос

**Возвращает:** `$this` — для fluent interface

---

### searchByEnName
```php
public function searchByEnName(string $query): self
```
**Описание:** Добавляет поисковый фильтр по английскому названию с использованием регулярных выражений

**Параметры:**
- `string $query` — Поисковый запрос

**Возвращает:** `$this` — для fluent interface

---

### searchByDescription
```php
public function searchByDescription(string $query): self
```
**Описание:** Добавляет поисковый фильтр по описанию с использованием регулярных выражений

**Параметры:**
- `string $query` — Поисковый запрос

**Возвращает:** `$this` — для fluent interface

---

### withMinRating
```php
public function withMinRating(float $minRating, string $field = 'kp'): self
```
**Описание:** Добавляет фильтр по минимальному рейтингу

**Параметры:**
- `float $minRating` — Минимальный рейтинг
- `string $field` — Поле рейтинга (kp, imdb и т.д.)

**Возвращает:** `$this` — для fluent interface

---

### withMaxRating
```php
public function withMaxRating(float $maxRating, string $field = 'kp'): self
```
**Описание:** Добавляет фильтр по максимальному рейтингу

**Параметры:**
- `float $maxRating` — Максимальный рейтинг
- `string $field` — Поле рейтинга (kp, imdb и т.д.)

**Возвращает:** `$this` — для fluent interface

---

### withRatingBetween
```php
public function withRatingBetween(float $minRating, float $maxRating, string $field = 'kp'): self
```
**Описание:** Добавляет фильтр по диапазону рейтинга

**Параметры:**
- `float $minRating` — Минимальный рейтинг
- `float $maxRating` — Максимальный рейтинг
- `string $field` — Поле рейтинга (kp, imdb и т.д.)

**Возвращает:** `$this` — для fluent interface

---

### addRangeFilter
```php
protected function addRangeFilter(string $field, int $minValue, int $maxValue): self
```
**Описание:** Добавляет фильтр по диапазону

**Параметры:**
- `string $field` — Имя поля
- `int $minValue` — Минимальное значение
- `int $maxValue` — Максимальное значение

**Возвращает:** `$this` — для fluent interface

---

### seasonRange
```php
public function seasonRange(int $fromSeason, int $toSeason): self
```
**Описание:** Добавляет фильтр по диапазону сезонов

**Параметры:**
- `int $fromSeason` — Начальный сезон
- `int $toSeason` — Конечный сезон

**Возвращает:** `$this` — для fluent interface

---

### ageRange
```php
public function ageRange(int $minAge, int $maxAge): self
```
**Описание:** Добавляет фильтр по возрастному диапазону

**Параметры:**
- `int $minAge` — Минимальный возраст
- `int $maxAge` — Максимальный возраст

**Возвращает:** `$this` — для fluent interface

---

## Примеры использования

```php
use KinopoiskDev\Utils\FilterTrait;

class MyFilter {
    use FilterTrait;
    
    // Использование методов трейта
    public function searchPopularMovies() {
        return $this->withMinRating(7.0)
                   ->searchByName('драма')
                   ->movieId(12345);
    }
    
    public function searchByAgeRange() {
        return $this->ageRange(18, 65)
                   ->searchByDescription('актер');
    }
}
```

---

## Связи
- **Используется в:** [MovieSearchFilter](../Filter/MovieSearchFilter.md), [PersonSearchFilter](../Filter/PersonSearchFilter.md), [ReviewSearchFilter](../Filter/ReviewSearchFilter.md), [SeasonSearchFilter](../Filter/SeasonSearchFilter.md), [StudioSearchFilter](../Filter/StudioSearchFilter.md), [ImageSearchFilter](../Filter/ImageSearchFilter.md), [KeywordSearchFilter](../Filter/KeywordSearchFilter.md)