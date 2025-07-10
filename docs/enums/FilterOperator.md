# FilterOperator Enum

**Файл:** `src/Enums/FilterOperator.php`  
**Пространство имен:** `KinopoiskDev\Enums`

## Описание

Enum для операторов фильтрации. Содержит все возможные операторы, которые можно использовать при фильтрации данных через API Kinopoisk.dev.

## Константы

### Операторы сравнения
| Константа | Значение | Описание | Пример |
|-----------|----------|----------|---------|
| `EQUALS` | `'eq'` | Равно | `rating.kp=7.5` |
| `NOT_EQUALS` | `'ne'` | Не равно | `year!=2023` |
| `GREATER_THAN` | `'gt'` | Больше | `rating.kp>7` |
| `GREATER_THAN_EQUALS` | `'gte'` | Больше или равно | `year>=2020` |
| `LESS_THAN` | `'lt'` | Меньше | `movieLength<120` |
| `LESS_THAN_EQUALS` | `'lte'` | Меньше или равно | `ageRating<=16` |

### Операторы для массивов
| Константа | Значение | Описание | Пример |
|-----------|----------|----------|---------|
| `IN` | `'in'` | Значение содержится в массиве | `genres.name=драма&genres.name=комедия` |
| `NOT_IN` | `'nin'` | Значение не содержится в массиве | `!genres.name=ужасы` |
| `ALL` | `'all'` | Все значения должны присутствовать | `genres.name=+драма&genres.name=+комедия` |

### Операторы для строк
| Константа | Значение | Описание | Пример |
|-----------|----------|----------|---------|
| `REGEX` | `'regex'` | Регулярное выражение | `name=/Матрица/i` |

### Специальные операторы
| Константа | Значение | Описание | Пример |
|-----------|----------|----------|---------|
| `RANGE` | `'range'` | Диапазон значений | `year=2020-2023` |
| `INCLUDE` | `'include'` | Включить (для жанров и стран) | `+genres.name=драма` |
| `EXCLUDE` | `'exclude'` | Исключить (для жанров и стран) | `!genres.name=ужасы` |

## Методы

### getDefaultForFieldType()
```php
public static function getDefaultForFieldType(string $fieldType): self
```

Возвращает оператор по умолчанию для указанного типа поля.

**Параметры:**
- `$fieldType` (string) - Тип поля ('array', 'text', 'number', 'boolean', etc.)

**Возвращает:** Подходящий оператор по умолчанию

**Пример использования:**
```php
$arrayOperator = FilterOperator::getDefaultForFieldType('array');   // IN
$textOperator = FilterOperator::getDefaultForFieldType('text');     // REGEX
$numberOperator = FilterOperator::getDefaultForFieldType('number'); // EQUALS
```

### getPrefix()
```php
public function getPrefix(): ?string
```

Возвращает префикс для операторов включения/исключения, используемый в URL параметрах.

**Возвращает:** Префикс ('+', '!') или `null` для обычных операторов

**Пример использования:**
```php
$include = FilterOperator::INCLUDE;
$exclude = FilterOperator::EXCLUDE;
$equals = FilterOperator::EQUALS;

echo $include->getPrefix(); // '+'
echo $exclude->getPrefix(); // '!'
var_dump($equals->getPrefix()); // null
```

### isRangeOperator()
```php
public function isRangeOperator(): bool
```

Проверяет, является ли оператор оператором диапазона.

**Возвращает:** `true`, если оператор RANGE, `false` в противном случае

**Пример использования:**
```php
$range = FilterOperator::RANGE;
$equals = FilterOperator::EQUALS;

var_dump($range->isRangeOperator());  // true
var_dump($equals->isRangeOperator()); // false
```

### isIncludeExcludeOperator()
```php
public function isIncludeExcludeOperator(): bool
```

Проверяет, является ли оператор оператором включения/исключения.

**Возвращает:** `true`, если оператор INCLUDE или EXCLUDE, `false` в противном случае

**Пример использования:**
```php
$include = FilterOperator::INCLUDE;
$exclude = FilterOperator::EXCLUDE;
$equals = FilterOperator::EQUALS;

var_dump($include->isIncludeExcludeOperator()); // true
var_dump($exclude->isIncludeExcludeOperator()); // true
var_dump($equals->isIncludeExcludeOperator());  // false
```

## Применение операторов

