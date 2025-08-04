# SeasonRequests

**Описание:** Класс для API-запросов, связанных с сезонами
Предоставляет методы для работы с сезонами сериалов через API Kinopoisk.dev.
Включает получение информации о сезонах, их эпизодах, поиск по различным
критериям и фильтрацию. Поддерживает работу с многосезонными сериалами.
Основные возможности:
- Получение сезона по ID
- Получение всех сезонов сериала
- Поиск сезонов по различным критериям
- Получение сезона по номеру и ID фильма
- Фильтрация по номеру сезона, количеству эпизодов
- Пагинация результатов

**С версии:** 1.0.0

**Версия:** 1.0.0

**Пример:**
```php
$seasonRequests = new SeasonRequests('your-api-token');
// Получение сезона по ID
$season = $seasonRequests->getSeasonById(123);
// Получение всех сезонов сериала
$seasons = $seasonRequests->getSeasonsForMovie(456);
// Поиск сезонов с фильтрами
$filter = new SeasonSearchFilter();
$filter->number(1)->episodesCount(10, 20);
$seasons = $seasonRequests->searchSeasons($filter, 1, 20);
// Получение конкретного сезона по номеру
$season = $seasonRequests->getSeasonByNumber(456, 2);
```

**См. также:**

* `\KinopoiskDev\Filter\SeasonSearchFilter`: Для настройки фильтрации
* `\KinopoiskDev\Models\Season`: Модель сезона
* `\KinopoiskDev\Responses\Api\SeasonDocsResponseDto`: Ответ с сезонами

## `getSeasonById()`

**Описание:** Получает сезон по его ID
Выполняет запрос к API для получения полной информации о сезоне
по его уникальному идентификатору. Возвращает объект Season
со всеми доступными данными: названием, номером, эпизодами,
датами выхода и другими метаданными.

**С версии:** 1.0.0

**API Endpoint:** `/v1.4/season/{id}`

**Параметры:**

* `$seasonId` (int): Уникальный идентификатор сезона в системе Kinopoisk

**Возвращает:** `Season` Сезон со всеми доступными данными

**Исключения:**

* `KinopoiskDevException`: При ошибках API или проблемах с сетью
* `KinopoiskResponseException`: При ошибках HTTP-запроса (401, 403, 404)
* `\JsonException`: При ошибках парсинга JSON-ответа

**Пример:**
```php
$season = $seasonRequests->getSeasonById(123);
echo $season->name; // Название сезона
echo $season->number; // Номер сезона
echo count($season->episodes); // Количество эпизодов
```

## `getSeasonsForMovie()`

**Описание:** Получает сезоны для определенного фильма/сериала

**Параметры:**

* `$movieId` (int): Идентификатор фильма/сериала
* `$page` (int): Номер страницы
* `$limit` (int): Результатов на странице

**Возвращает:** `SeasonDocsResponseDto` Сезоны для фильма/сериала

**Исключения:**

* `KinopoiskDevException`: При ошибках API
* `\JsonException`: При ошибках парсинга JSON

## `searchSeasons()`

**Описание:** Ищет сезоны по различным критериям

**API Endpoint:** `/v1.4/season`

**Параметры:**

* `$filters` (SeasonSearchFilter|null): Объект фильтра для поиска
* `$page` (int): Номер страницы (по умолчанию: 1)
* `$limit` (int): Количество результатов на странице (по умолчанию: 10)

**Возвращает:** `SeasonDocsResponseDto` Результаты поиска с пагинацией

**Исключения:**

* `KinopoiskDevException`: При ошибках API
* `\JsonException|\KinopoiskDev\Exceptions\KinopoiskResponseException`: При ошибках парсинга JSON

## `getSeasonByNumber()`

**Описание:** Получает сезон по ID фильма и номеру сезона

**Параметры:**

* `$movieId` (int): Идентификатор фильма/сериала
* `$seasonNumber` (int): Номер сезона

**Возвращает:** `Season|null` Сезон или null если не найден

**Исключения:**

* `KinopoiskDevException`: При ошибках API
* `\JsonException|\KinopoiskDev\Exceptions\KinopoiskResponseException`: При ошибках парсинга JSON

