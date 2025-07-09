<?php

require_once __DIR__ . '/../vendor/autoload.php';

use KinopoiskDev\Kinopoisk;
use KinopoiskDev\Filter\KeywordSearchFilter;
use KinopoiskDev\Http\KeywordRequests;

/**
 * Пример использования KeywordSearchFilter с новой архитектурой
 * 
 * Демонстрирует различные способы фильтрации и поиска ключевых слов
 * с использованием обновленной архитектуры, основанной на MovieFilter и FilterTrait
 */

// Инициализация API клиента
$api = new Kinopoisk('your-api-token');
$keywordRequests = new KeywordRequests($api->getApiToken());

echo "=== Примеры использования KeywordSearchFilter ===\n\n";

// 1. Базовый поиск по названию
echo "1. Поиск ключевых слов по названию:\n";
$filter = new KeywordSearchFilter();
$filter->search('комедия')->sortByTitle();

try {
    $response = $keywordRequests->searchKeywords($filter, 1, 5);
    echo "Найдено {$response->total} ключевых слов\n";
    foreach ($response->docs as $keyword) {
        echo "- {$keyword->title} (ID: {$keyword->id})\n";
    }
} catch (Exception $e) {
    echo "Ошибка: {$e->getMessage()}\n";
}
echo "\n";

// 2. Поиск ключевых слов для конкретного фильма
echo "2. Ключевые слова для фильма (ID: 666):\n";
$filter = new KeywordSearchFilter();
$filter->movieId(666)->sortByPopularity();

try {
    $response = $keywordRequests->searchKeywords($filter, 1, 10);
    echo "Найдено {$response->total} ключевых слов для фильма\n";
    foreach ($response->docs as $keyword) {
        echo "- {$keyword->title}\n";
    }
} catch (Exception $e) {
    echo "Ошибка: {$e->getMessage()}\n";
}
echo "\n";

// 3. Поиск популярных ключевых слов
echo "3. Популярные ключевые слова:\n";
$filter = new KeywordSearchFilter();
$filter->onlyPopular(20)->sortByPopularity('desc');

try {
    $response = $keywordRequests->searchKeywords($filter, 1, 10);
    echo "Найдено {$response->total} популярных ключевых слов\n";
    
    foreach ($response->docs as $keyword) {
        $movieCount = $keyword->getMoviesCount();
        echo "- {$keyword->title} ({$movieCount} фильмов)\n";
    }
} catch (Exception $e) {
    echo "Ошибка: {$e->getMessage()}\n";
}
echo "\n";

// 4. Недавно созданные ключевые слова
echo "4. Недавно созданные ключевые слова (за последние 90 дней):\n";
$filter = new KeywordSearchFilter();
$filter->recentlyCreated(90)->sortByCreatedAt('desc');

try {
    $response = $keywordRequests->searchKeywords($filter, 1, 5);
    echo "Найдено {$response->total} недавно созданных ключевых слов\n";
    
    foreach ($response->docs as $keyword) {
        echo "- {$keyword->title}\n";
    }
} catch (Exception $e) {
    echo "Ошибка: {$e->getMessage()}\n";
}
echo "\n";

// 5. Комплексная фильтрация с выборкой полей
echo "5. Комплексная фильтрация:\n";
$filter = new KeywordSearchFilter();
$filter->search('драма')
       ->onlyPopular(5)
       ->recentlyUpdated(30)
       ->selectFields(['id', 'title', 'movies'])
       ->notNullFields(['title', 'movies'])
       ->sortByTitle('asc');

try {
    $response = $keywordRequests->searchKeywords($filter, 1, 10);
    echo "Найдено {$response->total} ключевых слов с комплексной фильтрацией\n";
    
    foreach ($response->docs as $keyword) {
        echo "- {$keyword->title} (ID: {$keyword->id})\n";
    }
} catch (Exception $e) {
    echo "Ошибка: {$e->getMessage()}\n";
}
echo "\n";

// 6. Использование методов DTO для анализа
echo "6. Анализ полученных данных:\n";
$filter = new KeywordSearchFilter();
$filter->onlyPopular(10)->sortByPopularity('desc');

try {
    $response = $keywordRequests->searchKeywords($filter, 1, 20);
    
    // Группировка по популярности
    $groups = $response->groupByPopularity();
    echo "Группировка ключевых слов по популярности:\n";
    echo "- Очень популярные (100+ фильмов): " . count($groups['very_popular']) . "\n";
    echo "- Популярные (10-99 фильмов): " . count($groups['popular']) . "\n";
    echo "- Умеренные (2-9 фильмов): " . count($groups['moderate']) . "\n";
    echo "- Редкие (0-1 фильм): " . count($groups['rare']) . "\n";
    
    // Статистика
    $stats = $response->getStatistics();
    echo "\nОбщая статистика:\n";
    echo "- Всего ключевых слов: {$stats['total_keywords']}\n";
    echo "- Ключевых слов с фильмами: {$stats['keywords_with_movies']}\n";
    echo "- Популярных ключевых слов: {$stats['popular_keywords']}\n";
    echo "- Среднее количество фильмов на ключевое слово: {$stats['average_movies_per_keyword']}\n";
    
} catch (Exception $e) {
    echo "Ошибка: {$e->getMessage()}\n";
}
echo "\n";

// 7. Демонстрация fluent interface
echo "7. Использование fluent interface:\n";
try {
    $response = (new KeywordSearchFilter())
        ->search('ужас')
        ->onlyPopular()
        ->recentlyUpdated(14)
        ->sortByPopularity('desc')
        ->sortByTitle('asc'); // Вторичная сортировка
    
    $results = $keywordRequests->searchKeywords($response, 1, 5);
    echo "Найдено {$results->total} ключевых слов через fluent interface\n";
    
    foreach ($results->docs as $keyword) {
        echo "- {$keyword->title}\n";
    }
} catch (Exception $e) {
    echo "Ошибка: {$e->getMessage()}\n";
}

echo "\n=== Примеры завершены ===\n";