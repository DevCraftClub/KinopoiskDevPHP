# Movie

**Файл:** `src/Models/Movie.php`

## Описание

`Movie` — основная модель, представляющая фильм в системе. Содержит все основные свойства, связанные с фильмом, включая идентификаторы, названия, рейтинги, даты, связанные сущности и т.д. Используется для передачи и хранения информации о фильмах, получаемой из API.

## Свойства

- `id` — уникальный идентификатор фильма
- `name` — название фильма
- `alternativeName` — альтернативное название
- `enName` — английское название
- `type` — тип фильма (`MovieType` enum)
- `year` — год выпуска
- `description` — описание
- `shortDescription` — краткое описание
- `slogan` — слоган
- `status` — статус фильма (`MovieStatus` enum)
- `rating` — рейтинг (`Rating`)
- `votes` — голоса (`Votes`)
- `movieLength` — длительность (в минутах)
- `ageRating` — возрастной рейтинг
- `poster` — постер (`Image`)
- `backdrop` — фон (`Image`)
- `genres` — жанры (массив строк)
- `countries` — страны (массив строк)
- `persons` — связанные персоны (`PersonInMovie[]`)
- `facts` — факты о фильме (`FactInMovie[]`)
- `lists` — списки (`Lists[]`)
- `externalId` — внешние идентификаторы (`ExternalId`)
- `fees` — сборы (`Fees`)
- `premiere` — премьера (`Premiere`)
- `similarMovies` — похожие фильмы (`LinkedMovie[]`)
- `sequelsAndPrequels` — сиквелы и приквелы (`LinkedMovie[]`)
- `videos` — видео (`Video[]`)
- `watchability` — доступность к просмотру (`Watchability`)
- `releaseYears` — годы релиза (`YearRange[]`)
- `logo` — логотип (`Logo`)
- `isSeries` — является ли сериалом (bool)
- `seriesLength` — длительность серии (int|null)
- `totalSeriesLength` — общая длительность (int|null)
- `seasonsInfo` — информация о сезонах (`SeasonInfo[]`)
- `updatedAt` — дата обновления

## Основные методы

- Конструктор: принимает массив данных и инициализирует все свойства.
- Геттеры для всех свойств.
- Методы для преобразования в массив/JSON.

## Пример использования

```php
use KinopoiskDev\Models\Movie;

$movie = new Movie($dataArray);
echo $movie->name;
```

## Связи с другими классами

- Использует: `Rating`, `Votes`, `Image`, `PersonInMovie`, `FactInMovie`, `Lists`, `ExternalId`, `Fees`, `Premiere`, `LinkedMovie`, `Video`, `Watchability`, `YearRange`, `Logo`, `SeasonInfo` и др.
- Enum-свойства: `MovieType`, `MovieStatus`

## Особенности реализации

- Все свойства объявлены как readonly, что обеспечивает неизменяемость экземпляра после создания.
- Поддерживает вложенные объекты и массивы связанных сущностей.
- Использует строгую типизацию для всех свойств.