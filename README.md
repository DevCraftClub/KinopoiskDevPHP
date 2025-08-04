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
composer require devcraftclub/kinopoisk-dev-php
```

### Требования

- PHP >= 8.3
- Guzzle HTTP >= 7.0
- Токен API от [kinopoisk.dev](https://kinopoisk.dev)

## 🚀 Быстрый старт

### Получение токена

Для работы с API необходимо получить токен через Telegram бота:

1. Напишите боту [@kinopoiskdev_bot](https://t.me/kinopoiskdev_bot) в Telegram
2. Следуйте инструкциям бота для получения токена

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

### Автоматическая генерация документации

Проект включает в себя автоматический генератор документации, который создает полную документацию на основе PHPDoc комментариев.

#### Генерация документации

```bash
# Способ 1: Прямой вызов
php bin/generate_docs.php

# Способ 2: Через composer
composer docs
```

Документация будет создана в папке `docs/` и включает:

- **Классы** - полная документация всех классов с методами и свойствами
- **Интерфейсы** - документация интерфейсов и их методов
- **Enum'ы** - документация enum'ов со всеми cases и их значениями
- **Trait'ы** - документация trait'ов и их методов
- **PHPDoc теги** - поддержка всех стандартных PHPDoc тегов:
  - `@param`, `@return`, `@throws`
  - `@example`, `@see`, `@since`
  - `{@inheritDoc}` - наследование документации из родительских классов/интерфейсов
  - Сложные типы: `array<string, mixed>`, `int|null`, etc.

#### Структура документации

```
docs/
├── README.md                    # Главная страница с навигацией
├── Kinopoisk.md                # Основной класс Kinopoisk
├── Models/                     # Модели данных
│   ├── Movie.md
│   ├── Person.md
│   └── ...
├── Http/                       # HTTP клиенты
│   ├── MovieRequests.md
│   ├── PersonRequests.md
│   └── ...
├── Enums/                      # Enum'ы
│   ├── MovieType.md
│   ├── PersonProfession.md
│   └── ...
├── Contracts/                  # Интерфейсы
│   ├── CacheInterface.md
│   └── LoggerInterface.md
└── Utils/                      # Утилиты и trait'ы
    ├── FilterTrait.md
    ├── SortManager.md
    └── ...
```

#### Особенности генератора

- **Автоматическое наследование** - `{@inheritDoc}` наследует документацию из родительских классов/интерфейсов
- **Поддержка всех типов PHP** - классы, интерфейсы, enum'ы, trait'ы
- **Сложные типы** - правильное отображение `array<string, mixed>`, union типов
- **Многострочные описания** - сохранение форматирования в PHPDoc
- **Примеры кода** - поддержка `@example` тегов с подсветкой синтаксиса

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

### 🔄 Автоматические обновления

Проект использует **Dependabot** для автоматического обновления зависимостей:

- **Composer зависимости** - обновляются еженедельно по понедельникам
- **GitHub Actions** - обновляются еженедельно по понедельникам
- **Автоматическое слияние** - patch и minor обновления сливаются автоматически
- **Major обновления** - требуют ручного ревью для критических зависимостей

Dependabot создает Pull Request'ы с обновлениями зависимостей, которые:

- Автоматически проходят тесты
- Генерируют обновленную документацию
- Помечаются соответствующими лейблами
- Назначаются на команду разработки для ревью

## 📄 Лицензия

Этот проект лицензирован под лицензией MIT - см. файл [LICENSE](LICENSE) для деталей.

## 🔗 Полезные ссылки

- [Официальная документация API](https://kinopoiskdev.readme.io/)
- [Сайт Kinopoisk.dev](https://kinopoisk.dev/)
- [Telegram бот для получения токена](https://t.me/kinopoiskdev_bot)
- [GitHub репозиторий](https://github.com/DevCraftClub/KinopoiskDevPHP)

## 📞 Поддержка

Если у вас есть вопросы или проблемы:

1. Проверьте [существующие issues](https://github.com/DevCraftClub/KinopoiskDevPHP/issues)
2. Создайте новый issue с детальным описанием
3. Напишите в [Telegram чат](https://t.me/kinopoiskdev_chat)
