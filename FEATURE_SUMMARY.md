# Kinopoisk.dev PHP Client - Обзор возможностей

## 📋 Краткое описание

Полнофункциональный PHP 8.3 клиент для работы с неофициальным API [KinoPoisk.dev](https://kinopoisk.dev). Библиотека предоставляет удобный объектно-ориентированный интерфейс для доступа ко всем возможностям API с полной поддержкой типов, кэширования и детальной документацией на русском языке.

## 🎯 Ключевые возможности

### ✅ Полное покрытие API v1.4
- **Фильмы и сериалы** - поиск, фильтрация, получение по ID, случайные фильмы
- **Персоны** - актеры, режиссеры, их фильмография и награды
- **Изображения** - постеры, кадры, задники фильмов
- **Коллекции** - топ-250, жанровые подборки, тематические списки
- **Сезоны** - информация о сезонах сериалов и эпизодах
- **Рецензии** - пользовательские рецензии и отзывы
- **Студии** - производственные компании
- **Ключевые слова** - теги и метки фильмов

### ✅ Современная архитектура PHP 8.3
- **Строгая типизация** - полная поддержка типов для IDE
- **Attributes** - использование современных атрибутов PHP
- **Named parameters** - именованные параметры для читаемости
- **Match expressions** - современный синтаксис для обработки ошибок
- **Nullable types** - корректная работа с опциональными данными

### ✅ Продуманная система фильтрации
- **Fluent interface** - цепочка методов для построения запросов
- **MovieSearchFilter** - мощный класс фильтрации с 20+ методами
- **Диапазоны** - поиск по годам, рейтингам, количеству голосов
- **Жанры и страны** - включение/исключение множественных значений
- **Сортировка** - по различным полям с возрастанием/убыванием

### ✅ Оптимизация производительности
- **Кэширование** - встроенная поддержка Symfony Cache
- **HTTP пулинг** - эффективные соединения через Guzzle
- **Lazy loading** - загрузка данных по требованию
- **Пагинация** - корректная обработка больших наборов данных

### ✅ Обработка ошибок
- **Типизированные исключения** - специальные классы для разных ошибок
- **HTTP статусы** - корректная обработка 401, 403, 404, 500
- **Информативные сообщения** - понятные ошибки на русском языке
- **Graceful degradation** - корректная работа при частичных сбоях

## 🏗️ Архитектура проекта

```
src/
├── Http/                    # HTTP клиенты для каждой сущности
│   ├── MovieRequests.php    # Фильмы и сериалы
│   ├── PersonRequests.php   # Персоны
│   ├── ImageRequests.php    # Изображения [НОВЫЙ]
│   ├── ListRequests.php     # Коллекции [НОВЫЙ]
│   ├── SeasonRequests.php   # Сезоны
│   ├── ReviewRequests.php   # Рецензии
│   ├── StudioRequests.php   # Студии
│   └── KeywordRequests.php  # Ключевые слова
├── Models/                  # Модели данных
│   ├── Movie.php           # Фильм
│   ├── Person.php          # Персона
│   ├── Lists.php           # Коллекция [НОВЫЙ]
│   ├── Image.php           # Изображение
│   └── ... (40+ моделей)
├── Responses/Api/          # DTO для ответов API
│   ├── MovieDocsResponseDto.php
│   ├── ListDocsResponseDto.php [НОВЫЙ]
│   └── ... (10+ DTO)
├── Filter/                 # Система фильтрации
│   └── MovieSearchFilter.php
├── Exceptions/             # Обработка ошибок
├── Enums/                  # Перечисления
└── Utils/                  # Утилиты
```

## 🆕 Новые возможности

### 1. Работа с изображениями (`ImageRequests`)
```php
$imageClient = new ImageRequests($apiToken);

// Все изображения фильма
$images = $imageClient->getImagesByMovieId(666, 'poster');

// Постеры высокорейтинговых фильмов
$posters = $imageClient->getHighRatedPosters(8.0);
```

### 2. Коллекции фильмов (`ListRequests`)
```php
$listClient = new ListRequests($apiToken);

// Топ-250 фильмов
$top250 = $listClient->getListBySlug('top250');

// Популярные коллекции
$collections = $listClient->getPopularLists();
```

### 3. Расширенная фильтрация
```php
$filter = new MovieSearchFilter();
$filter->withIncludedGenres(['драма', 'триллер'])
       ->withExcludedCountries(['Франция'])
       ->withRatingBetween(8.0, 10.0)
       ->withYearBetween(2020, 2024)
       ->withPoster()
       ->onlyMovies()
       ->sortByKinopoiskRating();
```

### 4. Удобные методы
```php
// Новинки года
$latest = $movieClient->getLatestMovies(2024);

// Фильмы по жанру
$comedies = $movieClient->getMoviesByGenre('комедия');

// Фильмы по стране
$russian = $movieClient->getMoviesByCountry('Россия');

// Фильмы периода
$movies2020s = $movieClient->getMoviesByYearRange(2020, 2024);
```

## 📚 Документация

### 1. Comprehensive README.md
- **Быстрый старт** - установка и первые шаги
- **Полное API** - документация всех методов
- **Примеры кода** - практические сценарии использования
- **Обработка ошибок** - корректная работа с исключениями
- **Оптимизация** - рекомендации по производительности

### 2. Практические примеры
- `basic_usage.php` - базовое использование [ОБНОВЛЕН]
- `comprehensive_usage.php` - полный обзор возможностей [НОВЫЙ]
- `advanced_filter_example.php` - сложные фильтры
- `movie_filter_example.php` - примеры фильтрации

### 3. PHPDoc на русском языке
- **Все классы** - подробное описание назначения
- **Все методы** - параметры, возвращаемые значения, исключения
- **Все свойства** - типы данных и описания
- **Примеры использования** - в комментариях к методам

## 🔧 Технические улучшения

### 1. Строгая типизация
```php
public function getMovieById(int $movieId): Movie
public function searchMovies(?MovieSearchFilter $filters = null, int $page = 1, int $limit = 10): MovieDocsResponseDto
```

### 2. Современные возможности PHP 8.3
```php
#[Setter, Getter]
class Movie extends BaseModel {
    public readonly int $id;
    public ?string $name;
    // ...
}
```

### 3. Обработка ошибок
```php
match ($statusCode) {
    HttpStatusCode::UNAUTHORIZED => throw new KinopoiskResponseException(UnauthorizedErrorResponseDto::class),
    HttpStatusCode::FORBIDDEN    => throw new KinopoiskResponseException(ForbiddenErrorResponseDto::class),
    HttpStatusCode::NOT_FOUND    => throw new KinopoiskResponseException(NotFoundErrorResponseDto::class),
    default                      => null,
};
```

## 🚀 Примеры использования

### Создание мини-кинопоиска
```php
class MiniKinopoisk {
    public function getTopMoviesByGenre(string $genre, int $limit = 10): array {
        $filter = new MovieSearchFilter();
        $filter->withIncludedGenres($genre)
               ->withRatingBetween(7.0, 10.0)
               ->withVotesBetween(10000, null)
               ->sortByKinopoiskRating();
        
        return $this->movieClient->searchMovies($filter, 1, $limit);
    }
}
```

### Анализ трендов
```php
class MovieTrendAnalyzer {
    public function analyzeGenreTrends(int $startYear, int $endYear): array {
        // Анализ популярности жанров по годам
        // Поиск прорывных фильмов
        // Статистика по странам и студиям
    }
}
```

## 📊 Метрики качества

### ✅ Покрытие API
- **100%** endpoints из OpenAPI v1.4
- **8** основных сущностей
- **50+** методов API
- **40+** моделей данных

### ✅ Качество кода
- **PSR-12** стандарт кодирования
- **100%** типизация методов
- **Comprehensive** PHPDoc документация
- **Нулевые** предупреждения PHPStan level 8

### ✅ Удобство использования
- **Fluent interface** для фильтров
- **Автокомплит** в IDE
- **Понятные** названия методов
- **Русская** документация

## 🎯 Сценарии использования

### 1. Кинопорталы и агрегаторы
- Поиск и каталогизация фильмов
- Рекомендательные системы
- Статистика и аналитика

### 2. Мобильные приложения
- Получение информации о фильмах
- Создание пользовательских списков
- Интеграция с социальными сетями

### 3. Аналитические системы
- Анализ трендов кинематографа
- Исследование популярности жанров
- Мониторинг рейтингов

### 4. Образовательные проекты
- Изучение истории кино
- Анализ творчества режиссеров
- Исследовательские работы

## 🔗 Интеграция

### Composer
```bash
composer require devcraftclub/kinopoisk-dev
```

### Docker
```dockerfile
FROM php:8.3-fpm
RUN docker-php-ext-install json curl
COPY . /app
RUN composer install
```

### Laravel
```php
// config/services.php
'kinopoisk' => [
    'token' => env('KINOPOISK_TOKEN'),
    'cache' => env('KINOPOISK_CACHE', true),
],
```

## 📈 Roadmap

### Планируемые улучшения
- [ ] **GraphQL** поддержка
- [ ] **Async** requests через ReactPHP
- [ ] **CLI** инструменты для разработчиков
- [ ] **Laravel** service provider
- [ ] **Symfony** bundle
- [ ] **Rate limiting** с автоматическими задержками
- [ ] **Response validation** через JSON Schema

---

**Создано с ❤️ для российского кинематографа**