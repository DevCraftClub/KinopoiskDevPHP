# Класс ValidationException

## Описание

`ValidationException` - специализированное исключение для ошибок валидации данных с поддержкой множественных ошибок и детальной диагностики.

## Пространство имен

```php
namespace KinopoiskDev\Exceptions;
```

## Объявление класса

```php
final class ValidationException extends RuntimeException
```

## Свойства

| Свойство | Тип | Описание |
|----------|-----|----------|
| `$errors` | `array` | Список ошибок валидации |
| `$field` | `?string` | Поле, вызвавшее ошибку |
| `$value` | `mixed` | Значение, не прошедшее валидацию |

## Методы

### __construct()

Конструктор исключения валидации.

```php
public function __construct(
    string $message = 'Ошибка валидации данных',
    private readonly array $errors = [],
    private readonly ?string $field = null,
    private readonly mixed $value = null,
    int $code = 0,
    ?Throwable $previous = null,
)
```

**Параметры:**
- `$message` - Основное сообщение об ошибке
- `$errors` - Список ошибок валидации
- `$field` - Поле, вызвавшее ошибку
- `$value` - Значение, не прошедшее валидацию
- `$code` - Код ошибки
- `$previous` - Предыдущее исключение

### getErrors()

Возвращает список всех ошибок валидации.

```php
public function getErrors(): array
```

**Возвращает:** Массив ошибок в формате `['field' => 'error_message']`

### getField()

Возвращает поле, вызвавшее ошибку.

```php
public function getField(): ?string
```

**Возвращает:** Название поля или null

### getValue()

Возвращает значение, не прошедшее валидацию.

```php
public function getValue(): mixed
```

**Возвращает:** Проблемное значение

### hasErrors()

Проверяет, есть ли ошибки валидации.

```php
public function hasErrors(): bool
```

**Возвращает:** True если есть ошибки

### getFirstError()

Возвращает первую ошибку валидации.

```php
public function getFirstError(): ?string
```

**Возвращает:** Текст первой ошибки или null

### forField()

Статический метод для создания исключения для конкретного поля.

```php
public static function forField(string $field, string $message, mixed $value = null): self
```

**Параметры:**
- `$field` - Название поля
- `$message` - Сообщение об ошибке
- `$value` - Значение поля

**Возвращает:** Экземпляр исключения

### withErrors()

Статический метод для создания исключения с множественными ошибками.

```php
public static function withErrors(array $errors): self
```

**Параметры:**
- `$errors` - Массив ошибок `['field' => 'message']`

**Возвращает:** Экземпляр исключения

## Примеры использования

### Простая валидация поля

```php
use KinopoiskDev\Exceptions\ValidationException;

class UserValidator
{
    public function validateEmail(string $email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw ValidationException::forField(
                'email',
                'Неверный формат email адреса',
                $email
            );
        }
    }
    
    public function validateAge(int $age): void
    {
        if ($age < 18 || $age > 120) {
            throw ValidationException::forField(
                'age',
                'Возраст должен быть от 18 до 120 лет',
                $age
            );
        }
    }
}
```

### Множественная валидация

```php
use KinopoiskDev\Exceptions\ValidationException;

class FormValidator
{
    public function validate(array $data): void
    {
        $errors = [];
        
        // Валидация имени
        if (empty($data['name'])) {
            $errors['name'] = 'Имя обязательно для заполнения';
        } elseif (strlen($data['name']) < 2) {
            $errors['name'] = 'Имя должно содержать минимум 2 символа';
        }
        
        // Валидация email
        if (empty($data['email'])) {
            $errors['email'] = 'Email обязателен для заполнения';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Неверный формат email адреса';
        }
        
        // Валидация пароля
        if (empty($data['password'])) {
            $errors['password'] = 'Пароль обязателен';
        } elseif (strlen($data['password']) < 8) {
            $errors['password'] = 'Пароль должен содержать минимум 8 символов';
        }
        
        if (!empty($errors)) {
            throw ValidationException::withErrors($errors);
        }
    }
}
```

### Обработка исключения

```php
use KinopoiskDev\Exceptions\ValidationException;

try {
    $validator = new FormValidator();
    $validator->validate($_POST);
} catch (ValidationException $e) {
    // Получение всех ошибок
    $errors = $e->getErrors();
    
    // Проверка наличия ошибок
    if ($e->hasErrors()) {
        // Получение первой ошибки
        $firstError = $e->getFirstError();
        
        // JSON ответ с ошибками
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
            'errors' => $errors,
            'first_error' => $firstError
        ], 422);
    }
}
```

