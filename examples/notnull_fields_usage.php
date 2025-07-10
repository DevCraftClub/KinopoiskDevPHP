<?php

require_once __DIR__ . '/../vendor/autoload.php';

use KinopoiskDev\Kinopoisk;
use KinopoiskDev\Filter\MovieSearchFilter;
use KinopoiskDev\Filter\PersonSearchFilter;
use KinopoiskDev\Filter\ReviewSearchFilter;
use KinopoiskDev\Filter\SeasonSearchFilter;
use KinopoiskDev\Filter\StudioSearchFilter;
use KinopoiskDev\Filter\ImageSearchFilter;
use KinopoiskDev\Filter\KeywordSearchFilter;
use KinopoiskDev\Http\MovieRequests;
use KinopoiskDev\Http\PersonRequests;
use KinopoiskDev\Http\ReviewRequests;
use KinopoiskDev\Http\SeasonRequests;
use KinopoiskDev\Http\StudioRequests;
use KinopoiskDev\Http\ImageRequests;
use KinopoiskDev\Http\KeywordRequests;

/**
 * Пример использования notNullFields во всех фильтрах
 * 
 * Демонстрирует, как исключить записи с пустыми значениями в указанных полях
 */

// Инициализация API клиента
$api = new Kinopoisk('your-api-token');

echo "=== Примеры использования notNullFields ===\n\n";

// 1. MovieSearchFilter - исключение фильмов без постеров и описаний
echo "1. Поиск фильмов с исключением пустых постеров и описаний:\n";
$movieFilter = new MovieSearchFilter();
$movieFilter->withYearBetween(2020, 2024)
           ->withRatingBetween(7.0, 10.0)
           ->notNullFields(['poster.url', 'description', 'name']);

try {
    $movieClient = new MovieRequests($api->getApiToken());
    $response = $movieClient->searchMovies($movieFilter, 1, 5);
    echo "Найдено {$response->total} фильмов с заполненными полями\n";
    foreach ($response->docs as $movie) {
        echo "- {$movie->name} (ID: {$movie->id})\n";
    }
} catch (Exception $e) {
    echo "Ошибка: {$e->getMessage()}\n";
}
echo "\n";

// 2. PersonSearchFilter - исключение персон без фото и описаний
echo "2. Поиск персон с исключением пустых фото и описаний:\n";
$personFilter = new PersonSearchFilter();
$personFilter->onlyActors()
            ->notNullFields(['photo', 'description', 'name']);

try {
    $personClient = new PersonRequests($api->getApiToken());
    $response = $personClient->searchPersons($personFilter, 1, 5);
    echo "Найдено {$response->total} персон с заполненными полями\n";
    foreach ($response->docs as $person) {
        echo "- {$person->name} (ID: {$person->id})\n";
    }
} catch (Exception $e) {
    echo "Ошибка: {$e->getMessage()}\n";
}
echo "\n";

// 3. ReviewSearchFilter - исключение отзывов без текста
echo "3. Поиск отзывов с исключением пустого текста:\n";
$reviewFilter = new ReviewSearchFilter();
$reviewFilter->onlyPositive()
            ->notNullFields(['review', 'title', 'author']);

try {
    $reviewClient = new ReviewRequests($api->getApiToken());
    $response = $reviewClient->getReviews($reviewFilter, 1, 5);
    echo "Найдено {$response->total} отзывов с заполненным текстом\n";
    foreach ($response->docs as $review) {
        echo "- {$review->title} (Автор: {$review->author})\n";
    }
} catch (Exception $e) {
    echo "Ошибка: {$e->getMessage()}\n";
}
echo "\n";

// 4. SeasonSearchFilter - исключение сезонов без эпизодов
echo "4. Поиск сезонов с исключением пустых эпизодов:\n";
$seasonFilter = new SeasonSearchFilter();
$seasonFilter->notNullFields(['episodesCount', 'number']);

try {
    $seasonClient = new SeasonRequests($api->getApiToken());
    $response = $seasonClient->getSeasons($seasonFilter, 1, 5);
    echo "Найдено {$response->total} сезонов с заполненными данными\n";
    foreach ($response->docs as $season) {
        echo "- Сезон {$season->number} (Эпизодов: {$season->episodesCount})\n";
    }
} catch (Exception $e) {
    echo "Ошибка: {$e->getMessage()}\n";
}
echo "\n";

// 5. StudioSearchFilter - исключение студий без логотипов
echo "5. Поиск студий с исключением пустых логотипов:\n";
$studioFilter = new StudioSearchFilter();
$studioFilter->productionStudios()
            ->notNullFields(['logo.url', 'title']);

try {
    $studioClient = new StudioRequests($api->getApiToken());
    $response = $studioClient->getStudios($studioFilter, 1, 5);
    echo "Найдено {$response->total} студий с логотипами\n";
    foreach ($response->docs as $studio) {
        echo "- {$studio->title} (ID: {$studio->id})\n";
    }
} catch (Exception $e) {
    echo "Ошибка: {$e->getMessage()}\n";
}
echo "\n";

// 6. ImageSearchFilter - исключение изображений без размеров
echo "6. Поиск изображений с исключением пустых размеров:\n";
$imageFilter = new ImageSearchFilter();
$imageFilter->onlyPosters()
           ->onlyHighRes()
           ->notNullFields(['width', 'height', 'url']);

try {
    $imageClient = new ImageRequests($api->getApiToken());
    $response = $imageClient->getImages($imageFilter, 1, 5);
    echo "Найдено {$response->total} изображений с размерами\n";
    foreach ($response->docs as $image) {
        echo "- Изображение {$image->width}x{$image->height} (ID: {$image->id})\n";
    }
} catch (Exception $e) {
    echo "Ошибка: {$e->getMessage()}\n";
}
echo "\n";

// 7. KeywordSearchFilter - исключение ключевых слов без фильмов
echo "7. Поиск ключевых слов с исключением пустых фильмов:\n";
$keywordFilter = new KeywordSearchFilter();
$keywordFilter->onlyPopular(5)
             ->notNullFields(['title', 'movies']);

try {
    $keywordClient = new KeywordRequests($api->getApiToken());
    $response = $keywordClient->searchKeywords($keywordFilter, 1, 5);
    echo "Найдено {$response->total} ключевых слов с фильмами\n";
    foreach ($response->docs as $keyword) {
        $movieCount = $keyword->getMoviesCount();
        echo "- {$keyword->title} ({$movieCount} фильмов)\n";
    }
} catch (Exception $e) {
    echo "Ошибка: {$e->getMessage()}\n";
}
echo "\n";

// 8. Комплексный пример с несколькими полями
echo "8. Комплексный пример - фильмы с полной информацией:\n";
$complexFilter = new MovieSearchFilter();
$complexFilter->withYearBetween(2020, 2024)
             ->withRatingBetween(8.0, 10.0)
             ->withIncludedGenres(['драма', 'триллер'])
             ->notNullFields([
                 'poster.url',
                 'backdrop.url', 
                 'description',
                 'name',
                 'rating.kp',
                 'votes.kp',
                 'year',
                 'genres.name'
             ])
             ->sortByKinopoiskRating();

try {
    $response = $movieClient->searchMovies($complexFilter, 1, 3);
    echo "Найдено {$response->total} фильмов с полной информацией\n";
    foreach ($response->docs as $movie) {
        echo "- {$movie->name} ({$movie->year}, Рейтинг: {$movie->rating->kp})\n";
    }
} catch (Exception $e) {
    echo "Ошибка: {$e->getMessage()}\n";
}

echo "\n=== Примеры завершены ===\n";