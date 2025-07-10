# Класс `Movie`

> **Namespace:** `KinopoiskDev\Models`

Класс для представления фильма/сериала из API Kinopoisk.dev. Содержит полную информацию о фильме или сериале, включая базовые данные, рейтинги, участников, изображения, связанные произведения и другую метаинформацию. Используется для работы с детальной информацией о произведениях кинематографа.

---

## Описание

`readonly class Movie implements BaseModel`

- Представляет детальную модель фильма или сериала.
- Используется для получения, хранения и передачи информации о произведениях из API Kinopoisk.dev.
- Связан с множеством других моделей (жанры, страны, участники, изображения и др.).

---

## Конструктор

```
public function __construct(
    ?int $id = null,
    ?ExternalId $externalId = null,
    ?string $name = null,
    ?string $alternativeName = null,
    ?string $enName = null,
    ?array $names = [],
    ?MovieType $type = null,
    ?int $typeNumber = null,
    ?int $year = null,
    ?string $description = null,
    ?string $shortDescription = null,
    ?string $slogan = null,
    ?MovieStatus $status = null,
    ?array $facts = [],
    ?int $movieLength = null,
    ?RatingMpaa $ratingMpaa = null,
    ?int $ageRating = null,
    ?Rating $rating = null,
    ?Votes $votes = null,
    ?Logo $logo = null,
    ?ShortImage $poster = null,
    ?ShortImage $backdrop = null,
    ?VideoTypes $videos = null,
    array $genres = [],
    array $countries = [],
    array $persons = [],
    ?ReviewInfo $reviewInfo = null,
    ?array $seasonsInfo = [],
    ?CurrencyValue $budget = null,
    ?Fees $fees = null,
    ?Premiere $premiere = null,
    ?array $similarMovies = [],
    ?array $sequelsAndPrequels = [],
    ?Watchability $watchability = null,
    ?array $releaseYears = [],
    ?int $top10 = null,
    ?int $top250 = null,
    bool $isSeries = false,
    ?bool $ticketsOnSale = null,
    ?int $totalSeriesLength = null,
    ?int $seriesLength = null,
    ?array $audience = [],
    array $lists = [],
    ?Networks $networks = null,
    ?string $createdAt = null,
    ?string $updatedAt = null,
)
```

### Параметры конструктора (основные)
- `id` — Уникальный идентификатор фильма в системе Kinopoisk
- `externalId` — Внешние идентификаторы (IMDB, TMDB, KinopoiskHD)
- `name` — Название фильма на русском языке
- `alternativeName` — Альтернативное название фильма
- `enName` — Название фильма на английском языке
- `names` — Массив всех названий фильма на разных языках (`Name[]`)
- `type` — Тип произведения (`MovieType`)
- `year` — Год выпуска
- `description` — Полное описание сюжета
- `shortDescription` — Краткое описание
- `status` — Статус производства (`MovieStatus`)
- `facts` — Массив интересных фактов (`FactInMovie[]`)
- `movieLength` — Длительность фильма в минутах
- `ratingMpaa` — Рейтинг MPAA (`RatingMpaa`)
- `ageRating` — Возрастной рейтинг
- `rating` — Рейтинги от различных источников (`Rating`)
- `votes` — Количество голосов (`Votes`)
- `logo` — Логотип фильма (`Logo`)
- `poster` — Постер фильма (`ShortImage`)
- `backdrop` — Фоновое изображение (`ShortImage`)
- `videos` — Видеоматериалы (`VideoTypes`)
- `genres` — Жанры фильма (`ItemName[]`)
- `countries` — Страны производства (`ItemName[]`)
- `persons` — Участники фильма (`Person[]`)
- `reviewInfo` — Информация о рецензиях (`ReviewInfo`)
- `seasonsInfo` — Информация о сезонах (`SeasonInfo[]`)
- `budget` — Бюджет фильма (`CurrencyValue`)
- `fees` — Кассовые сборы (`Fees`)
- `premiere` — Даты премьер (`Premiere`)
- `similarMovies` — Похожие фильмы (`LinkedMovie[]`)
- `sequelsAndPrequels` — Сиквелы и приквелы (`LinkedMovie[]`)
- `watchability` — Информация о просмотрах (`Watchability`)
- `releaseYears` — Годы выпуска (для сериалов) (`YearRange[]`)
- `top10` — Позиция в топ-10
- `top250` — Позиция в топ-250
- `isSeries` — Является ли сериалом
- `ticketsOnSale` — Доступны ли билеты к покупке
- `totalSeriesLength` — Общая длительность всех серий
- `seriesLength` — Длительность одной серии
- `audience` — Информация об аудитории (`Audience[]`)
- `lists` — Списки, в которые входит фильм
- `networks` — Телевизионные сети (`Networks`)
- `createdAt` — Дата создания записи
- `updatedAt` — Дата последнего обновления

