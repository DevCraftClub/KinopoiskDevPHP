<?php

require_once __DIR__ . '/../vendor/autoload.php';

use KinopoiskDev\Http\MovieRequests;
use KinopoiskDev\Types\MovieSearchFilter;
use KinopoiskDev\Utils\MovieFilter;

// Инициализация клиента API с токеном
$token = getenv('KINOPOISK_TOKEN') ?: 'YOUR_API_TOKEN';
$movieRequests = new MovieRequests($token);

// Пример 1: Использование диапазонов для числовых полей
echo "Пример 1: Поиск фильмов с использованием диапазонов\n";
$filter = new MovieFilter();
$filter->ratingRange(7.5, 9.0, 'kp')  // Рейтинг KP от 7.5 до 9.0
       ->votesRange(10000, 1000000, 'kp') // Количество голосов от 10000 до 1000000
       ->yearRange(2020, 2023);      // Год выпуска от 2020 до 2023

$movies = $movieRequests->searchMovies($filter->getFilters(), 1, 5);
printMovies($movies->docs);

// Пример 2: Использование операторов включения и исключения для жанров
echo "\nПример 2: Поиск фильмов с включением и исключением жанров\n";
$genreFilter = new MovieFilter();
$genreFilter->includeGenres(['драма', 'триллер'])  // Включить фильмы с жанрами драма И триллер
            ->excludeGenres('ужасы')               // Исключить фильмы с жанром ужасы
            ->ratingRange(7.0, 10.0, 'kp');        // Рейтинг от 7.0 до 10.0

$movies = $movieRequests->searchMovies($genreFilter->getFilters(), 1, 5);
printMovies($movies->docs);

// Пример 3: Использование операторов включения и исключения для стран
echo "\nПример 3: Поиск фильмов с включением и исключением стран\n";
$countryFilter = new MovieFilter();
$countryFilter->includeCountries('США')        // Включить фильмы из США
              ->excludeCountries(['Франция'])  // Исключить фильмы из Франции
              ->yearRange(2015, 2023)          // Год выпуска от 2015 до 2023
              ->ratingRange(6.0, 10.0, 'kp');  // Рейтинг от 6.0 до 10.0

$movies = $movieRequests->searchMovies($countryFilter->getFilters(), 1, 5);
printMovies($movies->docs);

// Пример 4: Использование диапазона дат для премьеры
echo "\nПример 4: Поиск фильмов по диапазону дат премьеры\n";
$premiereFilter = new MovieFilter();
$premiereFilter->premiereRange('01.01.2023', '31.12.2023', 'world')  // Мировая премьера в 2023 году
               ->ratingRange(6.0, 10.0, 'kp');                       // Рейтинг от 6.0 до 10.0

$movies = $movieRequests->searchMovies($premiereFilter->getFilters(), 1, 5);
printMovies($movies->docs);

// Пример 5: Комбинирование различных фильтров с использованием MovieSearchFilter
echo "\nПример 5: Комбинирование различных фильтров с использованием MovieSearchFilter\n";
$combinedFilter = new MovieSearchFilter();
$combinedFilter->withRatingBetween(7.0, 9.5, 'kp')           // Рейтинг от 7.0 до 9.5
               ->withIncludedGenres('фантастика')             // Включить фильмы с жанром фантастика
               ->withExcludedGenres(['ужасы', 'мультфильм'])  // Исключить фильмы с жанрами ужасы и мультфильм
               ->withIncludedCountries('США')                 // Включить фильмы из США
               ->withYearBetween(2010, 2023)                  // Год выпуска от 2010 до 2023
               ->onlyMovies();                                // Только фильмы, не сериалы

$movies = $movieRequests->searchMovies($combinedFilter->getFilters(), 1, 5);
printMovies($movies->docs);

// Пример 6: Использование методов withVotesBetween и withPremiereRange
echo "\nПример 6: Использование методов withVotesBetween и withPremiereRange\n";
$advancedFilter = new MovieSearchFilter();
$advancedFilter->withVotesBetween(50000, 1000000, 'kp')         // Количество голосов от 50000 до 1000000
               ->withPremiereRange('01.01.2022', '31.12.2022', 'russia') // Премьера в России в 2022 году
               ->withRatingBetween(7.5, 10.0, 'kp')             // Рейтинг от 7.5 до 10.0
               ->withIncludedGenres('драма')                     // Включить фильмы с жанром драма
               ->onlyMovies();                                   // Только фильмы, не сериалы

$movies = $movieRequests->searchMovies($advancedFilter->getFilters(), 1, 5);
printMovies($movies->docs);

// Функция для вывода информации о фильмах
function printMovies(array $movies): void {
    if (empty($movies)) {
        echo "Фильмы не найдены.\n";
        return;
    }

    foreach ($movies as $index => $movie) {
        echo ($index + 1) . ". {$movie->name} ({$movie->year})\n";
        echo "   Рейтинг KP: " . ($movie->rating?->kp ?? 'N/A') . "\n";
        echo "   Жанры: " . implode(', ', array_map(fn($genre) => $genre->name, $movie->genres ?? [])) . "\n";
        echo "   Страны: " . implode(', ', array_map(fn($country) => $country->name, $movie->countries ?? [])) . "\n";
        echo "\n";
    }
}
