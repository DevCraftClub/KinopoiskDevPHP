# Model классы для работы с данными

## Описание

Пространство имен `KinopoiskDev\Models` содержит модели данных для представления различных сущностей API Kinopoisk.dev. Все модели наследуются от базовых классов и предоставляют типизированный доступ к данным.

## Базовые классы

### BaseModel

Абстрактный базовый класс для простых моделей.

```php
abstract class BaseModel
{
    public static function fromArray(array $data): static;
    public function toArray(): array;
}
```

### AbstractBaseModel

Расширенный базовый класс с дополнительной функциональностью.

```php
abstract class AbstractBaseModel extends BaseModel
{
    protected function parseArray(array $data, string $class): array;
    protected function parseObject(array $data, string $class): ?object;
}
```

## Основные модели

### Movie

Полная модель фильма со всеми данными.

```php
class Movie extends AbstractBaseModel
{
    public ?int $id;
    public ?string $name;
    public ?string $alternativeName;
    public ?string $enName;
    public ?string $type;
    public ?int $typeNumber;
    public ?int $year;
    public ?string $description;
    public ?string $shortDescription;
    public ?string $slogan;
    public ?string $status;
    public ?Rating $rating;
    public ?Votes $votes;
    public ?int $movieLength;
    public ?string $ratingMpaa;
    public ?int $ageRating;
    public ?array $genres;           // ItemName[]
    public ?array $countries;        // ItemName[]
    public ?array $persons;          // PersonInMovie[]
    public ?array $poster;           // ShortImage
    public ?array $backdrop;         // ShortImage
    public ?array $videos;           // Video[]
    public ?array $similarMovies;    // LinkedMovie[]
    public ?array $sequelsAndPrequels; // LinkedMovie[]
    public ?Watchability $watchability;
    public ?array $releaseYears;     // YearRange[]
    public ?int $top10;
    public ?int $top250;
    public ?int $ticketsOnSale;
    public ?int $totalSeriesLength;
    public ?int $seriesLength;
    public ?bool $isSeries;
    public ?Audience $audience;
    public ?array $facts;            // FactInMovie[]
    public ?string $imdbId;
    public ?string $kpId;
    public ?Networks $networks;
    public ?Fees $fees;
    public ?Premiere $premiere;
    public ?ExternalId $externalId;
}
```

### SearchMovie

Упрощенная модель для результатов поиска.

```php
class SearchMovie extends AbstractBaseModel
{
    public ?int $id;
    public ?string $name;
    public ?string $alternativeName;
    public ?string $enName;
    public ?string $type;
    public ?int $year;
    public ?string $description;
    public ?string $shortDescription;
    public ?int $movieLength;
    public ?array $names;
    public ?ExternalId $externalId;
    public ?Logo $logo;
    public ?ShortImage $poster;
    public ?Rating $rating;
    public ?array $genres;
    public ?array $countries;
    public ?array $releaseYears;
}
```

### Person

Модель персоны (актер, режиссер и т.д.).

```php
class Person extends AbstractBaseModel
{
    public ?int $id;
    public ?string $photo;
    public ?string $name;
    public ?string $enName;
    public ?string $description;
    public ?string $profession;
    public ?string $enProfession;
    public ?BirthPlace $birthPlace;
    public ?DeathPlace $deathPlace;
    public ?array $facts;        // FactInPerson[]
    public ?array $movies;       // MovieInPerson[]
    public ?array $birthday;
    public ?array $death;
    public ?int $age;
    public ?int $countAwards;
    public ?int $growth;
    public ?string $sex;
    public ?array $spouses;      // Spouses[]
}
```

### Review

Модель отзыва.

```php
class Review extends AbstractBaseModel
{
    public ?int $id;
    public ?int $movieId;
    public ?string $title;
    public ?string $type;
    public ?string $review;
    public ?string $date;
    public ?string $author;
    public ?string $userRating;
    public ?int $authorId;
    public ?ReviewInfo $reviewDislikes;
    public ?ReviewInfo $reviewLikes;
}
```

### Season

