<?php

/**
 * Полный пример использования PHP клиента для Kinopoisk.dev API
 * 
 * Этот файл демонстрирует все основные возможности библиотеки:
 * - Работа с фильмами, персонами, изображениями, коллекциями
 * - Использование фильтров и поиска
 * - Обработка ошибок
 * - Кэширование и оптимизация
 */

require_once __DIR__ . '/../vendor/autoload.php';

use KinopoiskDev\Http\MovieRequests;
use KinopoiskDev\Http\PersonRequests;
use KinopoiskDev\Http\ImageRequests;
use KinopoiskDev\Http\ListRequests;
use KinopoiskDev\Http\SeasonRequests;
use KinopoiskDev\Http\ReviewRequests;
use KinopoiskDev\Filter\MovieSearchFilter;
use KinopoiskDev\Exceptions\KinopoiskDevException;
use KinopoiskDev\Exceptions\KinopoiskResponseException;

// Конфигурация
$apiToken = getenv('KINOPOISK_TOKEN') ?: 'YOUR_API_TOKEN_HERE';
$useCache = true;

echo "=== Полный пример использования Kinopoisk.dev PHP клиента ===\n\n";

try {
    // Инициализация клиентов с кэшированием
    $movieClient = new MovieRequests($apiToken, null, $useCache);
    $personClient = new PersonRequests($apiToken, null, $useCache);
    $imageClient = new ImageRequests($apiToken, null, $useCache);
    $listClient = new ListRequests($apiToken, null, $useCache);
    $seasonClient = new SeasonRequests($apiToken, null, $useCache);
    $reviewClient = new ReviewRequests($apiToken, null, $useCache);

    // ==================== РАБОТА С ФИЛЬМАМИ ====================
    
    echo "🎬 РАБОТА С ФИЛЬМАМИ\n";
    echo str_repeat("=", 50) . "\n";
    
    // 1. Получение конкретного фильма
    echo "\n1. Получение фильма по ID (Бойцовский клуб):\n";
    $movie = $movieClient->getMovieById(361);
    displayMovieInfo($movie);
    
    // 2. Случайный фильм с фильтрами
    echo "\n2. Случайный высокорейтинговый фильм:\n";
    $randomFilter = new MovieSearchFilter();
    $randomFilter->withRatingBetween(8.0, 10.0)
                 ->withVotesBetween(10000, null)
                 ->withPoster()
                 ->onlyMovies();
    
    $randomMovie = $movieClient->getRandomMovie($randomFilter);
    displayMovieInfo($randomMovie);
    
    // 3. Поиск по названию
    echo "\n3. Поиск фильмов по названию 'Матрица':\n";
    $searchResults = $movieClient->searchByName('Матрица', 1, 5);
    foreach ($searchResults->docs as $movie) {
        echo "- {$movie->name} ({$movie->year}) - Рейтинг: " . 
             ($movie->rating->kp ?? 'N/A') . "\n";
    }
    
    // 4. Сложный поиск с фильтрами
    echo "\n4. Лучшие российские драмы 2020-2024:\n";
    $complexFilter = new MovieSearchFilter();
    $complexFilter->withIncludedCountries('Россия')
                  ->withIncludedGenres('драма')
                  ->withYearBetween(2020, 2024)
                  ->withRatingBetween(7.0, 10.0)
                  ->withVotesBetween(1000, null)
                  ->withPoster()
                  ->onlyMovies()
                  ->sortByKinopoiskRating();
    
    $complexResults = $movieClient->searchMovies($complexFilter, 1, 10);
    foreach ($complexResults->docs as $movie) {
        echo "- {$movie->name} ({$movie->year}) - {$movie->rating->kp}/10\n";
    }
    
    // 5. Удобные методы
    echo "\n5. Новинки 2024 года:\n";
    $latest = $movieClient->getLatestMovies(2024, 1, 5);
    foreach ($latest->docs as $movie) {
        echo "- {$movie->name} ({$movie->year})\n";
    }
    
    echo "\n6. Комедии с высоким рейтингом:\n";
    $comedies = $movieClient->getMoviesByGenre('комедия', 1, 5);
    foreach ($comedies->docs as $movie) {
        echo "- {$movie->name} ({$movie->year}) - {$movie->rating->kp}/10\n";
    }
    
    // ==================== РАБОТА С ПЕРСОНАМИ ====================
    
    echo "\n\n👤 РАБОТА С ПЕРСОНАМИ\n";
    echo str_repeat("=", 50) . "\n";
    
    // 1. Получение персоны по ID
    echo "\n1. Получение персоны по ID (Леонардо ДиКаприо):\n";
    $person = $personClient->getPersonById(29485);
    displayPersonInfo($person);
    
    // 2. Поиск персон по имени
    echo "\n2. Поиск персон по имени 'Леонардо':\n";
    $personSearchResults = $personClient->searchByName('Леонардо', 1, 3);
    foreach ($personSearchResults->docs as $person) {
        $age = $person->age ? " ({$person->age} лет)" : '';
        echo "- {$person->name}{$age}\n";
    }
    
    // ==================== РАБОТА С ИЗОБРАЖЕНИЯМИ ====================
    
    echo "\n\n🖼️ РАБОТА С ИЗОБРАЖЕНИЯМИ\n";
    echo str_repeat("=", 50) . "\n";
    
    // 1. Изображения конкретного фильма
    echo "\n1. Постеры Бойцовского клуба:\n";
    $movieImages = $imageClient->getImagesByMovieId(361, 'poster', 1, 3);
    foreach ($movieImages->docs as $image) {
        echo "- {$image->type}: {$image->url}\n";
    }
    
    // ==================== РАБОТА С КОЛЛЕКЦИЯМИ ====================
    
    echo "\n\n📚 РАБОТА С КОЛЛЕКЦИЯМИ\n";
    echo str_repeat("=", 50) . "\n";
    
    // 1. Популярные коллекции
    echo "\n1. Популярные коллекции:\n";
    $collections = $listClient->getPopularLists(1, 5);
    foreach ($collections->docs as $list) {
        echo "- {$list->name}";
        if ($list->moviesCount) {
            echo " ({$list->moviesCount} фильмов)";
        }
        echo "\n";
    }
    
    // ==================== РАБОТА С СЕЗОНАМИ ====================
    
    echo "\n\n📺 РАБОТА С СЕЗОНАМИ\n";
    echo str_repeat("=", 50) . "\n";
    
    // 1. Поиск сезонов
    echo "\n1. Последние сезоны сериалов:\n";
    $seasonFilter = new MovieSearchFilter();
    $seasons = $seasonClient->getSeasons($seasonFilter, 1, 5);
    foreach ($seasons->docs as $season) {
        echo "- Сезон {$season->number} (ID фильма: {$season->movieId})\n";
        if ($season->name) {
            echo "  Название: {$season->name}\n";
        }
    }
    
    // ==================== РАБОТА С РЕЦЕНЗИЯМИ ====================
    
    echo "\n\n📝 РАБОТА С РЕЦЕНЗИЯМИ\n";
    echo str_repeat("=", 50) . "\n";
    
    // 1. Последние рецензии
    echo "\n1. Последние рецензии:\n";
    $reviewFilter = new MovieSearchFilter();
    $reviews = $reviewClient->getReviews($reviewFilter, 1, 3);
    foreach ($reviews->docs as $review) {
        echo "- {$review->title} (Автор: {$review->author})\n";
        if ($review->userRating) {
            echo "  Оценка: {$review->userRating}/10\n";
        }
    }
    
    // ==================== ДОПОЛНИТЕЛЬНЫЕ ВОЗМОЖНОСТИ ====================
    
    echo "\n\n⚡ ДОПОЛНИТЕЛЬНЫЕ ВОЗМОЖНОСТИ\n";
    echo str_repeat("=", 50) . "\n";
    
    // 1. Получение возможных значений для полей
    echo "\n1. Доступные жанры:\n";
    $genres = $movieClient->getPossibleValuesByField('genres.name');
    $genreNames = array_slice(array_column($genres, 'name'), 0, 10);
    echo "Первые 10 жанров: " . implode(', ', $genreNames) . "\n";
    
    // 2. Награды фильмов
    echo "\n2. Последние награды фильмов:\n";
    $awardFilter = new MovieSearchFilter();
    $awards = $movieClient->getMovieAwards($awardFilter, 1, 3);
    foreach ($awards->docs as $award) {
        $title = $award->nomination->title ?? 'Неизвестная номинация';
        echo "- {$title}";
        if ($award->winning) {
            echo " (Победитель)";
        }
        echo "\n";
    }
    
    // ==================== ПРАКТИЧЕСКИЕ ПРИМЕРЫ ====================
    
    echo "\n\n🔥 ПРАКТИЧЕСКИЕ ПРИМЕРЫ\n";
    echo str_repeat("=", 50) . "\n";
    
    // 1. Топ-10 лучших фильмов всех времен
    echo "\n1. Топ-10 лучших фильмов всех времен:\n";
    $topFilter = new MovieSearchFilter();
    $topFilter->withRatingBetween(9.0, 10.0)
              ->withVotesBetween(100000, null)
              ->withPoster()
              ->onlyMovies()
              ->sortByKinopoiskRating();
    
    $topMovies = $movieClient->searchMovies($topFilter, 1, 10);
    $rank = 1;
    foreach ($topMovies->docs as $movie) {
        echo "{$rank}. {$movie->name} ({$movie->year}) - {$movie->rating->kp}/10\n";
        $rank++;
    }
    
    // 2. Лучшие фантастические фильмы последних лет
    echo "\n2. Лучшие фантастические фильмы 2020-2024:\n";
    $scifiFilter = new MovieSearchFilter();
    $scifiFilter->withIncludedGenres('фантастика')
                ->withYearBetween(2020, 2024)
                ->withRatingBetween(7.5, 10.0)
                ->withVotesBetween(5000, null)
                ->withPoster()
                ->onlyMovies()
                ->sortByKinopoiskRating();
    
    $scifiMovies = $movieClient->searchMovies($scifiFilter, 1, 5);
    foreach ($scifiMovies->docs as $movie) {
        echo "- {$movie->name} ({$movie->year}) - {$movie->rating->kp}/10\n";
        if ($movie->shortDescription) {
            echo "  {$movie->shortDescription}\n";
        }
    }
    
    // 3. Анализ популярности жанров
    echo "\n3. Анализ популярности жанров в 2023 году:\n";
    $genreAnalysisFilter = new MovieSearchFilter();
    $genreAnalysisFilter->year(2023)
                        ->withRatingBetween(6.0, 10.0)
                        ->withVotesBetween(1000, null)
                        ->onlyMovies();
    
    $genreMovies = $movieClient->searchMovies($genreAnalysisFilter, 1, 100);
    $genreCount = [];
    
    foreach ($genreMovies->docs as $movie) {
        if ($movie->genres) {
            foreach ($movie->genres as $genre) {
                $genreCount[$genre->name] = ($genreCount[$genre->name] ?? 0) + 1;
            }
        }
    }
    
    arsort($genreCount);
    $topGenres = array_slice($genreCount, 0, 5, true);
    
    echo "Самые популярные жанры в 2023:\n";
    foreach ($topGenres as $genre => $count) {
        echo "- {$genre}: {$count} фильмов\n";
    }

} catch (KinopoiskResponseException $e) {
    echo "\n❌ Ошибка API:\n";
    switch ($e->getCode()) {
        case 401:
            echo "Неверный или отсутствующий токен API. Проверьте переменную KINOPOISK_TOKEN.\n";
            break;
        case 403:
            echo "Превышен лимит запросов. Попробуйте позже.\n";
            break;
        case 404:
            echo "Запрашиваемый ресурс не найден.\n";
            break;
        default:
            echo "Код ошибки: {$e->getCode()}\n";
            echo "Сообщение: {$e->getMessage()}\n";
    }
} catch (KinopoiskDevException $e) {
    echo "\n❌ Ошибка клиента: {$e->getMessage()}\n";
} catch (\JsonException $e) {
    echo "\n❌ Ошибка парсинга JSON: {$e->getMessage()}\n";
} catch (\Exception $e) {
    echo "\n❌ Неожиданная ошибка: {$e->getMessage()}\n";
}

