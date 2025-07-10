# DataManager

**Путь:** src/Utils/DataManager.php  
**Пространство имён:** KinopoiskDev\Utils  
**Использует:** [KinopoiskDevException](../Exceptions/KinopoiskDevException.md)

---

## Описание

> Абстрактный класс для управления данными
>
> Предоставляет статические методы для преобразования и парсинга данных между массивами и объектами. Используется для автоматизации процесса обработки данных API.

---

## Методы

### getObjectsArray
```php
public static function getObjectsArray(mixed $objects): array
```
**Описание:** Преобразует массив объектов в массив массивов

> Статический вспомогательный метод для преобразования коллекции объектов в массив массивов путем вызова метода toArray() для каждого объекта. Используется для сериализации связанных объектов (например, жанров, стран, персон) при преобразовании основного объекта в массив данных.

**Параметры:**
- `mixed $objects` — Коллекция объектов для преобразования или любое другое значение. Может быть массивом объектов с методом toArray(), null, false или пустым массивом.

**Возвращает:** `array` — Массив массивов, полученный путем вызова toArray() для каждого объекта. Возвращает пустой массив, если входные данные являются falsy-значением.

**Исключения:**
- `KinopoiskDevException` — При ошибках обработки

**Примеры:**
```php
// Преобразование массива объектов жанров
$genres = [new Genre('драма'), new Genre('триллер')];
$result = DataManager::getObjectsArray($genres);
// Результат: [['name' => 'драма'], ['name' => 'триллер']]

// Обработка пустого значения
$result = DataManager::getObjectsArray(null);
// Результат: []
```

---

### parseObjectAuto
```php
public static function parseObjectAuto(array $data, string $key, string $cls, mixed $default = NULL): mixed
```
**Описание:** Автоматически парсит объект из массива данных в зависимости от типа

> Универсальный метод для автоматической обработки объектов, который определяет, является ли значение по указанному ключу массивом объектов или одиночным объектом, и соответственно выбирает подходящий метод парсинга.

**Параметры:**
- `array $data` — Массив данных, содержащий информацию для парсинга
- `string $key` — Ключ в массиве данных, по которому находится значение для парсинга
- `string $cls` — Полное имя класса для создания объектов (должен иметь метод fromArray)
- `mixed $default` — Значение по умолчанию, возвращаемое при отсутствии данных (по умолчанию null)

**Возвращает:** `mixed` — Массив объектов указанного класса, одиночный объект или значение по умолчанию

**Исключения:**
- `KinopoiskDevException` — Если указанный класс не существует или не имеет метода fromArray

---

### parseObjectArray
```php
public static function parseObjectArray(array $data, string $key, string $cls, mixed $default = []): array
```
**Описание:** Разбирает данные объекта из массива API

> Статический метод для извлечения и преобразования данных объектов из массива API по указанному ключу. Если ключ существует в массиве, применяет функцию преобразования к каждому элементу: создает объект через fromArray() для массивов или возвращает элемент как есть для других типов данных.

**Параметры:**
- `array $data` — Массив данных API, содержащий различные поля фильма или других объектов
- `string $key` — Ключ для поиска в массиве данных (например, 'genres', 'countries', 'persons')
- `string $cls` — Имя класса для создания объектов (например, 'Genre', 'Country', 'Person'). Класс должен содержать статический метод fromArray()
- `mixed $default` — Значение по умолчанию, возвращаемое при отсутствии ключа (по умолчанию пустой массив)

**Возвращает:** `array` — Массив объектов указанного типа или значение по умолчанию, если ключ не найден

**Исключения:**
- `KinopoiskDevException` — Если указанный класс не существует или не содержит метод fromArray()

**Примеры:**
```php
// Обработка массива жанров из API
$apiData = ['genres' => [['name' => 'драма'], ['name' => 'триллер']]];
$genres = DataManager::parseObjectArray($apiData, 'genres', 'Genre');
// Результат: [Genre объект 'драма', Genre объект 'триллер']

// Обработка отсутствующего ключа с кастомным значением по умолчанию
$result = DataManager::parseObjectArray($apiData, 'missing_key', 'Country', []);
// Результат: []
```

---

### parseObjectData
```php
public static function parseObjectData(array $data, string $key, string $cls, mixed $default = NULL): mixed
```
**Описание:** Парсит данные объекта из массива через фабричный метод

> Универсальный статический метод для создания объектов из массивов данных с использованием фабричного метода fromArray. Выполняет валидацию существования класса и требуемого метода, возвращая экземпляр объекта указанного класса или значение по умолчанию при отсутствии данных.

**Параметры:**
- `array $data` — Массив данных для парсинга, содержащий ключи объектов
- `string $key` — Ключ в массиве данных для извлечения значения
- `string $cls` — Полное имя класса для создания объекта (с пространством имен)
- `mixed $default` — Значение по умолчанию, возвращаемое при отсутствии данных

**Возвращает:** `mixed` — Экземпляр указанного класса, созданный через fromArray, или значение по умолчанию

**Исключения:**
- `KinopoiskDevException` — Если указанный класс не существует
- `KinopoiskDevException` — Если в классе отсутствует метод fromArray

**Примеры:**
```php
// Создание объекта рейтинга из массива данных
$rating = DataManager::parseObjectData(
    $apiData,
    'rating',
    Rating::class,
    new Rating()
);

// Создание объекта изображения с null по умолчанию
$poster = DataManager::parseObjectData(
    $movieData,
    'poster',
    ShortImage::class
);
```

---

### parseEnumValue
```php
public static function parseEnumValue(array $data, string $key, string $enumClass, mixed $default = NULL): mixed
```
**Описание:** Парсит значение enum из массива данных

> Статический метод для создания объектов enum из массивов данных. Выполняет валидацию существования класса enum и создает объект через tryFrom() или from() методы.

**Параметры:**
- `array $data` — Массив данных для парсинга
- `string $key` — Ключ в массиве данных для извлечения значения
- `string $enumClass` — Полное имя класса enum
- `mixed $default` — Значение по умолчанию, возвращаемое при отсутствии данных

**Возвращает:** `mixed` — Объект enum или значение по умолчанию

**Исключения:**
- `KinopoiskDevException` — Если указанный класс не существует или не является enum

---

## Примеры использования

```php
use KinopoiskDev\Utils\DataManager;
use KinopoiskDev\Models\Genre;
use KinopoiskDev\Models\Country;

// Преобразование объектов в массивы
$genres = [new Genre('драма'), new Genre('триллер')];
$genreArrays = DataManager::getObjectsArray($genres);

// Автоматический парсинг объектов
$apiData = [
    'genres' => [['name' => 'драма'], ['name' => 'триллер']],
    'rating' => ['kp' => 8.5, 'imdb' => 8.2]
];

$genres = DataManager::parseObjectAuto($apiData, 'genres', Genre::class, []);
$rating = DataManager::parseObjectAuto($apiData, 'rating', Rating::class);

// Парсинг массивов объектов
$countries = DataManager::parseObjectArray($apiData, 'countries', Country::class, []);

// Парсинг одиночных объектов
$poster = DataManager::parseObjectData($apiData, 'poster', Image::class);

// Парсинг enum значений
$type = DataManager::parseEnumValue($apiData, 'type', MovieType::class);
```

---

## Связи
- **Использует:** [KinopoiskDevException](../Exceptions/KinopoiskDevException.md)
- **Используется в:** [Movie](../Models/Movie.md), [Person](../Models/Person.md), [Genre](../Models/Genre.md), [Country](../Models/Country.md) и других моделях