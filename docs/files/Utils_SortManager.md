# SortManager

**Путь:** src/Utils/SortManager.php  
**Пространство имён:** KinopoiskDev\Utils  
**Использует:** [SortDirection](../Enums/SortDirection.md), [SortField](../Enums/SortField.md), [SortCriteria](../Filter/SortCriteria.md)

---

## Описание

> Trait для добавления функциональности сортировки к фильтрам
>
> Этот trait предоставляет методы для управления параметрами сортировки при выполнении запросов к API Kinopoisk.dev. Может использоваться в классах фильтрации для расширения их функциональности.
>
> @package KinopoiskDev\Utils  
> @since 1.0.0  
> @author Maxim Harder  
> @version 1.0.0

---

## Свойства

### sortCriteria
```php
protected array $sortCriteria = []
```
**Описание:** Массив критериев сортировки

---

### sortCriteriaByField
```php
protected array $sortCriteriaByField = []
```
**Описание:** Ассоциативный массив критериев сортировки, индексированный по полю. Используется для быстрого доступа к критериям по полю.

---

## Основные методы

### removeSortByField
```php
public function removeSortByField(SortField $field): static
```
**Описание:** Удаляет сортировку по указанному полю

**Параметры:**
- `SortField $field` — Поле для удаления из сортировки

**Возвращает:** `$this` — Возвращает текущий экземпляр для цепочки вызовов

---

### toggleSort
```php
public function toggleSort(SortField $field): static
```
**Описание:** Переключает направление сортировки для указанного поля

> Если сортировка по полю существует, меняет направление на противоположное. Если сортировки нет, добавляет с направлением по умолчанию.

**Параметры:**
- `SortField $field` — Поле для переключения сортировки

**Возвращает:** `$this` — Возвращает текущий экземпляр для цепочки вызовов

---

### sortBy
```php
public function sortBy(SortField $field, ?SortDirection $direction = NULL): static
```
**Описание:** Добавляет сортировку по указанному полю

**Параметры:**
- `SortField $field` — Поле для сортировки
- `SortDirection|null $direction` — Направление сортировки (по умолчанию используется рекомендуемое)

**Возвращает:** `$this` — Возвращает текущий экземпляр для цепочки вызовов

---

### addSortCriteria
```php
public function addSortCriteria(SortCriteria $criteria): static
```
**Описание:** Добавляет критерий сортировки

> Добавляет новый критерий сортировки к текущему набору. Если критерий для указанного поля уже существует, он будет заменен.

**Параметры:**
- `SortCriteria $criteria` — Критерий сортировки

**Возвращает:** `$this` — Возвращает текущий экземпляр для цепочки вызовов

---

### hasSortBy
```php
public function hasSortBy(SortField $field): bool
```
**Описание:** Проверяет, установлена ли сортировка по указанному полю

**Параметры:**
- `SortField $field` — Поле для проверки

**Возвращает:** `bool` — true, если сортировка по полю установлена, false в противном случае

---

### getSortDirection
```php
public function getSortDirection(SortField $field): ?SortDirection
```
**Описание:** Возвращает направление сортировки для указанного поля

**Параметры:**
- `SortField $field` — Поле для получения направления

**Возвращает:** `SortDirection|null` — Направление сортировки или null, если сортировка не установлена

---

### getSortCriteria
```php
public function getSortCriteria(): array
```
**Описание:** Возвращает все критерии сортировки

**Возвращает:** `SortCriteria[]` — Массив критериев сортировки

---

### setSortCriteria
```php
public function setSortCriteria(array $criteria): static
```
**Описание:** Устанавливает множественные критерии сортировки

> Заменяет текущие критерии сортировки новым набором.

**Параметры:**
- `SortCriteria[] $criteria` — Массив критериев сортировки

**Возвращает:** `$this` — Возвращает текущий экземпляр для цепочки вызовов

---

### clearSort
```php
public function clearSort(): static
```
**Описание:** Очищает все критерии сортировки

**Возвращает:** `$this` — Возвращает текущий экземпляр для цепочки вызовов

---

### addMultipleSort
```php
public function addMultipleSort(array $sorts): static
```
**Описание:** Добавляет множественные критерии сортировки

**Параметры:**
- `array $sorts` — Массив критериев сортировки

**Возвращает:** `$this` — Возвращает текущий экземпляр для цепочки вызовов

---

### getSortString
```php
public function getSortString(): ?string
```
**Описание:** Возвращает строковое представление сортировки для API

**Возвращает:** `string|null` — Строковое представление или null, если сортировка не установлена

---

### getSortCount
```php
public function getSortCount(): int
```
**Описание:** Возвращает количество критериев сортировки

