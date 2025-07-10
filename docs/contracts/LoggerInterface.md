# Интерфейс LoggerInterface

## Описание

`LoggerInterface` - интерфейс для логирования, определяющий контракт для ведения журнала событий с поддержкой различных уровней логирования.

## Пространство имен

```php
namespace KinopoiskDev\Contracts;
```

## Объявление интерфейса

```php
interface LoggerInterface
```

## Методы

### debug()

Записывает сообщение уровня DEBUG.

```php
public function debug(string $message, array $context = []): void
```

**Параметры:**
- `$message` - Сообщение для записи
- `$context` - Контекстные данные (дополнительная информация)

### info()

Записывает информационное сообщение.

```php
public function info(string $message, array $context = []): void
```

**Параметры:**
- `$message` - Сообщение для записи
- `$context` - Контекстные данные

### warning()

Записывает предупреждение.

```php
public function warning(string $message, array $context = []): void
```

**Параметры:**
- `$message` - Сообщение для записи
- `$context` - Контекстные данные

### error()

Записывает сообщение об ошибке.

```php
public function error(string $message, array $context = []): void
```

**Параметры:**
- `$message` - Сообщение для записи
- `$context` - Контекстные данные

### critical()

Записывает критическое сообщение.

```php
public function critical(string $message, array $context = []): void
```

**Параметры:**
- `$message` - Сообщение для записи
- `$context` - Контекстные данные

## Уровни логирования

| Уровень | Описание | Пример использования |
|---------|----------|---------------------|
| `DEBUG` | Детальная отладочная информация | Трассировка выполнения, состояние переменных |
| `INFO` | Информационные сообщения | Успешные операции, статусы процессов |
| `WARNING` | Предупреждения о потенциальных проблемах | Устаревшие методы, некритичные ошибки |
| `ERROR` | Ошибки, не прерывающие выполнение | Ошибки валидации, проблемы с внешними сервисами |
| `CRITICAL` | Критические ошибки | Недоступность базы данных, системные сбои |

## Примеры использования

### Простая реализация с файлом

```php
use KinopoiskDev\Contracts\LoggerInterface;

class FileLogger implements LoggerInterface
{
    private string $logFile;
    
    public function __construct(string $logFile = 'app.log')
    {
        $this->logFile = $logFile;
    }
    
    public function debug(string $message, array $context = []): void
    {
        $this->log('DEBUG', $message, $context);
    }
    
    public function info(string $message, array $context = []): void
    {
        $this->log('INFO', $message, $context);
    }
    
    public function warning(string $message, array $context = []): void
    {
        $this->log('WARNING', $message, $context);
    }
    
    public function error(string $message, array $context = []): void
    {
        $this->log('ERROR', $message, $context);
    }
    
    public function critical(string $message, array $context = []): void
    {
        $this->log('CRITICAL', $message, $context);
    }
    
    private function log(string $level, string $message, array $context): void
    {
        $timestamp = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? json_encode($context) : '';
        $logMessage = "[$timestamp] [$level] $message $contextStr\n";
        
        file_put_contents($this->logFile, $logMessage, FILE_APPEND);
    }
}
```

### Адаптер для PSR-3 логгера

```php
use KinopoiskDev\Contracts\LoggerInterface;
use Psr\Log\LoggerInterface as PsrLoggerInterface;

class PsrLoggerAdapter implements LoggerInterface
{
    public function __construct(
        private readonly PsrLoggerInterface $psrLogger
    ) {}
    
    public function debug(string $message, array $context = []): void
    {
        $this->psrLogger->debug($message, $context);
    }
    
    public function info(string $message, array $context = []): void
    {
        $this->psrLogger->info($message, $context);
    }
    
    public function warning(string $message, array $context = []): void
    {
        $this->psrLogger->warning($message, $context);
    }
    
    public function error(string $message, array $context = []): void
    {
        $this->psrLogger->error($message, $context);
    }
    
    public function critical(string $message, array $context = []): void
    {
        $this->psrLogger->critical($message, $context);
    }
}
```

