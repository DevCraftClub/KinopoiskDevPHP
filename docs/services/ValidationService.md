# Класс ValidationService

## Описание

`ValidationService` - сервис для валидации данных, выполняющий валидацию объектов на основе атрибутов PHP 8.3. Поддерживает различные типы валидации: обязательные поля, ограничения длины, диапазоны значений, регулярные выражения.

## Пространство имен

```php
namespace KinopoiskDev\Services;
```

## Объявление класса

```php
final class ValidationService
```

## Методы

### validate()

Валидирует объект на основе атрибутов свойств.

```php
public function validate(object $object): bool
```

**Параметры:**
- `$object` - Объект для валидации

**Возвращает:** `bool` - True если валидация прошла успешно

**Исключения:**
- `ValidationException` - При ошибках валидации

### validateArray()

Валидирует массив данных по правилам.

```php
public function validateArray(array $data, array $rules): bool
```

**Параметры:**
- `$data` - Данные для валидации
- `$rules` - Правила валидации

**Возвращает:** `bool` - True если валидация прошла успешно

**Исключения:**
- `ValidationException` - При ошибках валидации

## Приватные методы

### validateProperty()

Валидирует конкретное свойство объекта.

```php
private function validateProperty(object $object, ReflectionProperty $property): array
```

### validateString()

Валидирует строковое значение.

```php
private function validateString(string $value, Validation $validation, string $propertyName): array
```

### validateNumeric()

Валидирует числовое значение.

```php
private function validateNumeric(float|int $value, Validation $validation, string $propertyName): array
```

### validateFieldValue()

Валидирует значение поля по правилам.

```php
private function validateFieldValue(mixed $value, array $rules, string $fieldName): array
```

## Примеры использования

### Валидация объектов с атрибутами

```php
use KinopoiskDev\Services\ValidationService;
use KinopoiskDev\Attributes\Validation;

class UserRegistrationForm
{
    #[Validation(required: true, minLength: 3, maxLength: 20)]
    public string $username;
    
    #[Validation(required: true, pattern: '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/')]
    public string $email;
    
    #[Validation(required: true, minLength: 8)]
    public string $password;
    
    #[Validation(min: 18, max: 120)]
    public ?int $age = null;
    
    #[Validation(required: true, allowedValues: ['male', 'female', 'other'])]
    public string $gender;
}

// Использование
$validator = new ValidationService();
$form = new UserRegistrationForm();
$form->username = 'jo'; // Слишком короткое
$form->email = 'invalid-email';
$form->password = 'pass123'; // Слишком короткий
$form->gender = 'unknown'; // Недопустимое значение

try {
    $validator->validate($form);
} catch (ValidationException $e) {
    $errors = $e->getErrors();
    // [
    //     'username' => "Поле 'username' должно содержать не менее 3 символов",
    //     'email' => "Поле 'email' не соответствует требуемому формату",
    //     'password' => "Поле 'password' должно содержать не менее 8 символов",
    //     'gender' => "Поле 'gender' должно содержать одно из значений: male, female, other"
    // ]
}
```

### Валидация массивов данных

```php
use KinopoiskDev\Services\ValidationService;

$validator = new ValidationService();

$data = [
    'name' => 'Jo',
    'email' => 'test@example',
    'age' => 15,
    'status' => 'active'
];

$rules = [
    'name' => [
        'required' => true,
        'min_length' => 3,
        'max_length' => 50
    ],
    'email' => [
        'required' => true,
        'min_length' => 5
    ],
    'age' => [
        'required' => true,
        'min' => 18,
        'max' => 65
    ],
    'status' => [
        'in' => ['active', 'inactive', 'pending']
    ]
];

try {
    $validator->validateArray($data, $rules);
} catch (ValidationException $e) {
    $errors = $e->getErrors();
    // [
    //     'name' => "Поле 'name' должно содержать не менее 3 символов",
    //     'age' => "Поле 'age' должно быть не менее 18"
    // ]
}
```

### Создание модели с валидацией

