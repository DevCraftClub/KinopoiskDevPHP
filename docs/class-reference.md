# Справочник классов KinoPoisk.dev PHP Client

## HTTP Клиенты (src/Http/)

### MovieRequests
**Назначение:** Работа с фильмами и сериалами
**Основные методы:**
- `getMovieById(int $movieId): Movie`
- `getRandomMovie(?MovieSearchFilter $filters = null): Movie`
- `searchMovies(?MovieSearchFilter $filters = null, int $page = 1, int $limit = 10): MovieDocsResponseDto`
- `getMovieAwards(?MovieSearchFilter $filters = null, int $page = 1, int $limit = 10): MovieAwardDocsResponseDto`
- `getPossibleValuesByField(string $field): array`
- `getLatestMovies(?int $year = null, int $page = 1, int $limit = 10): MovieDocsResponseDto`
- `getMoviesByGenre(string|array $genres, int $page = 1, int $limit = 10): MovieDocsResponseDto`
- `getMoviesByCountry(string|array $countries, int $page = 1, int $limit = 10): MovieDocsResponseDto`
- `getMoviesByYearRange(int $fromYear, int $toYear, int $page = 1, int $limit = 10): MovieDocsResponseDto`

### PersonRequests
**Назначение:** Работа с персонами (актеры, режиссеры)
**Основные методы:**
- `getPersonById(int $personId): Person`
- `searchPersons(?PersonSearchFilter $filters = null, int $page = 1, int $limit = 10): PersonDocsResponseDto`
- `getPersonAwards(?PersonSearchFilter $filters = null, int $page = 1, int $limit = 10): PersonAwardDocsResponseDto`

### ImageRequests
**Назначение:** Работа с изображениями
**Основные методы:**
- `getImages(?ImageSearchFilter $filters = null, int $page = 1, int $limit = 10): ImageDocsResponseDto`
- `getImagesByMovieId(int $movieId, string $type = null): ImageDocsResponseDto`

### ListRequests
**Назначение:** Работа с коллекциями фильмов
**Основные методы:**
- `getAllLists(?ListSearchFilter $filters = null, int $page = 1, int $limit = 10): ListDocsResponseDto`
- `getListBySlug(string $slug): Lists`

### KeywordRequests
**Назначение:** Работа с ключевыми словами
**Основные методы:**
- `searchKeywords(?KeywordSearchFilter $filters = null, int $page = 1, int $limit = 10): KeywordDocsResponseDto`
- `getKeywordById(int $keywordId): Keyword`

### StudioRequests
**Назначение:** Работа со студиями
**Основные методы:**
- `getStudios(?StudioSearchFilter $filters = null, int $page = 1, int $limit = 10): StudioDocsResponseDto`
- `getStudioById(int $studioId): Studio`

### ReviewRequests
**Назначение:** Работа с рецензиями
**Основные методы:**
- `getReviews(?ReviewSearchFilter $filters = null, int $page = 1, int $limit = 10): ReviewDocsResponseDto`

### SeasonRequests
**Назначение:** Работа с сезонами сериалов
**Основные методы:**
- `getSeasons(?SeasonSearchFilter $filters = null, int $page = 1, int $limit = 10): SeasonDocsResponseDto`

## Модели данных (src/Models/)

### Movie
**Назначение:** Представление фильма/сериала
**Основные свойства:**
- `int $id` - Уникальный идентификатор
- `string $name` - Название на русском
- `string $alternativeName` - Альтернативное название
- `int $year` - Год выпуска
- `Rating $rating` - Рейтинги
- `array $genres` - Жанры
- `array $countries` - Страны
- `array $persons` - Участники

**Основные методы:**
- `static fromArray(array $data): Movie`
- `toArray(): array`
- `getKinopoiskRating(): ?float`
- `getImdbRating(): ?float`
- `getPosterUrl(): ?string`
- `getGenreNames(): array`
- `getCountryNames(): array`

### Person
**Назначение:** Представление персоны
**Основные свойства:**
- `int $id` - Уникальный идентификатор
- `string $name` - Имя
- `string $enName` - Имя на английском
- `string $profession` - Профессия
- `array $movies` - Фильмы персоны

### Image
**Назначение:** Представление изображения
**Основные свойства:**
- `string $url` - URL изображения
- `string $previewUrl` - URL превью
- `int $height` - Высота
- `int $width` - Ширина

### Rating
**Назначение:** Рейтинги фильма
**Основные свойства:**
- `float $kp` - Рейтинг Кинопоиска
- `float $imdb` - Рейтинг IMDB
- `float $tmdb` - Рейтинг TMDB
- `float $filmCritics` - Рейтинг кинокритиков
- `float $russianFilmCritics` - Рейтинг российских кинокритиков

### Votes
**Назначение:** Количество голосов
**Основные свойства:**
- `int $kp` - Голоса Кинопоиска
- `int $imdb` - Голоса IMDB
- `int $tmdb` - Голоса TMDB

### ExternalId
**Назначение:** Внешние идентификаторы
**Основные свойства:**
- `string $imdb` - ID в IMDB
- `int $tmdb` - ID в TMDB
- `string $kpHD` - ID в KinopoiskHD

### Keyword
**Назначение:** Ключевое слово
**Основные свойства:**
- `int $id` - Уникальный идентификатор
- `string $title` - Название
- `array $movies` - Связанные фильмы

### Studio
**Назначение:** Студия
**Основные свойства:**
- `int $id` - Уникальный идентификатор
- `string $title` - Название
- `string $type` - Тип студии

