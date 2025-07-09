# SortCriteria

**Путь:** src/Filter/SortCriteria.php  
**Пространство имён:** KinopoiskDev\Filter  
**Использует:** [SortDirection](../Enums/SortDirection.md), [SortField](../Enums/SortField.md)

---

## Описание

> Класс для представления критериев сортировки
>
> Инкапсулирует информацию о поле сортировки и направлении, предоставляя удобные методы для работы с параметрами сортировки.
>
> @package KinopoiskDev\Filter  
> @since 1.0.0  
> @author Maxim Harder  
> @version 1.0.0

---

## Конструктор

```php
public function __construct(
    public readonly SortField     $field,
    public readonly SortDirection $direction,
) {}
```

**Параметры:**
- `SortField $field` — Поле для сортировки
- `SortDirection $direction` — Направление сортировки

---

## Методы

### __toString
```php
public function __toString(): string
```
**Описание:** Возвращает строковое представление критериев

**Возвращает:** `string` — Человекочитаемое описание критериев сортировки

---

### create
```php
public static function create(SortField $field): self
```
**Описание:** Создает критерии сортировки с автоматическим направлением по умолчанию

> Фабричный метод, который создает SortCriteria используя рекомендуемое направление сортировки для указанного поля.

**Параметры:**
- `SortField $field` — Поле для сортировки

**Возвращает:** `self` — Новый экземпляр SortCriteria с направлением по умолчанию

---

### ascending
```php
public static function ascending(SortField $field): self
```
**Описание:** Создает критерии сортировки по возрастанию

**Параметры:**
- `SortField $field` — Поле для сортировки

**Возвращает:** `self` — Новый экземпляр SortCriteria с направлением ASC

---

### descending
```php
public static function descending(SortField $field): self
```
**Описание:** Создает критерии сортировки по убыванию

**Параметры:**
- `SortField $field` — Поле для сортировки

**Возвращает:** `self` — Новый экземпляр SortCriteria с направлением DESC

---

### fromArray
```php
public static function fromArray(array $data): ?self
```
**Описание:** Создает критерии из массива данных

> Фабричный метод для создания SortCriteria из ассоциативного массива с ключами 'field' и 'direction'.

**Параметры:**
- `array $data` — Массив с данными сортировки

**Возвращает:** `self|null` — Новый экземпляр SortCriteria или null при некорректных данных

---

### fromStrings
```php
public static function fromStrings(string $field, ?string $direction = NULL): ?self
```
**Описание:** Создает критерии из строковых значений

> Фабричный метод для создания SortCriteria из строковых представлений поля и направления сортировки с возможностью указания fallback значений.

**Параметры:**
- `string $field` — Строковое значение поля
- `string|null $direction` — Строковое значение направления (опционально)

**Возвращает:** `self|null` — Новый экземпляр SortCriteria или null при неудачном преобразовании

---

### toArray
```php
public function toArray(): array
```
**Описание:** Преобразует критерии в массив

**Возвращает:** `array` — Ассоциативный массив с ключами 'field' и 'direction'

---

### toApiString
```php
public function toApiString(): string
```
**Описание:** Преобразует критерии в строку для URL параметров API

> Формирует строковое представление критериев сортировки в формате, ожидаемом API Kinopoisk.dev (например: "-rating.kp" для убывания).

**Возвращает:** `string` — Строковое представление для API

---

### reverse
```php
public function reverse(): self
```
**Описание:** Возвращает противоположные критерии сортировки

> Создает новый экземпляр SortCriteria с тем же полем, но противоположным направлением сортировки.

**Возвращает:** `self` — Новый экземпляр с обращенным направлением

---

### hasSameField
```php
public function hasSameField(SortCriteria $other): bool
```
**Описание:** Проверяет, совпадают ли критерии по полю

**Параметры:**
- `SortCriteria $other` — Другие критерии для сравнения

**Возвращает:** `bool` — true, если поля совпадают, false в противном случае

---

### equals
```php
public function equals(SortCriteria $other): bool
```
**Описание:** Проверяет полное равенство критериев

**Параметры:**
- `SortCriteria $other` — Другие критерии для сравнения

**Возвращает:** `bool` — true, если поле и направление совпадают, false в противном случае

---

### toShortString
```php
public function toShortString(): string
```
**Описание:** Возвращает краткое строковое представление

**Возвращает:** `string` — Краткое описание с символом направления

---

### isRatingSort
```php
public function isRatingSort(): bool
```
**Описание:** Проверяет, является ли поле рейтинговым

**Возвращает:** `bool` — true, если поле относится к рейтингам

---

### isVotesSort
```php
public function isVotesSort(): bool
```
**Описание:** Проверяет, является ли поле связанным с голосами

**Возвращает:** `bool` — true, если поле связано с количеством голосов

---

### isDateSort
```php
public function isDateSort(): bool
```
**Описание:** Проверяет, является ли поле датой

**Возвращает:** `bool` — true, если поле является датой

---

### getFieldDataType
```php
public function getFieldDataType(): string
```
**Описание:** Возвращает тип данных поля

**Возвращает:** `string` — Тип данных поля (rating, votes, date, text, number)

---

## Примеры использования

```php
use KinopoiskDev\Filter\SortCriteria;
use KinopoiskDev\Enums\SortField;
use KinopoiskDev\Enums\SortDirection;

// Создание критериев сортировки
$sortByRating = new SortCriteria(SortField::RATING_KP, SortDirection::DESC);

// Использование фабричных методов
$sortByName = SortCriteria::create(SortField::NAME);
$sortByYear = SortCriteria::ascending(SortField::YEAR);
$sortByVotes = SortCriteria::descending(SortField::VOTES_KP);

// Создание из массива
$data = ['field' => 'rating.kp', 'direction' => 'desc'];
$sortCriteria = SortCriteria::fromArray($data);

// Создание из строк
$sortCriteria = SortCriteria::fromStrings('name', 'asc');

// Преобразование в API строку
$apiString = $sortCriteria->toApiString(); // "-rating.kp" или "name"

// Обращение направления
$reverseSort = $sortCriteria->reverse();

// Проверки
if ($sortCriteria->isRatingSort()) {
    echo "Это сортировка по рейтингу";
}

if ($sortCriteria->equals($otherCriteria)) {
    echo "Критерии идентичны";
}
```

---

## Связи
- **Использует:** [SortDirection](../Enums/SortDirection.md), [SortField](../Enums/SortField.md)
- **Используется в:** [MovieFilter](../Utils/MovieFilter.md), [FilterTrait](../Utils/FilterTrait.md)