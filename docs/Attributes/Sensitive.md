# Sensitive

**Описание:** Атрибут для конфиденциальных полей
Позволяет пометить поля как конфиденциальные для контроля
их отображения в различных контекстах (JSON, массивы, логи).

**С версии:** 1.0.0

**Версия:** 1.0.0

**Пример:**
```php
#[Sensitive(hideInJson: true, hideInArray: false)]
public string $apiToken;
#[Sensitive(hideInJson: true, hideInArray: true)]
public string $password;
```

## `__construct()`

**Описание:** Конструктор атрибута конфиденциального поля

**Параметры:**

* `$hideInJson` (bool): Скрывать ли поле в JSON сериализации (по умолчанию true)
* `$hideInArray` (bool): Скрывать ли поле в массивах (по умолчанию false)

