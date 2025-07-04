<?php

require_once __DIR__ . '/../vendor/autoload.php';

use KinopoiskDev\Http\MovieRequests;
use KinopoiskDev\Types\MovieSearchFilter;
use KinopoiskDev\Utils\MovieFilter;

// Инициализация клиента API с токеном
$token = getenv('KINOPOISK_TOKEN') ?: 'YOUR_API_TOKEN';
$movieRequests = new MovieRequests($token);

// Пример 1: Использование базового MovieFilter
echo "Пример 1: Поиск фильмов с высоким рейтингом\n";
$filter = new MovieFilter();
$filter->rating(8.0, 'kp', 'gte')  // Рейтинг KP >= 8.0
       ->votes(10000, 'kp', 'gte') // Количество голосов >= 10000
       ->year(2020, 'gte')         // Год выпуска >= 2020
       ->genres('драма', 'in');    // Жанр содержит "драма"

$movies = $movieRequests->searchMovies($filter->getFilters(), 1, 5);
printMovies($movies->docs);

// Пример 2: Использование MovieSearchFilter для более сложного поиска
echo "\nПример 2: Поиск фильмов по нескольким критериям\n";
$searchFilter = new MovieSearchFilter();
$searchFilter->searchByName('Властелин')                // Поиск по названию
             ->withRatingBetween(7.0, 9.0)              // Рейтинг от 7.0 до 9.0
             ->withAllGenres(['фэнтези', 'приключения']) // Оба жанра должны присутствовать
             ->onlyMovies();                            // Только фильмы, не сериалы

$movies = $movieRequests->searchMovies($searchFilter->getFilters(), 1, 5);
printMovies($movies->docs);

// Пример 3: Поиск фильмов с участием конкретного актера
echo "\nПример 3: Поиск фильмов с участием актера\n";
$actorFilter = new MovieSearchFilter();
$actorFilter->withActor('Том Хэнкс')
            ->withMinRating(7.0)
            ->withMinVotes(5000);

$movies = $movieRequests->searchMovies($actorFilter->getFilters(), 1, 5);
printMovies($movies->docs);

// Пример 4: Поиск фильмов из топ-250
echo "\nПример 4: Поиск фильмов из топ-250\n";
$topFilter = new MovieSearchFilter();
$topFilter->inTop250()
          ->withYearBetween(2000, 2022);

$movies = $movieRequests->searchMovies($topFilter->getFilters(), 1, 5);
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
        echo "\n";
    }
}