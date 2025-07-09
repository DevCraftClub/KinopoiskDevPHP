# StudioSearchFilter

**Путь:** src/Filter/StudioSearchFilter.php  
**Пространство имён:** KinopoiskDev\Filter  
**Родительский класс:** [MovieFilter](../Utils/MovieFilter.md)  
**Использует:** [FilterTrait](../Utils/FilterTrait.md), [SortDirection](../Enums/SortDirection.md), [SortField](../Enums/SortField.md), [StudioType](../Enums/StudioType.md)

---

## Описание

> Фильтр для поиска студий
>
> Класс предоставляет методы для создания фильтров поиска студий по различным критериям: названию, типу, подтипу, связанным фильмам и т.д. Используется в StudioRequests для формирования параметров запроса к API.
>
> @package KinopoiskDev\Filter  
> @since 1.0.0  
> @author Maxim Harder  
> @version 1.0.0
>
> @see \KinopoiskDev\Http\StudioRequests Для использования фильтра  
> @see \KinopoiskDev\Enums\StudioType Для типов студий

---

## Методы

### movieId
```php
public function movieId(int|array $movieIds): self
```
**Описание:** Фильтр по идентификатору фильма

> Находит студии, которые участвовали в создании указанного фильма.

**Параметры:**
- `int|array $movieIds` — ID фильма или массив ID фильмов

**Возвращает:** `self` — Текущий экземпляр для цепочки методов

---

### studioType
```php
public function studioType(string|StudioType|array $types): self
```
**Описание:** Фильтр по типу студии

**Параметры:**
- `string|StudioType|array $types` — Тип студии, enum или массив типов

**Возвращает:** `self` — Текущий экземпляр для цепочки методов

---

### subType
```php
public function subType(string|array $subTypes): self
```
**Описание:** Фильтр по подтипу студии

**Параметры:**
- `string|array $subTypes` — Подтип студии или массив подтипов

**Возвращает:** `self` — Текущий экземпляр для цепочки методов

---

### title
```php
public function title(string|array $titles): self
```
**Описание:** Фильтр по названию студии

> Поиск по точному или частичному совпадению названия.

**Параметры:**
- `string|array $titles` — Название студии или массив названий

**Возвращает:** `self` — Текущий экземпляр для цепочки методов

---

### productionStudios
```php
public function productionStudios(): self
```
**Описание:** Удобный метод для фильтрации производственных студий

**Возвращает:** `self` — Текущий экземпляр для цепочки методов

---

### specialEffectsStudios
```php
public function specialEffectsStudios(): self
```
**Описание:** Удобный метод для фильтрации студий спецэффектов

**Возвращает:** `self` — Текущий экземпляр для цепочки методов

---

### distributionCompanies
```php
public function distributionCompanies(): self
```
**Описание:** Удобный метод для фильтрации прокатных компаний

**Возвращает:** `self` — Текущий экземпляр для цепочки методов

---

### dubbingStudios
```php
public function dubbingStudios(): self
```
**Описание:** Удобный метод для фильтрации студий дубляжа

**Возвращает:** `self` — Текущий экземпляр для цепочки методов

---

### excludeTypes
```php
public function excludeTypes(string|StudioType|array $types): self
```
**Описание:** Исключить определенные типы студий

**Параметры:**
- `string|StudioType|array $types` — Типы для исключения

**Возвращает:** `self` — Текущий экземпляр для цепочки методов

---

### participatedInAllMovies
```php
public function participatedInAllMovies(array $movieIds): self
```
**Описание:** Поиск студий, участвовавших в нескольких фильмах

**Параметры:**
- `array $movieIds` — Массив ID фильмов (студия должна участвовать во всех)

**Возвращает:** `self` — Текущий экземпляр для цепочки методов

---

### sortByTitle
```php
public function sortByTitle(string $direction = 'asc'): self
```
**Описание:** Сортировка по названию студии

**Параметры:**
- `string $direction` — Направление сортировки ('asc' или 'desc')

**Возвращает:** `self` — Текущий экземпляр для цепочки методов

---

### sortByType
```php
public function sortByType(string $direction = 'asc'): self
```
**Описание:** Сортировка по типу студии

**Параметры:**
- `string $direction` — Направление сортировки ('asc' или 'desc')

**Возвращает:** `self` — Текущий экземпляр для цепочки методов

---

## Примеры использования

```php
use KinopoiskDev\Filter\StudioSearchFilter;
use KinopoiskDev\Enums\StudioType;

$filter = new StudioSearchFilter();

// Поиск производственных студий
$filter->productionStudios()
       ->sortByTitle();

// Поиск студий, участвовавших в конкретном фильме
$filter->movieId(12345)
       ->sortByType();

// Поиск студий спецэффектов с определенным названием
$filter->specialEffectsStudios()
       ->title('Industrial Light & Magic')
       ->sortByTitle();

// Исключение определенных типов студий
$filter->excludeTypes([StudioType::DISTRIBUTION, StudioType::DUBBING_STUDIO])
       ->sortByTitle();

// Поиск студий, участвовавших в нескольких фильмах
$filter->participatedInAllMovies([123, 456, 789])
       ->productionStudios()
       ->sortByTitle();
```

---

## Связи
- **Родительский класс:** [MovieFilter](../Utils/MovieFilter.md)
- **Использует:** [FilterTrait](../Utils/FilterTrait.md), [SortDirection](../Enums/SortDirection.md), [SortField](../Enums/SortField.md), [StudioType](../Enums/StudioType.md)
- **Используется в:** [StudioRequests](../Http/StudioRequests.md)