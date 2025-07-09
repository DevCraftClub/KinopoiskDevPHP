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
 *
 * @package KinopoiskDev\Models
 * @since   1.0.0
 * @author  Maxim Harder
 * @version 1.0.0
 */
abstract readonly class AbstractBaseModel implements BaseModel {

	private static ?ValidationService $validator = null;

	/**
	 * {@inheritDoc}
	 */
	public function toArray(bool $includeNulls = true): array {
		$reflection = new ReflectionClass($this);
		$result = [];

		foreach ($reflection->getProperties() as $property) {
			$property->setAccessible(true);
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
			if (!$includeNulls && $value === null) {
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
	 * {@inheritDoc}
	 */
	public function validate(): bool {
		return $this->getValidator()->validate($this);
	}

	/**
	 * {@inheritDoc}
	 */
	public function toJson(int $flags = JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE): string {
		$data = $this->toArray(includeNulls: false);

		// Обрабатываем sensitive поля для JSON
		$data = $this->filterSensitiveForJson($data);

		return json_encode($data, $flags);
	}

	/**
	 * {@inheritDoc}
	 */
	public static function fromJson(string $json): static {
		try {
			$data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
			return static::fromArray($data);
		} catch (\JsonException $e) {
			throw new ValidationException(
				message: "Ошибка парсинга JSON: {$e->getMessage()}",
				code: $e->getCode(),
				previous: $e,
			);
		}
	}

	/**
	 * Создает копию объекта с измененными свойствами
	 *
	 * @param   array $changes Массив изменений ['property' => 'newValue']
	 *
	 * @return static Новый экземпляр с изменениями
	 * @throws ValidationException При ошибках валидации
	 */
	public function with(array $changes): static {
		$currentData = $this->toArray();
		$newData = array_merge($currentData, $changes);

		return static::fromArray($newData);
	}

	/**
	 * Сравнивает текущий объект с другим
	 *
	 * @param   BaseModel $other Объект для сравнения
	 *
	 * @return bool True если объекты равны
	 */
	public function equals(BaseModel $other): bool {
		if (!($other instanceof static)) {
			return false;
		}

		return $this->toArray() === $other->toArray();
	}

	/**
	 * Возвращает хэш объекта
	 *
	 * @return string Хэш объекта
	 */
	public function getHash(): string {
		return hash('sha256', $this->toJson());
	}

	/**
	 * Проверяет, является ли объект пустым
	 *
	 * @return bool True если все свойства null или пустые
	 */
	public function isEmpty(): bool {
		$data = $this->toArray(includeNulls: false);
		return empty($data);
	}

	/**
	 * Возвращает только заполненные свойства
	 *
	 * @return array Массив непустых свойств
	 */
	public function getFilledProperties(): array {
		return array_filter($this->toArray(includeNulls: false), function ($value) {
			return $value !== null && $value !== '' && $value !== [];
		});
	}

	/**
	 * Получает экземпляр валидатора
	 *
	 * @return ValidationService Экземпляр валидатора
	 */
	protected function getValidator(): ValidationService {
		return self::$validator ??= new ValidationService();
	}

	/**
	 * Определяет имя поля для API на основе атрибутов
	 *
	 * @param   ReflectionProperty $property Свойство
	 *
	 * @return string Имя поля для API
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
	 * @param   array $value        Массив для обработки
	 * @param   bool  $includeNulls Включать ли null значения
	 *
	 * @return array Обработанный массив
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
	 * Фильтрует конфиденциальные поля для JSON вывода
	 *
	 * @param   array $data Данные для фильтрации
	 *
	 * @return array Отфильтрованные данные
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
	 * Безопасно извлекает значение из массива данных
	 *
	 * @param   array  $data    Массив данных
	 * @param   string $key     Ключ
	 * @param   mixed  $default Значение по умолчанию
	 *
	 * @return mixed Значение или default
	 */
	protected static function getDataValue(array $data, string $key, mixed $default = null): mixed {
		return $data[$key] ?? $default;
	}

	/**
	 * Безопасно извлекает массив из данных
	 *
	 * @param   array  $data Массив данных
	 * @param   string $key  Ключ
	 *
	 * @return array Массив значений
	 */
	protected static function getArrayValue(array $data, string $key): array {
		$value = $data[$key] ?? [];
		return is_array($value) ? $value : [];
	}

	/**
	 * Безопасно извлекает строку из данных
	 *
	 * @param   array       $data Массив данных
	 * @param   string      $key  Ключ
	 * @param   string|null $default Значение по умолчанию
	 *
	 * @return string|null Строковое значение
	 */
	protected static function getStringValue(array $data, string $key, ?string $default = null): ?string {
		$value = $data[$key] ?? $default;
		return is_string($value) ? $value : $default;
	}

	/**
	 * Безопасно извлекает целое число из данных
	 *
	 * @param   array    $data Массив данных
	 * @param   string   $key  Ключ
	 * @param   int|null $default Значение по умолчанию
	 *
	 * @return int|null Целочисленное значение
	 */
	protected static function getIntValue(array $data, string $key, ?int $default = null): ?int {
		$value = $data[$key] ?? $default;
		return is_numeric($value) ? (int)$value : $default;
	}

	/**
	 * Безопасно извлекает логическое значение из данных
	 *
	 * @param   array     $data Массив данных
	 * @param   string    $key  Ключ
	 * @param   bool|null $default Значение по умолчанию
	 *
	 * @return bool|null Логическое значение
	 */
	protected static function getBoolValue(array $data, string $key, ?bool $default = null): ?bool {
		$value = $data[$key] ?? $default;
		return is_bool($value) ? $value : $default;
	}
}