# KinoPoisk.dev PHP клиент

![PHP Version](https://img.shields.io/badge/PHP-%3E%3D8.3-blue)
![License](https://img.shields.io/badge/license-MIT-green)
![API Version](https://img.shields.io/badge/API-v1.4-orange)

Полнофункциональный PHP 8.3 клиент для работы с неофициальным API [KinoPoisk.dev](https://kinopoisk.dev). Библиотека предоставляет удобный объектно-ориентированный интерфейс для доступа ко всем возможностям API с полной поддержкой типов, кэширования и детальной документацией на русском языке.

## 🎯 Возможности

- ✅ **Полное покрытие API v1.4** - поддержка всех доступных эндпоинтов
- ✅ **PHP 8.3+** - использование современных возможностей языка
- ✅ **Типизация** - полная поддержка типов для IDE и статического анализа
- ✅ **Кэширование** - встроенная поддержка кэширования запросов
- ✅ **Удобные фильтры** - мощная система фильтрации с fluent interface
- ✅ **Документация на русском** - подробная документация и примеры
- ✅ **Обработка ошибок** - продуманная система исключений
- ✅ **PSR-совместимость** - следование стандартам PHP

## 📦 Установка

### Через Composer

```bash
composer require devcraftclub/kinopoisk-dev
```

### Требования

- PHP >= 8.3
- Guzzle HTTP >= 7.0
- Токен API от [kinopoisk.dev](https://kinopoisk.dev)

## 🚀 Быстрый старт

### Получение токена

Для работы с API необходимо получить токен:
1. Перейдите на [kinopoisk.dev](https://kinopoisk.dev)
2. Зарегистрируйтесь и получите токен
3. Или напишите боту [@kinopoiskdev_bot](https://t.me/kinopoiskdev_bot) в Telegram

### Базовое использование

```php
<?php

require_once 'vendor/autoload.php';

use KinopoiskDev\Http\MovieRequests;
use KinopoiskDev\Http\PersonRequests;
use KinopoiskDev\Filter\MovieSearchFilter;
use KinopoiskDev\Exceptions\KinopoiskDevException;

// Инициализация клиента
$apiToken = 'ваш_токен_здесь';
$movieClient = new MovieRequests($apiToken);

try {
    // Получение фильма по ID
    $movie = $movieClient->getMovieById(666);
    echo "Фильм: {$movie->name} ({$movie->year})\n";
    echo "Рейтинг КП: {$movie->rating->kp}\n";
    
    // Поиск фильмов с фильтрами
    $filter = new MovieSearchFilter();
    $filter->withRatingBetween(8.0, 10.0)
           ->withYearBetween(2020, 2024)
           ->withIncludedGenres(['драма', 'триллер'])
           ->onlyMovies();
    
    $results = $movieClient->searchMovies($filter, 1, 10);
    echo "Найдено: {$results->total} фильмов\n";
    
} catch (KinopoiskDevException $e) {
    echo "Ошибка API: " . $e->getMessage() . "\n";
}
```

## 📚 Документация API

### Фильмы и сериалы

#### Основные методы

```php
use KinopoiskDev\Http\MovieRequests;

$movieClient = new MovieRequests($apiToken);

// Получение фильма по ID
$movie = $movieClient->getMovieById(666);

// Случайный фильм
$randomMovie = $movieClient->getRandomMovie();

// Поиск по названию
$searchResults = $movieClient->searchByName('Матрица');

// Расширенный поиск с фильтрами
$results = $movieClient->searchMovies($filter, $page, $limit);

// Получение наград фильмов
$awards = $movieClient->getMovieAwards($filter, $page, $limit);

// Возможные значения для полей
$genres = $movieClient->getPossibleValuesByField('genres.name');
```

#### Удобные методы

```php
// Новинки текущего года
$latest = $movieClient->getLatestMovies();

// Фильмы по жанру
$comedies = $movieClient->getMoviesByGenre('комедия');
$dramaThrillers = $movieClient->getMoviesByGenre(['драма', 'триллер']);

// Фильмы по стране
$russianMovies = $movieClient->getMoviesByCountry('Россия');

// Фильмы по периоду
$movies2020s = $movieClient->getMoviesByYearRange(2020, 2024);
```

### Персоны

```php
use KinopoiskDev\Http\PersonRequests;

$personClient = new PersonRequests($apiToken);

// Получение персоны по ID
$person = $personClient->getPersonById(123);

// Поиск персон
$personFilter = new MovieSearchFilter(); // Можно использовать для персон
$persons = $personClient->searchPersons($personFilter);

// Поиск по имени
$searchResults = $personClient->searchByName('Леонардо ДиКаприо');

// Награды персон
$awards = $personClient->getPersonAwards($personFilter);
```

### Изображения

```php
use KinopoiskDev\Http\ImageRequests;

$imageClient = new ImageRequests($apiToken);

// Все изображения с фильтрацией
$images = $imageClient->getImages($filter);

// Изображения конкретного фильма
$movieImages = $imageClient->getImagesByMovieId(666, 'poster');

// Постеры высокорейтинговых фильмов
$posters = $imageClient->getHighRatedPosters(8.0);
```

### Коллекции

```php
use KinopoiskDev\Http\ListRequests;

$listClient = new ListRequests($apiToken);

// Все коллекции
$collections = $listClient->getAllLists();

// Конкретная коллекция
$top250 = $listClient->getListBySlug('top250');

// Популярные коллекции
$popular = $listClient->getPopularLists();

// Коллекции по категории
$genreCollections = $listClient->getListsByCategory('жанровые');
```

### Другие сущности

```php
use KinopoiskDev\Http\SeasonRequests;
use KinopoiskDev\Http\ReviewRequests;
use KinopoiskDev\Http\StudioRequests;
use KinopoiskDev\Http\KeywordRequests;

// Сезоны сериалов
$seasonClient = new SeasonRequests($apiToken);
$seasons = $seasonClient->getSeasons($filter);

// Рецензии
$reviewClient = new ReviewRequests($apiToken);
$reviews = $reviewClient->getReviews($filter);

// Студии
$studioClient = new StudioRequests($apiToken);
$studios = $studioClient->getStudios($filter);

// Ключевые слова
$keywordClient = new KeywordRequests($apiToken);
$keywords = $keywordClient->getKeywords($filter);
```

## 🔍 Система фильтрации

Библиотека предоставляет мощную систему фильтрации с fluent interface:

### MovieSearchFilter

```php
use KinopoiskDev\Filter\MovieSearchFilter;

$filter = new MovieSearchFilter();

// Базовые фильтры
$filter->searchByName('Матрица')                    // Поиск по названию
       ->year(2020)                                 // Конкретный год
       ->withYearBetween(2020, 2024)               // Диапазон лет
       ->withRatingBetween(7.0, 10.0)              // Диапазон рейтинга
       ->withVotesBetween(10000, 1000000);         // Диапазон голосов

// Жанры и страны
$filter->withIncludedGenres(['драма', 'триллер'])   // Включить жанры
       ->withExcludedGenres(['ужасы'])              // Исключить жанры
       ->withIncludedCountries('США')               // Включить страны
       ->withExcludedCountries(['Франция']);        // Исключить страны

// Типы контента
$filter->onlyMovies()                               // Только фильмы
       ->onlySeries()                               // Только сериалы
       ->onlyAnimated();                            // Только анимация

// Сортировка
$filter->sortByKinopoiskRating()                    // По рейтингу КП
       ->sortByImdbRating()                         // По рейтингу IMDB
       ->sortByYear()                               // По году
       ->sortByCreated();                           // По дате добавления

// Дополнительные условия
$filter->withPoster()                               // Только с постером
       ->withTrailer()                              // Только с трейлером
       ->withHighRating(8.0);                       // Высокий рейтинг
```

### Сложные фильтры

```php
// Поиск лучших российских драм 2020-2024
$filter = new MovieSearchFilter();
$filter->withIncludedCountries('Россия')
       ->withIncludedGenres('драма')
       ->withYearBetween(2020, 2024)
       ->withRatingBetween(7.0, 10.0)
       ->withVotesBetween(1000, null)
       ->withPoster()
       ->sortByKinopoiskRating()
       ->onlyMovies();

$results = $movieClient->searchMovies($filter, 1, 20);

// Поиск зарубежных сериалов-триллеров с высоким рейтингом
$filter = new MovieSearchFilter();
$filter->withExcludedCountries(['Россия', 'СССР'])
       ->withIncludedGenres('триллер')
       ->withRatingBetween(8.0, 10.0)
       ->withVotesBetween(50000, null)
       ->onlySeries()
       ->sortByImdbRating();

$series = $movieClient->searchMovies($filter);
```

## ⚙️ Конфигурация

### Кэширование

```php
// Включение кэширования
$movieClient = new MovieRequests($apiToken, null, true);

// Кастомный HTTP клиент
$httpClient = new \GuzzleHttp\Client([
    'timeout' => 60,
    'headers' => [
        'User-Agent' => 'MyApp/1.0'
    ]
]);

$movieClient = new MovieRequests($apiToken, $httpClient, true);
```

### Переменные окружения

Создайте файл `.env`:

```env
KINOPOISK_TOKEN=ваш_токен_здесь
KINOPOISK_USE_CACHE=true
```

```php
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$apiToken = $_ENV['KINOPOISK_TOKEN'];
$useCache = $_ENV['KINOPOISK_USE_CACHE'] === 'true';

$movieClient = new MovieRequests($apiToken, null, $useCache);
```

## 🛠️ Примеры использования

### Создание кинопоиска

```php
<?php

require_once 'vendor/autoload.php';

use KinopoiskDev\Http\MovieRequests;
use KinopoiskDev\Filter\MovieSearchFilter;

class MiniKinopoisk {
    private MovieRequests $movieClient;
    
    public function __construct(string $apiToken) {
        $this->movieClient = new MovieRequests($apiToken, null, true);
    }
    
    /**
     * Получает топ фильмов по жанру
     */
    public function getTopMoviesByGenre(string $genre, int $limit = 10): array {
        $filter = new MovieSearchFilter();
        $filter->withIncludedGenres($genre)
               ->withRatingBetween(7.0, 10.0)
               ->withVotesBetween(10000, null)
               ->withPoster()
               ->onlyMovies()
               ->sortByKinopoiskRating();
        
        $results = $this->movieClient->searchMovies($filter, 1, $limit);
        
        return array_map(function($movie) {
            return [
                'id' => $movie->id,
                'name' => $movie->name,
                'year' => $movie->year,
                'rating' => $movie->rating->kp,
                'poster' => $movie->poster->url ?? null,
                'genres' => array_map(fn($g) => $g->name, $movie->genres ?? []),
                'countries' => array_map(fn($c) => $c->name, $movie->countries ?? [])
            ];
        }, $results->docs);
    }
    
    /**
     * Рекомендации на основе жанров
     */
    public function getRecommendations(array $favoriteGenres, int $limit = 5): array {
        $filter = new MovieSearchFilter();
        $filter->withIncludedGenres($favoriteGenres)
               ->withRatingBetween(7.5, 10.0)
               ->withVotesBetween(5000, null)
               ->withYearBetween(2010, date('Y'))
               ->withPoster()
               ->sortByKinopoiskRating();
        
        $results = $this->movieClient->searchMovies($filter, 1, $limit);
        return $this->formatMovieResults($results->docs);
    }
    
    private function formatMovieResults(array $movies): array {
        return array_map(function($movie) {
            return [
                'title' => $movie->name,
                'year' => $movie->year,
                'rating' => round($movie->rating->kp ?? 0, 1),
                'description' => $movie->shortDescription ?? $movie->description,
                'poster' => $movie->poster->url ?? null,
                'kinopoisk_url' => "https://www.kinopoisk.ru/film/{$movie->id}/",
                'genres' => implode(', ', array_map(fn($g) => $g->name, $movie->genres ?? [])),
                'countries' => implode(', ', array_map(fn($c) => $c->name, $movie->countries ?? []))
            ];
        }, $movies);
    }
}

// Использование
$kinopoisk = new MiniKinopoisk($apiToken);

// Топ драм
$topDramas = $kinopoisk->getTopMoviesByGenre('драма', 20);

// Рекомендации
$recommendations = $kinopoisk->getRecommendations(['фантастика', 'триллер'], 10);

foreach ($recommendations as $movie) {
    echo "{$movie['title']} ({$movie['year']}) - {$movie['rating']}/10\n";
    echo "{$movie['description']}\n\n";
}
```

### Анализ трендов

```php
<?php

class MovieTrendAnalyzer {
    private MovieRequests $movieClient;
    
    public function __construct(string $apiToken) {
        $this->movieClient = new MovieRequests($apiToken, null, true);
    }
    
    /**
     * Анализирует популярные жанры по годам
     */
    public function analyzeGenreTrends(int $startYear, int $endYear): array {
        $trends = [];
        
        for ($year = $startYear; $year <= $endYear; $year++) {
            $filter = new MovieSearchFilter();
            $filter->year($year)
                   ->withRatingBetween(7.0, 10.0)
                   ->withVotesBetween(1000, null)
                   ->onlyMovies();
            
            $results = $this->movieClient->searchMovies($filter, 1, 100);
            
            $genreCount = [];
            foreach ($results->docs as $movie) {
                foreach ($movie->genres ?? [] as $genre) {
                    $genreCount[$genre->name] = ($genreCount[$genre->name] ?? 0) + 1;
                }
            }
            
            arsort($genreCount);
            $trends[$year] = array_slice($genreCount, 0, 5, true);
        }
        
        return $trends;
    }
    
    /**
     * Находит прорывы года
     */
    public function findBreakthroughMovies(int $year, float $minRating = 8.0): array {
        $filter = new MovieSearchFilter();
        $filter->year($year)
               ->withRatingBetween($minRating, 10.0)
               ->withVotesBetween(5000, null)
               ->withPoster()
               ->sortByKinopoiskRating();
        
        $results = $this->movieClient->searchMovies($filter, 1, 20);
        
        return array_map(function($movie) {
            return [
                'name' => $movie->name,
                'rating' => $movie->rating->kp,
                'votes' => $movie->votes->kp ?? 0,
                'genres' => implode(', ', array_map(fn($g) => $g->name, $movie->genres ?? [])),
                'director' => $this->getDirector($movie->persons ?? [])
            ];
        }, $results->docs);
    }
    
    private function getDirector(array $persons): ?string {
        foreach ($persons as $person) {
            if ($person->profession === 'режиссеры') {
                return $person->name;
            }
        }
        return null;
    }
}

// Использование
$analyzer = new MovieTrendAnalyzer($apiToken);

// Анализ трендов жанров 2020-2024
$trends = $analyzer->analyzeGenreTrends(2020, 2024);
foreach ($trends as $year => $genres) {
    echo "Топ жанры {$year}: " . implode(', ', array_keys($genres)) . "\n";
}

// Прорывы 2023 года
$breakthroughs = $analyzer->findBreakthroughMovies(2023, 8.5);
echo "\nПрорывы 2023 года:\n";
foreach ($breakthroughs as $movie) {
    echo "- {$movie['name']} ({$movie['rating']}/10) - {$movie['director']}\n";
}
```

## 🚨 Обработка ошибок

```php
use KinopoiskDev\Exceptions\KinopoiskDevException;
use KinopoiskDev\Exceptions\KinopoiskResponseException;

try {
    $movie = $movieClient->getMovieById(999999999);
} catch (KinopoiskResponseException $e) {
    // Ошибки API (401, 403, 404)
    switch ($e->getCode()) {
        case 401:
            echo "Неверный или отсутствующий токен API\n";
            break;
        case 403:
            echo "Превышен лимит запросов\n";
            break;
        case 404:
            echo "Фильм не найден\n";
            break;
    }
} catch (KinopoiskDevException $e) {
    // Другие ошибки клиента
    echo "Ошибка клиента: " . $e->getMessage() . "\n";
} catch (\JsonException $e) {
    // Ошибки парсинга JSON
    echo "Ошибка парсинга ответа: " . $e->getMessage() . "\n";
} catch (\Exception $e) {
    // Общие ошибки
    echo "Неожиданная ошибка: " . $e->getMessage() . "\n";
}
```

## 📈 Производительность

### Рекомендации по оптимизации

1. **Используйте кэширование** для повторяющихся запросов
2. **Ограничивайте количество полей** через selectFields
3. **Используйте пагинацию** для больших результатов
4. **Обрабатывайте ошибки лимитов** и реализуйте retry логику

```php
// Оптимизированный запрос
$filter = new MovieSearchFilter();
$filter->addFilter('selectFields', ['id', 'name', 'year', 'rating', 'poster'])
       ->withRatingBetween(8.0, 10.0)
       ->withPoster();

$results = $movieClient->searchMovies($filter, 1, 50);
```

## 🤝 Вклад в проект

Мы приветствуем ваш вклад в развитие проекта! Пожалуйста:

1. Форкните репозиторий
2. Создайте ветку для новой функции (`git checkout -b feature/amazing-feature`)
3. Зафиксируйте изменения (`git commit -m 'Add amazing feature'`)
4. Отправьте в ветку (`git push origin feature/amazing-feature`)
5. Откройте Pull Request

### Требования к коду

- Следуйте PSR-12 стандарту
- Добавляйте PHPDoc комментарии на русском языке
- Покрывайте код тестами
- Обновляйте документацию

## 📄 Лицензия

Этот проект лицензирован под лицензией MIT - см. файл [LICENSE](LICENSE) для деталей.

## 🔗 Полезные ссылки

- [Официальная документация API](https://kinopoiskdev.readme.io/)
- [Сайт Kinopoisk.dev](https://kinopoisk.dev/)
- [Telegram бот для получения токена](https://t.me/kinopoiskdev_bot)
- [GitHub репозиторий](https://github.com/your-username/kinopoisk-dev-client)

## 📞 Поддержка

Если у вас есть вопросы или проблемы:

1. Проверьте [существующие issues](https://github.com/your-username/kinopoisk-dev-client/issues)
2. Создайте новый issue с детальным описанием
3. Напишите в [Telegram чат](https://t.me/kinopoiskdev_chat)

---

**Создано с ❤️ для российского кинематографа**