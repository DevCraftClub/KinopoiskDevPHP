<?php

namespace KinopoiskDev\Utils;

use KinopoiskDev\Exceptions\KinopoiskDevException;

abstract class DataManager {

	/**
	 * Преобразует массив объектов в массив массивов
	 *
	 * Статический вспомогательный метод для преобразования коллекции объектов
	 * в массив массивов путем вызова метода toArray() для каждого объекта.
	 * Используется для сериализации связанных объектов (например, жанров, стран,
	 * персон) при преобразовании основного объекта в массив данных.
	 *
	 * @see      Movie::toArray() Основной метод преобразования объекта фильма в массив
	 * @see      Genre::toArray() Метод преобразования жанра в массив
	 * @see      Country::toArray() Метод преобразования страны в массив
	 * @see      Person::toArray() Метод преобразования персоны в массив
	 *
	 * @param   mixed  $objects   Коллекция объектов для преобразования или любое другое значение.
	 *                            Может быть массивом объектов с методом toArray(), null, false или пустым массивом.
	 *                            Если передан массив, каждый элемент должен содержать метод toArray()
	 *
	 * @return   array<mixed>  Массив массивов, полученный путем вызова toArray() для каждого объекта.
	 *                  Возвращает пустой массив, если входные данные являются falsy-значением
	 *                  (null, false, 0, пустой массив, пустая строка)
	 *
	 * @internal Внутренний метод, не является частью публичного API
	 *
	 * @example
	 * ```php
	 * // Преобразование массива объектов жанров
	 * $genres = [new Genre('драма'), new Genre('триллер')];
	 * $result = DataManager::getObjectsArray($genres);
	 * // Результат: [['name' => 'драма'], ['name' => 'триллер']]
	 *
	 * // Преобразование массива объектов стран
	 * $countries = [new Country('США'), new Country('Великобритания')];
	 * $result = DataManager::getObjectsArray($countries);
	 * // Результат: [['name' => 'США'], ['name' => 'Великобритания']]
	 *
	 * // Обработка пустого значения
	 * $result = DataManager::getObjectsArray(null);
	 * // Результат: []
	 *
	 * // Обработка false
	 * $result = DataManager::getObjectsArray(false);
	 * // Результат: []
	 * ```
	 */
	public static function getObjectsArray(mixed $objects): array {
		return $objects ? array_map(fn ($o) => $o->toArray(), $objects) : [];
	}

	/**
	 * Автоматически парсит объект из массива данных в зависимости от типа
	 *
	 * Универсальный метод для автоматической обработки объектов, который определяет,
	 * является ли значение по указанному ключу массивом объектов или одиночным объектом,
	 * и соответственно выбирает подходящий метод парсинга. Используется для автоматизации
	 * процесса обработки данных API, где одно и то же поле может содержать как массив
	 * объектов, так и одиночный объект.
	 *
	 * @see DataManager::parseObjectArray() Для обработки массива объектов
	 * @see DataManager::parseObjectData() Для обработки одиночного объекта
	 *
	 * @param   array<string, mixed>   $data     Массив данных, содержащий информацию для парсинга
	 * @param   string  $key      Ключ в массиве данных, по которому находится значение для парсинга
	 * @param   string  $cls      Полное имя класса для создания объектов (должен иметь метод fromArray)
	 * @param   mixed   $default  Значение по умолчанию, возвращаемое при отсутствии данных (по умолчанию null)
	 *
	 * @return  mixed             Массив объектов указанного класса, одиночный объект или значение по умолчанию
	 *
	 * @throws  \KinopoiskDev\Exceptions\KinopoiskDevException Если указанный класс не существует или не имеет метода fromArray
	 */
	public static function parseObjectAuto(array $data, string $key, string $cls, mixed $default = NULL): mixed {
		if (is_array($data[$key])) {
			return self::parseObjectArray($data, $key, $cls, $default);
		}

		return self::parseObjectData($data, $key, $cls, $default);
	}

