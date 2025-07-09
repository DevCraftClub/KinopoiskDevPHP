# SortField Enum

**Файл:** `src/Enums/SortField.php`  
**Пространство имен:** `KinopoiskDev\Enums`

## Описание

Enum для полей сортировки при поиске фильмов. Содержит все возможные поля, которые можно использовать для сортировки результатов поиска через API Kinopoisk.dev.

## Константы

### Основные поля
| Константа | Значение | Описание |
|-----------|----------|----------|
| `ID` | `'id'` | ID фильма |
| `NAME` | `'name'` | Название (русское) |
| `EN_NAME` | `'enName'` | Название (английское) |
| `ALTERNATIVE_NAME` | `'alternativeName'` | Альтернативное название |
| `YEAR` | `'year'` | Год выпуска |
| `CREATED_AT` | `'createdAt'` | Дата создания записи |
| `UPDATED_AT` | `'updatedAt'` | Дата обновления записи |
| `TITLE` | `'title'` | Название |
| `TYPE` | `'type'` | Тип |

### Поля рейтингов
| Константа | Значение | Описание |
|-----------|----------|----------|
| `RATING_KP` | `'rating.kp'` | Рейтинг Кинопоиска |
| `RATING_IMDB` | `'rating.imdb'` | Рейтинг IMDB |
| `RATING_TMDB` | `'rating.tmdb'` | Рейтинг TMDB |
| `RATING_FILM_CRITICS` | `'rating.filmCritics'` | Рейтинг кинокритиков |
| `RATING_RUSSIAN_FILM_CRITICS` | `'rating.russianFilmCritics'` | Рейтинг российских кинокритиков |
| `RATING_AWAIT` | `'rating.await'` | Рейтинг ожидания |

### Поля голосов
| Константа | Значение | Описание |
|-----------|----------|----------|
| `VOTES_KP` | `'votes.kp'` | Голоса Кинопоиска |
| `VOTES_IMDB` | `'votes.imdb'` | Голоса IMDB |
| `VOTES_TMDB` | `'votes.tmdb'` | Голоса TMDB |
| `VOTES_FILM_CRITICS` | `'votes.filmCritics'` | Голоса кинокритиков |
| `VOTES_RUSSIAN_FILM_CRITICS` | `'votes.russianFilmCritics'` | Голоса российских кинокритиков |
| `VOTES_AWAIT` | `'votes.await'` | Голоса ожидания |

### Технические параметры
| Константа | Значение | Описание |
|-----------|----------|----------|
| `MOVIE_LENGTH` | `'movieLength'` | Длительность фильма |
| `SERIES_LENGTH` | `'seriesLength'` | Длительность серии |
| `TOTAL_SERIES_LENGTH` | `'totalSeriesLength'` | Общая длительность сериала |
| `AGE_RATING` | `'ageRating'` | Возрастной рейтинг |

### Топы и позиции
| Константа | Значение | Описание |
|-----------|----------|----------|
| `TOP_10` | `'top10'` | Позиция в топ-10 |
| `TOP_250` | `'top250'` | Позиция в топ-250 |

### Даты премьер
| Константа | Значение | Описание |
|-----------|----------|----------|
| `PREMIERE_WORLD` | `'premiere.world'` | Дата мировой премьеры |
| `PREMIERE_RUSSIA` | `'premiere.russia'` | Дата премьеры в России |
| `PREMIERE_USA` | `'premiere.usa'` | Дата премьеры в США |

## Методы

### getDescription()
```php
public function getDescription(): string
```

Возвращает человекочитаемое описание поля на русском языке для использования в пользовательских интерфейсах и документации.

**Возвращает:** Описательное название поля на русском языке

**Пример использования:**
```php
$field = SortField::RATING_KP;
echo $field->getDescription(); // 'Рейтинг Кинопоиска'

$field = SortField::YEAR;
echo $field->getDescription(); // 'Год выпуска'
```

### isRatingField()
```php
public function isRatingField(): bool
```

Проверяет, является ли поле рейтинговым. Используется для группировки и специальной обработки рейтинговых полей.

**Возвращает:** `true`, если поле является рейтинговым, `false` в противном случае

**Пример использования:**
```php
$kpRating = SortField::RATING_KP;
$year = SortField::YEAR;

var_dump($kpRating->isRatingField()); // true
var_dump($year->isRatingField());     // false
```

### isVotesField()
```php
public function isVotesField(): bool
```

Проверяет, является ли поле полем голосов. Используется для группировки и специальной обработки полей голосов.

**Возвращает:** `true`, если поле является полем голосов, `false` в противном случае

**Пример использования:**
```php
$votes = SortField::VOTES_IMDB;
$rating = SortField::RATING_IMDB;

var_dump($votes->isVotesField());  // true
var_dump($rating->isVotesField()); // false
```

### isDateField()
```php
public function isDateField(): bool
```

Проверяет, является ли поле полем даты. Используется для валидации и специальной обработки временных полей.

**Возвращает:** `true`, если поле является полем даты, `false` в противном случае

**Пример использования:**
```php
$createdAt = SortField::CREATED_AT;
$year = SortField::YEAR;

var_dump($createdAt->isDateField()); // true
var_dump($year->isDateField());      // false
```

