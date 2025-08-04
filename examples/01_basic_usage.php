<?php

/**
 * Базовый пример использования KinopoiskDev
 * 
 * Этот пример демонстрирует основные возможности библиотеки:
 * - Инициализация клиента
 * - Получение фильма по ID
 * - Поиск фильмов
 * - Работа с персонами
 * - Обработка ошибок
 */

require_once __DIR__ . '/../vendor/autoload.php';

use KinopoiskDev\Http\MovieRequests;
use KinopoiskDev\Http\PersonRequests;
use KinopoiskDev\Filter\MovieSearchFilter;
use KinopoiskDev\Filter\PersonSearchFilter;
use KinopoiskDev\Exceptions\KinopoiskDevException;
use KinopoiskDev\Exceptions\KinopoiskResponseException;
use KinopoiskDev\Exceptions\ValidationException;

// Проверяем наличие токена
if (!getenv('KINOPOISK_TOKEN')) {
    echo "❌ Ошибка: Не найден токен API. Установите переменную окружения KINOPOISK_TOKEN\n";
    echo "Пример: export KINOPOISK_TOKEN='ABC1DEF-2GH3IJK-4LM5NOP-6QR7STU'\n";
    exit(1);
}

echo "🚀 KinopoiskDev - Базовый пример использования\n";
echo "===============================================\n\n";

