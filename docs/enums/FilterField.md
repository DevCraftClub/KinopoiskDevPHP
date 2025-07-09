# FilterField Enum

**Файл:** `src/Enums/FilterField.php`  
**Пространство имен:** `KinopoiskDev\Enums`

## Описание

Enum для полей фильтрации. Содержит все возможные поля, которые можно использовать при фильтрации данных через API Kinopoisk.dev.

## Константы

### Основные поля
| Константа | Значение | Описание |
|-----------|----------|----------|
| `ID` | `'id'` | Идентификатор фильма |
| `EXTERNAL_ID` | `'externalId'` | Внешний идентификатор |
| `NAME` | `'name'` | Название (русское) |
| `EN_NAME` | `'enName'` | Название (английское) |
| `ALTERNATIVE_NAME` | `'alternativeName'` | Альтернативное название |
| `NAMES` | `'names.name'` | Все названия |
| `DESCRIPTION` | `'description'` | Описание |
| `SHORT_DESCRIPTION` | `'shortDescription'` | Краткое описание |
| `SLOGAN` | `'slogan'` | Слоган фильма |

### Типы и статусы
| Константа | Значение | Описание |
|-----------|----------|----------|
| `TYPE` | `'type'` | Тип произведения |
| `TYPE_NUMBER` | `'typeNumber'` | Номер типа |
| `IS_SERIES` | `'isSeries'` | Является ли сериалом |
| `STATUS` | `'status'` | Статус производства |

### Даты и годы
| Константа | Значение | Описание |
|-----------|----------|----------|
| `YEAR` | `'year'` | Год выпуска |
| `RELEASE_YEARS` | `'releaseYears'` | Годы выпуска |
| `UPDATED_AT` | `'updatedAt'` | Дата обновления |
| `CREATED_AT` | `'createdAt'` | Дата создания |

### Рейтинги и оценки
| Константа | Значение | Описание |
|-----------|----------|----------|
| `RATING_KP` | `'rating.kp'` | Рейтинг Кинопоиска |
| `RATING_IMDB` | `'rating.imdb'` | Рейтинг IMDB |
| `RATING_TMDB` | `'rating.tmdb'` | Рейтинг TMDB |
| `RATING_FILM_CRITICS` | `'rating.filmCritics'` | Рейтинг кинокритиков |
| `RATING_RUSSIAN_FILM_CRITICS` | `'rating.russianFilmCritics'` | Рейтинг российских кинокритиков |
| `RATING_AWAIT` | `'rating.await'` | Рейтинг ожидания |
| `RATING_MPAA` | `'ratingMpaa'` | Рейтинг MPAA |
| `AGE_RATING` | `'ageRating'` | Возрастной рейтинг |

### Голоса
| Константа | Значение | Описание |
|-----------|----------|----------|
| `VOTES_KP` | `'votes.kp'` | Голоса Кинопоиска |
| `VOTES_IMDB` | `'votes.imdb'` | Голоса IMDB |
| `VOTES_TMDB` | `'votes.tmdb'` | Голоса TMDB |
| `VOTES_FILM_CRITICS` | `'votes.filmCritics'` | Голоса кинокритиков |
| `VOTES_RUSSIAN_FILM_CRITICS` | `'votes.russianFilmCritics'` | Голоса российских кинокритиков |
| `VOTES_AWAIT` | `'votes.await'` | Голоса ожидания |

### Длительность
| Константа | Значение | Описание |
|-----------|----------|----------|
| `MOVIE_LENGTH` | `'movieLength'` | Длительность фильма |
| `SERIES_LENGTH` | `'seriesLength'` | Длительность серии |
| `TOTAL_SERIES_LENGTH` | `'totalSeriesLength'` | Общая длительность сериала |

### Жанры и страны
| Константа | Значение | Описание |
|-----------|----------|----------|
| `GENRES` | `'genres.name'` | Жанры |
| `COUNTRIES` | `'countries.name'` | Страны |