### Lists
**Назначение:** Коллекция фильмов
**Основные свойства:**
- `string $slug` - Слаг коллекции
- `string $name` - Название
- `string $description` - Описание
- `array $movies` - Фильмы в коллекции

## Фильтры (src/Filter/)

### MovieSearchFilter
**Назначение:** Фильтрация фильмов
**Основные методы:**
- `searchByName(string $query): self`
- `year(int $year): self`
- `withYearBetween(int $fromYear, int $toYear): self`
- `withRatingBetween(float $minRating, float $maxRating): self`
- `withIncludedGenres(string|array $genres): self`
- `withExcludedGenres(string|array $genres): self`
- `withIncludedCountries(string|array $countries): self`
- `onlyMovies(): self`
- `onlySeries(): self`
- `sortByKinopoiskRating(): self`

### PersonSearchFilter
**Назначение:** Фильтрация персон
**Основные методы:**
- `searchByName(string $query): self`
- `withProfession(string $profession): self`
- `withAge(int $minAge, int $maxAge): self`

### KeywordSearchFilter
**Назначение:** Фильтрация ключевых слов
**Основные методы:**
- `search(string $query): self`
- `movieId(int $movieId): self`
- `onlyPopular(int $minMovies): self`
- `sortByTitle(): self`

### ImageSearchFilter
**Назначение:** Фильтрация изображений
**Основные методы:**
- `movieId(int $movieId): self`
- `type(string $type): self`

## Исключения (src/Exceptions/)

### KinopoiskDevException
**Назначение:** Базовое исключение библиотеки
**Расширяет:** Exception

### KinopoiskResponseException
**Назначение:** Исключения ответов API
**Расширяет:** KinopoiskDevException

### ValidationException
**Назначение:** Исключения валидации
**Расширяет:** KinopoiskDevException
**Основные методы:**
- `static forField(string $field, string $message, $value = null): self`

## Перечисления (src/Enums/)

### MovieType
**Значения:**
- `MOVIE` - фильм
- `TV_SERIES` - сериал
- `CARTOON` - мультфильм
- `ANIME` - аниме
- `ANIMATED_SERIES` - анимационный сериал

### MovieStatus
**Значения:**
- `FILMING` - снимается
- `PRE_PRODUCTION` - пре-продакшн
- `COMPLETED` - завершен
- `ANNOUNCED` - анонсирован

### PersonProfession
**Значения:**
- `ACTOR` - актер
- `DIRECTOR` - режиссер
- `PRODUCER` - продюсер
- `WRITER` - сценарист
- `OPERATOR` - оператор

### HttpStatusCode
**Значения:**
- `OK = 200`
- `UNAUTHORIZED = 401`
- `FORBIDDEN = 403`
- `NOT_FOUND = 404`

## Интерфейсы (src/Contracts/)

### CacheInterface
**Назначение:** Контракт для кэширования
**Методы:**
- `get(string $key): mixed`
- `set(string $key, mixed $value, int $ttl = 3600): bool`
- `delete(string $key): bool`
- `has(string $key): bool`
- `clear(): bool`

### HttpClientInterface
**Назначение:** Контракт для HTTP клиента
**Методы:**
- `send(RequestInterface $request): ResponseInterface`

### LoggerInterface
**Назначение:** Контракт для логирования
**Методы:**
- `debug(string $message, array $context = []): void`
- `info(string $message, array $context = []): void`
- `warning(string $message, array $context = []): void`
- `error(string $message, array $context = []): void`

## Сервисы (src/Services/)

### ValidationService
**Назначение:** Валидация данных
**Основные методы:**
- `validateApiToken(string $token): bool`
- `validateEmail(string $email): bool`
- `validateUrl(string $url): bool`

### CacheService
**Назначение:** Реализация кэширования
**Реализует:** CacheInterface
**Основан на:** Symfony Cache

### HttpService
**Назначение:** HTTP клиент
**Реализует:** HttpClientInterface
**Основан на:** Guzzle HTTP

## Утилиты (src/Utils/)

### DataManager
**Назначение:** Управление данными и их преобразование
**Основные методы:**
- `parseObjectArray(array $data, string $key, string $class, array $default = []): array`
- `parseObjectData(array $data, string $key, string $class): ?object`
- `getObjectsArray(?array $objects): array`

### MovieFilter
**Назначение:** Базовый функционал фильтрации фильмов
**Основные методы:**
- `addFilter(string $field, mixed $value, string $operator = 'eq'): void`
- `getFilters(): array`
- `isSeries(bool $isSeries): self`
- `ratingRange(float $min, float $max, string $field = 'kp'): self`

### SortManager
**Назначение:** Управление сортировкой
**Основные методы:**
- `addSort(string $field, string $direction = 'asc'): self`
- `getSorts(): array`

## Ответы API (src/Responses/)

### MovieDocsResponseDto
**Назначение:** Ответ со списком фильмов
**Свойства:**
- `array $docs` - Массив фильмов
- `int $total` - Общее количество
- `int $limit` - Лимит на странице
- `int $page` - Текущая страница
- `int $pages` - Общее количество страниц

### BaseDocsResponseDto
**Назначение:** Базовый класс для ответов с пагинацией
**Свойства:**
- `int $total`
- `int $limit`
- `int $page`
- `int $pages`

### ErrorResponseDto
**Назначение:** Ответ с ошибкой
**Свойства:**
- `string $message` - Сообщение об ошибке
- `int $statusCode` - Код ошибки