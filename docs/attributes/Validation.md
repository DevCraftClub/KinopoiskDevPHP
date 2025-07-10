# Атрибуты валидации и метаданных

## Описание

Файл содержит три атрибута для работы с моделями:
- `Validation` - атрибут для валидации свойств модели
- `ApiField` - атрибут для указания источника поля в API
- `Sensitive` - атрибут для конфиденциальных полей

## Пространство имен

```php
namespace KinopoiskDev\Attributes;
```

---

## Атрибут Validation

### Описание

`Validation` - атрибут для валидации свойств модели. Предоставляет декларативный способ задания правил валидации для свойств моделей с использованием PHP 8.3 Attributes.

### Объявление

```php
#[Attribute(Attribute::TARGET_PROPERTY)]
final readonly class Validation
```

### Свойства

| Свойство | Тип | Описание |
|----------|-----|----------|
| `$required` | `bool` | Обязательное ли поле (по умолчанию false) |
| `$minLength` | `?int` | Минимальная длина для строк |
| `$maxLength` | `?int` | Максимальная длина для строк |
| `$min` | `?float` | Минимальное значение для чисел |
| `$max` | `?float` | Максимальное значение для чисел |
| `$pattern` | `?string` | Регулярное выражение для проверки |
| `$allowedValues` | `array` | Массив допустимых значений |
| `$customMessage` | `?string` | Кастомное сообщение об ошибке |

### Примеры использования

```php
use KinopoiskDev\Attributes\Validation;

class User
{
    #[Validation(required: true, minLength: 3, maxLength: 50)]
    public string $username;
    
    #[Validation(required: true, pattern: '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/')]
    public string $email;
    
    #[Validation(min: 18, max: 120, customMessage: 'Возраст должен быть от 18 до 120 лет')]
    public int $age;
    
    #[Validation(allowedValues: ['active', 'inactive', 'pending'])]
    public string $status;
}
```

---

## Атрибут ApiField

### Описание

`ApiField` - атрибут для указания источника поля в API и его характеристик.

### Объявление

```php
#[Attribute(Attribute::TARGET_PROPERTY)]
final readonly class ApiField
```

### Свойства

| Свойство | Тип | Описание |
|----------|-----|----------|
| `$name` | `?string` | Имя поля в API (если отличается от свойства) |
| `$nullable` | `bool` | Может ли поле быть null (по умолчанию true) |
| `$default` | `mixed` | Значение по умолчанию |

### Примеры использования

```php
use KinopoiskDev\Attributes\ApiField;

class Movie
{
    #[ApiField(name: 'title_ru')]
    public string $titleRu;
    
    #[ApiField(name: 'release_date', nullable: false)]
    public DateTime $releaseDate;
    
    #[ApiField(default: 0)]
    public int $rating;
    
    #[ApiField(name: 'is_published', default: false)]
    public bool $isPublished;
}
```

---

## Атрибут Sensitive

### Описание

`Sensitive` - атрибут для маркировки конфиденциальных полей, которые должны быть скрыты при сериализации.

### Объявление

```php
#[Attribute(Attribute::TARGET_PROPERTY)]
final readonly class Sensitive
```

### Свойства

| Свойство | Тип | Описание |
|----------|-----|----------|
| `$hideInJson` | `bool` | Скрывать при JSON сериализации (по умолчанию true) |
| `$hideInArray` | `bool` | Скрывать при преобразовании в массив (по умолчанию false) |

### Примеры использования

```php
use KinopoiskDev\Attributes\Sensitive;

class UserAccount
{
    public string $username;
    
    #[Sensitive]
    public string $password;
    
    #[Sensitive(hideInJson: true, hideInArray: true)]
    public string $apiSecret;
    
    #[Sensitive(hideInArray: true)]
    public string $personalData;
}
```

## Комплексный пример

