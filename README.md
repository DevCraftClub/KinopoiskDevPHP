# KinoPoiskDevPHP

PHP-клиент для работы с API Kinopoisk.dev

## Установка

```bash
composer require your-vendor/kinopoiskdevphp
```

## Использование

### Базовый пример

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use KinopoiskDev\Http\MovieRequests;
use KinopoiskDev\Utils\MovieFilter;

// Инициализация клиента API с токеном
$token = getenv('KINOPOISK_TOKEN') ?: 'YOUR_API_TOKEN';
$movieRequests = new MovieRequests($token);

// Получение фильма по ID
$movie = $movieRequests->getMovieById(123);
echo "Название: {$movie->name}\n";
echo "Рейтинг: {$movie->rating->kp}\n";

// Поиск фильмов с фильтрами
$filter = new MovieFilter();
$filter->rating(8.0, 'kp', 'gte')  // Рейтинг KP >= 8.0
       ->votes(10000, 'kp', 'gte') // Количество голосов >= 10000
       ->year(2020, 'gte')         // Год выпуска >= 2020
       ->genres('драма', 'in');    // Жанр содержит "драма"

$movies = $movieRequests->searchMovies($filter->getFilters(), 1, 10);
foreach ($movies->docs as $movie) {
    echo "{$movie->name} ({$movie->year}) - Рейтинг KP: {$movie->rating->kp}\n";
}
```

### Расширенные возможности фильтрации

Библиотека поддерживает все возможности фильтрации API Kinopoisk.dev, включая:

#### Диапазоны для числовых полей

```php
// Рейтинг от 7.5 до 9.0
$filter->ratingRange(7.5, 9.0, 'kp');

// Количество голосов от 10000 до 1000000
$filter->votesRange(10000, 1000000, 'kp');

// Год выпуска от 2020 до 2023
$filter->yearRange(2020, 2023);
```

#### Диапазоны дат

```php
// Мировая премьера в 2023 году
$filter->premiereRange('01.01.2023', '31.12.2023', 'world');

// Премьера в России в 2022 году
$filter->premiereRange('01.01.2022', '31.12.2022', 'russia');
```

#### Операторы включения и исключения для жанров и стран

```php
// Включить фильмы с жанрами драма И триллер
$filter->includeGenres(['драма', 'триллер']);

// Исключить фильмы с жанром ужасы
$filter->excludeGenres('ужасы');

// Включить фильмы из США
$filter->includeCountries('США');

// Исключить фильмы из Франции
$filter->excludeCountries(['Франция']);
```

### Использование MovieSearchFilter

Класс `MovieSearchFilter` расширяет базовый `MovieFilter` и предоставляет дополнительные методы для удобного поиска:

```php
use KinopoiskDev\Filter\MovieSearchFilter;

$searchFilter = new MovieSearchFilter();
$searchFilter->searchByName('Властелин')                // Поиск по названию
             ->withRatingBetween(7.0, 9.0)              // Рейтинг от 7.0 до 9.0
             ->withIncludedGenres('фантастика')          // Включить фильмы с жанром фантастика
             ->withExcludedGenres(['ужасы', 'мультфильм']) // Исключить фильмы с жанрами ужасы и мультфильм
             ->withIncludedCountries('США')              // Включить фильмы из США
             ->withYearBetween(2010, 2023)              // Год выпуска от 2010 до 2023
             ->onlyMovies();                            // Только фильмы, не сериалы

$movies = $movieRequests->searchMovies($searchFilter->getFilters(), 1, 10);
```

## Примеры

В директории `examples` находятся примеры использования библиотеки:

- `movie_filter_example.php` - Базовые примеры использования фильтров
- `advanced_filter_example.php` - Примеры использования расширенных возможностей фильтрации

## Документация API

Подробная документация API доступна на сайте [kinopoiskdev.readme.io](https://kinopoiskdev.readme.io/reference/moviecontroller_findmanybyqueryv1_4)