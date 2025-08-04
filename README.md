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

## 📚 Документация

Подробная документация доступна в папке [`docs/`](docs/):

- **[Основная документация](docs/Kinopoisk.md)** - полное описание API
- **[Модели данных](docs/Models/)** - структуры данных и их свойства
- **[HTTP запросы](docs/Http/)** - методы для работы с различными сущностями
- **[Фильтры](docs/Filter/)** - система фильтрации и поиска
- **[Примеры использования](examples/)** - готовые примеры кода

### Основные компоненты

- **MovieRequests** - работа с фильмами и сериалами
- **PersonRequests** - работа с персонами (актеры, режиссеры)
- **ImageRequests** - работа с изображениями
- **ListRequests** - работа с коллекциями
- **KeywordRequests** - работа с ключевыми словами
- **ReviewRequests** - работа с отзывами
- **SeasonRequests** - работа с сезонами сериалов
- **StudioRequests** - работа со студиями

## 🔍 Система фильтрации

Библиотека предоставляет мощную систему фильтрации с fluent interface:

```php
use KinopoiskDev\Filter\MovieSearchFilter;

$filter = new MovieSearchFilter();

// Базовые фильтры
$filter->searchByName('Матрица')                    // Поиск по названию
       ->withYearBetween(2020, 2024)               // Диапазон лет
       ->withRatingBetween(7.0, 10.0)              // Диапазон рейтинга
       ->withIncludedGenres(['драма', 'триллер'])   // Включить жанры
       ->onlyMovies()                               // Только фильмы
       ->sortByKinopoiskRating();                   // Сортировка
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
KINOPOISK_API_TOKEN=ваш_токен_здесь
KINOPOISK_USE_CACHE=true
```

## 🛠️ Примеры

В папке [`examples/`](examples/) доступны готовые примеры:

- [Базовое использование](examples/01_basic_usage.php)
- [Расширенный поиск](examples/02_advanced_search.php)
- [Поиск персон](examples/03_person_search.php)
- [Сезоны и эпизоды](examples/04_seasons_and_episodes.php)
- [Изображения и медиа](examples/05_images_and_media.php)
- [Отзывы и рейтинги](examples/06_reviews_and_ratings.php)
- [Ключевые слова и студии](examples/07_keywords_and_studios.php)
- [Обработка ошибок](examples/08_error_handling.php)

## 🚨 Обработка ошибок

```php
use KinopoiskDev\Exceptions\KinopoiskDevException;
use KinopoiskDev\Exceptions\KinopoiskResponseException;

try {
    $movie = $movieClient->getMovieById(999999999);
} catch (KinopoiskResponseException $e) {
    // Ошибки API (401, 403, 404)
    echo "Ошибка API: " . $e->getMessage() . "\n";
} catch (KinopoiskDevException $e) {
    // Другие ошибки клиента
    echo "Ошибка клиента: " . $e->getMessage() . "\n";
}
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