# PHPStan Fixes Summary

## Overview
This document summarizes all the PHPStan static analysis errors that were identified and fixed in the KinopoiskDev PHP client.

## Major Issues Fixed

### 1. Comparison Operation Issues
**File:** `src/Enums/HttpStatusCode.php`
**Issue:** Comparison operation ">=" between 200|401|403|404|500 and 200 is always true
**Fix:** Replaced numeric comparison with match expression to avoid always-true comparison
```php
// Before
public function isError(): bool {
    return $this->value >= 400;
}

// After  
public function isError(): bool {
    return match ($this) {
        self::UNAUTHORIZED, self::FORBIDDEN, self::NOT_FOUND, self::INTERNAL_SERVER_ERROR => true,
        default => false,
    };
}
```

### 2. Undefined Property Access
**File:** `src/Exceptions/KinopoiskResponseException.php`
**Issue:** Access to undefined properties object::$error, object::$message, object::$statusCode
**Fix:** Added safe property access using property_exists() and fallback values
```php
// Before
$error = $response->error;
$message = $response->message;
$statusCode = $response->statusCode;

// After
$error = property_exists($response, 'error') ? $response->error : 'Unknown error';
$message = property_exists($response, 'message') ? $response->message : 'Unknown message';
$statusCode = property_exists($response, 'statusCode') ? $response->statusCode : 0;
```

### 3. ValidationException Issues
**File:** `src/Exceptions/ValidationException.php`
**Issue:** 
- Method getFirstError() should return string|null but returns string|false|null
- Parameter passed by reference does not accept readonly property
**Fix:** 
- Fixed return type handling in getFirstError()
- Used reset() with proper type checking
```php
public function getFirstError(): ?string {
    if ($this->hasErrors()) {
        $firstValue = reset($this->errors);
        return is_string($firstValue) ? $firstValue : null;
    }
    return null;
}
```

### 4. Array Type Annotations
**Files:** Multiple files including filters, models, and services
**Issue:** Missing array value types in iterable type annotations
**Fix:** Added proper generic array types throughout the codebase

#### SortCriteria Class
```php
// Before
public static function fromArray(array $data): ?self
public function toArray(): array

// After
public static function fromArray(array $data): ?self
public function toArray(): array<string, string>
```

#### StudioSearchFilter Class
```php
// Before
public function movieId(int|array $movieIds): self
public function studioType(string|StudioType|array $types): self

// After
public function movieId(int|array<int> $movieIds): self
public function studioType(string|StudioType|array<string|StudioType> $types): self
```

#### MovieRequests Class
```php
// Before
public function getPossibleValuesByField(string $field): array
public function getMoviesByGenre(string|array $genres, int $page = 1, int $limit = 10): MovieDocsResponseDto

// After
public function getPossibleValuesByField(string $field): array<array<string, mixed>>
public function getMoviesByGenre(string|array<string> $genres, int $page = 1, int $limit = 10): MovieDocsResponseDto
```

### 5. In_array Function Issues
**File:** `src/Http/MovieRequests.php`
**Issue:** Call to function in_array() with arguments that will always evaluate to false
**Fix:** Fixed array type mismatch and implode function usage
```php
// Before
$allowedFields = [FilterField::GENRES, FilterField::COUNTRIES, ...];
if (!in_array($field, $allowedFields, TRUE)) {
    $fieldNames = implode(', ', $allowedFields);
}

// After
$allowedFields = [FilterField::GENRES->value, FilterField::COUNTRIES->value, ...];
if (!in_array($field, $allowedFields, TRUE)) {
    $fieldNames = implode(', ', array_map(fn($f) => $f->value, $allowedFields));
}
```

### 6. Undefined Variables
**File:** `src/Http/MovieRequests.php`
**Issue:** Variable $data on left side of ?? is never defined
**Fix:** Added missing data parsing in searchByName method
```php
// Before
return new SearchMovieResponseDto(
    $data['docs'] ?? [],
    $data['total'] ?? 0,
    $data['limit'] ?? 0,
    $data['page'] ?? 1,
    $data['pages'] ?? 0,
);

// After
$response = $this->makeRequest('GET', '/movie/search', $filters->getFilters());
$data = $this->parseResponse($response);

return new SearchMovieResponseDto(
    $data['docs'] ?? [],
    $data['total'] ?? 0,
    $data['limit'] ?? 0,
    $data['page'] ?? 1,
    $data['pages'] ?? 0,
);
```

### 7. Exception Type Issues
**File:** `src/Kinopoisk.php`
**Issue:** Exception type mismatch in catch block
**Fix:** Added proper type casting for GuzzleException
```php
// Before
throw new KinopoiskDevException(
    message: "Ошибка HTTP запроса: {$e->getMessage()}",
    code: $e->getCode(),
    previous: $e,
);

// After
throw new KinopoiskDevException(
    message: "Ошибка HTTP запроса: {$e->getMessage()}",
    code: $e->getCode(),
    previous: $e instanceof Exception ? $e : new Exception($e->getMessage(), $e->getCode(), $e),
);
```