---

## Методы

### `public static function fromArray(array $data): Movie`
Фабричный метод для создания экземпляра класса Movie из массива данных, полученных от API Kinopoisk.dev. Безопасно обрабатывает отсутствующие значения и преобразует вложенные объекты в соответствующие классы.

**Параметры:**
- `array $data` — Массив данных о фильме от API

**Возвращает:**
- `Movie` — Новый экземпляр класса Movie

---

### `public function toArray(): array`
Преобразует объект в массив данных, совместимый с форматом API Kinopoisk.dev.

**Возвращает:**
- `array` — Массив с полными данными о фильме

---

### `public function getKinopoiskRating(): ?float`
Возвращает рейтинг фильма на Кинопоиске (от 0.0 до 10.0) или null, если не установлен.

---

### `public function getImdbRating(): ?float`
Возвращает рейтинг фильма на IMDB (от 0.0 до 10.0) или null, если не установлен.

---

### `public function getPosterUrl(): ?string`
Возвращает URL постера фильма или null, если не установлен.

---

### `public function getGenreNames(): array`
Возвращает массив строк с названиями жанров.

---

### `public function getCountryNames(): array`
Возвращает массив строк с названиями стран производства.

---

### `public function getImdbUrl(): ?string`
Возвращает URL страницы фильма в системе IMDB или null, если не доступен.

---

### `public function getTmdbUrl(): ?string`
Возвращает URL страницы фильма в системе TMDB или null, если не доступен.

---

## Связанные классы
- [`SearchMovie`](./Models_SearchMovie.md) — Для поисковых результатов фильмов
- [`LinkedMovie`](./Models_LinkedMovie.md) — Для связанных фильмов
- [`ExternalId`](./Models_ExternalId.md) — Для внешних идентификаторов
- [`Rating`](./Models_Rating.md) — Для рейтингов
- [`Votes`](./Models_Votes.md) — Для голосов
- [`Logo`](./Models_Logo.md) — Для логотипа
- [`ShortImage`](./Models_ShortImage.md) — Для изображений
- [`Person`](./Models_Person.md) — Для участников
- [`ReviewInfo`](./Models_ReviewInfo.md) — Для рецензий
- [`SeasonInfo`](./Models_SeasonInfo.md) — Для сезонов
- [`CurrencyValue`](./Models_CurrencyValue.md) — Для бюджета
- [`Fees`](./Models_Fees.md) — Для сборов
- [`Premiere`](./Models_Premiere.md) — Для премьер
- [`Watchability`](./Models_Watchability.md) — Для информации о просмотрах
- [`YearRange`](./Models_YearRange.md) — Для диапазона лет
- [`Audience`](./Models_Audience.md) — Для аудитории
- [`Networks`](./Models_Networks.md) — Для ТВ сетей

---

## Пример использования

```php
use KinopoiskDev\Models\Movie;

$data = [/* ...данные из API... */];
$movie = Movie::fromArray($data);

echo $movie->name;
print_r($movie->toArray());
```