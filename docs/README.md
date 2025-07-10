# Документация KinoPoisk.dev PHP Client

Данная папка содержит полную документацию для библиотеки KinoPoisk.dev PHP Client.

## Содержание документации

### 📋 [api-documentation.md](./api-documentation.md)
**Полная API документация**

Основной документ, содержащий:
- Введение в проект и его возможности
- Архитектуру проекта
- Подробное описание всех компонентов
- API Reference с примерами кода
- Документацию по моделям данных
- Руководство по фильтрам и поиску
- Обработку ошибок и исключений
- Примеры использования

Рекомендуется для:
- Изучения всех возможностей библиотеки
- Глубокого понимания архитектуры
- Поиска конкретных методов и их параметров
- Изучения примеров кода

### 📚 [class-reference.md](./class-reference.md)
**Краткий справочник классов**

Компактный справочник, содержащий:
- Список всех классов с их назначением
- Основные методы каждого класса
- Краткое описание свойств
- Структурированное разделение по категориям

Рекомендуется для:
- Быстрого поиска нужного класса
- Понимания структуры проекта
- Справочной информации во время разработки
- Ориентирования в коде

## Структура проекта

```
docs/
├── README.md                 # Этот файл - описание документации
├── api-documentation.md      # Полная API документация
└── class-reference.md        # Краткий справочник классов
```

## Как пользоваться документацией

### Для начинающих
1. Начните с [api-documentation.md](./api-documentation.md)
2. Изучите раздел "Введение" и "Архитектура проекта"
3. Ознакомьтесь с примерами в разделе "Примеры использования"
4. Используйте [class-reference.md](./class-reference.md) как справочник

### Для опытных разработчиков
1. Используйте [class-reference.md](./class-reference.md) для быстрого поиска
2. Обращайтесь к [api-documentation.md](./api-documentation.md) за детальной информацией
3. Изучите раздел "Фильтры и поиск" для сложных запросов

### Для понимания архитектуры
1. Изучите раздел "Архитектура проекта" в [api-documentation.md](./api-documentation.md)
2. Ознакомьтесь с разделами "Интерфейсы и контракты" и "Сервисы"
3. Используйте [class-reference.md](./class-reference.md) для понимания связей между классами

## Основные компоненты

### 🎬 HTTP Клиенты
- **MovieRequests** - работа с фильмами и сериалами
- **PersonRequests** - работа с персонами
- **ImageRequests** - работа с изображениями
- **ListRequests** - работа с коллекциями
- **KeywordRequests** - работа с ключевыми словами
- **StudioRequests** - работа со студиями
- **ReviewRequests** - работа с рецензиями
- **SeasonRequests** - работа с сезонами

### 📊 Модели данных
- **Movie** - основная модель фильма/сериала
- **Person** - модель персоны
- **Image** - модель изображения
- **Rating** - модель рейтингов
- **Votes** - модель голосов

### 🔍 Фильтры
- **MovieSearchFilter** - фильтрация фильмов
- **PersonSearchFilter** - фильтрация персон
- **KeywordSearchFilter** - фильтрация ключевых слов
- **ImageSearchFilter** - фильтрация изображений

### ⚙️ Сервисы
- **CacheService** - кэширование
- **HttpService** - HTTP запросы
- **ValidationService** - валидация данных

## Быстрый старт

```php
<?php

require_once 'vendor/autoload.php';

use KinopoiskDev\Http\MovieRequests;
use KinopoiskDev\Filter\MovieSearchFilter;

// Инициализация
$apiToken = 'YOUR_API_TOKEN';
$movieClient = new MovieRequests($apiToken);

// Получение фильма по ID
$movie = $movieClient->getMovieById(666);
echo "Фильм: {$movie->name} ({$movie->year})\n";

// Поиск с фильтрами
$filter = new MovieSearchFilter();
$filter->withIncludedGenres('драма')
       ->withYearBetween(2020, 2024)
       ->sortByKinopoiskRating();

$results = $movieClient->searchMovies($filter, 1, 10);
```

## Полезные ссылки

- [Официальная документация API Kinopoisk.dev](https://kinopoisk.dev)
- [Telegram бот для получения токена](https://t.me/kinopoiskdev_bot)
- [Composer package](https://packagist.org/packages/devcraftclub/kinopoisk-dev)

## Обратная связь

При обнаружении ошибок в документации или предложениях по улучшению, пожалуйста:
1. Создайте issue в репозитории проекта
2. Укажите конкретную страницу документации
3. Опишите проблему или предложение

---

**Версия документации:** 1.0.0  
**Совместимость:** PHP 8.3+  
**API версия:** v1.4