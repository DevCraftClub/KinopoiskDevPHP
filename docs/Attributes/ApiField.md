# ApiField

**Описание:** Атрибут для указания источника поля в API
Позволяет настроить маппинг между свойствами модели и полями API.
Поддерживает указание имени поля в API, возможность null значений
и значения по умолчанию.

**С версии:** 1.0.0

**Версия:** 1.0.0

**Пример:**
```php
#[ApiField(name: 'movie_title', nullable: false, default: 'Unknown')]
public string $title;
#[ApiField(name: 'release_year', nullable: true)]
public ?int $year;
```

## `__construct()`

**Описание:** Конструктор атрибута API поля

**Параметры:**

* `$name` (string|null): Имя поля в API (если null, используется имя свойства)
* `$nullable` (bool): Разрешены ли null значения (по умолчанию true)
* `$default` (mixed): Значение по умолчанию при отсутствии данных