### Числовые поля
```php
// Поиск фильмов с рейтингом больше 8
FilterOperator::GREATER_THAN       // rating.kp>8

// Поиск фильмов 2020-2023 годов
FilterOperator::RANGE              // year=2020-2023

// Поиск фильмов не 2023 года
FilterOperator::NOT_EQUALS         // year!=2023
```

### Текстовые поля
```php
// Поиск по регулярному выражению
FilterOperator::REGEX              // name=/Матрица/i

// Точное совпадение названия
FilterOperator::EQUALS             // name=Матрица
```

### Массивы (жанры, страны)
```php
// Включить жанры
FilterOperator::INCLUDE            // +genres.name=драма
FilterOperator::IN                 // genres.name=драма&genres.name=комедия

// Исключить жанры
FilterOperator::EXCLUDE            // !genres.name=ужасы
FilterOperator::NOT_IN             // !genres.name=ужасы&!genres.name=триллер

// Все указанные жанры должны присутствовать
FilterOperator::ALL                // genres.name=+драма&genres.name=+комедия
```

### Логические поля
```php
// Только сериалы
FilterOperator::EQUALS             // isSeries=true

// Исключить сериалы
FilterOperator::NOT_EQUALS         // isSeries=false
```

## Совместимость с типами полей

| Тип поля | Совместимые операторы |
|----------|----------------------|
| **number** | EQUALS, NOT_EQUALS, GREATER_THAN, GREATER_THAN_EQUALS, LESS_THAN, LESS_THAN_EQUALS, RANGE |
| **text** | EQUALS, NOT_EQUALS, REGEX |
| **boolean** | EQUALS, NOT_EQUALS |
| **array** | IN, NOT_IN, ALL, INCLUDE, EXCLUDE |
| **date** | EQUALS, NOT_EQUALS, GREATER_THAN, GREATER_THAN_EQUALS, LESS_THAN, LESS_THAN_EQUALS, RANGE |

## Особенности реализации

### Кэширование
Все методы используют статическое кэширование для оптимизации производительности.

### Префиксы операторов
Операторы INCLUDE и EXCLUDE имеют специальные префиксы ('+' и '!'), которые используются в URL параметрах для обозначения включения и исключения значений.

## Полный пример использования

```php
use KinopoiskDev\Enums\FilterOperator;
use KinopoiskDev\Enums\FilterField;

// Создание фильтра по рейтингу
$ratingField = FilterField::RATING_KP;
$operator = FilterOperator::GREATER_THAN;
// Результат: rating.kp>8

// Создание фильтра по диапазону лет
$yearField = FilterField::YEAR;
$rangeOperator = FilterOperator::RANGE;
// Результат: year=2020-2023

// Фильтрация по жанрам с исключением
$genresField = FilterField::GENRES;
$excludeOperator = FilterOperator::EXCLUDE;
$prefix = $excludeOperator->getPrefix(); // '!'
// Результат: !genres.name=ужасы

// Автоматический выбор оператора по типу поля
$textField = FilterField::NAME;
$defaultOperator = FilterOperator::getDefaultForFieldType('text'); // REGEX

// Проверка возможностей оператора
if ($rangeOperator->isRangeOperator()) {
    echo "Можно использовать диапазоны значений";
}

if ($excludeOperator->isIncludeExcludeOperator()) {
    echo "Оператор для включения/исключения значений";
}

// Группировка операторов по назначению
$comparisonOperators = [
    FilterOperator::EQUALS,
    FilterOperator::NOT_EQUALS,
    FilterOperator::GREATER_THAN,
    FilterOperator::LESS_THAN,
];

$arrayOperators = [
    FilterOperator::IN,
    FilterOperator::NOT_IN,
    FilterOperator::ALL,
    FilterOperator::INCLUDE,
    FilterOperator::EXCLUDE,
];

// Создание конфигурации фильтра
$filterConfig = [
    'field' => $ratingField->value,
    'operator' => $operator->value,
    'prefix' => $operator->getPrefix(),
    'is_range' => $operator->isRangeOperator(),
    'is_include_exclude' => $operator->isIncludeExcludeOperator(),
];
```

## Связанные классы

- [`FilterField`](FilterField.md) - Поля для фильтрации
- [`MovieFilter`](../utils/MovieFilter.md) - Основной класс фильтрации
- [`FilterTrait`](../utils/FilterTrait.md) - Трейт с методами фильтрации