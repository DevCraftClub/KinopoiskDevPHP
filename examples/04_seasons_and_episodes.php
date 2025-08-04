<?php

/**
 * Работа с сезонами и эпизодами сериалов в KinopoiskDev
 * 
 * Этот пример демонстрирует:
 * - Поиск сериалов
 * - Получение информации о сезонах
 * - Работа с эпизодами
 * - Фильтрация по статусу и типу
 * - Поиск сериалов по жанрам
 */

require_once __DIR__ . '/../vendor/autoload.php';

use KinopoiskDev\Http\MovieRequests;
use KinopoiskDev\Http\SeasonRequests;
use KinopoiskDev\Filter\MovieSearchFilter;
use KinopoiskDev\Filter\SeasonSearchFilter;
use KinopoiskDev\Enums\MovieType;
use KinopoiskDev\Enums\MovieStatus;
use KinopoiskDev\Exceptions\KinopoiskDevException;

// Проверяем наличие токена
if (!getenv('KINOPOISK_TOKEN')) {
    echo "❌ Ошибка: Не найден токен API. Установите переменную окружения KINOPOISK_TOKEN\n";
    exit(1);
}

echo "📺 KinopoiskDev - Работа с сезонами и эпизодами\n";
echo "=================================================\n\n";

try {
    $movieRequests = new \KinopoiskDev\Http\MovieRequests(useCache: true);
    $seasonRequests = new \KinopoiskDev\Http\SeasonRequests(useCache: true);
    
    // 1. Поиск популярных сериалов
    echo "📺 1. Популярные сериалы 2023 года\n";
    echo "----------------------------------\n";
    
    $seriesFilter = new MovieSearchFilter();
    $seriesFilter->type(MovieType::TV_SERIES)
                 ->year(2023)
                 ->withMinRating(7.0, 'kp')
                 ->withMinVotes(1000, 'kp')
                 ->status(MovieStatus::COMPLETED);
    
    $series = $movieRequests->searchMovies($seriesFilter, 1, 5);
    
    echo "📊 Найдено сериалов: {$series->total}\n\n";
    
    foreach ($series->docs as $index => $series) {
        $rating = $series->rating->kp ?? 'N/A';
        $seasons = $series->seasonsInfo->count ?? 0;
        echo sprintf("%d. %s (%d) - ⭐ %s (%d сезонов)\n", 
            $index + 1, 
            $series->name, 
            $series->year, 
            $rating,
            $seasons
        );
    }
    
    echo "\n";

    // 2. Получение информации о сезонах конкретного сериала
    echo "🎬 2. Сезоны сериала 'Во все тяжкие'\n";
    echo "-----------------------------------\n";
    
    $breakingBadId = 3498; // ID сериала "Во все тяжкие"
    $seasons = $seasonRequests->getSeasonsByMovieId($breakingBadId);
    
    echo "📺 Сериал: Во все тяжкие\n";
    echo "📊 Всего сезонов: " . count($seasons->docs) . "\n\n";
    
    foreach ($seasons->docs as $index => $season) {
        echo sprintf("Сезон %d (%d эпизодов):\n", 
            $season->number, 
            count($season->episodes)
        );
        
        // Показываем первые 3 эпизода
        foreach (array_slice($season->episodes, 0, 3) as $episode) {
            $rating = $episode->rating->kp ?? 'N/A';
            echo sprintf("   Эпизод %d: %s - ⭐ %s\n", 
                $episode->number, 
                $episode->name, 
                $rating
            );
        }
        echo "\n";
    }
    
    echo "\n";

    // 3. Поиск сериалов по жанрам
    echo "🎭 3. Драматические сериалы с высоким рейтингом\n";
    echo "------------------------------------------------\n";
    
    $dramaFilter = new MovieSearchFilter();
    $dramaFilter->type(MovieType::TV_SERIES)
                ->genres('драма')
                ->withMinRating(8.0, 'kp')
                ->withMinVotes(5000, 'kp')
                ->withYearBetween(2015, 2023);
    
    $dramaSeries = $movieRequests->searchMovies($dramaFilter, 1, 5);
    
    echo "📊 Найдено драматических сериалов: {$dramaSeries->total}\n\n";
    
    foreach ($dramaSeries->docs as $index => $series) {
        $rating = $series->rating->kp ?? 'N/A';
        $votes = $series->votes->kp ?? 0;
        echo sprintf("%d. %s (%d) - ⭐ %s (голосов: %s)\n", 
            $index + 1, 
            $series->name, 
            $series->year, 
            $rating,
            number_format($votes)
        );
    }
    
    echo "\n";

    // 4. Поиск сериалов по количеству сезонов
    echo "📊 4. Сериалы с большим количеством сезонов\n";
    echo "-------------------------------------------\n";
    
    $longSeriesFilter = new MovieSearchFilter();
    $longSeriesFilter->type(MovieType::TV_SERIES)
                     ->withMinRating(7.5, 'kp')
                     ->status(MovieStatus::COMPLETED);
    
    $longSeries = $movieRequests->searchMovies($longSeriesFilter, 1, 5);
    
    echo "📊 Найдено сериалов: {$longSeries->total}\n\n";
    
    foreach ($longSeries->docs as $index => $series) {
        $rating = $series->rating->kp ?? 'N/A';
        $seasons = $series->seasonsInfo->count ?? 0;
        echo sprintf("%d. %s (%d) - ⭐ %s (%d сезонов)\n", 
            $index + 1, 
            $series->name, 
            $series->year, 
            $rating,
            $seasons
        );
    }
    
    echo "\n";

    // 5. Поиск сериалов по статусу
    echo "🔄 5. Сериалы в производстве\n";
    echo "-----------------------------\n";
    
    $ongoingFilter = new MovieSearchFilter();
    $ongoingFilter->type(MovieType::TV_SERIES)
                  ->status(MovieStatus::ONGOING)
                  ->withMinRating(7.0, 'kp');
    
    $ongoingSeries = $movieRequests->searchMovies($ongoingFilter, 1, 5);
    
    echo "📊 Найдено сериалов в производстве: {$ongoingSeries->total}\n\n";
    
    foreach ($ongoingSeries->docs as $index => $series) {
        $rating = $series->rating->kp ?? 'N/A';
        echo sprintf("%d. %s (%d) - ⭐ %s\n", 
            $index + 1, 
            $series->name, 
            $series->year, 
            $rating
        );
    }
    
    echo "\n";

    // 6. Детальная информация о сезоне
    echo "📋 6. Детальная информация о сезоне\n";
    echo "-----------------------------------\n";
    
    $seasonId = 1; // ID сезона (можно изменить)
    $season = $seasonRequests->getSeasonById($seasonId);
    
    echo "📺 Сериал: {$season->movieId}\n";
    echo "📊 Сезон: {$season->number}\n";
    echo "📝 Название: {$season->name}\n";
    echo "📅 Год: {$season->year}\n";
    echo "🎬 Эпизодов: " . count($season->episodes) . "\n\n";
    
    // Показываем первые 5 эпизодов
    echo "📺 Эпизоды:\n";
    foreach (array_slice($season->episodes, 0, 5) as $episode) {
        $rating = $episode->rating->kp ?? 'N/A';
        echo sprintf("   %d. %s - ⭐ %s\n", 
            $episode->number, 
            $episode->name, 
            $rating
        );
        
        if (!empty($episode->description)) {
            echo "      📝 " . substr($episode->description, 0, 100) . "...\n";
        }
    }
    
    echo "\n";

    // 7. Поиск сериалов по стране
    echo "🌍 7. Американские сериалы\n";
    echo "--------------------------\n";
    
    $usFilter = new MovieSearchFilter();
    $usFilter->type(MovieType::TV_SERIES)
             ->withIncludedCountries(['США'])
             ->withMinRating(7.5, 'kp')
             ->withYearBetween(2020, 2023);
    
    $usSeries = $movieRequests->searchMovies($usFilter, 1, 5);
    
    echo "📊 Найдено американских сериалов: {$usSeries->total}\n\n";
    
    foreach ($usSeries->docs as $index => $series) {
        $rating = $series->rating->kp ?? 'N/A';
        $genres = implode(', ', array_map(fn($g) => $g->name, $series->genres));
        echo sprintf("%d. %s (%d) - ⭐ %s\n", 
            $index + 1, 
            $series->name, 
            $series->year, 
            $rating
        );
        echo "   🎭 Жанры: {$genres}\n";
    }
    
    echo "\n";

    // 8. Комплексный поиск сериалов
    echo "🔍 8. Комплексный поиск сериалов\n";
    echo "--------------------------------\n";
    
    $complexFilter = new MovieSearchFilter();
    $complexFilter->type(MovieType::TV_SERIES)
                  ->withIncludedGenres(['комедия', 'драма'])
                  ->withExcludedGenres(['ужасы'])
                  ->withMinRating(7.0, 'kp')
                  ->withMinVotes(1000, 'kp')
                  ->withYearBetween(2018, 2023);
    
    $complexResults = $movieRequests->searchMovies($complexFilter, 1, 5);
    
    echo "📊 Найдено сериалов: {$complexResults->total}\n";
    echo "🔍 Критерии: комедия/драма, рейтинг 7.0+, 2018-2023\n\n";
    
    foreach ($complexResults->docs as $index => $series) {
        $rating = $series->rating->kp ?? 'N/A';
        $genres = implode(', ', array_map(fn($g) => $g->name, $series->genres));
        echo sprintf("%d. %s (%d) - ⭐ %s\n", 
            $index + 1, 
            $series->name, 
            $series->year, 
            $rating
        );
        echo "   🎭 Жанры: {$genres}\n";
    }
    
    echo "\n";

    echo "✅ Все примеры работы с сезонами и эпизодами выполнены успешно!\n";

} catch (KinopoiskDevException $e) {
    echo "❌ Ошибка: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "❌ Неожиданная ошибка: " . $e->getMessage() . "\n";
}

echo "\n🎉 Работа с сезонами завершена!\n"; 