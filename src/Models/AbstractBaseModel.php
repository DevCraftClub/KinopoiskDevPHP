<?php

declare(strict_types=1);

namespace KinopoiskDev\Models;

use KinopoiskDev\Attributes\{ApiField, Sensitive};
use KinopoiskDev\Exceptions\ValidationException;
use KinopoiskDev\Services\ValidationService;
use ReflectionClass;
use ReflectionProperty;

/**
 * Абстрактный базовый класс для всех моделей
 *
 * Предоставляет общую функциональность для моделей данных:
 * валидацию, сериализацию, работу с атрибутами PHP 8.3.
 * Реализует интерфейс BaseModel и предоставляет готовые
 * методы для работы с данными API Kinopoisk.dev.
 *
 * Основные возможности:
 * - Автоматическая валидация на основе атрибутов Validation
 * - Сериализация/десериализация с поддержкой JSON
 * - Обработка конфиденциальных полей
 * - Безопасное извлечение данных из массивов
 * - Создание копий объектов с изменениями
 *
 * @package KinopoiskDev\Models
 * @since   1.0.0
 * @author  Maxim Harder
 * @version 1.0.0
 *
 * @see     \KinopoiskDev\Models\BaseModel Интерфейс базовой модели
 * @see     \KinopoiskDev\Attributes\Validation Атрибут валидации
 * @see     \KinopoiskDev\Attributes\Sensitive Атрибут конфиденциальных полей
 * @see     \KinopoiskDev\Services\ValidationService Сервис валидации
 *
 * @example
 * ```php
 * class Movie extends AbstractBaseModel {
 *     #[Validation(required: true, minLength: 1, maxLength: 255)]
 *     public string $title;
 *
 *     #[Validation(min: 1900, max: 2030)]
 *     public int $year;
 *
 *     #[Sensitive(hideInJson: true)]
 *     public string $apiKey;
 *
 *     public function __construct(string $title, int $year, string $apiKey) {
 *         $this->title = $title;
 *         $this->year = $year;
 *         $this->apiKey = $apiKey;
 *     }
 * }
 *
 * $movie = Movie::fromArray(['title' => 'The Matrix', 'year' => 1999, 'apiKey' => 'secret']);
 * $movie->validate();
 * $json = $movie->toJson(); // apiKey не включен в JSON
 * ```
 */
abstract class AbstractBaseModel implements BaseModel {

	/** @var ValidationService|null Статический экземпляр валидатора для переиспользования */
	private static ?ValidationService $validator = NULL;

	/**
	 * {@inheritDoc}
	 *
	 * Десериализует JSON строку в объект модели с обработкой ошибок
	 * парсинга и валидации данных.
	 *
	 * @param   string  $json  JSON строка с данными объекта
	 *
	 * @return static Экземпляр модели с заполненными данными
	 * @throws ValidationException При ошибках парсинга JSON или валидации данных
	 * @throws \JsonException При ошибках парсинга JSON
	 *
	 * @example
	 * ```php
	 * $json = '{"title":"The Matrix","year":1999}';
	 * $movie = Movie::fromJson($json);
	 * echo $movie->title; // The Matrix
	 * ```
	 */
	public static function fromJson(string $json): static {
		try {
			$data = json_decode($json, TRUE, 512, JSON_THROW_ON_ERROR);

			return static::fromArray($data);
		} catch (\JsonException $e) {
			throw new ValidationException(
				message : "Ошибка парсинга JSON: {$e->getMessage()}",
				code    : $e->getCode(),
				previous: $e,
			);
		} catch (\ReflectionException $e) {
			throw new ValidationException(
				"Ошибка инициализации класса: {$e->getMessage()}",
				code    : $e->getCode(),
				previous: $e,
			);
		}
	}