Модель сезона сериала.

```php
class Season extends AbstractBaseModel
{
    public ?int $movieId;
    public ?int $number;
    public ?int $episodesCount;
    public ?array $episodes;     // Episode[]
    public ?ShortImage $poster;
    public ?string $name;
    public ?string $enName;
    public ?string $description;
    public ?string $enDescription;
    public ?string $airDate;
    public ?int $year;
}
```

### Studio

Модель студии.

```php
class Studio extends AbstractBaseModel
{
    public ?string $id;
    public ?string $subType;
    public ?string $title;
    public ?string $type;
    public ?array $movies;       // MovieFromStudio[]
    public ?string $createdAt;
    public ?string $updatedAt;
}
```

### Image

Модель изображения.

```php
class Image extends AbstractBaseModel
{
    public ?string $movieId;
    public ?string $type;
    public ?string $language;
    public ?string $url;
    public ?string $previewUrl;
    public ?int $height;
    public ?int $width;
    public ?string $createdAt;
    public ?string $updatedAt;
}
```

### Keyword

Модель ключевого слова.

```php
class Keyword extends AbstractBaseModel
{
    public ?string $id;
    public ?string $title;
    public ?array $movies;       // MovieFromKeyword[]
    public ?string $createdAt;
    public ?string $updatedAt;
}
```

## Вспомогательные модели

### Rating

Рейтинги фильма.

```php
class Rating extends AbstractBaseModel
{
    public ?float $kp;
    public ?float $imdb;
    public ?float $filmCritics;
    public ?float $russianFilmCritics;
    public ?float $await;
}
```

### Votes

Голоса за фильм.

```php
class Votes extends AbstractBaseModel
{
    public ?int $kp;
    public ?int $imdb;
    public ?int $filmCritics;
    public ?int $russianFilmCritics;
    public ?int $await;
}
```

### PersonInMovie

Персона в фильме.

```php
class PersonInMovie extends AbstractBaseModel
{
    public ?int $id;
    public ?string $photo;
    public ?string $name;
    public ?string $enName;
    public ?string $description;
    public ?string $profession;
    public ?string $enProfession;
}
```

### MovieInPerson

Фильм в фильмографии персоны.

```php
class MovieInPerson extends AbstractBaseModel
{
    public ?int $id;
    public ?string $name;
    public ?string $alternativeName;
    public ?Rating $rating;
    public ?bool $general;
    public ?string $description;
    public ?string $enProfession;
}
```

### Episode

Эпизод сериала.

```php
class Episode extends AbstractBaseModel
{
    public ?int $number;
    public ?string $name;
    public ?string $enName;
    public ?string $description;
    public ?string $still;
    public ?string $airDate;
    public ?int $duration;
}
```

### ExternalId

Внешние идентификаторы.

```php
class ExternalId extends AbstractBaseModel
{
    public ?string $imdb;
    public ?string $tmdb;
    public ?string $kpHD;
}
```

### Premiere

Информация о премьерах.

```php
class Premiere extends AbstractBaseModel
{
    public ?string $world;
    public ?string $russia;
    public ?string $bluray;
    public ?string $dvd;
    public ?string $cinema;
}
```

### Fees

Сборы фильма.

```php
class Fees extends AbstractBaseModel
{
    public ?CurrencyValue $world;
    public ?CurrencyValue $russia;
    public ?CurrencyValue $usa;
}
```

### Awards

Награды (MovieAward и PersonAward).

```php
class MovieAward extends AbstractBaseModel
{
    public ?Movie $movie;
    public ?Nomination $nomination;
    public ?bool $winning;
    public ?string $createdAt;
    public ?string $updatedAt;
}

class PersonAward extends AbstractBaseModel
{
    public ?Person $person;
    public ?Nomination $nomination;
    public ?bool $winning;
    public ?int $personId;
    public ?Movie $movie;
    public ?string $createdAt;
    public ?string $updatedAt;
}
```

## Примеры использования

### Создание моделей из данных API