try {
    // Инициализация клиента с кэшированием
    echo "📡 Инициализация клиента...\n";
    $movieRequests = new \KinopoiskDev\Http\MovieRequests(useCache: true);
    $personRequests = new \KinopoiskDev\Http\PersonRequests(useCache: true);
    echo "✅ Клиент успешно инициализирован\n\n";

    // 1. Получение фильма по ID (Матрица)
    echo "🎬 1. Получение фильма по ID (Матрица)\n";
    echo "----------------------------------------\n";
    
    $movie = $movieRequests->getMovieById(301);
    
    echo "📽️  Название: {$movie->name}\n";
    echo "📅  Год: {$movie->year}\n";
    echo "⭐  Рейтинг Кинопоиск: " . ($movie->rating->kp ?? 'N/A') . "\n";
    echo "⭐  Рейтинг IMDB: " . ($movie->rating->imdb ?? 'N/A') . "\n";
    echo "📝  Описание: " . substr($movie->description ?? 'Нет описания', 0, 100) . "...\n";
    
    // Жанры
    if (!empty($movie->genres)) {
        echo "🎭  Жанры: " . implode(', ', array_map(fn($g) => $g->name, $movie->genres)) . "\n";
    }
    
    // Страны
    if (!empty($movie->countries)) {
        echo "🌍  Страны: " . implode(', ', array_map(fn($c) => $c->name, $movie->countries)) . "\n";
    }
    
    // Актеры (первые 5)
    if (!empty($movie->persons)) {
        $actors = array_filter($movie->persons, fn($p) => $p->profession === 'actor');
        $actorNames = array_slice(array_map(fn($a) => $a->name, $actors), 0, 5);
        echo "🎭  Актеры: " . implode(', ', $actorNames) . "\n";
    }
    
    echo "\n";

    // 2. Поиск фильмов
    echo "🔍 2. Поиск фильмов 2023 года с рейтингом выше 7.0\n";
    echo "---------------------------------------------------\n";
    
    $filter = new MovieSearchFilter();
    $filter->year(2023)
           ->withMinRating(7.0, 'kp')
           ->withMinVotes(1000, 'kp'); // Минимум 1000 голосов
    
    $results = $movieRequests->searchMovies($filter, 1, 5);
    
    echo "📊 Найдено фильмов: {$results->total}\n";
    echo "📋 Показываем первые 5:\n\n";
    
    foreach ($results->docs as $index => $movie) {
        $rating = $movie->rating->kp ?? 'N/A';
        echo sprintf("%d. %s (%d) - ⭐ %s\n", 
            $index + 1, 
            $movie->name, 
            $movie->year, 
            $rating
        );
    }
    
    echo "\n";

    // 3. Поиск фильмов по жанру
    echo "🎭 3. Поиск комедий 2023 года\n";
    echo "-----------------------------\n";
    
    $comedyFilter = new MovieSearchFilter();
    $comedyFilter->year(2023)
                 ->genres('комедия')
                 ->withMinRating(6.0, 'kp');
    
    $comedies = $movieRequests->searchMovies($comedyFilter, 1, 3);
    
    echo "📊 Найдено комедий: {$comedies->total}\n";
    echo "📋 Топ-3 комедии:\n\n";
    
    foreach ($comedies->docs as $index => $movie) {
        $rating = $movie->rating->kp ?? 'N/A';
        echo sprintf("%d. %s - ⭐ %s\n", 
            $index + 1, 
            $movie->name, 
            $rating
        );
    }
    
    echo "\n";

    // 4. Поиск персон
    echo "👥 4. Поиск актеров\n";
    echo "-------------------\n";
    
    $personFilter = new PersonSearchFilter();
    $personFilter->onlyActors()
                 ->age(30, 'gte')
                 ->age(50, 'lte');
    
    $actors = $personRequests->searchPersons($personFilter, 1, 3);
    
    echo "📊 Найдено актеров: {$actors->total}\n";
    echo "📋 Примеры актеров 30-50 лет:\n\n";
    
    foreach ($actors->docs as $index => $person) {
        echo sprintf("%d. %s (возраст: %d)\n", 
            $index + 1, 
            $person->name, 
            $person->age ?? 'N/A'
        );
    }
    
    echo "\n";

    // 5. Получение случайного фильма
    echo "🎲 5. Случайный фильм с высоким рейтингом\n";
    echo "-----------------------------------------\n";
    
    $randomFilter = new MovieSearchFilter();
    $randomFilter->withMinRating(8.0, 'kp')
                 ->withMinVotes(5000, 'kp');
    
    $randomMovie = $movieRequests->getRandomMovie($randomFilter);
    
    echo "🎬 Случайный фильм: {$randomMovie->name} ({$randomMovie->year})\n";
    echo "⭐ Рейтинг: " . ($randomMovie->rating->kp ?? 'N/A') . "\n";
    
    if (!empty($randomMovie->genres)) {
        echo "🎭 Жанры: " . implode(', ', array_map(fn($g) => $g->name, $randomMovie->genres)) . "\n";
    }
    
    echo "\n";

    // 6. Работа с кэшем
    echo "💾 6. Тестирование кэширования\n";
    echo "-------------------------------\n";
    
    $movieId = 301; // Матрица
    
    // Первый запрос
    $startTime = microtime(true);
    $movie1 = $movieRequests->getMovieById($movieId);
    $firstRequestTime = microtime(true) - $startTime;
    
    // Второй запрос (должен быть из кэша)
    $startTime = microtime(true);
    $movie2 = $movieRequests->getMovieById($movieId);
    $secondRequestTime = microtime(true) - $startTime;
    
    echo "⏱️  Первый запрос: " . round($firstRequestTime * 1000, 2) . " мс\n";
    echo "⏱️  Второй запрос: " . round($secondRequestTime * 1000, 2) . " мс\n";
    echo "🚀 Ускорение: " . round($firstRequestTime / $secondRequestTime, 1) . "x\n";
    
    echo "\n";

    echo "✅ Все примеры выполнены успешно!\n";

} catch (ValidationException $e) {
    echo "❌ Ошибка валидации: " . $e->getMessage() . "\n";
    if ($e->hasErrors()) {
        foreach ($e->getErrors() as $field => $error) {
            echo "   - {$field}: {$error}\n";
        }
    }
} catch (KinopoiskResponseException $e) {
    echo "❌ Ошибка API (код {$e->getCode()}): " . $e->getMessage() . "\n";
    switch ($e->getCode()) {
        case 401:
            echo "💡 Проверьте правильность API токена\n";
            break;
        case 403:
            echo "💡 Проверьте права доступа к API\n";
            break;
        case 404:
            echo "💡 Запрашиваемый ресурс не найден\n";
            break;
        case 429:
            echo "💡 Превышен лимит запросов, попробуйте позже\n";
            break;
    }
} catch (KinopoiskDevException $e) {
    echo "❌ Ошибка библиотеки: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "❌ Неожиданная ошибка: " . $e->getMessage() . "\n";
}

echo "\n🎉 Пример завершен!\n"; 