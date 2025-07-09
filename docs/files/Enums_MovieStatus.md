# MovieStatus

**Путь:** src/Enums/MovieStatus.php  
**Пространство имён:** KinopoiskDev\Enums

---

## Описание

> Enum для статусов фильмов
>
> Этот enum содержит все возможные статусы фильмов, которые могут быть возвращены API Kinopoisk.dev

---

## Константы

| Константа | Значение | Описание |
|-----------|----------|----------|
| `FILMING` | 'filming' | В производстве |
| `PRE_PRODUCTION` | 'pre-production' | Пре-продакшн |
| `COMPLETED` | 'completed' | Завершен |
| `ANNOUNCED` | 'announced' | Анонсирован |
| `POST_PRODUCTION` | 'post-production' | Пост-продакшн |

---

## Методы

### getLabel
```php
public function getLabel(): string
```
**Описание:** Возвращает человекочитаемое название статуса фильма

**Возвращает:** `string` — Человекочитаемое название статуса

**Примеры:**
```php
$status = MovieStatus::FILMING;
echo $status->getLabel(); // "В производстве"

$status = MovieStatus::COMPLETED;
echo $status->getLabel(); // "Завершен"

$status = MovieStatus::ANNOUNCED;
echo $status->getLabel(); // "Анонсирован"
```

---

## Примеры использования

```php
use KinopoiskDev\Enums\MovieStatus;

// Получение статуса фильма
$movieStatus = MovieStatus::FILMING;

// Отображение статуса пользователю
echo "Статус фильма: " . $movieStatus->getLabel();

// Проверка статуса
if ($movieStatus === MovieStatus::COMPLETED) {
    echo "Фильм уже вышел в прокат";
} elseif ($movieStatus === MovieStatus::ANNOUNCED) {
    echo "Фильм только анонсирован";
} elseif ($movieStatus === MovieStatus::FILMING) {
    echo "Фильм находится в производстве";
}

// Использование в фильтрах
$filter = new MovieFilter();
$filter->status(MovieStatus::COMPLETED->value);
```

---

## Связи
- **Используется в:** [Movie](../Models/Movie.md), [MovieFilter](../Utils/MovieFilter.md)