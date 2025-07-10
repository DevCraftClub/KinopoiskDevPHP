# RatingMpaa

**Путь:** src/Enums/RatingMpaa.php  
**Пространство имён:** KinopoiskDev\Enums

---

## Описание

> Enum для рейтингов MPAA
>
> Этот enum содержит все возможные рейтинги MPAA, которые могут быть возвращены API Kinopoisk.dev

---

## Константы

| Константа | Значение | Описание |
|-----------|----------|----------|
| `G` | 'g' | General Audiences (без ограничений) |
| `PG` | 'pg' | Parental Guidance Suggested (рекомендуется присутствие родителей) |
| `PG13` | 'pg13' | Parents Strongly Cautioned (дети до 13 лет допускаются на фильм только с родителями) |
| `R` | 'r' | Restricted (до 17 лет обязательно присутствие взрослого) |
| `NC17` | 'nc17' | No One 17 & Under Admitted (лица до 18 лет не допускаются) |

---

## Методы

### getDescription
```php
public function getDescription(): string
```
**Описание:** Возвращает описание рейтинга MPAA

**Возвращает:** `string` — Подробное описание рейтинга

**Примеры:**
```php
$rating = RatingMpaa::G;
echo $rating->getDescription(); // "General Audiences (без ограничений)"

$rating = RatingMpaa::R;
echo $rating->getDescription(); // "Restricted (до 17 лет обязательно присутствие взрослого)"

$rating = RatingMpaa::NC17;
echo $rating->getDescription(); // "No One 17 & Under Admitted (лица до 18 лет не допускаются)"
```

---

## Примеры использования

```php
use KinopoiskDev\Enums\RatingMpaa;

// Получение рейтинга MPAA
$mpaaRating = RatingMpaa::PG13;

// Отображение рейтинга пользователю
echo "Рейтинг MPAA: " . $mpaaRating->value . " - " . $mpaaRating->getDescription();

// Проверка возрастных ограничений
switch ($mpaaRating) {
    case RatingMpaa::G:
        echo "Фильм подходит для всех возрастов";
        break;
    case RatingMpaa::PG:
        echo "Рекомендуется присутствие родителей";
        break;
    case RatingMpaa::PG13:
        echo "Дети до 13 лет только с родителями";
        break;
    case RatingMpaa::R:
        echo "До 17 лет с взрослым";
        break;
    case RatingMpaa::NC17:
        echo "Только для лиц 18+";
        break;
}

// Использование в фильтрах
$filter = new MovieFilter();
$filter->ratingMpaa(RatingMpaa::PG13->value);

// Проверка подходящих рейтингов для детей
$childFriendlyRatings = [RatingMpaa::G, RatingMpaa::PG];
if (in_array($mpaaRating, $childFriendlyRatings)) {
    echo "Фильм подходит для детей";
}
```

---

## Связи
- **Используется в:** [Movie](../Models/Movie.md), [MovieFilter](../Utils/MovieFilter.md)