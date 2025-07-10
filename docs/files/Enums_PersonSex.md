# PersonSex

**Путь:** src/Enums/PersonSex.php  
**Пространство имён:** KinopoiskDev\Enums

---

## Описание

> Enum для пола персон
>
> Этот enum содержит все возможные значения пола персон, которые могут быть возвращены API Kinopoisk.dev

---

## Константы

| Константа | Значение | Описание |
|-----------|----------|----------|
| `MALE` | 'male' | Мужской пол |
| `FEMALE` | 'female' | Женский пол |

---

## Методы

### getRussianName
```php
public function getRussianName(): string
```
**Описание:** Возвращает название пола на русском языке

**Возвращает:** `string` — Название пола на русском языке

**Примеры:**
```php
$sex = PersonSex::MALE;
echo $sex->getRussianName(); // "мужской"

$sex = PersonSex::FEMALE;
echo $sex->getRussianName(); // "женский"
```

---

## Примеры использования

```php
use KinopoiskDev\Enums\PersonSex;

// Получение пола персоны
$personSex = PersonSex::MALE;

// Отображение пола пользователю
echo "Пол: " . $personSex->getRussianName();

// Проверка пола
if ($personSex === PersonSex::MALE) {
    echo "Это мужчина";
} elseif ($personSex === PersonSex::FEMALE) {
    echo "Это женщина";
}

// Использование в фильтрах
$filter = new PersonSearchFilter();
$filter->sex(PersonSex::FEMALE->value);

// Подсчет по полу
$maleCount = 0;
$femaleCount = 0;

foreach ($persons as $person) {
    if ($person->sex === PersonSex::MALE) {
        $maleCount++;
    } elseif ($person->sex === PersonSex::FEMALE) {
        $femaleCount++;
    }
}
```

---

## Связи
- **Используется в:** [Person](../Models/Person.md), [PersonSearchFilter](../Filter/PersonSearchFilter.md)