```php
use KinopoiskDev\Models\Movie;
use KinopoiskDev\Http\MovieRequests;

$movieApi = new MovieRequests($token);
$response = $movieApi->makeRequest('GET', '/movie/123');
$data = $movieApi->parseResponse($response);

// Создание модели из массива
$movie = Movie::fromArray($data);

// Доступ к данным
echo "Название: {$movie->name}\n";
echo "Год: {$movie->year}\n";
echo "Рейтинг КиноПоиск: {$movie->rating->kp}\n";
echo "Рейтинг IMDB: {$movie->rating->imdb}\n";

// Работа с вложенными данными
foreach ($movie->persons as $person) {
    if ($person->enProfession === 'director') {
        echo "Режиссер: {$person->name}\n";
    }
}

// Преобразование обратно в массив
$array = $movie->toArray();
```

### Работа с коллекциями

```php
use KinopoiskDev\Models\{Movie, Person};
use KinopoiskDev\Http\MovieRequests;

$movieApi = new MovieRequests($token);
$response = $movieApi->searchMovies($filter);

// Фильтрация фильмов
$highRatedMovies = array_filter(
    $response->docs,
    fn(Movie $movie) => $movie->rating->kp >= 8.0
);

// Группировка по годам
$moviesByYear = [];
foreach ($response->docs as $movie) {
    $moviesByYear[$movie->year][] = $movie;
}

// Извлечение уникальных жанров
$genres = [];
foreach ($response->docs as $movie) {
    foreach ($movie->genres ?? [] as $genre) {
        $genres[$genre->name] = true;
    }
}
$uniqueGenres = array_keys($genres);
```

### Создание собственных моделей

```php
use KinopoiskDev\Models\AbstractBaseModel;

class CustomMovie extends AbstractBaseModel
{
    public ?int $id;
    public ?string $title;
    public ?float $userRating;
    public ?array $customTags;
    
    public function isHighRated(): bool
    {
        return $this->userRating >= 8.0;
    }
    
    public function addTag(string $tag): void
    {
        $this->customTags[] = $tag;
    }
}
```

### Валидация данных модели

```php
use KinopoiskDev\Models\Movie;
use KinopoiskDev\Exceptions\ValidationException;

class MovieValidator
{
    public function validate(Movie $movie): void
    {
        $errors = [];
        
        if (empty($movie->name)) {
            $errors['name'] = 'Название фильма обязательно';
        }
        
        if ($movie->year < 1895 || $movie->year > date('Y') + 5) {
            $errors['year'] = 'Некорректный год выпуска';
        }
        
        if ($movie->rating && ($movie->rating->kp < 0 || $movie->rating->kp > 10)) {
            $errors['rating'] = 'Рейтинг должен быть от 0 до 10';
        }
        
        if (!empty($errors)) {
            throw ValidationException::withErrors($errors);
        }
    }
}
```

### Сериализация моделей

```php
use KinopoiskDev\Models\Movie;

class MovieSerializer
{
    public function toJson(Movie $movie): string
    {
        return json_encode($movie->toArray(), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
    
    public function toCsv(array $movies): string
    {
        $csv = "ID,Название,Год,Рейтинг\n";
        
        foreach ($movies as $movie) {
            $csv .= sprintf(
                "%d,\"%s\",%d,%.1f\n",
                $movie->id,
                str_replace('"', '""', $movie->name),
                $movie->year,
                $movie->rating->kp ?? 0
            );
        }
        
        return $csv;
    }
}
```

## Особенности

1. **Nullable свойства** - Все свойства могут быть null
2. **Типизация** - Строгая типизация всех свойств
3. **Вложенность** - Поддержка вложенных объектов
4. **Массивы объектов** - Автоматический парсинг массивов
5. **Гибкость** - Легко расширяемые модели

## Связанные компоненты

- [AbstractBaseModel](AbstractBaseModel.md) - Базовый класс
- [Http классы](../http/) - Получают данные для моделей
- [Responses](../responses/) - Содержат массивы моделей
- [Utils](../utils/) - Утилиты для работы с данными

## Требования

- PHP 8.3+
- JSON расширение