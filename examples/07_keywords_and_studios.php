<?php

/**
 * Работа с ключевыми словами и студиями в KinopoiskDev
 * 
 * Этот пример демонстрирует:
 * - Поиск фильмов по ключевым словам
 * - Работа со студиями
 * - Поиск студий
 * - Фильтрация по студиям
 * - Анализ ключевых слов
 */

require_once __DIR__ . '/../vendor/autoload.php';

use KinopoiskDev\Http\KeywordRequests;
use KinopoiskDev\Http\StudioRequests;
use KinopoiskDev\Http\MovieRequests;
use KinopoiskDev\Filter\KeywordSearchFilter;
use KinopoiskDev\Filter\StudioSearchFilter;
use KinopoiskDev\Filter\MovieSearchFilter;
use KinopoiskDev\Enums\StudioType;
use KinopoiskDev\Exceptions\KinopoiskDevException;

// Проверяем наличие токена
if (!getenv('KINOPOISK_TOKEN')) {
    echo "❌ Ошибка: Не найден токен API. Установите переменную окружения KINOPOISK_TOKEN\n";
    exit(1);
}

echo "🔍 KinopoiskDev - Работа с ключевыми словами и студиями\n";
echo "========================================================\n\n";

try {
    $keywordRequests = new \KinopoiskDev\Http\KeywordRequests(useCache: true);
    $studioRequests = new \KinopoiskDev\Http\StudioRequests(useCache: true);
    $movieRequests = new \KinopoiskDev\Http\MovieRequests(useCache: true);
    
    // 1. Поиск фильмов по ключевому слову
    echo "🔑 1. Поиск фильмов по ключевому слову 'робот'\n";
    echo "-----------------------------------------------\n";
    
    $keywordFilter = new KeywordSearchFilter();
    $keywordFilter->searchByName('робот');
    
    $keywords = $keywordRequests->searchKeywords($keywordFilter, 1, 5);
    
    echo "📊 Найдено ключевых слов: {$keywords->total}\n\n";
    
    foreach ($keywords->docs as $index => $keyword) {
        echo sprintf("%d. %s\n", 
            $index + 1, 
            $keyword->name
        );
        echo "   📝 Описание: " . ($keyword->description ?? 'Нет описания') . "\n\n";
    }
    
    echo "\n";

    // 2. Поиск фильмов по ключевому слову
    echo "🎬 2. Фильмы с ключевым словом 'космос'\n";
    echo "--------------------------------------\n";
    
    $spaceKeywordFilter = new MovieSearchFilter();
    $spaceKeywordFilter->withKeyword('космос')
                       ->withMinRating(7.0, 'kp')
                       ->withMinVotes(1000, 'kp');
    
    $spaceMovies = $movieRequests->searchMovies($spaceKeywordFilter, 1, 5);
    
    echo "📊 Найдено фильмов: {$spaceMovies->total}\n\n";
    
    foreach ($spaceMovies->docs as $index => $movie) {
        $rating = $movie->rating->kp ?? 'N/A';
        echo sprintf("%d. %s (%d) - ⭐ %s\n", 
            $index + 1, 
            $movie->name, 
            $movie->year, 
            $rating
        );
    }
    
    echo "\n";

    // 3. Поиск студий
    echo "🏢 3. Поиск студий\n";
    echo "------------------\n";
    
    $studioFilter = new StudioSearchFilter();
    $studioFilter->searchByName('Disney')
                 ->type(StudioType::PRODUCER);
    
    $studios = $studioRequests->searchStudios($studioFilter, 1, 5);
    
    echo "📊 Найдено студий: {$studios->total}\n\n";
    
    foreach ($studios->docs as $index => $studio) {
        echo sprintf("%d. %s\n", 
            $index + 1, 
            $studio->name
        );
        echo "   🏢 Тип: {$studio->type}\n";
        echo "   📝 Описание: " . ($studio->description ?? 'Нет описания') . "\n\n";
    }
    
    echo "\n";

    // 4. Фильмы конкретной студии
    echo "🎬 4. Фильмы студии Warner Bros.\n";
    echo "--------------------------------\n";
    
    $warnerFilter = new MovieSearchFilter();
    $warnerFilter->withStudio('Warner Bros.')
                 ->withMinRating(7.0, 'kp')
                 ->withMinVotes(1000, 'kp')
                 ->withYearBetween(2010, 2023);
    
    $warnerMovies = $movieRequests->searchMovies($warnerFilter, 1, 5);
    
    echo "📊 Найдено фильмов Warner Bros.: {$warnerMovies->total}\n\n";
    
    foreach ($warnerMovies->docs as $index => $movie) {
        $rating = $movie->rating->kp ?? 'N/A';
        echo sprintf("%d. %s (%d) - ⭐ %s\n", 
            $index + 1, 
            $movie->name, 
            $movie->year, 
            $rating
        );
    }
    
    echo "\n";

    // 5. Поиск студий по типу
    echo "🏢 5. Студии по типам\n";
    echo "---------------------\n";
    
    $studioTypes = [
        StudioType::PRODUCER => 'Продюсерские',
        StudioType::DISTRIBUTOR => 'Дистрибьюторские',
        StudioType::SPECIAL_EFFECTS => 'Спецэффекты'
    ];
    
    foreach ($studioTypes as $type => $typeName) {
        echo "🏢 {$typeName} студии:\n";
        
        $typeFilter = new StudioSearchFilter();
        $typeFilter->type($type)
                   ->limit(3);
        
        $typeStudios = $studioRequests->searchStudios($typeFilter, 1, 3);
        
        echo "   📊 Найдено: {$typeStudios->total}\n";
        
        foreach (array_slice($typeStudios->docs, 0, 2) as $studio) {
            echo "   • {$studio->name}\n";
        }
        echo "\n";
    }
    
    echo "\n";

    // 6. Поиск фильмов по множественным ключевым словам
    echo "🔑 6. Фильмы с несколькими ключевыми словами\n";
    echo "--------------------------------------------\n";
    
    $multiKeywordFilter = new MovieSearchFilter();
    $multiKeywordFilter->withKeyword('фантастика')
                       ->withKeyword('приключения')
                       ->withMinRating(7.5, 'kp')
                       ->withMinVotes(5000, 'kp');
    
    $multiKeywordMovies = $movieRequests->searchMovies($multiKeywordFilter, 1, 5);
    
    echo "📊 Найдено фильмов (фантастика + приключения): {$multiKeywordMovies->total}\n\n";
    
    foreach ($multiKeywordMovies->docs as $index => $movie) {
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

    // 7. Анализ ключевых слов фильма
    echo "🔍 7. Ключевые слова фильма\n";
    echo "----------------------------\n";
    
    $movie = $movieRequests->getMovieById(301); // Матрица
    
    echo "🎬 Фильм: {$movie->name}\n";
    
    if (!empty($movie->keywords)) {
        echo "🔑 Ключевые слова:\n";
        foreach (array_slice($movie->keywords, 0, 10) as $keyword) {
            echo "   • {$keyword->name}\n";
        }
    }
    
    if (!empty($movie->studios)) {
        echo "\n🏢 Студии:\n";
        foreach (array_slice($movie->studios, 0, 5) as $studio) {
            echo "   • {$studio->name} ({$studio->type})\n";
        }
    }
    
    echo "\n";

    // 8. Поиск популярных ключевых слов
    echo "📊 8. Популярные ключевые слова\n";
    echo "--------------------------------\n";
    
    $popularKeywordFilter = new KeywordSearchFilter();
    $popularKeywordFilter->limit(10);
    
    $popularKeywords = $keywordRequests->searchKeywords($popularKeywordFilter, 1, 10);
    
    echo "📊 Найдено ключевых слов: {$popularKeywords->total}\n\n";
    
    foreach (array_slice($popularKeywords->docs, 0, 5) as $index => $keyword) {
        echo sprintf("%d. %s\n", 
            $index + 1, 
            $keyword->name
        );
        if (!empty($keyword->description)) {
            echo "   📝 " . substr($keyword->description, 0, 100) . "...\n";
        }
        echo "\n";
    }
    
    echo "\n";

    // 9. Комплексный поиск по студиям и ключевым словам
    echo "🔍 9. Комплексный поиск\n";
    echo "------------------------\n";
    
    $complexFilter = new MovieSearchFilter();
    $complexFilter->withStudio('Marvel')
                  ->withKeyword('супергерой')
                  ->withMinRating(7.0, 'kp')
                  ->withMinVotes(10000, 'kp')
                  ->withYearBetween(2010, 2023);
    
    $complexResults = $movieRequests->searchMovies($complexFilter, 1, 5);
    
    echo "📊 Найдено фильмов Marvel с супергероями: {$complexResults->total}\n\n";
    
    foreach ($complexResults->docs as $index => $movie) {
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

    echo "✅ Все примеры работы с ключевыми словами и студиями выполнены успешно!\n";

} catch (KinopoiskDevException $e) {
    echo "❌ Ошибка: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "❌ Неожиданная ошибка: " . $e->getMessage() . "\n";
}

echo "\n🎉 Работа с ключевыми словами и студиями завершена!\n"; 