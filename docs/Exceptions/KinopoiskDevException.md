# KinopoiskDevException

**Описание:** Базовое исключение для всех ошибок библиотеки KinopoiskDev
Основной класс исключений, от которого наследуются все специфические
исключения библиотеки. Предоставляет единообразный интерфейс для
обработки ошибок с поддержкой цепочки исключений.

**С версии:** 1.0.0

**Версия:** 1.0.0

**Пример:**
```php
try {
$kinopoisk = new Kinopoisk();
$movie = $kinopoisk->getMovieById(123);
} catch (KinopoiskDevException $e) {
echo "Ошибка: " . $e->getMessage();
echo "Код: " . $e->getCode();
}
```

**См. также:**

* `\KinopoiskDev\Exceptions\ValidationException`: Для ошибок валидации
* `\KinopoiskDev\Exceptions\KinopoiskResponseException`: Для ошибок API

## `__construct()`

**Описание:** Конструктор исключения
Создает новый экземпляр исключения с указанным сообщением,
кодом ошибки и предыдущим исключением для цепочки.

**Параметры:**

* `$message` (string): Сообщение об ошибке
* `$code` (int): Код ошибки (по умолчанию 0)
* `$previous` (Exception|null): Предыдущее исключение в цепочке

**Пример:**
```php
throw new KinopoiskDevException(
'Ошибка подключения к API',
500,
$previousException
);
```

