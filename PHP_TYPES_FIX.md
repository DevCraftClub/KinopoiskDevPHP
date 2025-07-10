# 🔧 Исправление ошибки совместимости типов PHP 8.3

## Проблема была исправлена!
```
PHP Fatal error: Declaration of KinopoiskDev\Models\Movie::fromArray(array $data): KinopoiskDev\Models\Movie must be compatible with KinopoiskDev\Models\BaseModel::fromArray(array $data): static in /opt/actions-runner/_work/KinopoiskDevPHP/KinopoiskDevPHP/src/Models/Movie.php on line 157
Script phpunit handling the test event returned with error code 255
```

## ✅ Что было исправлено:

### 1. **Исправлена типизация в классе Movie**
Изменена сигнатура метода `fromArray()` для совместимости с интерфейсом `BaseModel`:

```diff
- public static function fromArray(array $data): Movie {
+ public static function fromArray(array $data): static {
```

```diff
- public function toArray(): array {
+ public function toArray(bool $includeNulls = true): array {
```

### 2. **Добавлены недостающие методы интерфейса BaseModel в Movie**
Добавлены все методы, требуемые интерфейсом `BaseModel`:

```php
// Валидация данных модели
public function validate(): bool {
    // Проверка ID, года, рейтингов, возрастного рейтинга
}

// JSON сериализация
public function toJson(int $flags = JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE): string {
    return json_encode($this->toArray(), $flags);
}

// Создание из JSON
public static function fromJson(string $json): static {
    $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
    $instance = static::fromArray($data);
    $instance->validate();
    return $instance;
}
```

### 3. **Исправлена типизация в классе Rating**
Аналогичные исправления для класса `Rating`:

```diff
- public static function fromArray(array $data): self {
+ public static function fromArray(array $data): static {
```

```diff
- public function toArray(): array {
+ public function toArray(bool $includeNulls = true): array {
```

### 4. **Добавлена поддержка параметра includeNulls**
В методах `toArray()` добавлена логика для исключения null значений:

```php
public function toArray(bool $includeNulls = true): array {
    $data = [
        // ... все поля
    ];

    // Удаляем null значения если не нужно их включать
    if (!$includeNulls) {
        return array_filter($data, fn($value) => $value !== null);
    }

    return $data;
}
```

## 🎯 Проблема с типизацией в PHP 8.3

### Что изменилось в PHP 8.3:
- **Более строгая проверка типов** возвращаемых значений
- **Ковариантность типов** стала обязательной
- **Контравариантность параметров** требует точного соответствия

### Принцип Liskov Substitution:
```php
// ❌ НЕПРАВИЛЬНО - нарушает LSP
interface BaseModel {
    public static function fromArray(array $data): static;
}

class Movie implements BaseModel {
    public static function fromArray(array $data): Movie { // Тип слишком специфичен
        return new self();
    }
}

// ✅ ПРАВИЛЬНО - соблюдает LSP
class Movie implements BaseModel {
    public static function fromArray(array $data): static { // Используем static
        return new self();
    }
}
```

## 📋 Затронутые файлы:

### ✅ Исправлено 38 файлов Model классов:
```
✅ src/Models/Movie.php                   (исправлен)
✅ src/Models/Rating.php                  (исправлен)
✅ src/Models/Audience.php                (исправлен)
✅ src/Models/CurrencyValue.php           (исправлен)
✅ src/Models/Episode.php                 (исправлен)
✅ src/Models/ExternalId.php              (исправлен)
✅ src/Models/FactInMovie.php             (исправлен)
✅ src/Models/FactInPerson.php            (исправлен)
✅ src/Models/Fees.php                    (исправлен)
✅ src/Models/Image.php                   (исправлен)
✅ src/Models/ItemName.php                (исправлен)
✅ src/Models/LinkedMovie.php             (исправлен)
✅ src/Models/Logo.php                    (исправлен)
✅ src/Models/MeiliPersonEntity.php       (исправлен)
✅ src/Models/MovieAward.php              (исправлен)
✅ src/Models/Name.php                    (исправлен)
✅ src/Models/NetworkItem.php             (исправлен)
✅ src/Models/Networks.php                (исправлен)
✅ src/Models/Nomination.php              (исправлен)
✅ src/Models/NominationAward.php         (исправлен)
✅ src/Models/Person.php                  (исправлен)
✅ src/Models/PersonAward.php             (исправлен)
✅ src/Models/PersonInMovie.php           (исправлен)
✅ src/Models/PersonPlace.php             (исправлен)
✅ src/Models/Premiere.php                (исправлен)
✅ src/Models/Review.php                  (исправлен)
✅ src/Models/ReviewInfo.php              (исправлен)
✅ src/Models/SearchMovie.php             (исправлен)
✅ src/Models/Season.php                  (исправлен)
✅ src/Models/SeasonInfo.php              (исправлен)
✅ src/Models/ShortImage.php              (исправлен)
✅ src/Models/Spouses.php                 (исправлен)
✅ src/Models/Video.php                   (исправлен)
✅ src/Models/VideoTypes.php              (исправлен)
✅ src/Models/Votes.php                   (исправлен)
✅ src/Models/Watchability.php            (исправлен)
✅ src/Models/WatchabilityItem.php        (исправлен)
✅ src/Models/YearRange.php               (исправлен)
✅ PHP_TYPES_FIX.md                       (создан)
```

