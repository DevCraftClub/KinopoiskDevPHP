# PersonProfession

**Путь:** src/Enums/PersonProfession.php  
**Пространство имён:** KinopoiskDev\Enums

---

## Описание

> Enum для профессий персон
>
> Этот enum содержит все возможные профессии персон, которые могут быть возвращены API Kinopoisk.dev

---

## Константы

| Константа | Значение | Описание |
|-----------|----------|----------|
| `ACTOR` | 'actor' | Актер |
| `DIRECTOR` | 'director' | Режиссер |
| `WRITER` | 'writer' | Сценарист |
| `PRODUCER` | 'producer' | Продюсер |
| `COMPOSER` | 'composer' | Композитор |
| `OPERATOR` | 'operator' | Оператор |
| `DESIGN` | 'design' | Художник |
| `EDITOR` | 'editor' | Монтажер |
| `VOICE_ACTOR` | 'voice_actor' | Актер дубляжа |
| `OTHER` | 'other' | Другое |

---

## Статические методы

### fromRussianName
```php
public static function fromRussianName(string $name): ?self
```
**Описание:** Создает экземпляр enum из русского названия профессии

**Параметры:**
- `string $name` — Русское название профессии

**Возвращает:** `self|null` — Экземпляр enum или null, если название не найдено

**Примеры:**
```php
$profession = PersonProfession::fromRussianName('актер');
// Результат: PersonProfession::ACTOR

$profession = PersonProfession::fromRussianName('режиссер');
// Результат: PersonProfession::DIRECTOR

$profession = PersonProfession::fromRussianName('неизвестная профессия');
// Результат: PersonProfession::OTHER
```

---

## Методы экземпляра

### getRussianName
```php
public function getRussianName(): string
```
**Описание:** Возвращает название профессии на русском языке

**Возвращает:** `string` — Название профессии на русском языке

**Примеры:**
```php
$profession = PersonProfession::ACTOR;
echo $profession->getRussianName(); // "актер"

$profession = PersonProfession::DIRECTOR;
echo $profession->getRussianName(); // "режиссер"
```

---

### getRussianPluralName
```php
public function getRussianPluralName(): string
```
**Описание:** Возвращает множественное название профессии на русском языке

**Возвращает:** `string` — Множественное название профессии на русском языке

**Примеры:**
```php
$profession = PersonProfession::ACTOR;
echo $profession->getRussianPluralName(); // "актеры"

$profession = PersonProfession::DIRECTOR;
echo $profession->getRussianPluralName(); // "режиссеры"
```

---

### getEnglishName
```php
public function getEnglishName(): string
```
**Описание:** Возвращает название профессии на английском языке

**Возвращает:** `string` — Название профессии на английском языке

**Примеры:**
```php
$profession = PersonProfession::ACTOR;
echo $profession->getEnglishName(); // "actor"

$profession = PersonProfession::DIRECTOR;
echo $profession->getEnglishName(); // "director"
```

---

### getEnglishPluralName
```php
public function getEnglishPluralName(): string
```
**Описание:** Возвращает множественное название профессии на английском языке

**Возвращает:** `string` — Множественное название профессии на английском языке

**Примеры:**
```php
$profession = PersonProfession::ACTOR;
echo $profession->getEnglishPluralName(); // "actors"

$profession = PersonProfession::DIRECTOR;
echo $profession->getEnglishPluralName(); // "directors"
```

---

## Примеры использования

```php
use KinopoiskDev\Enums\PersonProfession;

// Создание из русского названия
$profession = PersonProfession::fromRussianName('актер');

// Отображение на разных языках
echo "Профессия: " . $profession->getRussianName(); // "актер"
echo "Profession: " . $profession->getEnglishName(); // "actor"

// Использование в фильтрах
$filter = new PersonSearchFilter();
$filter->profession(PersonProfession::ACTOR->value);

// Группировка персон по профессиям
$actors = [];
$directors = [];
$writers = [];

foreach ($persons as $person) {
    switch ($person->profession) {
        case PersonProfession::ACTOR:
            $actors[] = $person;
            break;
        case PersonProfession::DIRECTOR:
            $directors[] = $person;
            break;
        case PersonProfession::WRITER:
            $writers[] = $person;
            break;
    }
}

// Отображение статистики
echo "Найдено " . count($actors) . " " . PersonProfession::ACTOR->getRussianPluralName();
echo "Найдено " . count($directors) . " " . PersonProfession::DIRECTOR->getRussianPluralName();
```

---

## Связи
- **Используется в:** [Person](../Models/Person.md), [PersonSearchFilter](../Filter/PersonSearchFilter.md)