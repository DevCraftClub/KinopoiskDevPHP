# Movie

**Путь:** src/Models/Movie.php  
**Пространство имён:** KinopoiskDev\Models  
**Реализует:** BaseModel  
**Использует:**
- [MovieStatus](../Enums/MovieStatus.md)
- [MovieType](../Enums/MovieType.md)
- [RatingMpaa](../Enums/RatingMpaa.md)
- [ExternalId](../Models/ExternalId.md)
- [Name](../Models/Name.md)
- [FactInMovie](../Models/FactInMovie.md)
- [Rating](../Models/Rating.md)
- [Votes](../Models/Votes.md)
- [Logo](../Models/Logo.md)
- [ShortImage](../Models/ShortImage.md)
- [VideoTypes](../Models/VideoTypes.md)
- [ItemName](../Models/ItemName.md)
- [Person](../Models/Person.md)
- [ReviewInfo](../Models/ReviewInfo.md)
- [SeasonInfo](../Models/SeasonInfo.md)
- [CurrencyValue](../Models/CurrencyValue.md)
- [Fees](../Models/Fees.md)
- [Premiere](../Models/Premiere.md)
- [LinkedMovie](../Models/LinkedMovie.md)
- [Watchability](../Models/Watchability.md)
- [YearRange](../Models/YearRange.md)
- [Audience](../Models/Audience.md)
- [Networks](../Models/Networks.md)

---

## Описание

> Класс для представления фильма/сериала из API Kinopoisk.dev
>
> Представляет полную информацию о фильме или сериале, включая базовые данные, рейтинги, участников, изображения, связанные произведения и другую метаинформацию. Используется для работы с детальной информацией о произведениях кинематографа.
>
> @package KinopoiskDev\Models
> @since   1.0.0
> @author  Maxim Harder
> @version 1.0.0
> @see     SearchMovie — для поисковых результатов фильмов
> @see     LinkedMovie — для связанных фильмов
> @see     ExternalId — для внешних идентификаторов
> @link    https://kinopoiskdev.readme.io/reference/moviecontroller_findonev1_4

---

## Свойства конструктора

- `int|null $id` — Уникальный идентификатор фильма в системе Kinopoisk
- `ExternalId|null $externalId` — Внешние идентификаторы (IMDB, TMDB, KinopoiskHD)
- `string|null $name` — Название фильма на русском языке
- `string|null $alternativeName` — Альтернативное название фильма
- `string|null $enName` — Название фильма на английском языке
- `Name[]|null $names` — Массив всех названий фильма на разных языках
- `MovieType|null $type` — Тип произведения (фильм, сериал, мультфильм и т.д.)
- `int|null $typeNumber` — Числовой код типа произведения
- `int|null $year` — Год выпуска произведения
- `string|null $description` — Полное описание сюжета фильма
- `string|null $shortDescription` — Краткое описание фильма
- `string|null $slogan` — Рекламный слоган фильма
- `MovieStatus|null $status` — Статус производства (анонс, производство, вышел и т.д.)
- `FactInMovie[]|null $facts` — Массив интересных фактов о фильме
- `int|null $movieLength` — Длительность фильма в минутах
- `RatingMpaa|null $ratingMpaa` — Рейтинг MPAA (G, PG, PG-13, R, NC-17)
- `int|null $ageRating` — Возрастной рейтинг (6+, 12+, 16+, 18+)
- `Rating|null $rating` — Рейтинги от различных источников
- `Votes|null $votes` — Количество голосов по рейтингам
- `Logo|null $logo` — Логотип фильма
- `ShortImage|null $poster` — Постер фильма
- `ShortImage|null $backdrop` — Фоновое изображение фильма
- `VideoTypes|null $videos` — Видеоматериалы (трейлеры, тизеры)
- `ItemName[] $genres` — Жанры фильма
- `ItemName[] $countries` — Страны производства
- `Person[] $persons` — Участники фильма (актеры, режиссеры и т.д.)
- `ReviewInfo|null $reviewInfo` — Информация о рецензиях
- `SeasonInfo[] $seasonsInfo` — Информация о сезонах (для сериалов)
- `CurrencyValue|null $budget` — Бюджет фильма
- `Fees|null $fees` — Кассовые сборы
- `Premiere|null $premiere` — Даты премьер в разных странах
- `LinkedMovie[]|null $similarMovies` — Похожие фильмы
- `LinkedMovie[]|null $sequelsAndPrequels` — Сиквелы и приквелы
- `Watchability|null $watchability` — Информация о просмотрах
- `YearRange[]|null $releaseYears` — Годы выпуска (для сериалов)
- `int|null $top10` — Позиция в топ-10 (если есть)
- `int|null $top250` — Позиция в топ-250 (если есть)
- `bool $isSeries` — Является ли произведение сериалом
- `bool|null $ticketsOnSale` — Доступны ли билеты к покупке
- `int|null $totalSeriesLength` — Общая длительность всех серий
- `int|null $seriesLength` — Длительность одной серии
- `Audience[] $audience` — Информация об аудитории
- `array $lists` — Списки, в которые входит фильм
- `Networks|null $networks` — Телевизионные сети
- `string|null $createdAt` — Дата создания записи
- `string|null $updatedAt` — Дата последнего обновления записи