	/**
	 * {@inheritDoc}
	 *
	 * Создает экземпляр модели из массива данных с использованием рефлексии.
	 * Автоматически определяет параметры конструктора и передает соответствующие
	 * значения из массива данных. Поддерживает значения по умолчанию.
	 *
	 * @param   array<string, mixed>  $data  Ассоциативный массив с данными для создания объекта
	 *
	 * @return static Экземпляр модели с заполненными данными
	 * @throws \ReflectionException При ошибках рефлексии
	 * @throws \LogicException При попытке создания абстрактного класса
	 * @throws ValidationException При некорректных данных
	 *
	 * @example
	 * ```php
	 * $data = ['title' => 'The Matrix', 'year' => 1999];
	 * $movie = Movie::fromArray($data);
	 * // Создается объект Movie с title='The Matrix' и year=1999
	 * ```
	 */
	public static function fromArray(array $data): static {
		$class = static::class;

		$reflection = new ReflectionClass($class);
		if ($reflection->isAbstract() || $reflection->isInterface()) {
			throw new \LogicException("Не стоит напрямую запускать имплементацию или абстракцию: {$class}");
		}

		$constructor = $reflection->getConstructor();
		if (!$constructor) {
			return new $class();
		}

		$params = [];
		foreach ($constructor->getParameters() as $param) {
			$paramName = $param->getName();
			$params[]  = $data[$paramName] ?? ($param->isDefaultValueAvailable() ? $param->getDefaultValue() : NULL);
		}

		return $reflection->newInstanceArgs($params);
	}

	/**
	 * Безопасно извлекает значение из массива данных
	 *
	 * Универсальный метод для безопасного извлечения значений из массива
	 * с поддержкой значения по умолчанию при отсутствии ключа.
	 *
	 * @param   array<string, mixed>  $data     Ассоциативный массив данных
	 * @param   string                $key      Ключ для поиска в массиве
	 * @param   mixed                 $default  Значение по умолчанию при отсутствии ключа
	 *
	 * @return mixed Значение из массива или значение по умолчанию
	 *
	 * @example
	 * ```php
	 * $data = ['title' => 'The Matrix', 'year' => 1999];
	 * $title = self::getDataValue($data, 'title', 'Unknown'); // 'The Matrix'
	 * $rating = self::getDataValue($data, 'rating', 0);       // 0
	 * ```
	 */
	protected static function getDataValue(array $data, string $key, mixed $default = NULL): mixed {
		return $data[$key] ?? $default;
	}

	/**
	 * Безопасно извлекает массив из данных
	 *
	 * Извлекает значение из массива данных и гарантирует, что результат
	 * будет массивом. Возвращает пустой массив, если ключ отсутствует
	 * или значение не является массивом.
	 *
	 * @param   array<string, mixed>  $data  Ассоциативный массив данных
	 * @param   string                $key   Ключ для поиска в массиве
	 *
	 * @return array<mixed> Массив значений или пустой массив
	 *
	 * @example
	 * ```php
	 * $data = ['genres' => ['action', 'sci-fi'], 'actors' => null];
	 * $genres = self::getArrayValue($data, 'genres'); // ['action', 'sci-fi']
	 * $actors = self::getArrayValue($data, 'actors'); // []
	 * ```
	 */
	protected static function getArrayValue(array $data, string $key): array {
		$value = $data[$key] ?? [];

		return is_array($value) ? $value : [];
	}

	/**
	 * Безопасно извлекает строку из данных
	 *
	 * Извлекает строковое значение из массива данных с поддержкой
	 * значения по умолчанию. Возвращает null, если значение не является строкой.
	 *
	 * @param   array<string, mixed>  $data     Ассоциативный массив данных
	 * @param   string                $key      Ключ для поиска в массиве
	 * @param   string|null           $default  Значение по умолчанию при отсутствии ключа
	 *
	 * @return string|null Строковое значение или null
	 *
	 * @example
	 * ```php
	 * $data = ['title' => 'The Matrix', 'year' => 1999];
	 * $title = self::getStringValue($data, 'title');           // 'The Matrix'
	 * $description = self::getStringValue($data, 'description'); // null
	 * ```
	 */
	protected static function getStringValue(array $data, string $key, ?string $default = NULL): ?string {
		$value = $data[$key] ?? $default;

		return is_string($value) ? $value : $default;
	}