### Изображения
| Константа | Значение | Описание |
|-----------|----------|----------|
| `POSTER` | `'poster'` | Постер |
| `BACKDROP` | `'backdrop'` | Фоновое изображение |
| `LOGO` | `'logo'` | Логотип |

### Персоналии
| Константа | Значение | Описание |
|-----------|----------|----------|
| `PERSONS` | `'persons'` | Персоналии |
| `PERSONS_NAME` | `'persons.name'` | Имена персоналий |
| `PERSONS_ID` | `'persons.id'` | ID персоналий |
| `PERSONS_PROFESSION` | `'persons.profession'` | Профессии персоналий |

### Премьеры и топы
| Константа | Значение | Описание |
|-----------|----------|----------|
| `PREMIERE` | `'premiere'` | Премьеры |
| `PREMIERE_WORLD` | `'premiere.world'` | Мировая премьера |
| `PREMIERE_RUSSIA` | `'premiere.russia'` | Премьера в России |
| `PREMIERE_USA` | `'premiere.usa'` | Премьера в США |
| `TOP_10` | `'top10'` | Топ-10 |
| `TOP_250` | `'top250'` | Топ-250 |

### Дополнительные поля
| Константа | Значение | Описание |
|-----------|----------|----------|
| `TICKETS_ON_SALE` | `'ticketsOnSale'` | Билеты в продаже |
| `VIDEOS` | `'videos'` | Видео |
| `NETWORKS` | `'networks'` | Телесети |
| `FACTS` | `'facts'` | Факты |
| `FEES` | `'fees'` | Сборы |
| `SIMILAR_MOVIES` | `'similarMovies'` | Похожие фильмы |
| `SEQUELS_AND_PREQUELS` | `'sequelsAndPrequels'` | Сиквелы и приквелы |
| `WATCHABILITY` | `'watchability'` | Возможность просмотра |
| `LISTS` | `'lists'` | Списки |
| `SEASONS_INFO` | `'seasonsInfo'` | Информация о сезонах |
| `BUDGET` | `'budget'` | Бюджет |
| `AUDIENCE` | `'audience'` | Аудитория |

## Методы

### getFieldType()
```php
public function getFieldType(): string
```

Возвращает тип поля для определения подходящих операторов фильтрации.

**Возвращает:** Тип поля ('number', 'boolean', 'text', 'date', 'include_exclude', 'object', 'string')

**Пример использования:**
```php
$field = FilterField::RATING_KP;
echo $field->getFieldType(); // 'number'

$field = FilterField::IS_SERIES;
echo $field->getFieldType(); // 'boolean'

$field = FilterField::GENRES;
echo $field->getFieldType(); // 'include_exclude'

$field = FilterField::NAME;
echo $field->getFieldType(); // 'text'
```

### supportsIncludeExclude()
```php
public function supportsIncludeExclude(): bool
```

Проверяет, поддерживает ли поле операторы включения/исключения (!). Обычно используется для полей типа массив (жанры, страны).

**Возвращает:** `true`, если поле поддерживает включение/исключение, `false` в противном случае

**Пример использования:**
```php
$genres = FilterField::GENRES;
$rating = FilterField::RATING_KP;

var_dump($genres->supportsIncludeExclude()); // true
var_dump($rating->supportsIncludeExclude()); // false
```

### supportsRange()
```php
public function supportsRange(): bool
```

Проверяет, поддерживает ли поле диапазоны значений (числовые и временные поля).

**Возвращает:** `true`, если поле поддерживает диапазоны, `false` в противном случае

**Пример использования:**
```php
$year = FilterField::YEAR;
$rating = FilterField::RATING_KP;
$name = FilterField::NAME;

var_dump($year->supportsRange());   // true (число)
var_dump($rating->supportsRange()); // true (число)
var_dump($name->supportsRange());   // false (текст)
```

### getDefaultOperator()
```php
public function getDefaultOperator(): FilterOperator
```

Возвращает оператор по умолчанию для поля на основе его типа.

**Возвращает:** Подходящий оператор фильтрации

