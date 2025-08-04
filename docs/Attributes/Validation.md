# Validation

**Описание:** Атрибут для валидации свойств модели
Предоставляет декларативный способ задания правил валидации
для свойств моделей с использованием PHP 8.3 Attributes.
Поддерживает различные типы валидации: обязательные поля,
ограничения длины, диапазоны значений, регулярные выражения.

**С версии:** 1.0.0

**Версия:** 1.0.0

**Пример:**
```php
class Movie {
#[Validation(required: true, minLength: 1, maxLength: 255)]
public string $title;
#[Validation(min: 1900, max: 2030)]
public int $year;
#[Validation(pattern: '/^[a-zA-Z0-9\s]+$/')]
public string $genre;
}
```

**См. также:**

* `\KinopoiskDev\Services\ValidationService`: Сервис валидации
* `\KinopoiskDev\Exceptions\ValidationException`: Исключения валидации

## `__construct()`

**Описание:** Конструктор атрибута валидации
Создает новый экземпляр атрибута валидации с указанными правилами.
Все параметры являются опциональными и могут быть настроены
в зависимости от требований к конкретному полю.

**Параметры:**

* `$required` (bool): Обязательное ли поле (по умолчанию false)
* `$minLength` (int|null): Минимальная длина строки (для строковых полей)
* `$maxLength` (int|null): Максимальная длина строки (для строковых полей)
* `$min` (float|null): Минимальное значение (для числовых полей)
* `$max` (float|null): Максимальное значение (для числовых полей)
* `$pattern` (string|null): Регулярное выражение для проверки формата
* `$allowedValues` (array<string>): Допустимые значения (для enum-подобных полей)
* `$customMessage` (string|null): Кастомное сообщение об ошибке валидации

**Пример:**
```php
// Обязательное поле с ограничением длины
#[Validation(required: true, minLength: 1, maxLength: 100)]
public string $name;
// Числовое поле с диапазоном
#[Validation(min: 0, max: 10)]
public float $rating;
// Поле с регулярным выражением
#[Validation(pattern: '/^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/i')]
public string $email;
// Поле с допустимыми значениями
#[Validation(allowedValues: ['movie', 'series', 'cartoon'])]
public string $type;
```