	/**
	 * Безопасно извлекает целое число из данных
	 *
	 * Извлекает целочисленное значение из массива данных с поддержкой
	 * значения по умолчанию. Преобразует числовые строки в целые числа.
	 *
	 * @param   array<string, mixed>  $data     Ассоциативный массив данных
	 * @param   string                $key      Ключ для поиска в массиве
	 * @param   int|null              $default  Значение по умолчанию при отсутствии ключа
	 *
	 * @return int|null Целочисленное значение или null
	 *
	 * @example
	 * ```php
	 * $data = ['year' => 1999, 'rating' => '8.7', 'votes' => null];
	 * $year = self::getIntValue($data, 'year');     // 1999
	 * $rating = self::getIntValue($data, 'rating'); // 8
	 * $votes = self::getIntValue($data, 'votes');   // null
	 * ```
	 */
	protected static function getIntValue(array $data, string $key, ?int $default = NULL): ?int {
		$value = $data[$key] ?? $default;

		return is_numeric($value) ? (int) $value : $default;
	}

	/**
	 * Безопасно извлекает логическое значение из данных
	 *
	 * Извлекает логическое значение из массива данных с поддержкой
	 * значения по умолчанию. Возвращает null, если значение не является boolean.
	 *
	 * @param   array<string, mixed>  $data     Ассоциативный массив данных
	 * @param   string                $key      Ключ для поиска в массиве
	 * @param   bool|null             $default  Значение по умолчанию при отсутствии ключа
	 *
	 * @return bool|null Логическое значение или null
	 *
	 * @example
	 * ```php
	 * $data = ['isActive' => true, 'isDeleted' => false, 'isPublished' => null];
	 * $isActive = self::getBoolValue($data, 'isActive');     // true
	 * $isDeleted = self::getBoolValue($data, 'isDeleted');   // false
	 * $isPublished = self::getBoolValue($data, 'isPublished'); // null
	 * ```
	 */
	protected static function getBoolValue(array $data, string $key, ?bool $default = NULL): ?bool {
		$value = $data[$key] ?? $default;

		return is_bool($value) ? $value : $default;
	}

	/**
	 * Создает копию объекта с измененными свойствами
	 *
	 * Иммутабельный метод для создания нового экземпляра объекта
	 * с измененными значениями свойств. Полезен для создания
	 * вариаций объекта без изменения оригинала.
	 *
	 * @param   array<string, mixed>  $changes  Массив изменений в формате ['property' => 'newValue']
	 *
	 * @return static Новый экземпляр с примененными изменениями
	 * @throws ValidationException При ошибках валидации нового объекта
	 *
	 * @example
	 * ```php
	 * $movie = new Movie('The Matrix', 1999);
	 *
	 * // Создаем копию с измененным годом
	 * $updatedMovie = $movie->with(['year' => 2000]);
	 * echo $updatedMovie->title; // The Matrix
	 * echo $updatedMovie->year;  // 2000
	 * echo $movie->year;         // 1999 (оригинал не изменился)
	 * ```
	 */
	public function with(array $changes): static {
		$currentData = $this->toArray();
		$newData     = array_merge($currentData, $changes);

		$instance = static::fromArray($newData);
		$instance->validate();

		return $instance;
	}

	/**
	 * {@inheritDoc}
	 *
	 * Преобразует объект в ассоциативный массив с поддержкой вложенных объектов,
	 * enum значений и конфиденциальных полей. Автоматически обрабатывает
	 * атрибуты ApiField для маппинга имен полей.
	 *
	 * @param   bool  $includeNulls  Включать ли null значения в результат (по умолчанию true)
	 *
	 * @return array<string, mixed> Ассоциативный массив с данными объекта
	 *
	 * @example
	 * ```php
	 * $movie = new Movie('The Matrix', 1999);
	 *
	 * // С null значениями
	 * $array = $movie->toArray(true);
	 * // Результат: ['title' => 'The Matrix', 'year' => 1999, 'rating' => null]
	 *
	 * // Без null значений
	 * $array = $movie->toArray(false);
	 * // Результат: ['title' => 'The Matrix', 'year' => 1999]
	 * ```
	 */
	public function toArray(bool $includeNulls = TRUE): array {
		$reflection = new ReflectionClass($this);
		$result     = [];

		foreach ($reflection->getProperties() as $property) {
			$property->setAccessible(TRUE);
			$value = $property->getValue($this);

			// Проверяем атрибут Sensitive
			$sensitiveAttributes = $property->getAttributes(Sensitive::class);
			if (!empty($sensitiveAttributes)) {
				$sensitive = $sensitiveAttributes[0]->newInstance();
				if ($sensitive->hideInArray) {
					continue;
				}
			}

			// Пропускаем null значения если не требуется их включать
			if (!$includeNulls && $value === NULL) {
				continue;
			}

			// Определяем имя поля для API
			$fieldName = $this->getApiFieldName($property);

			// Рекурсивно обрабатываем вложенные объекты
			if ($value instanceof BaseModel) {
				$result[$fieldName] = $value->toArray($includeNulls);
			} elseif (is_array($value)) {
				$result[$fieldName] = $this->processArrayValue($value, $includeNulls);
			} elseif ($value instanceof \BackedEnum) {
				$result[$fieldName] = $value->value;
			} else {
				$result[$fieldName] = $value;
			}
		}

		return $result;
	}