**Пример использования:**
```php
$rating = FilterField::RATING_KP;
$name = FilterField::NAME;
$genres = FilterField::GENRES;

$rating->getDefaultOperator(); // FilterOperator::EQUAL (для чисел)
$name->getDefaultOperator();   // FilterOperator::REGEX (для текста)
$genres->getDefaultOperator(); // FilterOperator::IN (для массивов)
```

### getBaseField()
```php
public function getBaseField(): string
```

Возвращает базовое поле для составных полей (разделенных точкой).

**Возвращает:** Базовое имя поля

**Пример использования:**
```php
$ratingKp = FilterField::RATING_KP; // 'rating.kp'
$votesImdb = FilterField::VOTES_IMDB; // 'votes.imdb'
$name = FilterField::NAME; // 'name'

echo $ratingKp->getBaseField();  // 'rating'
echo $votesImdb->getBaseField(); // 'votes'
echo $name->getBaseField();      // 'name'
```

### getSubField()
```php
public function getSubField(): ?string
```

Возвращает подполе для составных полей или `null` для простых полей.

**Возвращает:** Имя подполя или `null`

**Пример использования:**
```php
$ratingKp = FilterField::RATING_KP; // 'rating.kp'
$name = FilterField::NAME; // 'name'

echo $ratingKp->getSubField(); // 'kp'
var_dump($name->getSubField()); // null
```

## Типы полей

### number
Числовые поля, поддерживают операторы сравнения и диапазоны:
- ID, год, рейтинги, голоса, длительность, топы

### boolean
Логические поля, принимают значения true/false:
- IS_SERIES, TICKETS_ON_SALE

### text
Текстовые поля, поддерживают поиск по регулярным выражениям:
- NAME, EN_NAME, DESCRIPTION, PERSONS_NAME

### date
Поля дат, поддерживают операторы сравнения и диапазоны:
- CREATED_AT, UPDATED_AT, PREMIERE_*

### include_exclude
Поля массивов, поддерживают включение/исключение:
- GENRES, COUNTRIES

### object
Сложные объектные поля:
- EXTERNAL_ID, POSTER, VIDEOS, NETWORKS

### string
Простые строковые поля (по умолчанию)

## Особенности реализации

### Кэширование
Все методы используют статическое кэширование для оптимизации производительности.

### Составные поля
Поля с точкой в названии (`rating.kp`, `votes.imdb`) автоматически разбиваются на базовое поле и подполе.

## Полный пример использования

```php
use KinopoiskDev\Enums\FilterField;
use KinopoiskDev\Enums\FilterOperator;

// Работа с числовыми полями
$ratingField = FilterField::RATING_KP;
if ($ratingField->supportsRange()) {
    // Можно использовать диапазоны: rating.kp=7-10
    $operator = FilterOperator::RANGE;
} else {
    $operator = $ratingField->getDefaultOperator();
}

// Работа с полями включения/исключения
$genresField = FilterField::GENRES;
if ($genresField->supportsIncludeExclude()) {
    // Можно использовать !genres.name для исключения жанров
    $excludeOperator = FilterOperator::NOT_IN;
}

// Анализ составных полей
$votesField = FilterField::VOTES_IMDB;
$baseField = $votesField->getBaseField(); // 'votes'
$subField = $votesField->getSubField();   // 'imdb'

// Группировка полей по типам
$fieldsByType = [];
foreach (FilterField::cases() as $field) {
    $type = $field->getFieldType();
    $fieldsByType[$type][] = $field;
}

// Создание конфигурации фильтра
$filterConfig = [
    'field' => $ratingField->value,
    'type' => $ratingField->getFieldType(),
    'operator' => $ratingField->getDefaultOperator()->value,
    'supports_range' => $ratingField->supportsRange(),
    'supports_include_exclude' => $ratingField->supportsIncludeExclude(),
];
```

## Связанные классы

- [`FilterOperator`](FilterOperator.md) - Операторы фильтрации
- [`MovieFilter`](../utils/MovieFilter.md) - Основной класс фильтрации
- [`FilterTrait`](../utils/FilterTrait.md) - Трейт с методами фильтрации