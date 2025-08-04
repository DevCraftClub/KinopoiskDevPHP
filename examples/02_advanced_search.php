<?php

/**
 * Продвинутый поиск фильмов с KinopoiskDev
 * 
 * Этот пример демонстрирует сложные сценарии поиска:
 * - Множественные фильтры
 * - Сортировка результатов
 * - Поиск по актерам и режиссерам
 * - Работа с диапазонами
 * - Специальные фильтры
 */

require_once __DIR__ . '/../vendor/autoload.php';

use KinopoiskDev\Http\MovieRequests;
use KinopoiskDev\Filter\MovieSearchFilter;
use KinopoiskDev\Filter\SortCriteria;
use KinopoiskDev\Enums\SortField;
use KinopoiskDev\Enums\SortDirection;
use KinopoiskDev\Enums\MovieType;
use KinopoiskDev\Enums\MovieStatus;
use KinopoiskDev\Exceptions\KinopoiskDevException;

// Проверяем наличие токена
if (!getenv('KINOPOISK_TOKEN')) {
    echo "❌ Ошибка: Не найден токен API. Установите переменную окружения KINOPOISK_TOKEN\n";
    exit(1);
}

echo "🔍 KinopoiskDev - Продвинутый поиск фильмов\n";
echo "============================================\n\n";

