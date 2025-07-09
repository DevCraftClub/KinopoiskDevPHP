# StudioType Enum

**Файл:** `src/Enums/StudioType.php`  
**Пространство имен:** `KinopoiskDev\Enums`

## Описание

Enum для типов студий. Определяет возможные типы студий в системе Kinopoisk: производство, спецэффекты, прокат и студии дубляжа.

## Константы

| Константа | Значение | Описание |
|-----------|----------|----------|
| `PRODUCTION` | `'Производство'` | Производственная студия/кинокомпания |
| `SPECIAL_EFFECTS` | `'Спецэффекты'` | Студия спецэффектов |
| `DISTRIBUTION` | `'Прокат'` | Прокатная компания |
| `DUBBING_STUDIO` | `'Студия дубляжа'` | Студия дубляжа |

### Подробное описание типов

#### PRODUCTION - Производство
Компании, занимающиеся непосредственно производством фильмов и сериалов. Это основные кинокомпании, которые создают контент.

**Примеры:** Warner Bros., Universal Pictures, Sony Pictures, 20th Century Fox

#### SPECIAL_EFFECTS - Спецэффекты  
Компании, специализирующиеся на создании визуальных и компьютерных эффектов для фильмов и сериалов.

**Примеры:** Industrial Light & Magic, Weta Digital, Digital Domain

#### DISTRIBUTION - Прокат
Дистрибьюторы, занимающиеся распространением и показом фильмов в кинотеатрах и других платформах.

**Примеры:** Walt Disney Studios Motion Pictures, Universal Pictures International

#### DUBBING_STUDIO - Студия дубляжа
Компании, занимающиеся озвучиванием, дубляжом и локализацией контента для разных рынков.

**Примеры:** студии дубляжа для локализации зарубежного контента

## Методы

### getDescription()
```php
public function getDescription(): string
```

Получает развернутое описание типа студии на русском языке.

**Возвращает:** Человекочитаемое описание типа студии

**Пример использования:**
```php
$production = StudioType::PRODUCTION;
echo $production->getDescription();
// 'Кинокомпания, занимающаяся производством фильмов и сериалов'

$effects = StudioType::SPECIAL_EFFECTS;
echo $effects->getDescription();
// 'Студия, специализирующаяся на создании визуальных и компьютерных эффектов'
```

### getEnglishName()
```php
public function getEnglishName(): string
```

Получает английское название типа студии.

**Возвращает:** Английское название типа

**Пример использования:**
```php
$production = StudioType::PRODUCTION;
echo $production->getEnglishName(); // 'Production'

$distribution = StudioType::DISTRIBUTION;
echo $distribution->getEnglishName(); // 'Distribution'
```

## Статические методы

### getAllTypes()
```php
public static function getAllTypes(): array
```

Получает все доступные типы студий в виде строковых значений.

**Возвращает:** Массив всех возможных типов студий

**Пример использования:**
```php
$types = StudioType::getAllTypes();
// [
//     'Производство',
//     'Спецэффекты', 
//     'Прокат',
//     'Студия дубляжа'
// ]

foreach ($types as $type) {
    echo $type . "\n";
}
```

### isValidType()
```php
public static function isValidType(string $value): bool
```

Проверяет, является ли переданное значение валидным типом студии.

**Параметры:**
- `$value` (string) - Значение для проверки

**Возвращает:** `true`, если значение является валидным типом студии, `false` в противном случае

**Пример использования:**
```php
var_dump(StudioType::isValidType('Производство')); // true
var_dump(StudioType::isValidType('Спецэффекты'));  // true
var_dump(StudioType::isValidType('Неизвестный'));  // false
var_dump(StudioType::isValidType('production'));  // false (регистр важен)
```

### fromString()
```php
public static function fromString(string $value): ?self
```

Получает экземпляр enum по строковому значению.

**Параметры:**
- `$value` (string) - Строковое значение типа

**Возвращает:** Объект enum или `null`, если значение не найдено

**Пример использования:**
```php
$production = StudioType::fromString('Производство');
if ($production !== null) {
    echo $production->getEnglishName(); // 'Production'
}

$invalid = StudioType::fromString('Неизвестный');
var_dump($invalid); // null
```

