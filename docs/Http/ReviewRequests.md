# ReviewRequests

**Описание:** Класс для API-запросов, связанных с рецензиями
Предоставляет методы для работы с рецензиями пользователей на фильмы и сериалы
через API Kinopoisk.dev. Включает поиск рецензий по различным критериям,
фильтрацию по типу (позитивные, негативные, нейтральные) и получение
статистики по рецензиям.
Основные возможности:
- Поиск рецензий по различным критериям
- Получение позитивных рецензий
- Получение негативных рецензий
- Фильтрация по автору, фильму, типу рецензии
- Пагинация результатов

**С версии:** 1.0.0

**Версия:** 1.0.0

**Пример:**
```php
$reviewRequests = new ReviewRequests('your-api-token');
// Поиск всех рецензий
$reviews = $reviewRequests->searchReviews();
// Поиск с фильтрами
$filter = new ReviewSearchFilter();
$filter->movieId(123)->type('Позитивный');
$reviews = $reviewRequests->searchReviews($filter, 1, 20);
// Получение позитивных рецензий
$positiveReviews = $reviewRequests->getPositiveReviews(1, 50);
// Получение негативных рецензий
$negativeReviews = $reviewRequests->getNegativeReviews(1, 30);
```

**См. также:**

* `\KinopoiskDev\Filter\ReviewSearchFilter`: Для настройки фильтрации
* `\KinopoiskDev\Models\Review`: Модель рецензии
* `\KinopoiskDev\Responses\Api\ReviewDocsResponseDto`: Ответ с рецензиями

## `getPositiveReviews()`

**Описание:** Получает положительные рецензии
Удобный метод для получения только позитивных рецензий.
Автоматически применяет фильтр по типу "Позитивный" и
возвращает рецензии с положительной оценкой фильма.

**С версии:** 1.0.0

**Параметры:**

* `$page` (int): Номер страницы результатов (по умолчанию: 1)
* `$limit` (int): Количество результатов на странице (по умолчанию: 10)

**Возвращает:** `ReviewDocsResponseDto` Положительные рецензии с пагинацией

**Исключения:**

* `KinopoiskDevException`: При ошибках API или валидации
* `KinopoiskResponseException`: При ошибках HTTP-запроса
* `\JsonException`: При ошибках парсинга JSON-ответа

**Пример:**
```php
// Получить позитивные рецензии
$positiveReviews = $reviewRequests->getPositiveReviews();
// Получить больше позитивных рецензий
$positiveReviews = $reviewRequests->getPositiveReviews(1, 100);
```

## `searchReviews()`

**Описание:** Поиск рецензий по различным критериям
Основной метод для поиска рецензий с использованием расширенной
фильтрации. Поддерживает фильтрацию по фильму, автору, типу
рецензии, дате создания и другим критериям. Включает валидацию
параметров и автоматическую пагинацию.

**С версии:** 1.0.0

**API Endpoint:** `/v1.4/review`

**Параметры:**

* `$filters` (ReviewSearchFilter|null): Объект фильтра для настройки критериев поиска
* `$page` (int): Номер страницы результатов (по умолчанию: 1)
* `$limit` (int): Количество результатов на странице (по умолчанию: 10, максимум: 250)

**Возвращает:** `ReviewDocsResponseDto` Результаты поиска с пагинацией

**Исключения:**

* `KinopoiskDevException`: При ошибках валидации или превышении лимитов
* `KinopoiskResponseException`: При ошибках HTTP-запроса
* `\JsonException`: При ошибках парсинга JSON-ответа

**Пример:**
```php
// Простой поиск всех рецензий
$reviews = $reviewRequests->searchReviews();
// Поиск с фильтрами
$filter = new ReviewSearchFilter();
$filter->movieId(123)->type('Позитивный')->author('user123');
$reviews = $reviewRequests->searchReviews($filter, 1, 50);
```

## `getNegativeReviews()`

**Описание:** Получает отрицательные рецензии
Удобный метод для получения только негативных рецензий.
Автоматически применяет фильтр по типу "Негативный" и
возвращает рецензии с отрицательной оценкой фильма.

**С версии:** 1.0.0

**Параметры:**

* `$page` (int): Номер страницы результатов (по умолчанию: 1)
* `$limit` (int): Количество результатов на странице (по умолчанию: 10)

**Возвращает:** `ReviewDocsResponseDto` Отрицательные рецензии с пагинацией

**Исключения:**

* `KinopoiskDevException`: При ошибках API или валидации
* `KinopoiskResponseException`: При ошибках HTTP-запроса
* `\JsonException`: При ошибках парсинга JSON-ответа

**Пример:**
```php
// Получить негативные рецензии
$negativeReviews = $reviewRequests->getNegativeReviews();
// Получить больше негативных рецензий
$negativeReviews = $reviewRequests->getNegativeReviews(1, 50);
```

