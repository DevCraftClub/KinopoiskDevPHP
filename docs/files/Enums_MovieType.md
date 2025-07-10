# MovieType

**Путь:** src/Enums/MovieType.php  
**Пространство имён:** KinopoiskDev\Enums

---

## Описание

> Enum для типов фильмов
>
> Этот enum содержит все возможные типы фильмов, которые могут быть возвращены API Kinopoisk.dev

---

## Константы

| Константа | Значение | Описание |
|-----------|----------|----------|
| `MOVIE` | 'movie' | Фильм |
| `TV_SERIES` | 'tv-series' | Сериал |
| `CARTOON` | 'cartoon' | Мультфильм |
| `ANIME` | 'anime' | Аниме |
| `ANIMATED_SERIES` | 'animated-series' | Анимационный сериал |
| `TV_SHOW` | 'tv-show' | ТВ-шоу |

---

## Методы

### getLabel
```php
public function getLabel(): string
```
**Описание:** Возвращает человекочитаемое название типа фильма

**Возвращает:** `string` — Человекочитаемое название типа

**Примеры:**
```php
$type = MovieType::MOVIE;
echo $type->getLabel(); // "Фильм"

$type = MovieType::TV_SERIES;
echo $type->getLabel(); // "Сериал"

$type = MovieType::ANIME;
echo $type->getLabel(); // "Аниме"
```

---

## Примеры использования

```php
use KinopoiskDev\Enums\MovieType;

// Получение типа фильма
$movieType = MovieType::MOVIE;

// Отображение типа пользователю
echo "Тип: " . $movieType->getLabel();

// Проверка типа
if ($movieType === MovieType::MOVIE) {
    echo "Это полнометражный фильм";
} elseif ($movieType === MovieType::TV_SERIES) {
    echo "Это телевизионный сериал";
} elseif ($movieType === MovieType::ANIME) {
    echo "Это аниме";
}

// Использование в фильтрах
$filter = new MovieFilter();
$filter->type(MovieType::MOVIE->value);

// Фильтрация только фильмов (не сериалов)
if ($movieType === MovieType::MOVIE || $movieType === MovieType::CARTOON) {
    echo "Это фильм или мультфильм";
}
```

---

## Связи
- **Используется в:** [Movie](../Models/Movie.md), [MovieFilter](../Utils/MovieFilter.md)