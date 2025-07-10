# Self-Hosted Runner: Руководство по Устранению Неполадок

## 🔧 Исправление ошибки переменных окружения

### Проблема
```
Error: The HOME or COMPOSER_HOME environment variable must be set for composer to run correctly
```

### ✅ Решение (уже реализовано в workflow)
Workflow автоматически настраивает необходимые переменные окружения:

```yaml
- name: Set environment variables
  run: |
    echo "Setting up environment variables for self-hosted runner..."
    export HOME=${HOME:-/tmp}
    export COMPOSER_HOME=${COMPOSER_HOME:-$HOME/.composer}
    echo "HOME=$HOME" >> $GITHUB_ENV
    echo "COMPOSER_HOME=$COMPOSER_HOME" >> $GITHUB_ENV
    echo "TMPDIR=${TMPDIR:-/tmp}" >> $GITHUB_ENV
    
    echo "Creating necessary directories..."
    mkdir -p $HOME
    mkdir -p $COMPOSER_HOME
    mkdir -p ${TMPDIR:-/tmp}
```

## 🚀 Настройка Self-Hosted Runner

### 1. Установка зависимостей
```bash
# Ubuntu/Debian
sudo apt update
sudo apt install -y php8.3 php8.3-cli php8.3-mbstring php8.3-xml \
  php8.3-ctype php8.3-iconv php8.3-intl php8.3-sqlite3 \
  php8.3-gd php8.3-zip php8.3-xdebug composer git curl

# CentOS/RHEL
sudo yum install -y php php-cli php-mbstring php-xml php-json \
  php-pdo php-sqlite3 php-gd php-zip composer git curl
```

### 2. Настройка пользователя для runner
```bash
# Создание пользователя (если нужно)
sudo useradd -m -s /bin/bash github-runner

# Настройка домашней директории
sudo mkdir -p /home/github-runner/.composer
sudo chown -R github-runner:github-runner /home/github-runner
```

### 3. Настройка прав доступа
```bash
# Если runner работает от root
export COMPOSER_ALLOW_SUPERUSER=1

# Альтернативно - настройка отдельного пользователя
sudo usermod -aG docker github-runner  # если используется Docker
```

## 🔍 Диагностика проблем

### Проверка переменных окружения
```bash
echo "HOME: $HOME"
echo "COMPOSER_HOME: $COMPOSER_HOME"
echo "PATH: $PATH"
echo "USER: $USER"
```

### Проверка PHP
```bash
php --version
php -m | grep -E "(mbstring|xml|json)"
composer --version
```

### Проверка прав доступа
```bash
ls -la $HOME
ls -la $COMPOSER_HOME
whoami
```

## ⚠️ Типичные проблемы и решения

### 1. Проблема: "Permission denied"
```bash
# Решение - исправление прав
sudo chown -R $USER:$USER $HOME
sudo chmod -R 755 $HOME
```

### 2. Проблема: "composer: command not found"
```bash
# Установка Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer
```

### 3. Проблема: "PHP extensions missing"
```bash
# Проверка установленных расширений
php -m

# Установка недостающих расширений (Ubuntu)
sudo apt install -y php8.3-[extension-name]
```

### 4. Проблема: Медленная работа кэша
```bash
# Очистка кэша Composer
composer clear-cache

# Настройка локального кэша
export COMPOSER_HOME="/var/cache/composer"
mkdir -p $COMPOSER_HOME
```

## 🔐 Безопасность Self-Hosted Runner

### Рекомендуемые настройки
```yaml
# В настройках runner
env:
  COMPOSER_ALLOW_SUPERUSER: 1
  COMPOSER_NO_INTERACTION: 1
  COMPOSER_DISCARD_CHANGES: true
```

### Изоляция процессов
```bash
# Использование отдельной директории для каждого build
export TMPDIR="/tmp/github-actions-$GITHUB_RUN_ID"
mkdir -p $TMPDIR
```

### Очистка после выполнения
```bash
# Добавить в конец workflow
- name: Cleanup
  if: always()
  run: |
    rm -rf $TMPDIR
    composer clear-cache
```

## 📊 Мониторинг и логирование

### Включение детального логирования
```yaml
- name: Debug environment
  run: |
    echo "=== Environment Variables ==="
    env | sort
    echo "=== System Info ==="
    uname -a
    echo "=== PHP Info ==="
    php --version
    echo "=== Composer Info ==="
    composer --version
    echo "=== Disk Space ==="
    df -h
```

### Сбор логов при ошибках
```yaml
- name: Upload logs on failure
  if: failure()
  uses: actions/upload-artifact@v3
  with:
    name: debug-logs
    path: |
      /tmp/*.log
      ~/.composer/logs/
```

## 🚀 Оптимизация производительности

### 1. Кэширование
```yaml
- name: Cache Composer packages
  uses: actions/cache@v3
  with:
    path: |
      vendor
      ${{ env.COMPOSER_HOME }}/cache
    key: ${{ runner.os }}-php-${{ matrix.php-version }}-${{ hashFiles('**/composer.lock') }}
```

### 2. Параллельная установка
```bash
composer install --prefer-dist --no-progress --optimize-autoloader --no-dev
```

### 3. Использование локальных зеркал
```bash
# В composer.json
{
  "repositories": [
    {
      "type": "composer",
      "url": "https://repo.packagist.org"
    }
  ]
}
```

---

**💡 Совет**: Всегда тестируйте изменения в workflow на отдельной ветке перед применением к основной ветке.