## Применение в фильтрации

```php
use KinopoiskDev\Enums\StudioType;

// Поиск студий определенного типа
$productionStudios = StudioType::PRODUCTION;

// Валидация пользовательского ввода
$userInput = $_GET['studio_type'] ?? '';
if (StudioType::isValidType($userInput)) {
    $studioType = StudioType::fromString($userInput);
    echo "Выбран тип: " . $studioType->getDescription();
} else {
    echo "Некорректный тип студии";
}

// Создание списка для UI
$studioOptions = [];
foreach (StudioType::cases() as $type) {
    $studioOptions[$type->value] = [
        'name' => $type->value,
        'description' => $type->getDescription(),
        'english' => $type->getEnglishName(),
    ];
}
```

## Интеграция с API фильтрами

```php
use KinopoiskDev\Filter\StudioSearchFilter;
use KinopoiskDev\Enums\StudioType;

// Поиск студий производства
$filter = new StudioSearchFilter();
$filter->byType(StudioType::PRODUCTION);

// Поиск студий спецэффектов
$filter->byType(StudioType::SPECIAL_EFFECTS);

// Множественный поиск по типам
$productionAndEffects = [
    StudioType::PRODUCTION,
    StudioType::SPECIAL_EFFECTS
];

foreach ($productionAndEffects as $type) {
    $filter->orByType($type);
}
```

## Группировка студий по типам

```php
// Группировка студий по типам
$studiosByType = [];

foreach (StudioType::cases() as $type) {
    $studiosByType[$type->value] = [
        'type' => $type,
        'description' => $type->getDescription(),
        'english_name' => $type->getEnglishName(),
        'studios' => [], // здесь будут студии этого типа
    ];
}

// Создание конфигурации для фронтенда
$frontendConfig = [
    'studio_types' => array_map(function($type) {
        return [
            'value' => $type->value,
            'label' => $type->value,
            'description' => $type->getDescription(),
            'english' => $type->getEnglishName(),
        ];
    }, StudioType::cases())
];
```

## Особенности реализации

### Строковые значения
Enum использует русские строковые значения в качестве backing values, что соответствует API Kinopoisk.

### Валидация
Предоставляет методы для безопасной валидации и преобразования пользовательского ввода.

### Локализация
Поддерживает как русские, так и английские названия типов студий.

## Полный пример использования

```php
use KinopoiskDev\Enums\StudioType;

// Получение всех типов для интерфейса
$allTypes = StudioType::getAllTypes();
echo "Доступные типы студий:\n";
foreach ($allTypes as $type) {
    echo "- $type\n";
}

// Обработка пользовательского выбора
$selectedType = 'Производство';

if (StudioType::isValidType($selectedType)) {
    $studio = StudioType::fromString($selectedType);
    
    echo "\nВыбранный тип: " . $studio->value . "\n";
    echo "Описание: " . $studio->getDescription() . "\n";
    echo "English: " . $studio->getEnglishName() . "\n";
    
    // Использование в фильтрах
    switch ($studio) {
        case StudioType::PRODUCTION:
            echo "Поиск производственных компаний\n";
            break;
            
        case StudioType::SPECIAL_EFFECTS:
            echo "Поиск студий спецэффектов\n";
            break;
            
        case StudioType::DISTRIBUTION:
            echo "Поиск дистрибьюторов\n";
            break;
            
        case StudioType::DUBBING_STUDIO:
            echo "Поиск студий дубляжа\n";
            break;
    }
} else {
    echo "Неизвестный тип студии: $selectedType\n";
}

// Создание фильтра для каждого типа
foreach (StudioType::cases() as $type) {
    echo "\nТип: {$type->value}\n";
    echo "Описание: {$type->getDescription()}\n";
    echo "English: {$type->getEnglishName()}\n";
}
```

## Связанные классы

- [`StudioSearchFilter`](../filter/StudioSearchFilter.md) - Фильтр для поиска студий
- [`MovieFilter`](../utils/MovieFilter.md) - Основной класс фильтрации фильмов