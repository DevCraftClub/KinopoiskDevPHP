# BaseModel

**Описание:** Базовый интерфейс для всех моделей данных
Определяет контракт для моделей, работающих с данными API Kinopoisk.dev.
Включает методы для создания объектов из массивов данных, преобразования
в массивы, валидации и сериализации. Все модели данных должны реализовывать
этот интерфейс для обеспечения единообразного API.

**С версии:** 1.0.0

**Версия:** 2.0.0

**Пример:**
```php
class Movie implements BaseModel {
public function __construct(
public string $title,
public int $year
) {}
public static function fromArray(array $data): static {
return new static($data['title'], $data['year']);
}
public function toArray(bool $includeNulls = true): array {
return [
'title' => $this->title,
'year' => $this->year
];
}
public function validate(): bool {
return !empty($this->title) && $this->year > 1900;
}
public function toJson(int $flags = JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE): string {
return json_encode($this->toArray(), $flags);
}
public static function fromJson(string $json): static {
$data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
return static::fromArray($data);
}
}
```

**См. также:**

* `\KinopoiskDev\Models\AbstractBaseModel`: Абстрактная реализация базовой модели
* `\KinopoiskDev\Models\Movie`: Модель фильма
* `\KinopoiskDev\Models\Person`: Модель персоны
* `\KinopoiskDev\Models\Studio`: Модель студии

## `fromArray()`

**Описание:** Создает экземпляр модели из массива данных
Фабричный метод для создания объекта модели из ассоциативного массива,
полученного из API ответа. Должен обрабатывать маппинг полей API
на свойства модели и выполнять базовую валидацию данных.

**Возвращает:** `static` Экземпляр модели с заполненными данными

**Исключения:**

* `ValidationException`: При некорректных или неполных данных

**Пример:**
```php
$apiData = [
'title' => 'The Matrix',
'year' => 1999,
'rating' => 8.7
];
$movie = Movie::fromArray($apiData);
echo $movie->title; // The Matrix
```

## `fromJson()`

**Описание:** Создает объект из JSON строки
Десериализует JSON строку в объект модели. Парсит JSON,
создает массив данных и использует fromArray для создания объекта.

**Параметры:**

* `$json` (string): JSON строка с данными объекта

**Возвращает:** `static` Экземпляр модели с заполненными данными

**Исключения:**

* `\JsonException`: При ошибке парсинга JSON строки
* `ValidationException`: При некорректных данных после парсинга

**Пример:**
```php
$json = '{"title":"The Matrix","year":1999}';
$movie = Movie::fromJson($json);
echo $movie->title; // The Matrix
echo $movie->year;  // 1999
```

## `toArray()`

**Описание:** Преобразует объект в массив
Сериализует объект модели в ассоциативный массив для передачи
в API или сохранения в базу данных. Поддерживает контроль
включения null значений.

**Параметры:**

* `$includeNulls` (bool): Включать ли null значения в результат (по умолчанию true)

**Возвращает:** `array<string, mixed>` Данные объекта в виде ассоциативного массива

**Пример:**
```php
$movie = new Movie('The Matrix', 1999);
// С null значениями
$array = $movie->toArray(true);
// Результат: ['title' => 'The Matrix', 'year' => 1999, 'rating' => null]
// Без null значений
$array = $movie->toArray(false);
// Результат: ['title' => 'The Matrix', 'year' => 1999]
```

## `validate()`

**Описание:** Валидирует данные модели
Проверяет корректность данных модели согласно бизнес-правилам
и ограничениям. Может использовать атрибуты Validation для
автоматической валидации свойств.

**Возвращает:** `bool True` если данные валидны, false в противном случае

**Исключения:**

* `ValidationException`: При ошибке валидации с детальным описанием проблем

**Пример:**
```php
$movie = new Movie('', -1000); // Некорректные данные
try {
$movie->validate();
echo "Модель валидна";
} catch (ValidationException $e) {
echo "Ошибки валидации: " . $e->getMessage();
}
```

## `toJson()`

**Описание:** Возвращает JSON представление объекта
Сериализует объект модели в JSON строку для передачи
по сети или сохранения в файл. Поддерживает настройку
флагов кодирования JSON.

**Параметры:**

* `$flags` (int): Флаги для json_encode (по умолчанию JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE)

**Возвращает:** `string JSON` строка с данными объекта

**Исключения:**

* `\JsonException`: При ошибке сериализации в JSON

**Пример:**
```php
$movie = new Movie('The Matrix', 1999);
$json = $movie->toJson();
// Результат: '{"title":"The Matrix","year":1999}'
// С дополнительными флагами
$json = $movie->toJson(JSON_PRETTY_PRINT);
```

