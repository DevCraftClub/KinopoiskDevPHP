# ValidationService

**Описание:** Сервис для валидации данных
Выполняет валидацию объектов на основе атрибутов PHP 8.3.
Поддерживает различные типы валидации: обязательные поля,
ограничения длины, диапазоны значений, регулярные выражения.
Использует рефлексию для автоматического обнаружения правил валидации.

**С версии:** 1.0.0

**Версия:** 1.0.0

**Пример:**
```php
class Movie {
#[Validation(required: true, minLength: 1, maxLength: 255)]
public string $title;
#[Validation(min: 1900, max: 2030)]
public int $year;
}
$validator = new ValidationService();
$movie = new Movie();
$movie->title = '';
$movie->year = 1800;
try {
$validator->validate($movie);
} catch (ValidationException $e) {
foreach ($e->getErrors() as $field => $error) {
echo "{$field}: {$error}\n";
}
}
```

**См. также:**

* `\KinopoiskDev\Attributes\Validation`: Атрибут валидации
* `\KinopoiskDev\Exceptions\ValidationException`: Исключения валидации

## `validate()`

**Описание:** Валидирует объект на основе атрибутов свойств
Основной метод валидации, который анализирует все свойства объекта
и проверяет их на соответствие правилам, заданным в атрибутах Validation.
Выбрасывает ValidationException при обнаружении ошибок валидации.

**Параметры:**

* `$object` (object): Объект для валидации (должен иметь свойства с атрибутами Validation)

**Возвращает:** `bool True` если валидация прошла успешно

**Исключения:**

* `ValidationException`: При ошибках валидации с детальным описанием проблем

**Пример:**
```php
$movie = new Movie();
$movie->title = 'The Matrix';
$movie->year = 1999;
try {
$validator->validate($movie);
echo "Объект валиден";
} catch (ValidationException $e) {
echo "Ошибки валидации: " . $e->getMessage();
}
```

## `validateProperty()`

**Описание:** Валидирует конкретное свойство объекта
Проверяет одно свойство объекта на соответствие правилам валидации,
заданным в атрибуте Validation. Поддерживает различные типы проверок
в зависимости от типа значения свойства.

**Параметры:**

* `$object` (object): Объект для валидации
* `$property` (ReflectionProperty): Свойство для валидации

**Возвращает:** `array<string, string>` Массив ошибок валидации в формате ['property' => 'error']

## `validateString()`

**Описание:** Валидирует строковое значение
Выполняет валидацию строковых значений согласно правилам:
минимальная/максимальная длина и соответствие регулярному выражению.

**Параметры:**

* `$value` (string): Строковое значение для валидации
* `$validation` (Validation): Правила валидации из атрибута
* `$propertyName` (string): Название свойства для сообщений об ошибках

**Возвращает:** `array<string, string>` Массив ошибок валидации

## `validateNumeric()`

**Описание:** Валидирует числовое значение
Выполняет валидацию числовых значений согласно правилам:
минимальное/максимальное значение.

**Параметры:**

* `$value` (float|int): Числовое значение для валидации
* `$validation` (Validation): Правила валидации из атрибута
* `$propertyName` (string): Название свойства для сообщений об ошибках

**Возвращает:** `array<string, string>` Массив ошибок валидации

## `validateArray()`

**Описание:** Валидирует массив данных по правилам
Альтернативный метод валидации для работы с массивами данных
вместо объектов. Полезен для валидации входных данных API
или данных форм.

**Возвращает:** `bool True` если валидация прошла успешно

**Исключения:**

* `ValidationException`: При ошибках валидации

**Пример:**
```php
$data = [
'title' => 'The Matrix',
'year' => 1999,
'rating' => 8.7
];
$rules = [
'title' => ['required' => true, 'min_length' => 1, 'max_length' => 255],
'year' => ['min' => 1900, 'max' => 2030],
'rating' => ['min' => 0, 'max' => 10]
];
try {
$validator->validateArray($data, $rules);
echo "Данные валидны";
} catch (ValidationException $e) {
echo "Ошибки: " . $e->getMessage();
}
```

## `validateFieldValue()`

