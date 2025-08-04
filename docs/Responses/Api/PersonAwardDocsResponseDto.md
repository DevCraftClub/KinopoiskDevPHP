# PersonAwardDocsResponseDto

**Описание:** DTO для результатов поиска наград персон с пагинацией
Класс предназначен для представления ответа API при поиске наград персон.
Наследуется от BaseDocsResponseDto и специализируется на работе с коллекцией
объектов PersonAward. Обеспечивает типизированный доступ к данным наград
с поддержкой пагинации результатов.

**С версии:** 1.0.0

**Версия:** 1.0.0

**См. также:**

* `\KinopoiskDev\Models\PersonAward`: 
* `\KinopoiskDev\Responses\BaseDocsResponseDto`: 

## `fromArray()`

**Описание:** Создает экземпляр DTO из массива данных API
Фабричный метод для создания объекта PersonAwardDocsResponseDto из массива данных,
полученных от API Kinopoisk.dev. Метод использует DataManager для безопасного
преобразования каждого элемента массива docs в объект PersonAward и инициализирует
все параметры пагинации значениями по умолчанию в случае их отсутствия.
- docs: array - массив данных наград персон для преобразования
- total: int - общее количество наград в результате
- limit: int - максимальное количество элементов на странице
- page: int - номер текущей страницы (начиная с 1)
- pages: int - общее количество страниц

**Возвращает:** `static` Новый экземпляр PersonAwardDocsResponseDto с преобразованными данными

**Исключения:**

* `\KinopoiskDev\Exceptions\KinopoiskDevException`: При ошибках валидации класса PersonAward или отсутствии метода fromArray

**См. также:**

* `\KinopoiskDev\Utils\DataManager::parseObjectArray`: () Используется для преобразования массива объектов
* `\KinopoiskDev\Models\PersonAward::fromArray`: () Метод создания объектов PersonAward из массива данных