```php
use KinopoiskDev\Attributes\{Validation, ApiField, Sensitive};

class UserProfile
{
    #[Validation(required: true, minLength: 3, maxLength: 20)]
    #[ApiField(name: 'user_name')]
    public string $username;
    
    #[Validation(required: true, pattern: '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/')]
    #[ApiField(name: 'email_address')]
    public string $email;
    
    #[Validation(required: true, minLength: 8)]
    #[Sensitive]
    public string $password;
    
    #[Validation(min: 18, max: 120)]
    #[ApiField(nullable: true)]
    public ?int $age;
    
    #[Validation(allowedValues: ['user', 'admin', 'moderator'])]
    #[ApiField(default: 'user')]
    public string $role;
    
    #[Sensitive(hideInJson: true, hideInArray: true)]
    #[ApiField(name: 'api_token')]
    public ?string $apiToken;
}
```

## Обработка атрибутов с рефлексией

```php
use ReflectionClass;
use ReflectionProperty;
use KinopoiskDev\Attributes\{Validation, ApiField, Sensitive};

class AttributeProcessor
{
    public function validateObject(object $object): array
    {
        $errors = [];
        $reflection = new ReflectionClass($object);
        
        foreach ($reflection->getProperties() as $property) {
            $validationAttrs = $property->getAttributes(Validation::class);
            
            if (empty($validationAttrs)) {
                continue;
            }
            
            $validation = $validationAttrs[0]->newInstance();
            $value = $property->getValue($object);
            
            // Проверка обязательности
            if ($validation->required && empty($value)) {
                $errors[$property->getName()][] = $validation->customMessage 
                    ?? "Поле {$property->getName()} обязательно";
            }
            
            // Проверка длины строки
            if (is_string($value)) {
                if ($validation->minLength && strlen($value) < $validation->minLength) {
                    $errors[$property->getName()][] = "Минимальная длина: {$validation->minLength}";
                }
                if ($validation->maxLength && strlen($value) > $validation->maxLength) {
                    $errors[$property->getName()][] = "Максимальная длина: {$validation->maxLength}";
                }
            }
            
            // Проверка числовых значений
            if (is_numeric($value)) {
                if ($validation->min !== null && $value < $validation->min) {
                    $errors[$property->getName()][] = "Минимальное значение: {$validation->min}";
                }
                if ($validation->max !== null && $value > $validation->max) {
                    $errors[$property->getName()][] = "Максимальное значение: {$validation->max}";
                }
            }
            
            // Проверка паттерна
            if ($validation->pattern && !preg_match($validation->pattern, (string)$value)) {
                $errors[$property->getName()][] = "Значение не соответствует формату";
            }
            
            // Проверка допустимых значений
            if (!empty($validation->allowedValues) && !in_array($value, $validation->allowedValues)) {
                $errors[$property->getName()][] = "Недопустимое значение";
            }
        }
        
        return $errors;
    }
    
    public function toArray(object $object, bool $hideSensitive = true): array
    {
        $result = [];
        $reflection = new ReflectionClass($object);
        
        foreach ($reflection->getProperties() as $property) {
            // Проверка Sensitive атрибута
            $sensitiveAttrs = $property->getAttributes(Sensitive::class);
            if ($hideSensitive && !empty($sensitiveAttrs)) {
                $sensitive = $sensitiveAttrs[0]->newInstance();
                if ($sensitive->hideInArray) {
                    continue;
                }
            }
            
            // Получение имени из ApiField
            $apiFieldAttrs = $property->getAttributes(ApiField::class);
            $fieldName = $property->getName();
            
            if (!empty($apiFieldAttrs)) {
                $apiField = $apiFieldAttrs[0]->newInstance();
                if ($apiField->name) {
                    $fieldName = $apiField->name;
                }
            }
            
            $result[$fieldName] = $property->getValue($object);
        }
        
        return $result;
    }
}
```

## Особенности

1. **PHP 8.3 Attributes** - Использование современного механизма атрибутов
2. **Декларативность** - Правила валидации и метаданные задаются прямо в коде
3. **Гибкость** - Можно комбинировать несколько атрибутов на одном свойстве
4. **Типобезопасность** - Readonly классы с типизированными свойствами
5. **Рефлексия** - Легко обрабатывать с помощью Reflection API

## Связанные классы

- [ValidationService](../services/ValidationService.md) - Сервис валидации
- Модели в папке [Models](../models/) - Используют эти атрибуты
- `ReflectionAttribute` - PHP класс для работы с атрибутами

## Требования

- PHP 8.3+
- Поддержка атрибутов (Attributes)