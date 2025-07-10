# ImageSearchFilter

**Путь:** src/Filter/ImageSearchFilter.php  
**Пространство имён:** KinopoiskDev\Filter  
**Родительский класс:** [MovieFilter](../Utils/MovieFilter.md)  
**Использует:** [FilterTrait](../Utils/FilterTrait.md)

---

## Описание

> Класс для фильтров при поиске изображений
>
> @package KinopoiskDev\Filter  
> @since 1.0.0  
> @author Maxim Harder  
> @version 1.0.0

---

## Методы

### language
```php
public function language(string $language): self
```
**Описание:** Добавляет фильтр по языку изображения

**Параметры:**
- `string $language` — Язык изображения

**Возвращает:** `$this` — для fluent interface

---

### onlyPosters
```php
public function onlyPosters(): self
```
**Описание:** Фильтр только для постеров

**Возвращает:** `$this` — для fluent interface

---

### onlyStills
```php
public function onlyStills(): self
```
**Описание:** Фильтр только для кадров

**Возвращает:** `$this` — для fluent interface

---

### onlyShooting
```php
public function onlyShooting(): self
```
**Описание:** Фильтр только для фотосессий

**Возвращает:** `$this` — для fluent interface

---

### onlyScreenshots
```php
public function onlyScreenshots(): self
```
**Описание:** Фильтр только для скриншотов

**Возвращает:** `$this` — для fluent interface

---

### onlyHighRes
```php
public function onlyHighRes(): self
```
**Описание:** Фильтр только для изображений высокого разрешения (Full HD+)

**Возвращает:** `$this` — для fluent interface

---

### minResolution
```php
public function minResolution(int $minWidth, int $minHeight): self
```
**Описание:** Добавляет фильтр по минимальному разрешению

**Параметры:**
- `int $minWidth` — Минимальная ширина
- `int $minHeight` — Минимальная высота

**Возвращает:** `$this` — для fluent interface

---

### width
```php
public function width(int $width, string $operator = 'eq'): self
```
**Описание:** Добавляет фильтр по ширине изображения

**Параметры:**
- `int $width` — Ширина изображения в пикселях
- `string $operator` — Оператор сравнения

**Возвращает:** `$this` — для fluent interface

---

### height
```php
public function height(int $height, string $operator = 'eq'): self
```
**Описание:** Добавляет фильтр по высоте изображения

**Параметры:**
- `int $height` — Высота изображения в пикселях
- `string $operator` — Оператор сравнения

**Возвращает:** `$this` — для fluent interface

---

## Примеры использования

```php
use KinopoiskDev\Filter\ImageSearchFilter;

$filter = new ImageSearchFilter();

// Поиск постеров на русском языке
$filter->onlyPosters()
       ->language('ru')
       ->sortByDate();

// Поиск кадров высокого разрешения
$filter->onlyStills()
       ->onlyHighRes()
       ->sortByDate();

// Поиск скриншотов с определенным разрешением
$filter->onlyScreenshots()
       ->width(1920, 'gte')
       ->height(1080, 'gte')
       ->sortByDate();

// Поиск фотосессий с минимальным разрешением
$filter->onlyShooting()
       ->minResolution(1280, 720)
       ->sortByDate();
```

---

## Связи
- **Родительский класс:** [MovieFilter](../Utils/MovieFilter.md)
- **Использует:** [FilterTrait](../Utils/FilterTrait.md)
- **Используется в:** [ImageRequests](../Http/ImageRequests.md)