try {
    $movieRequests = new \KinopoiskDev\Http\MovieRequests(useCache: true);
    
    // 1. Поиск фильмов с множественными критериями
    echo "🎯 1. Поиск фильмов с множественными критериями\n";
    echo "------------------------------------------------\n";
    
    $filter = new MovieSearchFilter();
    $filter->withYearBetween(2020, 2023)
           ->withMinRating(7.5, 'kp')
           ->withMinVotes(1000, 'kp')
           ->withAllGenres(['драма', 'криминал'])
           ->withIncludedCountries(['США', 'Великобритания'])
           ->withExcludedGenres(['ужасы'])
           ->type(MovieType::MOVIE)
           ->status(MovieStatus::COMPLETED);
    
    $results = $movieRequests->searchMovies($filter, 1, 5);
    
    echo "📊 Найдено фильмов: {$results->total}\n";
    echo "🔍 Критерии поиска:\n";
    echo "   - Годы: 2020-2023\n";
    echo "   - Рейтинг: от 7.5\n";
    echo "   - Жанры: драма И криминал\n";
    echo "   - Страны: США или Великобритания\n";
    echo "   - Исключены: ужасы\n";
    echo "   - Тип: только фильмы\n";
    echo "   - Статус: завершенные\n\n";
    
    foreach ($results->docs as $index => $movie) {
        $rating = $movie->rating->kp ?? 'N/A';
        $genres = implode(', ', array_map(fn($g) => $g->name, $movie->genres));
        echo sprintf("%d. %s (%d) - ⭐ %s\n", 
            $index + 1, 
            $movie->name, 
            $movie->year, 
            $rating
        );
        echo "   🎭 Жанры: {$genres}\n";
    }
    
    echo "\n";

    // 2. Поиск фильмов с участием конкретного актера
    echo "🎭 2. Поиск фильмов с участием Леонардо Ди Каприо\n";
    echo "--------------------------------------------------\n";
    
    $actorFilter = new MovieSearchFilter();
    $actorFilter->withActor('Леонардо Ди Каприо')
                ->withMinRating(7.0, 'kp')
                ->withMinVotes(500, 'kp');
    
    $actorMovies = $movieRequests->searchMovies($actorFilter, 1, 5);
    
    echo "📊 Найдено фильмов: {$actorMovies->total}\n\n";
    
    foreach ($actorMovies->docs as $index => $movie) {
        $rating = $movie->rating->kp ?? 'N/A';
        echo sprintf("%d. %s (%d) - ⭐ %s\n", 
            $index + 1, 
            $movie->name, 
            $movie->year, 
            $rating
        );
    }
    
    echo "\n";

    // 3. Поиск фильмов определенного режиссера
    echo "🎬 3. Поиск фильмов Кристофера Нолана\n";
    echo "--------------------------------------\n";
    
    $directorFilter = new MovieSearchFilter();
    $directorFilter->withDirector('Кристофер Нолан')
                   ->withMinRating(7.0, 'kp');
    
    $directorMovies = $movieRequests->searchMovies($directorFilter, 1, 5);
    
    echo "📊 Найдено фильмов: {$directorMovies->total}\n\n";
    
    foreach ($directorMovies->docs as $index => $movie) {
        $rating = $movie->rating->kp ?? 'N/A';
        echo sprintf("%d. %s (%d) - ⭐ %s\n", 
            $index + 1, 
            $movie->name, 
            $movie->year, 
            $rating
        );
    }
    
    echo "\n";

    // 4. Поиск с сортировкой
    echo "📊 4. Топ фильмов 2023 года по рейтингу\n";
    echo "----------------------------------------\n";
    
    $topFilter = new MovieSearchFilter();
    $topFilter->year(2023)
              ->withMinVotes(1000, 'kp')
              ->type(MovieType::MOVIE);
    
    // Создаем критерии сортировки
    $sortCriteria = new SortCriteria();
    $sortCriteria->addSort(SortField::RATING_KP, SortDirection::DESC);
    $topFilter->setSortCriteria($sortCriteria);
    
    $topMovies = $movieRequests->searchMovies($topFilter, 1, 10);
    
    echo "📊 Топ-10 фильмов 2023 года:\n\n";
    
    foreach ($topMovies->docs as $index => $movie) {
        $rating = $movie->rating->kp ?? 'N/A';
        $votes = $movie->votes->kp ?? 0;
        echo sprintf("%d. %s - ⭐ %s (голосов: %s)\n", 
            $index + 1, 
            $movie->name, 
            $rating,
            number_format($votes)
        );
    }
    
    echo "\n";

    // 5. Поиск фильмов из топ-250
    echo "🏆 5. Фильмы из топ-250 с высоким рейтингом\n";
    echo "--------------------------------------------\n";
    
    $top250Filter = new MovieSearchFilter();
    $top250Filter->inTop250()
                 ->withMinRating(8.5, 'kp')
                 ->withYearBetween(2000, 2010);
    
    $top250Movies = $movieRequests->searchMovies($top250Filter, 1, 5);
    
    echo "📊 Найдено фильмов из топ-250 (2000-2010, рейтинг 8.5+): {$top250Movies->total}\n\n";
    
    foreach ($top250Movies->docs as $index => $movie) {
        $rating = $movie->rating->kp ?? 'N/A';
        echo sprintf("%d. %s (%d) - ⭐ %s\n", 
            $index + 1, 
            $movie->name, 
            $movie->year, 
            $rating
        );
    }
    
    echo "\n";

    // 6. Поиск по названию с регулярным выражением
    echo "🔍 6. Поиск фильмов по названию (содержит 'Матрица')\n";
    echo "-----------------------------------------------------\n";
    
    $searchFilter = new MovieSearchFilter();
    $searchFilter->searchByName('Матрица')
                 ->withMinRating(6.0, 'kp');
    
    $searchResults = $movieRequests->searchMovies($searchFilter, 1, 5);
    
    echo "📊 Найдено фильмов: {$searchResults->total}\n\n";
    
    foreach ($searchResults->docs as $index => $movie) {
        $rating = $movie->rating->kp ?? 'N/A';
        echo sprintf("%d. %s (%d) - ⭐ %s\n", 
            $index + 1, 
            $movie->name, 
            $movie->year, 
            $rating
        );
    }
    
    echo "\n";

    // 7. Поиск фильмов с определенным бюджетом (если доступно)
    echo "💰 7. Поиск фильмов с высоким рейтингом и голосами\n";
    echo "--------------------------------------------------\n";
    
    $budgetFilter = new MovieSearchFilter();
    $budgetFilter->withMinRating(8.0, 'kp')
                 ->withMinVotes(10000, 'kp')
                 ->withYearBetween(2015, 2023)
                 ->type(MovieType::MOVIE);
    
    $budgetResults = $movieRequests->searchMovies($budgetFilter, 1, 5);
    
    echo "📊 Найдено фильмов (рейтинг 8.0+, 10k+ голосов, 2015-2023): {$budgetResults->total}\n\n";
    
    foreach ($budgetResults->docs as $index => $movie) {
        $rating = $movie->rating->kp ?? 'N/A';
        $votes = $movie->votes->kp ?? 0;
        echo sprintf("%d. %s (%d) - ⭐ %s (голосов: %s)\n", 
            $index + 1, 
            $movie->name, 
            $movie->year, 
            $rating,
            number_format($votes)
        );
    }
    
    echo "\n";

    // 8. Комплексный поиск с пагинацией
    echo "📄 8. Комплексный поиск с пагинацией\n";
    echo "-------------------------------------\n";
    
    $complexFilter = new MovieSearchFilter();
    $complexFilter->withMinRating(7.0, 'kp')
                  ->withMinVotes(500, 'kp')
                  ->withIncludedGenres(['фантастика', 'боевик'])
                  ->type(MovieType::MOVIE);
    
    // Сортировка по году (новые сначала)
    $sortCriteria = new SortCriteria();
    $sortCriteria->addSort(SortField::YEAR, SortDirection::DESC);
    $complexFilter->setSortCriteria($sortCriteria);
    
    $page = 1;
    $limit = 3;
    $totalPages = 2;
    
    for ($page = 1; $page <= $totalPages; $page++) {
        echo "📄 Страница {$page}:\n";
        
        $complexResults = $movieRequests->searchMovies($complexFilter, $page, $limit);
        
        foreach ($complexResults->docs as $index => $movie) {
            $rating = $movie->rating->kp ?? 'N/A';
            $genres = implode(', ', array_map(fn($g) => $g->name, $movie->genres));
            echo sprintf("   %d. %s (%d) - ⭐ %s\n", 
                $index + 1, 
                $movie->name, 
                $movie->year, 
                $rating
            );
            echo "      🎭 Жанры: {$genres}\n";
        }
        
        echo "\n";
    }
    
    echo "✅ Все примеры продвинутого поиска выполнены успешно!\n";

} catch (KinopoiskDevException $e) {
    echo "❌ Ошибка: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "❌ Неожиданная ошибка: " . $e->getMessage() . "\n";
}

echo "\n🎉 Продвинутый поиск завершен!\n"; 