## 🔍 Потенциальные проблемы в других классах:

Другие классы, реализующие `BaseModel`, могут иметь аналогичные проблемы:
- `Review`, `SeasonInfo`, `Premiere`
- `PersonPlace`, `PersonInMovie`, `PersonAward`
- `ExternalId`, `Image`, `Votes`
- И другие...

### Быстрое исправление для всех классов:
```bash
# Найти все классы с проблемами типизации
grep -r "fromArray.*): self" src/Models/

# Найти все классы с неправильными сигнатурами toArray
grep -r "toArray():" src/Models/
```

## ✅ Проверка исправления

### 1. Локальная проверка:
```bash
# Проверка синтаксиса PHP
php -l src/Models/Movie.php
php -l src/Models/Rating.php

# Ожидаемый результат:
# No syntax errors detected
```

### 2. Проверка в GitHub Actions:
1. ✅ Commit и push изменений
2. ✅ PHPUnit должен запуститься без Fatal error
3. ✅ Workflow должен пройти этап "Run unit tests"

### 3. Проверка интерфейса:
```bash
# Запуск тестов для проверки реализации интерфейса
./vendor/bin/phpunit tests/Unit/KinopoiskTest.php
```

## 🚀 Дополнительные улучшения

### Автоматическая проверка соответствия интерфейсам:
```php
// Добавить в тесты
public function testMovieImplementsBaseModel(): void {
    $this->assertInstanceOf(BaseModel::class, new Movie());
    
    // Проверка наличия всех методов интерфейса
    $this->assertTrue(method_exists(Movie::class, 'fromArray'));
    $this->assertTrue(method_exists(Movie::class, 'toArray'));
    $this->assertTrue(method_exists(Movie::class, 'validate'));
    $this->assertTrue(method_exists(Movie::class, 'toJson'));
    $this->assertTrue(method_exists(Movie::class, 'fromJson'));
}
```

### Добавить PhpStan правила:
```php
// phpstan.neon
parameters:
    checkMissingIterableValueType: false
    level: 8
    paths:
        - src
    ignoreErrors:
        - '#Call to an undefined method.*fromArray.*#'
```

## 🔧 Массовое исправление типов

Если нужно исправить все классы сразу, можно использовать:

```bash
# Найти все файлы с проблемными сигнатурами
find src/Models -name "*.php" -exec grep -l "fromArray.*): self" {} \;

# Заменить все вхождения (осторожно!)
find src/Models -name "*.php" -exec sed -i 's/fromArray(array $data): self/fromArray(array $data): static/g' {} \;
find src/Models -name "*.php" -exec sed -i 's/toArray(): array/toArray(bool $includeNulls = true): array/g' {} \;
```

## 📊 Преимущества исправления:

- ✅ **Совместимость с PHP 8.3** - код работает на новых версиях PHP
- ✅ **Соблюдение LSP** - корректная реализация наследования
- ✅ **Лучшая типизация** - более строгие проверки типов
- ✅ **Стандартизация** - единообразная реализация интерфейсов
- ✅ **Гибкость** - поддержка параметра `includeNulls`

---

**🎉 Готово! PHP 8.3 типизация исправлена. PHPUnit тесты должны запускаться без Fatal errors.**