	/**
	 * Определяет имя поля для API на основе атрибутов
	 *
	 * Извлекает имя поля для API из атрибута ApiField или возвращает
	 * оригинальное имя свойства, если атрибут не задан.
	 *
	 * @param   ReflectionProperty  $property  Свойство для анализа
	 *
	 * @return string Имя поля для использования в API
	 *
	 * @internal Внутренний метод, используется только в toArray()
	 */
	private function getApiFieldName(ReflectionProperty $property): string {
		$apiFieldAttributes = $property->getAttributes(ApiField::class);

		if (!empty($apiFieldAttributes)) {
			$apiField = $apiFieldAttributes[0]->newInstance();

			return $apiField->name ?? $property->getName();
		}

		return $property->getName();
	}

	/**
	 * Обрабатывает значения массива для сериализации
	 *
	 * Рекурсивно обрабатывает элементы массива, преобразуя объекты BaseModel
	 * в массивы и enum значения в их скалярные представления.
	 *
	 * @param   array<mixed>  $value         Массив для обработки
	 * @param   bool          $includeNulls  Включать ли null значения в результат
	 *
	 * @return array<mixed> Обработанный массив
	 *
	 * @internal Внутренний метод, используется только в toArray()
	 */
	private function processArrayValue(array $value, bool $includeNulls): array {
		return array_map(function ($item) use ($includeNulls) {
			if ($item instanceof BaseModel) {
				return $item->toArray($includeNulls);
			} elseif ($item instanceof \BackedEnum) {
				return $item->value;
			}

			return $item;
		}, $value);
	}

	/**
	 * {@inheritDoc}
	 *
	 * Валидирует объект с использованием ValidationService и атрибутов Validation.
	 * Проверяет все свойства объекта на соответствие заданным правилам валидации.
	 *
	 * @return bool True если валидация прошла успешно
	 * @throws ValidationException При ошибках валидации с детальным описанием проблем
	 *
	 * @example
	 * ```php
	 * $movie = new Movie('', -1000); // Некорректные данные
	 *
	 * try {
	 *     $movie->validate();
	 *     echo "Модель валидна";
	 * } catch (ValidationException $e) {
	 *     foreach ($e->getErrors() as $field => $error) {
	 *         echo "{$field}: {$error}\n";
	 *     }
	 * }
	 * ```
	 */
	public function validate(): bool {
		return $this->getValidator()->validate($this);
	}

	/**
	 * Получает экземпляр валидатора
	 *
	 * Возвращает статический экземпляр ValidationService для переиспользования
	 * между объектами одного класса. Создает новый экземпляр при первом вызове.
	 *
	 * @return ValidationService Экземпляр сервиса валидации
	 *
	 * @internal Внутренний метод, используется только в validate()
	 */
	protected function getValidator(): ValidationService {
		return self::$validator ??= new ValidationService();
	}

	/**
	 * Сравнивает текущий объект с другим
	 *
	 * Выполняет глубокое сравнение объектов на основе их данных.
	 * Объекты считаются равными, если все их свойства имеют одинаковые значения.
	 *
	 * @param   BaseModel  $other  Объект для сравнения
	 *
	 * @return bool True если объекты равны, false в противном случае
	 *
	 * @example
	 * ```php
	 * $movie1 = new Movie('The Matrix', 1999);
	 * $movie2 = new Movie('The Matrix', 1999);
	 * $movie3 = new Movie('The Matrix', 2000);
	 *
	 * $movie1->equals($movie2); // true
	 * $movie1->equals($movie3); // false
	 * ```
	 */
	public function equals(BaseModel $other): bool {
		if (!($other instanceof static)) {
			return FALSE;
		}

		return $this->toArray() === $other->toArray();
	}

