<?php

/**
 * Работа с отзывами и рейтингами в KinopoiskDev
 * 
 * Этот пример демонстрирует:
 * - Поиск отзывов на фильмы
 * - Работа с различными типами отзывов
 * - Фильтрация отзывов по рейтингу
 * - Получение рейтингов фильмов
 * - Анализ отзывов
 */

require_once __DIR__ . '/../vendor/autoload.php';

use KinopoiskDev\Http\ReviewRequests;
use KinopoiskDev\Http\MovieRequests;
use KinopoiskDev\Filter\ReviewSearchFilter;
use KinopoiskDev\Enums\ReviewType;
use KinopoiskDev\Exceptions\KinopoiskDevException;

// Проверяем наличие токена
if (!getenv('KINOPOISK_TOKEN')) {
    echo "❌ Ошибка: Не найден токен API. Установите переменную окружения KINOPOISK_TOKEN\n";
    exit(1);
}

echo "⭐ KinopoiskDev - Работа с отзывами и рейтингами\n";
echo "==================================================\n\n";

try {
    $reviewRequests = new \KinopoiskDev\Http\ReviewRequests(useCache: true);
    $movieRequests = new \KinopoiskDev\Http\MovieRequests(useCache: true);
    
    // 1. Получение отзывов на фильм
    echo "📝 1. Отзывы на фильм 'Матрица'\n";
    echo "--------------------------------\n";
    
    $matrixId = 301; // ID фильма "Матрица"
    $reviewFilter = new ReviewSearchFilter();
    $reviewFilter->movieId($matrixId)
                 ->type(ReviewType::POSITIVE);
    
    $reviews = $reviewRequests->searchReviews($reviewFilter, 1, 5);
    
    echo "📊 Найдено отзывов: {$reviews->total}\n\n";
    
    foreach ($reviews->docs as $index => $review) {
        echo sprintf("%d. %s\n", 
            $index + 1, 
            $review->title ?? 'Без заголовка'
        );
        echo "   👤 Автор: {$review->author}\n";
        echo "   ⭐ Рейтинг: {$review->reviewRating}\n";
        echo "   📅 Дата: {$review->date}\n";
        echo "   📝 " . substr($review->review ?? 'Нет текста', 0, 150) . "...\n\n";
    }
    
    echo "\n";

    // 2. Поиск критических отзывов
    echo "🎭 2. Критические отзывы на популярные фильмы\n";
    echo "---------------------------------------------\n";
    
    $criticFilter = new ReviewSearchFilter();
    $criticFilter->type(ReviewType::NEGATIVE)
                 ->limit(5);
    
    $criticReviews = $reviewRequests->searchReviews($criticFilter, 1, 5);
    
    echo "📊 Найдено критических отзывов: {$criticReviews->total}\n\n";
    
    foreach ($criticReviews->docs as $index => $review) {
        echo sprintf("%d. %s\n", 
            $index + 1, 
            $review->title ?? 'Без заголовка'
        );
        echo "   🎬 Фильм: {$review->movieId}\n";
        echo "   👤 Автор: {$review->author}\n";
        echo "   ⭐ Рейтинг: {$review->reviewRating}\n";
        echo "   📝 " . substr($review->review ?? 'Нет текста', 0, 100) . "...\n\n";
    }
    
    echo "\n";

    // 3. Анализ рейтингов фильма
    echo "📊 3. Анализ рейтингов фильма\n";
    echo "------------------------------\n";
    
    $movie = $movieRequests->getMovieById($matrixId);
    
    echo "🎬 Фильм: {$movie->name}\n";
    echo "📊 Рейтинги:\n";
    
    if (!empty($movie->rating)) {
        $rating = $movie->rating;
        echo "   ⭐ Кинопоиск: " . ($rating->kp ?? 'N/A') . "\n";
        echo "   🌟 IMDB: " . ($rating->imdb ?? 'N/A') . "\n";
        echo "   🎭 Критики: " . ($rating->filmCritics ?? 'N/A') . "\n";
        echo "   👥 Зрители: " . ($rating->russianFilmCritics ?? 'N/A') . "\n";
    }
    
    if (!empty($movie->votes)) {
        $votes = $movie->votes;
        echo "📈 Голоса:\n";
        echo "   👥 Кинопоиск: " . number_format($votes->kp ?? 0) . "\n";
        echo "   🌟 IMDB: " . number_format($votes->imdb ?? 0) . "\n";
        echo "   🎭 Критики: " . number_format($votes->filmCritics ?? 0) . "\n";
    }
    
    echo "\n";

    // 4. Поиск отзывов по рейтингу
    echo "⭐ 4. Отзывы с высоким рейтингом\n";
    echo "--------------------------------\n";
    
    $highRatingFilter = new ReviewSearchFilter();
    $highRatingFilter->reviewRating(8, 'gte')
                     ->type(ReviewType::POSITIVE)
                     ->limit(5);
    
    $highRatingReviews = $reviewRequests->searchReviews($highRatingFilter, 1, 5);
    
    echo "📊 Найдено отзывов с рейтингом 8+: {$highRatingReviews->total}\n\n";
    
    foreach ($highRatingReviews->docs as $index => $review) {
        echo sprintf("%d. %s\n", 
            $index + 1, 
            $review->title ?? 'Без заголовка'
        );
        echo "   👤 Автор: {$review->author}\n";
        echo "   ⭐ Рейтинг: {$review->reviewRating}\n";
        echo "   📝 " . substr($review->review ?? 'Нет текста', 0, 120) . "...\n\n";
    }
    
    echo "\n";

    // 5. Поиск отзывов по автору
    echo "👤 5. Отзывы конкретного автора\n";
    echo "--------------------------------\n";
    
    $authorFilter = new ReviewSearchFilter();
    $authorFilter->author('Критик')
                 ->limit(3);
    
    $authorReviews = $reviewRequests->searchReviews($authorFilter, 1, 3);
    
    echo "📊 Найдено отзывов автора: {$authorReviews->total}\n\n";
    
    foreach ($authorReviews->docs as $index => $review) {
        echo sprintf("%d. %s\n", 
            $index + 1, 
            $review->title ?? 'Без заголовка'
        );
        echo "   🎬 Фильм: {$review->movieId}\n";
        echo "   ⭐ Рейтинг: {$review->reviewRating}\n";
        echo "   📅 Дата: {$review->date}\n";
        echo "   📝 " . substr($review->review ?? 'Нет текста', 0, 100) . "...\n\n";
    }
    
    echo "\n";

    // 6. Сравнение рейтингов фильмов
    echo "📊 6. Сравнение рейтингов популярных фильмов\n";
    echo "--------------------------------------------\n";
    
    $popularMovies = [301, 326, 328]; // Матрица, Побег из Шоушенка, Зеленая миля
    $movieNames = ['Матрица', 'Побег из Шоушенка', 'Зеленая миля'];
    
    echo "📊 Сравнение рейтингов:\n\n";
    
    for ($i = 0; $i < count($popularMovies); $i++) {
        $movie = $movieRequests->getMovieById($popularMovies[$i]);
        $rating = $movie->rating;
        
        echo sprintf("%d. %s:\n", 
            $i + 1, 
            $movieNames[$i]
        );
        echo "   ⭐ Кинопоиск: " . ($rating->kp ?? 'N/A') . "\n";
        echo "   🌟 IMDB: " . ($rating->imdb ?? 'N/A') . "\n";
        echo "   👥 Голосов КП: " . number_format($movie->votes->kp ?? 0) . "\n\n";
    }
    
    echo "\n";

    // 7. Поиск отзывов по дате
    echo "📅 7. Недавние отзывы\n";
    echo "----------------------\n";
    
    $recentFilter = new ReviewSearchFilter();
    $recentFilter->limit(5);
    
    $recentReviews = $reviewRequests->searchReviews($recentFilter, 1, 5);
    
    echo "📊 Найдено недавних отзывов: {$recentReviews->total}\n\n";
    
    foreach ($recentReviews->docs as $index => $review) {
        echo sprintf("%d. %s\n", 
            $index + 1, 
            $review->title ?? 'Без заголовка'
        );
        echo "   👤 Автор: {$review->author}\n";
        echo "   📅 Дата: {$review->date}\n";
        echo "   ⭐ Рейтинг: {$review->reviewRating}\n";
        echo "   📝 " . substr($review->review ?? 'Нет текста', 0, 80) . "...\n\n";
    }
    
    echo "\n";

    // 8. Анализ тональности отзывов
    echo "📈 8. Анализ тональности отзывов\n";
    echo "--------------------------------\n";
    
    $positiveFilter = new ReviewSearchFilter();
    $positiveFilter->type(ReviewType::POSITIVE)
                   ->limit(3);
    
    $negativeFilter = new ReviewSearchFilter();
    $negativeFilter->type(ReviewType::NEGATIVE)
                   ->limit(3);
    
    $neutralFilter = new ReviewSearchFilter();
    $neutralFilter->type(ReviewType::NEUTRAL)
                  ->limit(3);
    
    $positiveReviews = $reviewRequests->searchReviews($positiveFilter, 1, 3);
    $negativeReviews = $reviewRequests->searchReviews($negativeFilter, 1, 3);
    $neutralReviews = $reviewRequests->searchReviews($neutralFilter, 1, 3);
    
    echo "📊 Статистика отзывов:\n";
    echo "   👍 Позитивные: {$positiveReviews->total}\n";
    echo "   👎 Негативные: {$negativeReviews->total}\n";
    echo "   😐 Нейтральные: {$neutralReviews->total}\n\n";
    
    echo "👍 Примеры позитивных отзывов:\n";
    foreach (array_slice($positiveReviews->docs, 0, 2) as $review) {
        echo "   • {$review->title} (⭐ {$review->reviewRating})\n";
    }
    
    echo "\n👎 Примеры негативных отзывов:\n";
    foreach (array_slice($negativeReviews->docs, 0, 2) as $review) {
        echo "   • {$review->title} (⭐ {$review->reviewRating})\n";
    }
    
    echo "\n";

    echo "✅ Все примеры работы с отзывами и рейтингами выполнены успешно!\n";

} catch (KinopoiskDevException $e) {
    echo "❌ Ошибка: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "❌ Неожиданная ошибка: " . $e->getMessage() . "\n";
}

echo "\n🎉 Работа с отзывами завершена!\n"; 