---

## Методы

### fromArray
```php
public static function fromArray(array $data): Movie
```
**Описание:** Фабричный метод для создания экземпляра класса Movie из массива данных, полученных от API Kinopoisk.dev. Безопасно обрабатывает отсутствующие значения и преобразует вложенные объекты в соответствующие классы.

### toArray
```php
public function toArray(): array
```
**Описание:** Преобразует объект в массив данных, совместимый с форматом API Kinopoisk.dev. Используется для сериализации данных при отправке запросов к API или для экспорта данных.

### getKinopoiskRating
```php
public function getKinopoiskRating(): ?float
```
**Описание:** Возвращает рейтинг фильма на Кинопоиске (от 0.0 до 10.0) или null, если не установлен.

### getImdbRating
```php
public function getImdbRating(): ?float
```
**Описание:** Возвращает рейтинг фильма на IMDB (от 0.0 до 10.0) или null, если не установлен.

### getPosterUrl
```php
public function getPosterUrl(): ?string
```
**Описание:** Возвращает URL-адрес постера фильма или null, если не установлен.

### getGenreNames
```php
public function getGenreNames(): array
```
**Описание:** Возвращает массив строк с названиями жанров фильма.

### getCountryNames
```php
public function getCountryNames(): array
```
**Описание:** Возвращает массив строк с названиями стран производства.

### getImdbUrl
```php
public function getImdbUrl(): ?string
```
**Описание:** Возвращает URL страницы фильма в системе IMDB или null, если не доступен.

### getTmdbUrl
```php
public function getTmdbUrl(): ?string
```
**Описание:** Возвращает URL страницы фильма в системе TMDB или null, если не доступен.

---

## Примеры использования

```php
use KinopoiskDev\Models\Movie;

// Создание объекта из массива данных API
$movie = Movie::fromArray($apiData);

// Получение рейтинга Кинопоиска
$kpRating = $movie->getKinopoiskRating();

// Получение жанров
$genres = $movie->getGenreNames();

// Получение URL постера
$posterUrl = $movie->getPosterUrl();
```

---

## Связи
- **Реализует:** BaseModel
- **Использует:** MovieStatus, MovieType, RatingMpaa, ExternalId, Name, FactInMovie, Rating, Votes, Logo, ShortImage, VideoTypes, ItemName, Person, ReviewInfo, SeasonInfo, CurrencyValue, Fees, Premiere, LinkedMovie, Watchability, YearRange, Audience, Networks
- **Связан с:** SearchMovie, LinkedMovie, ExternalId (см. @see в PHPDoc)