	/**
	 * Возвращает хэш объекта
	 *
	 * Генерирует SHA256 хэш на основе JSON представления объекта.
	 * Полезен для кэширования, сравнения версий объектов или
	 * создания уникальных идентификаторов.
	 *
	 * @return string SHA256 хэш объекта
	 *
	 * @example
	 * ```php
	 * $movie = new Movie('The Matrix', 1999);
	 * $hash = $movie->getHash();
	 * // Результат: строка из 64 символов (SHA256)
	 * ```
	 */
	public function getHash(): string {
		return hash('sha256', $this->toJson());
	}

	/**
	 * {@inheritDoc}
	 *
	 * Сериализует объект в JSON строку с автоматической фильтрацией
	 * конфиденциальных полей согласно атрибутам Sensitive.
	 *
	 * @param   int  $flags  Флаги для json_encode (по умолчанию JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE)
	 *
	 * @return string JSON строка с данными объекта
	 * @throws ValidationException При ошибке кодирования JSON
	 *
	 * @example
	 * ```php
	 * $movie = new Movie('The Matrix', 1999);
	 * $movie->apiKey = 'secret123';
	 *
	 * $json = $movie->toJson();
	 * // Результат: '{"title":"The Matrix","year":1999}' (apiKey исключен)
	 * ```
	 */
	public function toJson(int $flags = JSON_THROW_ON_ERROR|JSON_UNESCAPED_UNICODE): string {
		$data = $this->toArray(includeNulls: FALSE);

		// Обрабатываем sensitive поля для JSON
		$data = $this->filterSensitiveForJson($data);

		$json = json_encode($data, $flags);
		if ($json === FALSE) {
			throw new ValidationException('Ошибка кодирования JSON');
		}

		return $json;
	}

	/**
	 * Фильтрует конфиденциальные поля для JSON вывода
	 *
	 * Удаляет поля, помеченные атрибутом Sensitive с hideInJson=true,
	 * из массива данных перед JSON сериализацией.
	 *
	 * @param   array<string, mixed>  $data  Данные для фильтрации
	 *
	 * @return array<string, mixed> Отфильтрованные данные без конфиденциальных полей
	 *
	 * @internal Внутренний метод, используется только в toJson()
	 */
	private function filterSensitiveForJson(array $data): array {
		$reflection = new ReflectionClass($this);

		foreach ($reflection->getProperties() as $property) {
			$sensitiveAttributes = $property->getAttributes(Sensitive::class);
			if (!empty($sensitiveAttributes)) {
				$sensitive = $sensitiveAttributes[0]->newInstance();
				if ($sensitive->hideInJson) {
					$fieldName = $this->getApiFieldName($property);
					unset($data[$fieldName]);
				}
			}
		}

		return $data;
	}

	/**
	 * Проверяет, является ли объект пустым
	 *
	 * Определяет, содержит ли объект какие-либо непустые данные.
	 * Исключает null значения, пустые строки и пустые массивы.
	 *
	 * @return bool True если объект не содержит данных, false в противном случае
	 *
	 * @example
	 * ```php
	 * $movie1 = new Movie('', 0);
	 * $movie1->isEmpty(); // true
	 *
	 * $movie2 = new Movie('The Matrix', 1999);
	 * $movie2->isEmpty(); // false
	 * ```
	 */
	public function isEmpty(): bool {
		$data = $this->toArray(includeNulls: FALSE);

		return empty($data);
	}

	/**
	 * Возвращает только заполненные свойства
	 *
	 * Возвращает ассоциативный массив, содержащий только свойства
	 * с непустыми значениями (исключая null, пустые строки и массивы).
	 *
	 * @return array<string, mixed> Массив непустых свойств
	 *
	 * @example
	 * ```php
	 * $movie = new Movie('The Matrix', 1999);
	 * $movie->description = '';
	 * $movie->tags = [];
	 *
	 * $filled = $movie->getFilledProperties();
	 * // Результат: ['title' => 'The Matrix', 'year' => 1999]
	 * ```
	 */
	public function getFilledProperties(): array {
		return array_filter($this->toArray(includeNulls: FALSE), function ($value) {
			return $value !== NULL && $value !== '' && $value !== [];
		});
	}

}