**Описание:** Валидирует значение поля по правилам
Вспомогательный метод для валидации отдельного поля
согласно переданным правилам. Поддерживает различные
типы правил валидации.

**Параметры:**

* `$value` (mixed): Значение поля для валидации
* `$fieldName` (string): Название поля для сообщений об ошибках

**Возвращает:** `array<string, string>` Массив ошибок валидации

## `validateValue()`

**Описание:** Валидирует значение на основе правил валидации
Универсальный метод для валидации любого значения
согласно объекту Validation. Возвращает сообщение об ошибке
или null при успешной валидации.

**Параметры:**

* `$value` (mixed): Значение для валидации
* `$validation` (Validation): Правила валидации

**Возвращает:** `string|null` Сообщение об ошибке или null если валидация прошла успешно

## `validateApiToken()`

**Описание:** Валидирует API токен

**Параметры:**

* `$token` (string|null): API токен для валидации

**Возвращает:** `bool` 

**Исключения:**

* `ValidationException`: 

## `validateHttpMethod()`

**Описание:** Валидирует HTTP метод

**Параметры:**

* `$method` (string): HTTP метод

**Возвращает:** `bool` 

**Исключения:**

* `ValidationException`: 

## `validateEndpoint()`

**Описание:** Валидирует endpoint

**Параметры:**

* `$endpoint` (string): Endpoint для валидации

**Возвращает:** `bool` 

**Исключения:**

* `ValidationException`: 

## `validateYear()`

**Описание:** Валидирует год

**Параметры:**

* `$year` (int): Год для валидации

**Возвращает:** `bool` 

**Исключения:**

* `ValidationException`: 

## `validateRating()`

**Описание:** Валидирует рейтинг

**Параметры:**

* `$rating` (float): Рейтинг для валидации

**Возвращает:** `bool` 

**Исключения:**

* `ValidationException`: 

## `validateLimit()`

**Описание:** Валидирует лимит

**Параметры:**

* `$limit` (int): Лимит для валидации

**Возвращает:** `bool` 

**Исключения:**

* `ValidationException`: 

## `validatePage()`

**Описание:** Валидирует номер страницы

**Параметры:**

* `$page` (int): Номер страницы

**Возвращает:** `bool` 

**Исключения:**

* `ValidationException`: 

## `validateMovieId()`

**Описание:** Валидирует ID фильма

**Параметры:**

* `$movieId` (int): ID фильма

**Возвращает:** `bool` 

**Исключения:**

* `ValidationException`: 

## `validatePersonId()`

**Описание:** Валидирует ID персоны

**Параметры:**

* `$personId` (int): ID персоны

**Возвращает:** `bool` 

**Исключения:**

* `ValidationException`: 

## `validateGenre()`

**Описание:** Валидирует жанр

**Параметры:**

* `$genre` (string): Жанр для валидации

**Возвращает:** `bool` 

**Исключения:**

* `ValidationException`: 

## `validateCountry()`

**Описание:** Валидирует страну

**Параметры:**

* `$country` (string): Страна для валидации

**Возвращает:** `bool` 

**Исключения:**

* `ValidationException`: 

## `validateProfession()`

**Описание:** Валидирует профессию

**Параметры:**

* `$profession` (string): Профессия для валидации

**Возвращает:** `bool` 

**Исключения:**

* `ValidationException`: 

## `validateSearchQuery()`

**Описание:** Валидирует поисковый запрос

**Параметры:**

* `$query` (string): Поисковый запрос

**Возвращает:** `bool` 

**Исключения:**

* `ValidationException`: 

## `validateDate()`

**Описание:** Валидирует дату

**Параметры:**

* `$date` (string): Дата в формате Y-m-d

**Возвращает:** `bool` 

**Исключения:**

* `ValidationException`: 

## `validateDateRange()`

**Описание:** Валидирует диапазон дат

**Параметры:**

* `$startDate` (string): Начальная дата
* `$endDate` (string): Конечная дата

**Возвращает:** `bool` 

**Исключения:**

* `ValidationException`: 

## `validateNotEmptyArray()`

**Описание:** Валидирует непустой массив

**Параметры:**

* `$array` (array): Массив для валидации

**Возвращает:** `bool` 

**Исключения:**

* `ValidationException`: 