/**
 * Выводит информацию о фильме
 */
function displayMovieInfo($movie): void {
    echo "Название: {$movie->name}\n";
    echo "Год: {$movie->year}\n";
    echo "Рейтинг КП: " . ($movie->rating->kp ?? 'N/A') . "\n";
    echo "Рейтинг IMDB: " . ($movie->rating->imdb ?? 'N/A') . "\n";
    echo "Голосов КП: " . ($movie->votes->kp ?? 'N/A') . "\n";
    
    if ($movie->genres) {
        $genres = array_map(fn($g) => $g->name, $movie->genres);
        echo "Жанры: " . implode(', ', $genres) . "\n";
    }
    
    if ($movie->countries) {
        $countries = array_map(fn($c) => $c->name, $movie->countries);
        echo "Страны: " . implode(', ', $countries) . "\n";
    }
    
    if ($movie->shortDescription) {
        echo "Описание: {$movie->shortDescription}\n";
    }
    
    if ($movie->poster && $movie->poster->url) {
        echo "Постер: {$movie->poster->url}\n";
    }
}

/**
 * Выводит информацию о персоне
 */
function displayPersonInfo($person): void {
    echo "Имя: {$person->name}\n";
    
    if ($person->enName) {
        echo "Английское имя: {$person->enName}\n";
    }
    
    if ($person->age) {
        echo "Возраст: {$person->age} лет\n";
    }
    
    if ($person->birthday) {
        echo "Дата рождения: {$person->birthday}\n";
    }
    
    if ($person->birthPlace) {
        $places = array_map(fn($p) => $p->value, $person->birthPlace);
        echo "Место рождения: " . implode(', ', $places) . "\n";
    }
    
    if ($person->profession) {
        $professions = array_map(fn($p) => $p->value, $person->profession);
        echo "Профессии: " . implode(', ', $professions) . "\n";
    }
    
    if ($person->countAwards) {
        echo "Количество наград: {$person->countAwards}\n";
    }
}

echo "\n\n✅ Пример выполнен успешно!\n";
echo "📖 Для получения дополнительной информации см. README.md\n";
echo "🔗 Документация API: https://kinopoiskdev.readme.io/\n";
echo "💬 Поддержка: https://t.me/kinopoiskdev_chat\n";