### Реализация с уровнями и фильтрацией

```php
use KinopoiskDev\Contracts\LoggerInterface;

class LevelLogger implements LoggerInterface
{
    private const LEVELS = [
        'DEBUG' => 100,
        'INFO' => 200,
        'WARNING' => 300,
        'ERROR' => 400,
        'CRITICAL' => 500
    ];
    
    private int $minLevel;
    private array $handlers = [];
    
    public function __construct(string $minLevel = 'DEBUG')
    {
        $this->minLevel = self::LEVELS[$minLevel] ?? 100;
    }
    
    public function addHandler(callable $handler): void
    {
        $this->handlers[] = $handler;
    }
    
    public function debug(string $message, array $context = []): void
    {
        $this->log('DEBUG', $message, $context);
    }
    
    public function info(string $message, array $context = []): void
    {
        $this->log('INFO', $message, $context);
    }
    
    public function warning(string $message, array $context = []): void
    {
        $this->log('WARNING', $message, $context);
    }
    
    public function error(string $message, array $context = []): void
    {
        $this->log('ERROR', $message, $context);
    }
    
    public function critical(string $message, array $context = []): void
    {
        $this->log('CRITICAL', $message, $context);
    }
    
    private function log(string $level, string $message, array $context): void
    {
        if (self::LEVELS[$level] < $this->minLevel) {
            return;
        }
        
        $record = [
            'level' => $level,
            'message' => $message,
            'context' => $context,
            'timestamp' => new \DateTime()
        ];
        
        foreach ($this->handlers as $handler) {
            $handler($record);
        }
    }
}
```

### Использование в сервисе

```php
use KinopoiskDev\Contracts\LoggerInterface;

class ApiClient
{
    public function __construct(
        private readonly LoggerInterface $logger
    ) {}
    
    public function fetchData(string $endpoint): array
    {
        $this->logger->info('Fetching data from API', [
            'endpoint' => $endpoint
        ]);
        
        try {
            $data = $this->makeRequest($endpoint);
            
            $this->logger->debug('API response received', [
                'endpoint' => $endpoint,
                'dataSize' => strlen(json_encode($data))
            ]);
            
            return $data;
            
        } catch (\Exception $e) {
            $this->logger->error('API request failed', [
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e;
        }
    }
}
```

### Композитный логгер

```php
use KinopoiskDev\Contracts\LoggerInterface;

class CompositeLogger implements LoggerInterface
{
    private array $loggers = [];
    
    public function addLogger(LoggerInterface $logger): void
    {
        $this->loggers[] = $logger;
    }
    
    public function debug(string $message, array $context = []): void
    {
        foreach ($this->loggers as $logger) {
            $logger->debug($message, $context);
        }
    }
    
    public function info(string $message, array $context = []): void
    {
        foreach ($this->loggers as $logger) {
            $logger->info($message, $context);
        }
    }
    
    public function warning(string $message, array $context = []): void
    {
        foreach ($this->loggers as $logger) {
            $logger->warning($message, $context);
        }
    }
    
    public function error(string $message, array $context = []): void
    {
        foreach ($this->loggers as $logger) {
            $logger->error($message, $context);
        }
    }
    
    public function critical(string $message, array $context = []): void
    {
        foreach ($this->loggers as $logger) {
            $logger->critical($message, $context);
        }
    }
}
```

## Особенности

1. **Простота** - Минимальный набор методов для основных уровней логирования
2. **Контекст** - Поддержка контекстных данных для каждого сообщения
3. **Совместимость** - Легко адаптируется к PSR-3 логгерам
4. **Гибкость** - Можно реализовать любой механизм хранения логов

## Связанные классы

- [Kinopoisk](../Kinopoisk.md) - Использует интерфейс для логирования операций
- `Psr\Log\LoggerInterface` - PSR-3 стандарт логирования
- `Monolog\Logger` - Популярная библиотека логирования

## Требования

- PHP 8.3+
- Опционально: PSR-3 Logger Interface