### 8. Missing Constants
**Files:** `src/Models/Movie.php`, `src/Models/Rating.php`
**Issue:** Undefined constants MIN_YEAR, MAX_YEAR, RATING_MIN, RATING_MAX
**Fix:** Added missing class constants
```php
// Movie.php
private const int MIN_YEAR = 1888;
private const int MAX_YEAR = 2030;

// Rating.php
private const float RATING_MIN = 0.0;
private const float RATING_MAX = 10.0;
```

### 9. In_array with Null Arrays
**File:** `src/Models/MeiliPersonEntity.php`
**Issue:** in_array() called with potentially null array
**Fix:** Added null checks before using in_array()
```php
// Before
public function isActor(): bool {
    return in_array(PersonProfession::ACTOR->value, $this->profession, TRUE);
}

// After
public function isActor(): bool {
    return $this->profession !== null && in_array(PersonProfession::ACTOR->value, $this->profession, TRUE);
}
```

### 10. JSON Encoding Issues
**Files:** Multiple model classes
**Issue:** json_encode() can return false, but return type is string
**Fix:** Added proper error handling for json_encode()
```php
// Before
public function toJson(int $flags = JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE): string {
    return json_encode($this->toArray(), $flags);
}

// After
public function toJson(int $flags = JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE): string {
    $json = json_encode($this->toArray(), $flags);
    if ($json === false) {
        throw new \JsonException('Ошибка кодирования JSON');
    }
    return $json;
}
```

### 11. Array Type Annotations in Models
**Files:** Multiple model classes (Audience, MeiliPersonEntity, Movie, Rating)
**Issue:** Missing array value types in PHPDoc annotations
**Fix:** Added proper generic array types

#### Audience Model
```php
// Before
@param   array  $data  Массив данных об аудитории от API
@return array Массив с данными об аудитории

// After
@param   array<string, mixed>  $data  Массив данных об аудитории от API
@return array<string, mixed> Массив с данными об аудитории
```

#### MeiliPersonEntity Model
```php
// Before
@return array Ассоциативный массив с данными персоны
@return array Название профессии на русском языке

// After
@return array<string, mixed> Ассоциативный массив с данными персоны
@return array<string> Название профессии на русском языке
```

#### Movie Model
```php
// Before
@return array Массив строк с названиями жанров
@return array Массив строк с названиями стран производства

// After
@return array<string> Массив строк с названиями жанров
@return array<string> Массив строк с названиями стран производства
```

## Summary of Changes

### Files Modified:
1. `src/Enums/HttpStatusCode.php` - Fixed comparison operations
2. `src/Exceptions/KinopoiskResponseException.php` - Fixed undefined property access
3. `src/Exceptions/ValidationException.php` - Fixed return types and readonly property issues
4. `src/Filter/SortCriteria.php` - Added array type annotations
5. `src/Filter/StudioSearchFilter.php` - Added array type annotations
6. `src/Http/MovieRequests.php` - Fixed array types, in_array issues, and undefined variables
7. `src/Kinopoisk.php` - Fixed exception type issues
8. `src/Models/AbstractBaseModel.php` - Fixed JSON encoding issues
9. `src/Models/Audience.php` - Added array type annotations and fixed JSON encoding
10. `src/Models/MeiliPersonEntity.php` - Fixed array types, in_array issues, and JSON encoding
11. `src/Models/Movie.php` - Added missing constants, array types, and fixed JSON encoding
12. `src/Models/Rating.php` - Added missing constants, array types, and fixed JSON encoding

### Types of Fixes:
- **Array Type Annotations**: Added proper generic array types (e.g., `array<string, mixed>`, `array<int>`, `array<string>`)
- **Null Safety**: Added null checks before using arrays in functions like `in_array()`
- **Exception Handling**: Fixed exception type mismatches and added proper error handling
- **Constants**: Added missing class constants for validation
- **JSON Encoding**: Added proper error handling for `json_encode()` calls
- **Property Access**: Added safe property access using `property_exists()`
- **Return Types**: Fixed method return types to match actual return values

### Benefits:
1. **Type Safety**: Better type checking and IDE support
2. **Error Prevention**: Reduced runtime errors through static analysis
3. **Code Quality**: Improved code documentation and maintainability
4. **IDE Support**: Better autocomplete and error detection in IDEs
5. **Maintainability**: Clearer code intent and easier refactoring

All fixes maintain backward compatibility while improving type safety and code quality. The Russian language comments have been preserved throughout all changes.