```php
use KinopoiskDev\Services\ValidationService;
use KinopoiskDev\Attributes\Validation;

abstract class BaseModel
{
    protected ValidationService $validator;
    
    public function __construct()
    {
        $this->validator = new ValidationService();
    }
    
    public function validate(): bool
    {
        return $this->validator->validate($this);
    }
}

class Product extends BaseModel
{
    #[Validation(required: true, minLength: 3)]
    public string $name;
    
    #[Validation(required: true, min: 0.01)]
    public float $price;
    
    #[Validation(min: 0)]
    public int $stock = 0;
    
    #[Validation(allowedValues: ['available', 'out_of_stock', 'discontinued'])]
    public string $status = 'available';
    
    public static function create(array $data): self
    {
        $product = new self();
        $product->name = $data['name'] ?? '';
        $product->price = $data['price'] ?? 0;
        $product->stock = $data['stock'] ?? 0;
        $product->status = $data['status'] ?? 'available';
        
        $product->validate(); // Выбросит исключение при ошибке
        
        return $product;
    }
}
```

### Валидация API запросов

```php
use KinopoiskDev\Services\ValidationService;
use KinopoiskDev\Attributes\Validation;

class MovieSearchRequest
{
    #[Validation(minLength: 1, maxLength: 100)]
    public ?string $query = null;
    
    #[Validation(min: 1900, max: 2100)]
    public ?int $year = null;
    
    #[Validation(min: 0, max: 10)]
    public ?float $minRating = null;
    
    #[Validation(allowedValues: ['movie', 'tv-series', 'cartoon', 'anime'])]
    public ?string $type = null;
    
    #[Validation(min: 1)]
    public int $page = 1;
    
    #[Validation(min: 1, max: 100)]
    public int $limit = 20;
}

class MovieController
{
    private ValidationService $validator;
    
    public function search(array $params): array
    {
        $request = new MovieSearchRequest();
        $request->query = $params['query'] ?? null;
        $request->year = isset($params['year']) ? (int)$params['year'] : null;
        $request->minRating = isset($params['minRating']) ? (float)$params['minRating'] : null;
        $request->type = $params['type'] ?? null;
        $request->page = isset($params['page']) ? (int)$params['page'] : 1;
        $request->limit = isset($params['limit']) ? (int)$params['limit'] : 20;
        
        try {
            $this->validator->validate($request);
        } catch (ValidationException $e) {
            return [
                'error' => true,
                'errors' => $e->getErrors()
            ];
        }
        
        // Выполнение поиска...
        return $this->movieService->search($request);
    }
}
```

### Кастомные сообщения об ошибках

```php
use KinopoiskDev\Attributes\Validation;

class LoginForm
{
    #[Validation(
        required: true, 
        minLength: 3,
        customMessage: 'Имя пользователя обязательно и должно содержать минимум 3 символа'
    )]
    public string $username;
    
    #[Validation(
        required: true, 
        minLength: 8,
        pattern: '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d).+$/',
        customMessage: 'Пароль должен содержать минимум 8 символов, включая заглавные и строчные буквы, а также цифры'
    )]
    public string $password;
    
    #[Validation(
        pattern: '/^\+?[1-9]\d{1,14}$/',
        customMessage: 'Неверный формат телефона. Используйте международный формат: +71234567890'
    )]
    public ?string $phone = null;
}
```

### Валидация вложенных объектов

```php
use KinopoiskDev\Services\ValidationService;
use KinopoiskDev\Attributes\Validation;

class Address
{
    #[Validation(required: true)]
    public string $street;
    
    #[Validation(required: true)]
    public string $city;
    
    #[Validation(pattern: '/^\d{6}$/')]
    public string $zipCode;
}

class User
{
    #[Validation(required: true)]
    public string $name;
    
    public ?Address $address = null;
    
    public function validate(): void
    {
        $validator = new ValidationService();
        
        // Валидация основного объекта
        $validator->validate($this);
        
        // Валидация вложенного объекта
        if ($this->address !== null) {
            $validator->validate($this->address);
        }
    }
}
```

## Особенности

1. **Атрибут-ориентированная валидация** - Использует PHP 8.3 атрибуты
2. **Гибкие правила** - Поддержка различных типов валидации
3. **Кастомные сообщения** - Возможность задать свои сообщения об ошибках
4. **Массовая валидация** - Валидация как объектов, так и массивов
5. **Детальные ошибки** - Подробная информация о каждой ошибке валидации

## Связанные классы

- [Validation](../attributes/Validation.md) - Атрибут валидации
- [ValidationException](../exceptions/ValidationException.md) - Исключение валидации
- [Kinopoisk](../Kinopoisk.md) - Использует сервис для валидации

## Требования

- PHP 8.3+
- Поддержка атрибутов (Attributes)
- Reflection API