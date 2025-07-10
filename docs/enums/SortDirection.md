# SortDirection Enum

**Файл:** `src/Enums/SortDirection.php`  
**Пространство имен:** `KinopoiskDev\Enums`

## Описание

Enum для направления сортировки результатов поиска. Определяет возможные направления сортировки данных при выполнении запросов к API Kinopoisk.dev.

## Константы

### ASC
```php
case ASC = 'asc';
```
Сортировка по возрастанию (от меньшего к большему, от А до Я).

### DESC
```php
case DESC = 'desc';
```
Сортировка по убыванию (от большего к меньшему, от Я до А).

## Методы

### reverse()
```php
public function reverse(): SortDirection
```

Возвращает противоположное направление сортировки. Полезно для переключения направления сортировки в пользовательских интерфейсах или для реализации логики "toggle" сортировки.

**Возвращает:** Противоположное направление сортировки

**Пример использования:**
```php
$direction = SortDirection::ASC;
$reversed = $direction->reverse(); // SortDirection::DESC

$direction = SortDirection::DESC;
$reversed = $direction->reverse(); // SortDirection::ASC
```

### getSymbol()
```php
public function getSymbol(): string
```

Возвращает символьное представление направления сортировки для использования в пользовательских интерфейсах.

**Возвращает:** Символ направления сортировки ('↑' для ASC, '↓' для DESC)

**Пример использования:**
```php
$asc = SortDirection::ASC;
echo $asc->getSymbol(); // '↑'

$desc = SortDirection::DESC;
echo $desc->getSymbol(); // '↓'
```

### getDescription()
```php
public function getDescription(): string
```

Возвращает описательное название направления на русском языке для отображения в русскоязычных интерфейсах.

**Возвращает:** Описание направления сортировки на русском языке

**Пример использования:**
```php
$asc = SortDirection::ASC;
echo $asc->getDescription(); // 'По возрастанию'

$desc = SortDirection::DESC;
echo $desc->getDescription(); // 'По убыванию'
```

### getShortDescription()
```php
public function getShortDescription(): string
```

Возвращает краткое описание направления для использования в компактных интерфейсах.

**Возвращает:** Краткое описание направления

**Пример использования:**
```php
$asc = SortDirection::ASC;
echo $asc->getShortDescription(); // 'А→Я'

$desc = SortDirection::DESC;
echo $desc->getShortDescription(); // 'Я→А'
```

### isAscending()
```php
public function isAscending(): bool
```

Проверяет, является ли направление возрастающим.

**Возвращает:** `true`, если направление ASC, `false` в противном случае

**Пример использования:**
```php
$asc = SortDirection::ASC;
$desc = SortDirection::DESC;

var_dump($asc->isAscending());  // true
var_dump($desc->isAscending()); // false
```

### isDescending()
```php
public function isDescending(): bool
```

Проверяет, является ли направление убывающим.

**Возвращает:** `true`, если направление DESC, `false` в противном случае

**Пример использования:**
```php
$asc = SortDirection::ASC;
$desc = SortDirection::DESC;

var_dump($asc->isDescending());  // false
var_dump($desc->isDescending()); // true
```

## Статические методы

### fromString()
```php
public static function fromString(string $value, ?SortDirection $default = null): SortDirection
```

Безопасно создает экземпляр SortDirection из строки с возможностью указания значения по умолчанию при неудачном преобразовании.

**Параметры:**
- `$value` (string) - Строковое значение направления
- `$default` (SortDirection|null) - Значение по умолчанию (ASC если не указано)

**Возвращает:** Экземпляр SortDirection

**Пример использования:**
```php
$direction1 = SortDirection::fromString('asc');    // SortDirection::ASC
$direction2 = SortDirection::fromString('desc');   // SortDirection::DESC
$direction3 = SortDirection::fromString('invalid'); // SortDirection::ASC (default)

// С кастомным default
$direction4 = SortDirection::fromString('invalid', SortDirection::DESC); // SortDirection::DESC
```

### getAllDirections()
```php
public static function getAllDirections(): array
```

Возвращает все доступные направления сортировки. Используется для создания интерфейсов выбора направления.

**Возвращает:** Массив всех направлений SortDirection

**Пример использования:**
```php
$directions = SortDirection::getAllDirections();
// [SortDirection::ASC, SortDirection::DESC]

foreach ($directions as $direction) {
    echo $direction->getDescription() . "\n";
}
// По возрастанию
// По убыванию
```

## Особенности реализации

### Кэширование
Все методы используют статическое кэширование для оптимизации производительности при множественных вызовах.

### Совместимость с backed enum
Enum основан на строковых значениях ('asc', 'desc'), что обеспечивает совместимость с API и базами данных.

## Полный пример использования

```php
use KinopoiskDev\Enums\SortDirection;

// Создание экземпляров
$asc = SortDirection::ASC;
$desc = SortDirection::DESC;

// Переключение направления
$reversed = $asc->reverse(); // DESC

// Получение символов для UI
echo "Текущее направление: " . $asc->getSymbol(); // ↑

// Проверка типа направления
if ($direction->isAscending()) {
    echo "Сортируем по возрастанию";
}

// Создание из строки
$userDirection = SortDirection::fromString($_GET['sort_dir'] ?? 'asc');

// Получение всех направлений для select
$options = [];
foreach (SortDirection::getAllDirections() as $direction) {
    $options[$direction->value] = $direction->getDescription();
}
// ['asc' => 'По возрастанию', 'desc' => 'По убыванию']
```

## Связанные классы

- [`SortField`](SortField.md) - Поля для сортировки
- [`SortCriteria`](../filter/SortCriteria.md) - Критерии сортировки
- [`SortManager`](../utils/SortManager.md) - Управление сортировкой