### getDataType()
```php
public function getDataType(): string
```

Возвращает тип данных поля для валидации. Определяет тип данных поля сортировки для обеспечения корректной валидации и обработки параметров сортировки.

**Возвращает:** Тип данных поля ('number', 'string', 'date')

**Пример использования:**
```php
$rating = SortField::RATING_KP;
$name = SortField::NAME;
$createdAt = SortField::CREATED_AT;

echo $rating->getDataType();   // 'number'
echo $name->getDataType();     // 'string' 
echo $createdAt->getDataType(); // 'date'
```

### isNumericField()
```php
public function isNumericField(): bool
```

Проверяет, является ли поле числовым. Используется для валидации и обработки числовых значений.

**Возвращает:** `true`, если поле является числовым, `false` в противном случае

**Пример использования:**
```php
$rating = SortField::RATING_KP;
$name = SortField::NAME;

var_dump($rating->isNumericField()); // true
var_dump($name->isNumericField());   // false
```

### getDefaultDirection()
```php
public function getDefaultDirection(): SortDirection
```

Возвращает рекомендуемое направление сортировки по умолчанию. Определяет наиболее логичное направление сортировки для каждого поля на основе его семантики и обычных пользовательских ожиданий.

**Возвращает:** Рекомендуемое направление сортировки

**Пример использования:**
```php
$rating = SortField::RATING_KP;
$name = SortField::NAME;
$top250 = SortField::TOP_250;

$rating->getDefaultDirection(); // SortDirection::DESC (лучшие рейтинги сначала)
$name->getDefaultDirection();   // SortDirection::ASC (алфавитный порядок)
$top250->getDefaultDirection(); // SortDirection::ASC (меньше = лучше позиция)
```

## Статические методы

### getRatingFields()
```php
public static function getRatingFields(): array
```

Возвращает все поля рейтингов. Используется для создания интерфейсов выбора рейтинговых полей.

**Возвращает:** Массив всех рейтинговых полей SortField

**Пример использования:**
```php
$ratingFields = SortField::getRatingFields();
// [
//     SortField::RATING_KP,
//     SortField::RATING_IMDB,
//     SortField::RATING_TMDB,
//     SortField::RATING_FILM_CRITICS,
//     SortField::RATING_RUSSIAN_FILM_CRITICS,
//     SortField::RATING_AWAIT
// ]

foreach ($ratingFields as $field) {
    echo $field->getDescription() . "\n";
}
```

### getVotesFields()
```php
public static function getVotesFields(): array
```

Возвращает все поля голосов. Используется для создания интерфейсов выбора полей голосов.

**Возвращает:** Массив всех полей голосов SortField

**Пример использования:**
```php
$votesFields = SortField::getVotesFields();
// [
//     SortField::VOTES_KP,
//     SortField::VOTES_IMDB,
//     SortField::VOTES_TMDB,
//     SortField::VOTES_FILM_CRITICS,
//     SortField::VOTES_RUSSIAN_FILM_CRITICS,
//     SortField::VOTES_AWAIT
// ]

foreach ($votesFields as $field) {
    echo $field->getDescription() . "\n";
}
```

## Особенности реализации

### Кэширование
Все методы используют статическое кэширование для оптимизации производительности при множественных вызовах.

### Типизация данных
Enum предоставляет информацию о типах данных полей:
- **number**: Числовые поля (ID, рейтинги, голоса, годы, длительность)
- **date**: Поля дат (даты создания, обновления, премьер)
- **string**: Строковые поля (названия, альтернативные названия)

### Рекомендуемые направления сортировки
- **DESC**: Рейтинги, голоса, годы, ID, даты (лучшие/новые сначала)
- **ASC**: Названия, топы, технические параметры (алфавитный порядок, лучшая позиция)

## Полный пример использования

```php
use KinopoiskDev\Enums\SortField;
use KinopoiskDev\Enums\SortDirection;

// Создание сортировки по рейтингу
$field = SortField::RATING_KP;
$direction = $field->getDefaultDirection(); // DESC

// Проверка типа поля
if ($field->isRatingField()) {
    echo "Сортируем по рейтингу: " . $field->getDescription();
}

// Валидация типа данных
if ($field->isNumericField()) {
    echo "Поле содержит числовые данные";
}

// Получение всех рейтинговых полей для UI
$ratingOptions = [];
foreach (SortField::getRatingFields() as $ratingField) {
    $ratingOptions[$ratingField->value] = $ratingField->getDescription();
}

// Группировка полей по типам
$fieldsByType = [
    'ratings' => SortField::getRatingFields(),
    'votes' => SortField::getVotesFields(),
];

// Создание конфигурации сортировки
$sortConfig = [
    'field' => $field->value,
    'direction' => $direction->value,
    'type' => $field->getDataType(),
    'description' => $field->getDescription(),
];
```

## Связанные классы

- [`SortDirection`](SortDirection.md) - Направления сортировки
- [`SortCriteria`](../filter/SortCriteria.md) - Критерии сортировки
- [`SortManager`](../utils/SortManager.md) - Управление сортировкой