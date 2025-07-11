# PHPUnit Tests для KinopoiskDev

Этот каталог содержит полный набор PHPUnit тестов для библиотеки KinopoiskDev API клиента.

## Структура тестов

```
tests/
├── Unit/                    # Модульные тесты
│   ├── Http/               # Тесты HTTP клиентов
│   │   ├── MovieRequestsTest.php
│   │   ├── PersonRequestsTest.php
│   │   ├── StudioRequestsTest.php
│   │   ├── SeasonRequestsTest.php
│   │   ├── ReviewRequestsTest.php
│   │   ├── ImageRequestsTest.php
│   │   ├── KeywordRequestsTest.php
│   │   └── ListRequestsTest.php
│   ├── Models/             # Тесты моделей данных
│   │   ├── MovieTest.php
│   │   ├── PersonTest.php
│   │   ├── StudioTest.php
│   │   └── ... (другие модели)
│   ├── Filter/             # Тесты фильтров и сортировки
│   │   ├── MovieSearchFilterTest.php
│   │   ├── PersonSearchFilterTest.php
│   │   ├── SortCriteriaTest.php
│   │   └── ... (другие фильтры)
│   ├── Services/           # Тесты сервисов
│   │   ├── CacheServiceTest.php
│   │   ├── ValidationServiceTest.php
│   │   └── ... (другие сервисы)
│   ├── Utils/              # Тесты утилит
│   │   ├── DataManagerTest.php
│   │   ├── SortManagerTest.php
│   │   └── ... (другие утилиты)
│   └── KinopoiskTest.php   # Тест главного класса
└── README.md               # Этот файл
```

## Установка и настройка

### 1. Установка зависимостей

```bash
composer install --dev
```

### 2. Настройка переменных окружения

Создайте файл `.env.testing` или установите переменные окружения:

```bash
# Скопируйте пример файла
cp env.testing.example .env.testing

# Отредактируйте файл .env.testing и установите ваш API токен
KINOPOISK_API_TOKEN=your_test_api_token_here
APP_ENV=testing
```

Или установите переменные окружения напрямую:

```bash
export KINOPOISK_API_TOKEN="your-test-api-token"
export APP_ENV="testing"
```

### 3. Проверка конфигурации PHPUnit

Убедитесь, что файл `phpunit.xml` настроен правильно и находится в корне проекта.

## Запуск тестов

### Запуск всех тестов

```bash
./vendor/bin/phpunit
```

### Запуск конкретной группы тестов

```bash
# Все HTTP тесты
./vendor/bin/phpunit --testsuite HTTP

# Все тесты моделей
./vendor/bin/phpunit --testsuite Models

# Все тесты фильтров
./vendor/bin/phpunit --testsuite Filter

# Все тесты сервисов
./vendor/bin/phpunit --testsuite Services

# Все тесты утилит
./vendor/bin/phpunit --testsuite Utils
```

### Запуск тестов по группам

```bash
# Тесты HTTP клиентов
./vendor/bin/phpunit --group http

# Тесты моделей
./vendor/bin/phpunit --group models

# Тесты фильтров
./vendor/bin/phpunit --group filter

# Тесты сервисов
./vendor/bin/phpunit --group services

# Тесты утилит
./vendor/bin/phpunit --group utils
```

### Запуск конкретного теста

```bash
# Конкретный тест
./vendor/bin/phpunit tests/Unit/Http/MovieRequestsTest.php

# Конкретный метод теста
./vendor/bin/phpunit --filter test_getMovieById_withValidId_returnsMovie tests/Unit/Http/MovieRequestsTest.php
```

### Запуск с покрытием кода

```bash
# HTML отчет о покрытии
./vendor/bin/phpunit --coverage-html coverage/html

# Текстовый отчет о покрытии
./vendor/bin/phpunit --coverage-text

# Clover XML отчет
./vendor/bin/phpunit --coverage-clover coverage/clover.xml
```

### Запуск с подробным выводом

```bash
# Подробный вывод
./vendor/bin/phpunit --verbose

# Вывод с деталями о времени выполнения
./vendor/bin/phpunit --debug
```

## Структура тестов

### HTTP тесты

HTTP тесты используют мокирование для имитации HTTP запросов:

- **MockHandler** - для имитации HTTP ответов
- **HandlerStack** - для настройки обработчиков
- **Response** - для создания ответов
- **RequestException** - для имитации ошибок сети

Пример:
```php
$mockHandler = new MockHandler();
$mockHandler->append(new Response(200, [], json_encode($data)));
```