**Возвращает:** `int` — Количество критериев

---

### hasAnySorting
```php
public function hasAnySorting(): bool
```
**Описание:** Проверяет, установлена ли какая-либо сортировка

**Возвращает:** `bool` — true, если есть критерии сортировки, false в противном случае

---

### getFirstSortCriteria
```php
public function getFirstSortCriteria(): ?SortCriteria
```
**Описание:** Возвращает первый критерий сортировки

**Возвращает:** `SortCriteria|null` — Первый критерий или null

---

### getLastSortCriteria
```php
public function getLastSortCriteria(): ?SortCriteria
```
**Описание:** Возвращает последний критерий сортировки

**Возвращает:** `SortCriteria|null` — Последний критерий или null

---

## Удобные методы сортировки

### sortByImdbRating
```php
public function sortByImdbRating(): static
```
**Описание:** Сортировка по рейтингу IMDb

**Возвращает:** `$this` — для fluent interface

---

### sortByDesc
```php
public function sortByDesc(SortField $field): static
```
**Описание:** Сортировка по убыванию

**Параметры:**
- `SortField $field` — Поле для сортировки

**Возвращает:** `$this` — для fluent interface

---

### sortByYearOldFirst
```php
public function sortByYearOldFirst(): static
```
**Описание:** Сортировка по году (старые сначала)

**Возвращает:** `$this` — для fluent interface

---

### sortByAsc
```php
public function sortByAsc(SortField $field): static
```
**Описание:** Сортировка по возрастанию

**Параметры:**
- `SortField $field` — Поле для сортировки

**Возвращает:** `$this` — для fluent interface

---

### sortByName
```php
public function sortByName(): static
```
**Описание:** Сортировка по названию

**Возвращает:** `$this` — для fluent interface

---

### sortByPopularity
```php
public function sortByPopularity(): static
```
**Описание:** Сортировка по популярности

**Возвращает:** `$this` — для fluent interface

---

### sortByCreated
```php
public function sortByCreated(): static
```
**Описание:** Сортировка по дате создания

**Возвращает:** `$this` — для fluent interface

---

### sortByUpdated
```php
public function sortByUpdated(): static
```
**Описание:** Сортировка по дате обновления

**Возвращает:** `$this` — для fluent interface

---

### sortByBest
```php
public function sortByBest(): static
```
**Описание:** Сортировка по лучшим (рейтинг по убыванию)

**Возвращает:** `$this` — для fluent interface

---

### sortByYear
```php
public function sortByYear(): static
```
**Описание:** Сортировка по году (новые сначала)

**Возвращает:** `$this` — для fluent interface

---

### sortByKinopoiskRating
```php
public function sortByKinopoiskRating(): static
```
**Описание:** Сортировка по рейтингу Кинопоиска

**Возвращает:** `$this` — для fluent interface

---

## Методы экспорта/импорта

### exportSortCriteria
```php
public function exportSortCriteria(): array
```
**Описание:** Экспортирует критерии сортировки в массив

**Возвращает:** `array` — Массив с данными критериев

---

### importSortCriteria
```php
public function importSortCriteria(array $data): static
```
**Описание:** Импортирует критерии сортировки из массива

**Параметры:**
- `array $data` — Массив с данными критериев

**Возвращает:** `$this` — для fluent interface

---

## Примеры использования

```php
use KinopoiskDev\Utils\SortManager;
use KinopoiskDev\Enums\SortField;
use KinopoiskDev\Enums\SortDirection;

class MyFilter {
    use SortManager;
    
    // Базовые методы сортировки
    public function setupSorting() {
        return $this->sortBy(SortField::RATING_KP, SortDirection::DESC)
                   ->sortBy(SortField::YEAR, SortDirection::ASC);
    }
    
    // Удобные методы
    public function sortByBestMovies() {
        return $this->sortByBest()
                   ->sortByYear();
    }
    
    // Управление сортировкой
    public function toggleRatingSort() {
        return $this->toggleSort(SortField::RATING_KP);
    }
    
    public function removeYearSort() {
        return $this->removeSortByField(SortField::YEAR);
    }
    
    // Проверки
    public function hasRatingSort(): bool {
        return $this->hasSortBy(SortField::RATING_KP);
    }
    
    public function getSortInfo(): array {
        return [
            'count' => $this->getSortCount(),
            'string' => $this->getSortString(),
            'hasAny' => $this->hasAnySorting()
        ];
    }
}
```

---

## Связи
- **Использует:** [SortDirection](../Enums/SortDirection.md), [SortField](../Enums/SortField.md), [SortCriteria](../Filter/SortCriteria.md)
- **Используется в:** [MovieFilter](MovieFilter.md)