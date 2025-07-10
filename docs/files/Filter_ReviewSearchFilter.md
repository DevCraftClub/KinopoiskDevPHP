# ReviewSearchFilter

**Путь:** src/Filter/ReviewSearchFilter.php  
**Пространство имён:** KinopoiskDev\Filter  
**Родительский класс:** [MovieFilter](../Utils/MovieFilter.md)  
**Использует:** [FilterTrait](../Utils/FilterTrait.md)

---

## Описание

> Класс для фильтров при поиске отзывов
>
> @package KinopoiskDev\Filter  
> @since 1.0.0  
> @author Maxim Harder  
> @version 1.0.0

---

## Методы

### author
```php
public function author(string $author, string $operator = 'regex'): self
```
**Описание:** Добавляет фильтр по автору

**Параметры:**
- `string $author` — Автор отзыва
- `string $operator` — Оператор сравнения

**Возвращает:** `$this` — для fluent interface

---

### review
```php
public function review(string $review, string $operator = 'regex'): self
```
**Описание:** Добавляет фильтр по тексту отзыва

**Параметры:**
- `string $review` — Текст отзыва
- `string $operator` — Оператор сравнения

**Возвращает:** `$this` — для fluent interface

---

### title
```php
public function title(string $title, string $operator = 'regex'): self
```
**Описание:** Добавляет фильтр по заголовку

**Параметры:**
- `string $title` — Заголовок отзыва
- `string $operator` — Оператор сравнения

**Возвращает:** `$this` — для fluent interface

---

### onlyPositive
```php
public function onlyPositive(): self
```
**Описание:** Фильтр только для положительных отзывов

**Возвращает:** `$this` — для fluent interface

---

### onlyNegative
```php
public function onlyNegative(): self
```
**Описание:** Фильтр только для отрицательных отзывов

**Возвращает:** `$this` — для fluent interface

---

### onlyNeutral
```php
public function onlyNeutral(): self
```
**Описание:** Фильтр только для нейтральных отзывов

**Возвращает:** `$this` — для fluent interface

---

## Примеры использования

```php
use KinopoiskDev\Filter\ReviewSearchFilter;

$filter = new ReviewSearchFilter();

// Поиск положительных отзывов конкретного автора
$filter->onlyPositive()
       ->author('Кинокритик')
       ->sortByDate();

// Поиск отзывов с определенным текстом
$filter->review('отличный фильм')
       ->sortByDate();

// Поиск негативных отзывов с определенным заголовком
$filter->onlyNegative()
       ->title('разочарование')
       ->sortByDate();
```

---

## Связи
- **Родительский класс:** [MovieFilter](../Utils/MovieFilter.md)
- **Использует:** [FilterTrait](../Utils/FilterTrait.md)
- **Используется в:** [ReviewRequests](../Http/ReviewRequests.md)