### Тесты моделей

Тесты моделей проверяют:

- Создание объектов из массивов данных
- Валидацию данных
- Сериализацию/десериализацию
- Геттеры/сеттеры
- Обработку null значений

### Тесты фильтров

Тесты фильтров проверяют:

- Создание фильтров
- Комбинации фильтров
- Сортировку
- Пагинацию
- Валидацию параметров

### Тесты сервисов

Тесты сервисов проверяют:

- Кэширование
- Валидацию
- Обработку ошибок
- Логирование

## Data Providers

Многие тесты используют data providers для тестирования различных сценариев:

```php
/**
 * @dataProvider validApiTokenProvider
 */
public function test_constructor_withValidApiToken_createsInstance(string $apiToken): void
{
    // Тест логика
}

public function validApiTokenProvider(): array
{
    return [
        'valid_token_1' => ['ABC1DEF-2GH3IJK-4LM5NOP-6QR7STU'],
        'valid_token_2' => ['XYZ9ABC-1DEF2GHI-3JKL4MNO-5PQR6STU'],
    ];
}
```

## Группировка тестов

Тесты сгруппированы с помощью аннотаций:

```php
/**
 * @group unit
 * @group http
 * @group movie-requests
 */
class MovieRequestsTest extends TestCase
```

## Обработка исключений

Тесты проверяют различные типы исключений:

```php
public function test_invalidApiToken_throwsException(): void
{
    $this->expectException(ValidationException::class);
    $this->expectExceptionMessage('API токен должен быть в формате: XXXX-XXXX-XXXX-XXXX');
    
    new Kinopoisk('invalid-token');
}
```

## Мокирование

Тесты используют мокирование для изоляции тестируемого кода:

```php
$cacheMock = $this->createMock(CacheInterface::class);
$loggerMock = $this->createMock(LoggerInterface::class);
```

## Покрытие кода

Цель - достичь 100% покрытия кода:

- Все публичные методы
- Все ветки условий
- Все исключения
- Все edge cases

## Отчеты о покрытии

После запуска тестов с покрытием, отчеты будут доступны в:

- `coverage/html/` - HTML отчет
- `coverage/coverage.txt` - Текстовый отчет
- `coverage/clover.xml` - XML отчет для CI/CD

## CI/CD интеграция

Тесты настроены для работы с CI/CD системами:

```yaml
# GitHub Actions пример
- name: Run tests
  run: ./vendor/bin/phpunit --coverage-clover coverage/clover.xml

- name: Upload coverage
  uses: codecov/codecov-action@v3
  with:
    file: coverage/clover.xml
```

## Отладка тестов

### Включение отладки

```bash
./vendor/bin/phpunit --debug --verbose
```

### Запуск одного теста

```bash
./vendor/bin/phpunit --filter testMethodName TestFile.php
```

### Просмотр переменных

Добавьте в тест:
```php
var_dump($variable);
```

## Лучшие практики

1. **Именование тестов**: `test_[method]_[scenario]_[expected_result]()`
2. **Изоляция**: Каждый тест должен быть независимым
3. **Мокирование**: Используйте моки для внешних зависимостей
4. **Data Providers**: Для тестирования множественных сценариев
5. **Группировка**: Используйте аннотации для логической группировки
6. **Покрытие**: Стремитесь к 100% покрытию кода

## Устранение неполадок

### Ошибки автозагрузки

```bash
composer dump-autoload
```

### Ошибки кэша PHPUnit

```bash
rm -rf .phpunit.cache
```

### Ошибки покрытия кода

```bash
# Установите Xdebug или PCOV
pecl install xdebug
# или
pecl install pcov
```

### Ошибки переменных окружения

```bash
# Проверьте наличие переменных
echo $KINOPOISK_TOKEN
echo $APP_ENV
```

## Дополнительные команды

### Быстрый запуск

```bash
# Только unit тесты
./vendor/bin/phpunit --testsuite Unit

# Без покрытия кода (быстрее)
./vendor/bin/phpunit --no-coverage

# Параллельный запуск (если доступно)
./vendor/bin/phpunit --parallel
```

### Анализ тестов

```bash
# Список всех тестов
./vendor/bin/phpunit --list-tests

# Список групп
./vendor/bin/phpunit --list-groups

# Список тестовых наборов
./vendor/bin/phpunit --list-testsuites
```

## Контакты

При возникновении проблем с тестами:

1. Проверьте документацию PHPUnit
2. Изучите логи ошибок
3. Создайте issue в репозитории проекта 