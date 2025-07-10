# Документация по файлам проекта KinoPoisk.dev PHP Client

---

## src/Kinopoisk.php
**Путь:** src/Kinopoisk.php  
**Назначение:** Главный класс клиента, базовая логика работы с API, кэш, логирование, обработка ошибок.  
**Основные методы:**
- `__construct()` — инициализация клиента
- `makeRequest()` — HTTP-запрос с кэшем
- `parseResponse()` — обработка ответа

---

## src/Http/

### MovieRequests.php
**Путь:** src/Http/MovieRequests.php  
**Назначение:** Работа с фильмами и сериалами (поиск, получение по ID, фильтрация, награды и т.д.).  
**Основные методы:**
- `getMovieById()`
- `getRandomMovie()`
- `searchMovies()`
- `getMovieAwards()`
- `getPossibleValuesByField()`
- `getLatestMovies()`
- `getMoviesByGenre()`
- `getMoviesByCountry()`
- `getMoviesByYearRange()`

### PersonRequests.php
**Путь:** src/Http/PersonRequests.php  
**Назначение:** Работа с персонами (актеры, режиссеры, поиск, получение по ID).

### ImageRequests.php
**Путь:** src/Http/ImageRequests.php  
**Назначение:** Работа с изображениями (поиск, получение по фильму).

### ListRequests.php
**Путь:** src/Http/ListRequests.php  
**Назначение:** Работа с коллекциями фильмов.

### KeywordRequests.php
**Путь:** src/Http/KeywordRequests.php  
**Назначение:** Работа с ключевыми словами.

### StudioRequests.php
**Путь:** src/Http/StudioRequests.php  
**Назначение:** Работа со студиями.

### ReviewRequests.php
**Путь:** src/Http/ReviewRequests.php  
**Назначение:** Работа с рецензиями.

### SeasonRequests.php
**Путь:** src/Http/SeasonRequests.php  
**Назначение:** Работа с сезонами сериалов.

---

## src/Models/

### Movie.php
**Путь:** src/Models/Movie.php  
**Назначение:** Модель фильма/сериала.  
**Основные свойства:** `id`, `name`, `year`, `rating`, `genres`, `countries`, `persons`, ...
**Основные методы:** `fromArray()`, `toArray()`, `getKinopoiskRating()`, `getImdbRating()`, `getPosterUrl()`, `getGenreNames()`, `getCountryNames()`

### Person.php
**Путь:** src/Models/Person.php  
**Назначение:** Модель персоны (актер, режиссер и т.д.).

### Image.php
**Путь:** src/Models/Image.php  
**Назначение:** Модель изображения.

### Rating.php
**Путь:** src/Models/Rating.php  
**Назначение:** Модель рейтингов (Кинопоиск, IMDB, TMDB и т.д.).

### Votes.php
**Путь:** src/Models/Votes.php  
**Назначение:** Модель количества голосов.

### ExternalId.php
**Путь:** src/Models/ExternalId.php  
**Назначение:** Внешние идентификаторы (IMDB, TMDB, KinopoiskHD).

### Keyword.php
**Путь:** src/Models/Keyword.php  
**Назначение:** Ключевое слово.

### Studio.php
**Путь:** src/Models/Studio.php  
**Назначение:** Студия.

### Lists.php
**Путь:** src/Models/Lists.php  
**Назначение:** Коллекция фильмов.

### ...
_Остальные модели: FactInMovie, FactInPerson, Review, ReviewInfo, Season, SeasonInfo, ShortImage, Spouses, BaseModel, BirthPlace, CurrencyValue, DeathPlace, Episode, AbstractBaseModel, Audience, PersonAward, PersonInMovie, PersonPlace, Premiere, MovieAward, MovieFromKeyword, MovieFromStudio, MovieInPerson, Name, NetworkItem, Networks, Nomination, NominationAward, ItemName, LinkedMovie, Logo, MeiliPersonEntity, Watchability, WatchabilityItem, YearRange._

---

## src/Filter/

