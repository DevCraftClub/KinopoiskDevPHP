# PersonSearchFilter

**Путь:** src/Filter/PersonSearchFilter.php  
**Пространство имён:** KinopoiskDev\Filter  
**Родительский класс:** [MovieFilter](../Utils/MovieFilter.md)  
**Использует:** [FilterTrait](../Utils/FilterTrait.md)

---

## Описание

> Класс для фильтров при поиске персон
>
> Расширяет базовый фильтр методами, специфичными для персон
>
> @package KinopoiskDev\Filter  
> @since 1.0.0  
> @author Maxim Harder  
> @version 1.0.0  
> @link https://kinopoiskdev.readme.io/reference/personcontroller_findmanybyqueryv1_4

---

## Методы

### age
```php
public function age(int $age, string $operator = 'eq'): self
```
**Описание:** Добавляет фильтр по возрасту

**Параметры:**
- `int $age` — Возраст
- `string $operator` — Оператор сравнения (eq, gte, lte, и т.д.)

**Возвращает:** `$this` — для fluent interface

---

### sex
```php
public function sex(string $sex): self
```
**Описание:** Добавляет фильтр по полу

**Параметры:**
- `string $sex` — Пол (male, female)

**Возвращает:** `$this` — для fluent interface

---

### birthPlace
```php
public function birthPlace(string $birthPlace, string $operator = 'regex'): self
```
**Описание:** Добавляет фильтр по месту рождения

**Параметры:**
- `string $birthPlace` — Место рождения
- `string $operator` — Оператор сравнения

**Возвращает:** `$this` — для fluent interface

---

### death
```php
public function death(string $death, string $operator = 'eq'): self
```
**Описание:** Добавляет фильтр по дате смерти

**Параметры:**
- `string $death` — Дата смерти
- `string $operator` — Оператор сравнения

**Возвращает:** `$this` — для fluent interface

---

### birthday
```php
public function birthday(string $birthday, string $operator = 'eq'): self
```
**Описание:** Добавляет фильтр по дате рождения

**Параметры:**
- `string $birthday` — Дата рождения
- `string $operator` — Оператор сравнения

**Возвращает:** `$this` — для fluent interface

---

### countAwards
```php
public function countAwards(int $countAwards, string $operator = 'gte'): self
```
**Описание:** Добавляет фильтр по количеству наград

**Параметры:**
- `int $countAwards` — Количество наград
- `string $operator` — Оператор сравнения

**Возвращает:** `$this` — для fluent interface

---

### onlyActors
```php
public function onlyActors(): self
```
**Описание:** Фильтр только для актеров

**Возвращает:** `$this` — для fluent interface

---

### profession
```php
public function profession(string $profession, string $operator = 'eq'): self
```
**Описание:** Добавляет фильтр по профессии

**Параметры:**
- `string $profession` — Профессия (актер, режиссер, сценарист, и т.д.)
- `string $operator` — Оператор сравнения

**Возвращает:** `$this` — для fluent interface

---

### onlyDirectors
```php
public function onlyDirectors(): self
```
**Описание:** Фильтр только для режиссеров

**Возвращает:** `$this` — для fluent interface

---

### onlyWriters
```php
public function onlyWriters(): self
```
**Описание:** Фильтр только для сценаристов

**Возвращает:** `$this` — для fluent interface

---

### onlyAlive
```php
public function onlyAlive(): self
```
**Описание:** Фильтр только для живых персон

**Возвращает:** `$this` — для fluent interface

---

## Примеры использования

```php
use KinopoiskDev\Filter\PersonSearchFilter;

$filter = new PersonSearchFilter();

// Поиск живых российских актеров старше 30 лет
$filter->onlyAlive()
       ->onlyActors()
       ->birthPlace('Россия')
       ->age(30, 'gte')
       ->sortByName();

// Поиск режиссеров с большим количеством наград
$filter->onlyDirectors()
       ->countAwards(10, 'gte')
       ->sortByCountAwards();

// Поиск женщин-сценаристов
$filter->onlyWriters()
       ->sex('female')
       ->sortByName();
```

---

## Связи
- **Родительский класс:** [MovieFilter](../Utils/MovieFilter.md)
- **Использует:** [FilterTrait](../Utils/FilterTrait.md)
- **Используется в:** [PersonRequests](../Http/PersonRequests.md)