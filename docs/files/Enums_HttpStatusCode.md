# HttpStatusCode

**Путь:** src/Enums/HttpStatusCode.php  
**Пространство имён:** KinopoiskDev\Enums

---

## Описание

> Enum для HTTP статус кодов
>
> Предоставляет типизированные константы для основных HTTP статус кодов, используемых в API Kinopoisk.dev

---

## Константы

| Константа | Значение | Описание |
|-----------|----------|----------|
| `OK` | 200 | Успешный запрос |
| `UNAUTHORIZED` | 401 | Неавторизован |
| `FORBIDDEN` | 403 | Доступ запрещён |
| `NOT_FOUND` | 404 | Не найдено |
| `INTERNAL_SERVER_ERROR` | 500 | Внутренняя ошибка сервера |

---

## Методы

### getDescription
```php
public function getDescription(): string
```
**Описание:** Возвращает описание статус кода на русском языке

**Возвращает:** `string` — Описание статус кода

**Примеры:**
```php
$status = HttpStatusCode::OK;
echo $status->getDescription(); // "Успешный запрос"

$status = HttpStatusCode::NOT_FOUND;
echo $status->getDescription(); // "Не найдено"
```

---

### isError
```php
public function isError(): bool
```
**Описание:** Проверяет, является ли статус кодом ошибки

**Возвращает:** `bool` — true, если статус код >= 400, false в противном случае

**Примеры:**
```php
$status = HttpStatusCode::OK;
echo $status->isError(); // false

$status = HttpStatusCode::NOT_FOUND;
echo $status->isError(); // true
```

---

### isSuccess
```php
public function isSuccess(): bool
```
**Описание:** Проверяет, является ли статус кодом успеха

**Возвращает:** `bool` — true, если статус код >= 200 и < 300, false в противном случае

**Примеры:**
```php
$status = HttpStatusCode::OK;
echo $status->isSuccess(); // true

$status = HttpStatusCode::NOT_FOUND;
echo $status->isSuccess(); // false
```

---

## Примеры использования

```php
use KinopoiskDev\Enums\HttpStatusCode;

// Проверка статуса ответа
$responseStatus = HttpStatusCode::OK;

if ($responseStatus->isSuccess()) {
    echo "Запрос выполнен успешно: " . $responseStatus->getDescription();
} elseif ($responseStatus->isError()) {
    echo "Произошла ошибка: " . $responseStatus->getDescription();
}

// Обработка различных статусов
switch ($responseStatus) {
    case HttpStatusCode::OK:
        echo "Данные получены успешно";
        break;
    case HttpStatusCode::UNAUTHORIZED:
        echo "Требуется авторизация";
        break;
    case HttpStatusCode::NOT_FOUND:
        echo "Ресурс не найден";
        break;
    case HttpStatusCode::INTERNAL_SERVER_ERROR:
        echo "Ошибка сервера";
        break;
}
```

---

## Связи
- **Используется в:** [HttpClient](../Services/HttpClient.md), [Response](../Responses/Response.md)