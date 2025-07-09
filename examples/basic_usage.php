<?php

/**
 * Базовый пример использования PHP клиента для Kinopoisk.dev API
 * 
 * Этот файл демонстрирует основные возможности библиотеки:
 * - Инициализация клиента
 * - Получение фильма по ID
 * - Поиск фильмов с фильтрами
 * - Обработка ошибок
 */

require_once __DIR__ . '/../vendor/autoload.php';

use KinopoiskDev\Http\MovieRequests;
use KinopoiskDev\Http\PersonRequests;
use KinopoiskDev\Filter\MovieSearchFilter;
use KinopoiskDev\Exceptions\KinopoiskDevException;
use KinopoiskDev\Exceptions\KinopoiskResponseException;

// Получение API токена из переменной окружения или использование значения по умолчанию
$apiToken = getenv('KINOPOISK_TOKEN') ?: 'YOUR_API_TOKEN_HERE';

echo "=== Базовый пример использования Kinopoisk.dev PHP клиента ===\n\n";

try {
    // Инициализация клиентов с включенным кэшированием
    $movieClient = new MovieRequests($apiToken, null, true);
    $personClient = new PersonRequests($apiToken, null, true);

    // ==================== 1. ПОЛУЧЕНИЕ ФИЛЬМА ПО ID ====================
    
    echo "🎬 1. Получение фильма по ID\n";
    echo str_repeat("-", 40) . "\n";
    
    $movie = $movieClient->getMovieById(666); // Один дома
    echo "📽️ Фильм: {$movie->name}\n";
    echo "📅 Год: {$movie->year}\n";
    echo "⭐ Рейтинг КП: " . ($movie->rating->kp ?? 'N/A') . "\n";
    echo "🌟 Рейтинг IMDB: " . ($movie->rating->imdb ?? 'N/A') . "\n";
    
    if ($movie->genres) {
        $genres = array_map(fn($g) => $g->name, $movie->genres);
        echo "🎭 Жанры: " . implode(', ', $genres) . "\n";
    }
    
    if ($movie->countries) {
        $countries = array_map(fn($c) => $c->name, $movie->countries);
        echo "🌍 Страны: " . implode(', ', $countries) . "\n";
    }
    
    if ($movie->shortDescription) {
        echo "📝 Описание: {$movie->shortDescription}\n";
    }

    // ==================== 2. СЛУЧАЙНЫЙ ФИЛЬМ ====================
    
    echo "\n🎲 2. Случайный высокорейтинговый фильм\n";
    echo str_repeat("-", 40) . "\n";
    
    $randomFilter = new MovieSearchFilter();
    $randomFilter->withRatingBetween(8.0, 10.0)
                 ->withVotesBetween(10000, null)
                 ->withPoster()
                 ->onlyMovies();
    
    $randomMovie = $movieClient->getRandomMovie($randomFilter);
    echo "📽️ Случайный фильм: {$randomMovie->name} ({$randomMovie->year})\n";
    echo "⭐ Рейтинг: {$randomMovie->rating->kp}/10\n";

    // ==================== 3. ПОИСК ФИЛЬМОВ ====================
    
    echo "\n🔍 3. Поиск фильмов с фильтрами\n";
    echo str_repeat("-", 40) . "\n";
    
    // Поиск лучших российских комедий 2020-2024
    $searchFilter = new MovieSearchFilter();
    $searchFilter->withIncludedCountries('Россия')
                 ->withIncludedGenres('комедия')
                 ->withYearBetween(2020, 2024)
                 ->withRatingBetween(7.0, 10.0)
                 ->withVotesBetween(1000, null)
                 ->onlyMovies()
                 ->sortByKinopoiskRating();

    $searchResults = $movieClient->searchMovies($searchFilter, 1, 5);
    
    echo "Найдено: {$searchResults->total} российских комедий 2020-2024\n";
    echo "Топ-5 лучших:\n";
    
    foreach ($searchResults->docs as $index => $movie) {
        $rank = $index + 1;
        echo "{$rank}. {$movie->name} ({$movie->year}) - {$movie->rating->kp}/10\n";
    }

    // ==================== 4. ПОИСК ПО НАЗВАНИЮ ====================
    
    echo "\n🔎 4. Поиск по названию\n";
    echo str_repeat("-", 40) . "\n";
    
    $nameSearchResults = $movieClient->searchByName('Матрица', 1, 3);
    echo "Фильмы с названием 'Матрица':\n";
    
    foreach ($nameSearchResults->docs as $movie) {
        echo "- {$movie->name} ({$movie->year})";
        if ($movie->rating && $movie->rating->kp) {
            echo " - {$movie->rating->kp}/10";
        }
        echo "\n";
    }

    // ==================== 5. РАБОТА С ПЕРСОНАМИ ====================
    
    echo "\n👤 5. Информация о персоне\n";
    echo str_repeat("-", 40) . "\n";
    
    $person = $personClient->getPersonById(7987); // Джим Керри
    echo "👨‍🎭 Персона: {$person->name}\n";
    
    if ($person->enName) {
        echo "🔤 Англ. имя: {$person->enName}\n";
    }
    
    if ($person->age) {
        echo "🎂 Возраст: {$person->age} лет\n";
    }
    
    if ($person->profession) {
        $professions = array_map(fn($p) => $p->value, $person->profession);
        echo "💼 Профессии: " . implode(', ', $professions) . "\n";
    }
    
    if ($person->countAwards) {
        echo "🏆 Наград: {$person->countAwards}\n";
    }

    // ==================== 6. УДОБНЫЕ МЕТОДЫ ====================
    
    echo "\n⚡ 6. Удобные методы поиска\n";
    echo str_repeat("-", 40) . "\n";
    
    // Новинки 2024 года
    echo "🆕 Новинки 2024 года (топ-3):\n";
    $latest = $movieClient->getLatestMovies(2024, 1, 3);
    foreach ($latest->docs as $movie) {
        echo "- {$movie->name} ({$movie->year})\n";
    }
    
    echo "\n😂 Лучшие комедии (топ-3):\n";
    $comedies = $movieClient->getMoviesByGenre('комедия', 1, 3);
    foreach ($comedies->docs as $movie) {
        echo "- {$movie->name} ({$movie->year}) - {$movie->rating->kp}/10\n";
    }
    
    echo "\n🇺🇸 Американские фильмы (топ-3):\n";
    $usMovies = $movieClient->getMoviesByCountry('США', 1, 3);
    foreach ($usMovies->docs as $movie) {
        echo "- {$movie->name} ({$movie->year}) - {$movie->rating->kp}/10\n";
    }

    // ==================== 7. ДОПОЛНИТЕЛЬНАЯ ИНФОРМАЦИЯ ====================
    
    echo "\n📊 7. Дополнительная информация\n";
    echo str_repeat("-", 40) . "\n";
    
    // Доступные жанры
    echo "🎭 Первые 10 доступных жанров:\n";
    $genres = $movieClient->getPossibleValuesByField('genres.name');
    $genreNames = array_slice(array_column($genres, 'name'), 0, 10);
    echo implode(', ', $genreNames) . "\n";

} catch (KinopoiskResponseException $e) {
    echo "\n❌ Ошибка API:\n";
    
    switch ($e->getCode()) {
        case 401:
            echo "🚫 Неверный или отсутствующий токен API.\n";
            echo "💡 Получите токен на https://kinopoisk.dev или у бота @kinopoiskdev_bot\n";
            break;
            
        case 403:
            echo "⏰ Превышен лимит запросов. Попробуйте позже.\n";
            echo "💡 Рассмотрите возможность кэширования или увеличения тарифа.\n";
            break;
            
        case 404:
            echo "🔍 Запрашиваемый ресурс не найден.\n";
            break;
            
        default:
            echo "⚠️ Ошибка API: {$e->getCode()} - {$e->getMessage()}\n";
    }
    
} catch (KinopoiskDevException $e) {
    echo "\n❌ Ошибка клиента: {$e->getMessage()}\n";
    echo "💡 Проверьте правильность параметров запроса.\n";
    
} catch (\JsonException $e) {
    echo "\n❌ Ошибка парсинга JSON: {$e->getMessage()}\n";
    echo "💡 Возможно, проблема с сетевым подключением или форматом ответа.\n";
    
} catch (\Exception $e) {
    echo "\n❌ Неожиданная ошибка: {$e->getMessage()}\n";
    echo "💡 Обратитесь к документации или в службу поддержки.\n";
}

echo "\n\n✅ Базовый пример завершен!\n";
echo "📖 Для изучения расширенных возможностей см. comprehensive_usage.php\n";
echo "📚 Полная документация: README.md\n";
echo "🌐 Сайт API: https://kinopoisk.dev\n";
echo "💬 Поддержка: https://t.me/kinopoiskdev_chat\n";