### MovieSearchFilter.php
**Путь:** src/Filter/MovieSearchFilter.php  
**Назначение:** Фильтрация фильмов.  
**Основные методы:** `searchByName()`, `withYearBetween()`, `withRatingBetween()`, `withIncludedGenres()`, `withExcludedGenres()`, `withIncludedCountries()`, `onlyMovies()`, `onlySeries()`, `sortByKinopoiskRating()`

### PersonSearchFilter.php
**Путь:** src/Filter/PersonSearchFilter.php  
**Назначение:** Фильтрация персон.

### KeywordSearchFilter.php
**Путь:** src/Filter/KeywordSearchFilter.php  
**Назначение:** Фильтрация ключевых слов.

### ImageSearchFilter.php
**Путь:** src/Filter/ImageSearchFilter.php  
**Назначение:** Фильтрация изображений.

### ...
_Остальные фильтры: SortCriteria, StudioSearchFilter, ReviewSearchFilter, SeasonSearchFilter._

---

## src/Exceptions/

### KinopoiskDevException.php
**Путь:** src/Exceptions/KinopoiskDevException.php  
**Назначение:** Базовое исключение библиотеки.

### KinopoiskResponseException.php
**Путь:** src/Exceptions/KinopoiskResponseException.php  
**Назначение:** Исключения ответов API.

### ValidationException.php
**Путь:** src/Exceptions/ValidationException.php  
**Назначение:** Исключения валидации.

---

## src/Contracts/

### CacheInterface.php
**Путь:** src/Contracts/CacheInterface.php  
**Назначение:** Интерфейс для кэширования.  
**Основные методы:** `get()`, `set()`, `delete()`, `has()`, `clear()`, `getMultiple()`, `setMultiple()`

### HttpClientInterface.php
**Путь:** src/Contracts/HttpClientInterface.php  
**Назначение:** Интерфейс HTTP клиента.

### LoggerInterface.php
**Путь:** src/Contracts/LoggerInterface.php  
**Назначение:** Интерфейс логгера.

---

## src/Services/

### CacheService.php
**Путь:** src/Services/CacheService.php  
**Назначение:** Реализация кэша на Symfony Cache.

### HttpService.php
**Путь:** src/Services/HttpService.php  
**Назначение:** Реализация HTTP клиента (Guzzle).

### ValidationService.php
**Путь:** src/Services/ValidationService.php  
**Назначение:** Сервис валидации.

---

## src/Enums/

### MovieType.php
**Путь:** src/Enums/MovieType.php  
**Назначение:** Перечисление типов фильмов.

### MovieStatus.php
**Путь:** src/Enums/MovieStatus.php  
**Назначение:** Перечисление статусов фильмов.

### PersonProfession.php
**Путь:** src/Enums/PersonProfession.php  
**Назначение:** Перечисление профессий персон.

### ...
_Остальные перечисления: HttpStatusCode, PersonSex, RatingMpaa, SortDirection, SortField, FilterField, FilterOperator, StudioType._

---

## src/Utils/

### DataManager.php
**Путь:** src/Utils/DataManager.php  
**Назначение:** Преобразование и парсинг данных.

### MovieFilter.php
**Путь:** src/Utils/MovieFilter.php  
**Назначение:** Базовый фильтр фильмов.

### SortManager.php
**Путь:** src/Utils/SortManager.php  
**Назначение:** Управление сортировкой.

### FilterTrait.php
**Путь:** src/Utils/FilterTrait.php  
**Назначение:** Трейт для фильтрации.

---

## src/Responses/

### MovieDocsResponseDto.php
**Путь:** src/Responses/MovieDocsResponseDto.php  
**Назначение:** Ответ API со списком фильмов.

### BaseDocsResponseDto.php
**Путь:** src/Responses/BaseDocsResponseDto.php  
**Назначение:** Базовый ответ с пагинацией.

### ErrorResponseDto.php
**Путь:** src/Responses/ErrorResponseDto.php  
**Назначение:** Ответ с ошибкой.

### ...
_Остальные DTO: BaseResponseDto, Api/, Errors/._

---

## src/Attributes/

### Validation.php
**Путь:** src/Attributes/Validation.php  
**Назначение:** Атрибут для валидации.