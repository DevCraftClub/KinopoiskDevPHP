# KinopoiskResponseException

**Описание:** Исключение для ошибок ответов API Kinopoisk.dev
Специализированное исключение для обработки ошибок HTTP ответов
от API Kinopoisk.dev. Автоматически извлекает информацию об ошибке
из объекта ответа API и формирует понятное сообщение об ошибке.

**С версии:** 1.0.0

**Версия:** 1.0.0

**Пример:**
```php
try {
$movie = $kinopoisk->getMovieById(999999);
} catch (KinopoiskResponseException $e) {
echo "Ошибка API: " . $e->getMessage();
echo "Код: " . $e->getCode(); // 404
}
```

**См. также:**

* `\KinopoiskDev\Responses\Errors\UnauthorizedErrorResponseDto`: Для ошибки 401
* `\KinopoiskDev\Responses\Errors\ForbiddenErrorResponseDto`: Для ошибки 403
* `\KinopoiskDev\Responses\Errors\NotFoundErrorResponseDto`: Для ошибки 404

## `__construct()`

**Описание:** Конструктор исключения ответа API
Создает исключение на основе класса ответа API. Автоматически
извлекает информацию об ошибке из объекта ответа и формирует
сообщение об ошибке с кодом статуса.

**Параметры:**

* `$rspnsCls` (string): Полное имя класса ответа API (например, UnauthorizedErrorResponseDto::class)
* `$previous` (Exception|null): Предыдущее исключение в цепочке

**Исключения:**

* `\Error`: При неверном имени класса ответа

**Пример:**
```php
throw new KinopoiskResponseException(
UnauthorizedErrorResponseDto::class,
$previousException
);
```