	/**
	 * Разбирает данные объекта из массива API
	 *
	 * Статический метод для извлечения и преобразования данных объектов из массива API
	 * по указанному ключу. Если ключ существует в массиве, применяет функцию преобразования
	 * к каждому элементу: создает объект через fromArray() для массивов или возвращает
	 * элемент как есть для других типов данных. Используется для обработки коллекций
	 * связанных объектов (жанры, страны, персоны и т.д.) при десериализации данных API.
	 *
	 * Метод выполняет автоматическую проверку существования указанного класса и наличия
	 * в нем метода fromArray() перед началом обработки данных. Это обеспечивает безопасность
	 * и предотвращает ошибки выполнения при работе с некорректными классами.
	 *
	 * @see      DataManager::getObjectsArray() Для преобразования объектов в массивы
	 * @see      Genre::fromArray() Для создания объектов жанров
	 * @see      Country::fromArray() Для создания объектов стран
	 * @see      Person::fromArray() Для создания объектов персон
	 * @see      LinkedMovie::fromArray() Для создания объектов связанных фильмов
	 *
	 * @param   array<string, mixed>   $data     Массив данных API, содержащий различные поля фильма или других объектов
	 * @param   string  $key      Ключ для поиска в массиве данных (например, 'genres', 'countries', 'persons')
	 * @param   string  $cls      Имя класса для создания объектов (например, 'Genre', 'Country', 'Person').
	 *                            Класс должен содержать статический метод fromArray()
	 * @param   mixed   $default  Значение по умолчанию, возвращаемое при отсутствии ключа (по умолчанию пустой массив)
	 *
	 * @return  array<mixed>   Массив объектов указанного типа или значение по умолчанию, если ключ не найден.
	 *                  Каждый элемент массива либо преобразован в объект через fromArray(),
	 *                  либо возвращен в исходном виде для неассоциативных данных
	 *
	 * @throws  \KinopoiskDev\Exceptions\KinopoiskDevException Если указанный класс не существует
	 *                                                          или не содержит метод fromArray()
	 * @internal Внутренний метод для обработки данных API, не является частью публичного API
	 *
	 * @example
	 * ```php
	 * // Обработка массива жанров из API
	 * $apiData = ['genres' => [['name' => 'драма'], ['name' => 'триллер']]];
	 * $genres = DataManager::parseObjectArray($apiData, 'genres', 'Genre');
	 * // Результат: [Genre объект 'драма', Genre объект 'триллер']
	 *
	 * // Обработка отсутствующего ключа с кастомным значением по умолчанию
	 * $result = DataManager::parseObjectArray($apiData, 'missing_key', 'Country', []);
	 * // Результат: []
	 *
	 * // Обработка данных с null значением по умолчанию
	 * $result = DataManager::parseObjectArray($apiData, 'actors', 'Person', null);
	 * // Результат: null (если ключ 'actors' отсутствует)
	 *
	 * // Обработка смешанных данных (массивы и примитивы)
	 * $mixedData = ['items' => [['id' => 1, 'name' => 'test'], 'simple_string']];
	 * $result = DataManager::parseObjectArray($mixedData, 'items', 'SomeClass');
	 * // Результат: [SomeClass объект, 'simple_string']
	 * ```
	 */
	public static function parseObjectArray(array $data, string $key, string $cls, mixed $default = []): array {
		if (!class_exists($cls)) {
			throw new KinopoiskDevException("Класс {$cls} не существует");
		}

		if (!method_exists($cls, 'fromArray')) {
			throw new KinopoiskDevException("Класс {$cls} не имеет метода fromArray");
		}

		return isset($data[$key]) ? array_map(fn ($g) => is_array($g) ? $cls::fromArray($g) : $g, $data[$key]) : $default;
	}

