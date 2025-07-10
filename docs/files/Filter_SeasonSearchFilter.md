# SeasonSearchFilter

**Путь:** src/Filter/SeasonSearchFilter.php  
**Пространство имён:** KinopoiskDev\Filter  
**Родительский класс:** [MovieFilter](../Utils/MovieFilter.md)  
**Использует:** [FilterTrait](../Utils/FilterTrait.md)

---

## Описание

> Класс для фильтров при поиске сезонов
>
> @package KinopoiskDev\Filter  
> @since 1.0.0  
> @author Maxim Harder  
> @version 1.0.0

---

## Методы

### number
```php
public function number(int $number, string $operator = 'eq'): self
```
**Описание:** Добавляет фильтр по номеру сезона

**Параметры:**
- `int $number` — Номер сезона
- `string $operator` — Оператор сравнения

**Возвращает:** `$this` — для fluent interface

---

### episodesCount
```php
public function episodesCount(int $episodesCount, string $operator = 'eq'): self
```
**Описание:** Добавляет фильтр по количеству эпизодов

**Параметры:**
- `int $episodesCount` — Количество эпизодов
- `string $operator` — Оператор сравнения

**Возвращает:** `$this` — для fluent interface

---

## Примеры использования

```php
use KinopoiskDev\Filter\SeasonSearchFilter;

$filter = new SeasonSearchFilter();

// Поиск первого сезона
$filter->number(1)
       ->sortByNumber();

// Поиск сезонов с большим количеством эпизодов
$filter->episodesCount(10, 'gte')
       ->sortByEpisodesCount();

// Поиск сезонов в диапазоне номеров
$filter->number(1, 'gte')
       ->number(5, 'lte')
       ->sortByNumber();
```

---

## Связи
- **Родительский класс:** [MovieFilter](../Utils/MovieFilter.md)
- **Использует:** [FilterTrait](../Utils/FilterTrait.md)
- **Используется в:** [SeasonRequests](../Http/SeasonRequests.md)