### Валидация в модели

```php
use KinopoiskDev\Exceptions\ValidationException;

class User
{
    private string $username;
    private string $email;
    private int $age;
    
    public function setUsername(string $username): void
    {
        if (strlen($username) < 3 || strlen($username) > 20) {
            throw ValidationException::forField(
                'username',
                'Имя пользователя должно быть от 3 до 20 символов',
                $username
            );
        }
        
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            throw ValidationException::forField(
                'username',
                'Имя пользователя может содержать только буквы, цифры и _',
                $username
            );
        }
        
        $this->username = $username;
    }
    
    public function setEmail(string $email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw ValidationException::forField('email', 'Неверный формат email', $email);
        }
        
        $this->email = $email;
    }
    
    public function validate(): void
    {
        $errors = [];
        
        if (empty($this->username)) {
            $errors['username'] = 'Имя пользователя не установлено';
        }
        
        if (empty($this->email)) {
            $errors['email'] = 'Email не установлен';
        }
        
        if ($this->age < 18) {
            $errors['age'] = 'Пользователь должен быть старше 18 лет';
        }
        
        if (!empty($errors)) {
            throw ValidationException::withErrors($errors);
        }
    }
}
```

### Валидация с контекстом

```php
use KinopoiskDev\Exceptions\ValidationException;

class PasswordValidator
{
    private array $requirements = [
        'минимум 8 символов',
        'хотя бы одна заглавная буква',
        'хотя бы одна цифра',
        'хотя бы один специальный символ'
    ];
    
    public function validate(string $password): void
    {
        $errors = [];
        
        if (strlen($password) < 8) {
            $errors[] = 'Пароль должен содержать минимум 8 символов';
        }
        
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Пароль должен содержать хотя бы одну заглавную букву';
        }
        
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'Пароль должен содержать хотя бы одну цифру';
        }
        
        if (!preg_match('/[^A-Za-z0-9]/', $password)) {
            $errors[] = 'Пароль должен содержать хотя бы один специальный символ';
        }
        
        if (!empty($errors)) {
            $exception = new ValidationException(
                'Пароль не соответствует требованиям безопасности',
                ['password' => $errors],
                'password',
                '***' // Не сохраняем реальный пароль
            );
            
            throw $exception;
        }
    }
}
```

### Интеграция с формами

```php
use KinopoiskDev\Exceptions\ValidationException;

class RegistrationForm
{
    private array $rules = [
        'username' => ['required', 'min:3', 'max:20', 'unique'],
        'email' => ['required', 'email', 'unique'],
        'password' => ['required', 'min:8', 'confirmed'],
        'age' => ['required', 'integer', 'min:18']
    ];
    
    public function validate(array $data): array
    {
        try {
            $this->runValidation($data);
            return ['success' => true];
        } catch (ValidationException $e) {
            return [
                'success' => false,
                'errors' => $e->getErrors(),
                'message' => $e->getMessage()
            ];
        }
    }
    
    private function runValidation(array $data): void
    {
        $errors = [];
        
        foreach ($this->rules as $field => $rules) {
            $value = $data[$field] ?? null;
            
            foreach ($rules as $rule) {
                $error = $this->checkRule($field, $value, $rule);
                if ($error) {
                    $errors[$field] = $error;
                    break; // Одна ошибка на поле
                }
            }
        }
        
        if (!empty($errors)) {
            throw ValidationException::withErrors($errors);
        }
    }
}
```

## Особенности

1. **Final класс** - Не может быть расширен
2. **Readonly свойства** - Неизменяемые после создания
3. **Множественные ошибки** - Поддержка списка ошибок
4. **Удобные фабричные методы** - `forField()` и `withErrors()`
5. **Детальная информация** - Сохранение контекста ошибки

## Связанные классы

- [KinopoiskDevException](KinopoiskDevException.md) - Базовый класс исключений
- [ValidationService](../services/ValidationService.md) - Сервис валидации
- [Validation](../attributes/Validation.md) - Атрибуты валидации
- `RuntimeException` - Базовый класс PHP

## Требования

- PHP 8.3+