	/**
	 * Парсит данные объекта из массива через фабричный метод
	 *
	 * Универсальный статический метод для создания объектов из массивов данных
	 * с использованием фабричного метода fromArray. Выполняет валидацию существования
	 * класса и требуемого метода, возвращая экземпляр объекта указанного класса
	 * или значение по умолчанию при отсутствии данных.
	 *
	 * @see     \KinopoiskDev\Models\Rating::fromArray()     Пример использования с моделью рейтинга
	 * @see     \KinopoiskDev\Models\Image::fromArray()      Пример использования с моделью изображения
	 * @see     \KinopoiskDev\Models\ExternalId::fromArray() Пример использования с внешними ID
	 *
	 * @param   array<string, mixed>   $data     Массив данных для парсинга, содержащий ключи объектов
	 * @param   string  $key      Ключ в массиве данных для извлечения значения
	 * @param   string  $cls      Полное имя класса для создания объекта (с пространством имен)
	 * @param   mixed   $default  Значение по умолчанию, возвращаемое при отсутствии данных
	 *
	 * @return  mixed  Экземпляр указанного класса, созданный через fromArray, или значение по умолчанию
	 *
	 * @throws  \KinopoiskDev\Exceptions\KinopoiskDevException  Если указанный класс не существует
	 * @throws  \KinopoiskDev\Exceptions\KinopoiskDevException  Если в классе отсутствует метод fromArray
	 *
	 * @example
	 * ```php
	 * // Создание объекта рейтинга из массива данных
	 * $rating = Helper::parseObjectData(
	 *     $apiData,
	 *     'rating',
	 *     Rating::class,
	 *     new Rating()
	 * );
	 *
	 * // Создание объекта изображения с null по умолчанию
	 * $poster = Helper::parseObjectData(
	 *     $movieData,
	 *     'poster',
	 *     ShortImage::class
	 * );
	 * ```
	 */
	public static function parseObjectData(array $data, string $key, string $cls, mixed $default = NULL): mixed {
		if (!class_exists($cls)) {
			throw new KinopoiskDevException("Класс {$cls} не существует");
		}

		if (!method_exists($cls, 'fromArray')) {
			throw new KinopoiskDevException("Класс {$cls} не имеет метода fromArray");
		}

		return isset($data[$key]) ? $cls::fromArray($data[$key]) : $default;
	}

	/**
	 * Разбирает значение enum из массива данных по указанному ключу
	 *
	 * Безопасно извлекает значение из массива данных и пытается преобразовать его
	 * в соответствующий enum с помощью метода tryFrom. Если ключ отсутствует в массиве
	 * или значение не может быть преобразовано в enum, возвращается значение по умолчанию.
	 * Выполняет проверку существования указанного класса enum перед попыткой преобразования.
	 *
	 * @see     \KinopoiskDev\Enums\MovieType Пример использования с enum типов фильмов
	 * @see     \KinopoiskDev\Enums\FilterField Пример использования с enum полей фильтрации
	 *
	 * @param   array<string, mixed>   $data       Массив данных, содержащий значения для разбора
	 * @param   string  $key        Ключ в массиве, значение которого необходимо получить
	 * @param   string  $enumClass  Полное имя класса enum для преобразования значения
	 * @param   mixed   $default    Значение по умолчанию, возвращаемое при отсутствии ключа или неудачном преобразовании
	 *
	 * @return  mixed   Экземпляр enum или значение по умолчанию при неудачном преобразовании
	 *
	 * @throws  \KinopoiskDev\Exceptions\KinopoiskDevException Если указанный класс enum не существует
	 *
	 * @example
	 * ```php
	 * // Разбор типа фильма из данных API
	 * $movieType = parseEnumValue(
	 *     data: ['type' => 'movie'],
	 *     key: 'type',
	 *     enumClass: MovieType::class
	 * );
	 *
	 * // Разбор с значением по умолчанию
	 * $status = parseEnumValue(
	 *     data: $apiData,
	 *     key: 'status',
	 *     enumClass: Status::class,
	 *     default: Status::UNKNOWN
	 * );
	 * ```
	 */
	public static function parseEnumValue(array $data, string $key, string $enumClass, mixed $default = NULL): mixed {
		if (!class_exists($enumClass)) {
			throw new KinopoiskDevException("Enum {$enumClass} не существует");
		}

		return isset($data[$key]) ? $enumClass::tryFrom($data[$key]) : $default;
	}

}
