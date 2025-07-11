<?php

declare(strict_types=1);

namespace KinopoiskDev\Services;

use KinopoiskDev\Attributes\Validation;
use KinopoiskDev\Exceptions\ValidationException;
use ReflectionClass;
use ReflectionProperty;

/**
 * Сервис для валидации данных
 *
 * Выполняет валидацию объектов на основе атрибутов PHP 8.3.
 * Поддерживает различные типы валидации: обязательные поля,
 * ограничения длины, диапазоны значений, регулярные выражения.
 * Использует рефлексию для автоматического обнаружения правил валидации.
 *
 * @package KinopoiskDev\Services
 * @since   1.0.0
 * @author  Maxim Harder
 * @version 1.0.0
 * 
 * @see \KinopoiskDev\Attributes\Validation Атрибут валидации
 * @see \KinopoiskDev\Exceptions\ValidationException Исключения валидации
 * 
 * @example
 * ```php
 * class Movie {
 *     #[Validation(required: true, minLength: 1, maxLength: 255)]
 *     public string $title;
 *     
 *     #[Validation(min: 1900, max: 2030)]
 *     public int $year;
 * }
 * 
 * $validator = new ValidationService();
 * $movie = new Movie();
 * $movie->title = '';
 * $movie->year = 1800;
 * 
 * try {
 *     $validator->validate($movie);
 * } catch (ValidationException $e) {
 *     foreach ($e->getErrors() as $field => $error) {
 *         echo "{$field}: {$error}\n";
 *     }
 * }
 * ```
 */
final class ValidationService {

	/**
	 * Валидирует объект на основе атрибутов свойств
	 *
	 * Основной метод валидации, который анализирует все свойства объекта
	 * и проверяет их на соответствие правилам, заданным в атрибутах Validation.
	 * Выбрасывает ValidationException при обнаружении ошибок валидации.
	 *
	 * @param   object $object Объект для валидации (должен иметь свойства с атрибутами Validation)
	 *
	 * @return bool True если валидация прошла успешно
	 * @throws ValidationException При ошибках валидации с детальным описанием проблем
	 * 
	 * @example
	 * ```php
	 * $movie = new Movie();
	 * $movie->title = 'The Matrix';
	 * $movie->year = 1999;
	 * 
	 * try {
	 *     $validator->validate($movie);
	 *     echo "Объект валиден";
	 * } catch (ValidationException $e) {
	 *     echo "Ошибки валидации: " . $e->getMessage();
	 * }
	 * ```
	 */
	public function validate(object $object): bool {
		$reflection = new ReflectionClass($object);
		$errors = [];

		foreach ($reflection->getProperties() as $property) {
			$validationErrors = $this->validateProperty($object, $property);
			if (!empty($validationErrors)) {
				$errors = array_merge($errors, $validationErrors);
			}
		}

		if (!empty($errors)) {
			throw ValidationException::withErrors($errors);
		}

		return true;
	}

	/**
	 * Валидирует конкретное свойство объекта
	 *
	 * Проверяет одно свойство объекта на соответствие правилам валидации,
	 * заданным в атрибуте Validation. Поддерживает различные типы проверок
	 * в зависимости от типа значения свойства.
	 *
	 * @param   object             $object   Объект для валидации
	 * @param   ReflectionProperty $property Свойство для валидации
	 *
	 * @return array<string, string> Массив ошибок валидации в формате ['property' => 'error']
	 * 
	 * @internal Внутренний метод, используется только в validate()
	 */
	private function validateProperty(object $object, ReflectionProperty $property): array {
		$errors = [];
		$attributes = $property->getAttributes(Validation::class);

		if (empty($attributes)) {
			return $errors;
		}

		$validation = $attributes[0]->newInstance();
		$property->setAccessible(true);
		$value = $property->getValue($object);
		$propertyName = $property->getName();

		// Проверка обязательного поля
		if ($validation->required && (is_null($value) || $value === '')) {
			$errors[$propertyName] = $validation->customMessage 
				?? "Поле '{$propertyName}' является обязательным";
			return $errors;
		}

		// Если значение null и поле не обязательное, пропускаем остальные проверки
		if ($value === null) {
			return $errors;
		}

		// Валидация строк
		if (is_string($value)) {
			$stringErrors = $this->validateString($value, $validation, $propertyName);
			$errors = array_merge($errors, $stringErrors);
		}

		// Валидация чисел
		if (is_numeric($value) && !is_string($value)) {
			$numericErrors = $this->validateNumeric($value, $validation, $propertyName);
			$errors = array_merge($errors, $numericErrors);
		}

		// Валидация допустимых значений
		if (!empty($validation->allowedValues) && !in_array($value, $validation->allowedValues, true)) {
			$allowedValues = implode(', ', $validation->allowedValues);
			$errors[$propertyName] = $validation->customMessage 
				?? "Поле '{$propertyName}' должно содержать одно из значений: {$allowedValues}";
		}

		return $errors;
	}

	/**
	 * Валидирует строковое значение
	 *
	 * Выполняет валидацию строковых значений согласно правилам:
	 * минимальная/максимальная длина и соответствие регулярному выражению.
	 *
	 * @param   string     $value        Строковое значение для валидации
	 * @param   Validation $validation   Правила валидации из атрибута
	 * @param   string     $propertyName Название свойства для сообщений об ошибках
	 *
	 * @return array<string, string> Массив ошибок валидации
	 * 
	 * @internal Внутренний метод, используется только в validateProperty()
	 */
	private function validateString(string $value, Validation $validation, string $propertyName): array {
		$errors = [];
		$length = mb_strlen($value);

		// Проверка минимальной длины
		if ($validation->minLength !== null && $length < $validation->minLength) {
			$errors[$propertyName] = $validation->customMessage 
				?? "Поле '{$propertyName}' должно содержать не менее {$validation->minLength} символов";
		}

		// Проверка максимальной длины
		if ($validation->maxLength !== null && $length > $validation->maxLength) {
			$errors[$propertyName] = $validation->customMessage 
				?? "Поле '{$propertyName}' должно содержать не более {$validation->maxLength} символов";
		}

		// Проверка регулярного выражения
		if ($validation->pattern !== null && !preg_match($validation->pattern, $value)) {
			$errors[$propertyName] = $validation->customMessage 
				?? "Поле '{$propertyName}' не соответствует требуемому формату";
		}

		return $errors;
	}

	/**
	 * Валидирует числовое значение
	 *
	 * Выполняет валидацию числовых значений согласно правилам:
	 * минимальное/максимальное значение.
	 *
	 * @param   float|int  $value        Числовое значение для валидации
	 * @param   Validation $validation   Правила валидации из атрибута
	 * @param   string     $propertyName Название свойства для сообщений об ошибках
	 *
	 * @return array<string, string> Массив ошибок валидации
	 * 
	 * @internal Внутренний метод, используется только в validateProperty()
	 */
	private function validateNumeric(float|int $value, Validation $validation, string $propertyName): array {
		$errors = [];

		// Проверка минимального значения
		if ($validation->min !== null && $value < $validation->min) {
			$errors[$propertyName] = $validation->customMessage 
				?? "Поле '{$propertyName}' должно быть не менее {$validation->min}";
		}

		// Проверка максимального значения
		if ($validation->max !== null && $value > $validation->max) {
			$errors[$propertyName] = $validation->customMessage 
				?? "Поле '{$propertyName}' должно быть не более {$validation->max}";
		}

		return $errors;
	}

	/**
	 * Валидирует массив данных по правилам
	 *
	 * Альтернативный метод валидации для работы с массивами данных
	 * вместо объектов. Полезен для валидации входных данных API
	 * или данных форм.
	 *
	 * @param   array<string, mixed> $data  Ассоциативный массив данных для валидации
	 * @param   array<string, mixed> $rules Правила валидации в формате ['field' => ['rule' => 'value']]
	 *
	 * @return bool True если валидация прошла успешно
	 * @throws ValidationException При ошибках валидации
	 * 
	 * @example
	 * ```php
	 * $data = [
	 *     'title' => 'The Matrix',
	 *     'year' => 1999,
	 *     'rating' => 8.7
	 * ];
	 * 
	 * $rules = [
	 *     'title' => ['required' => true, 'min_length' => 1, 'max_length' => 255],
	 *     'year' => ['min' => 1900, 'max' => 2030],
	 *     'rating' => ['min' => 0, 'max' => 10]
	 * ];
	 * 
	 * try {
	 *     $validator->validateArray($data, $rules);
	 *     echo "Данные валидны";
	 * } catch (ValidationException $e) {
	 *     echo "Ошибки: " . $e->getMessage();
	 * }
	 * ```
	 */
	public function validateArray(array $data, array $rules): bool {
		$errors = [];

		foreach ($rules as $field => $fieldRules) {
			$value = $data[$field] ?? null;
			$fieldErrors = $this->validateFieldValue($value, $fieldRules, $field);
			if (!empty($fieldErrors)) {
				$errors = array_merge($errors, $fieldErrors);
			}
		}

		if (!empty($errors)) {
			throw ValidationException::withErrors($errors);
		}

		return true;
	}

	/**
	 * Валидирует значение поля по правилам
	 *
	 * Вспомогательный метод для валидации отдельного поля
	 * согласно переданным правилам. Поддерживает различные
	 * типы правил валидации.
	 *
	 * @param   mixed  $value     Значение поля для валидации
	 * @param   array<string, mixed>  $rules     Правила валидации для поля
	 * @param   string $fieldName Название поля для сообщений об ошибках
	 *
	 * @return array<string, string> Массив ошибок валидации
	 * 
	 * @internal Внутренний метод, используется только в validateArray()
	 */
	private function validateFieldValue(mixed $value, array $rules, string $fieldName): array {
		$errors = [];

		foreach ($rules as $rule => $parameter) {
			$error = match ($rule) {
				'required' => $parameter && ($value === null || $value === '') 
					? "Поле '{$fieldName}' обязательно для заполнения" 
					: null,
				'min_length' => is_string($value) && mb_strlen($value) < $parameter 
					? "Поле '{$fieldName}' должно содержать не менее {$parameter} символов" 
					: null,
				'max_length' => is_string($value) && mb_strlen($value) > $parameter 
					? "Поле '{$fieldName}' должно содержать не более {$parameter} символов" 
					: null,
				'min' => is_numeric($value) && $value < $parameter 
					? "Поле '{$fieldName}' должно быть не менее {$parameter}" 
					: null,
				'max' => is_numeric($value) && $value > $parameter 
					? "Поле '{$fieldName}' должно быть не более {$parameter}" 
					: null,
				'in' => !in_array($value, $parameter, true) 
					? "Поле '{$fieldName}' содержит недопустимое значение" 
					: null,
				default => null,
			};

			if ($error !== null) {
				$errors[$fieldName] = $error;
				break; // Останавливаемся на первой ошибке для поля
			}
		}

		return $errors;
	}

	/**
	 * Валидирует значение на основе правил валидации
	 *
	 * Универсальный метод для валидации любого значения
	 * согласно объекту Validation. Возвращает сообщение об ошибке
	 * или null при успешной валидации.
	 *
	 * @param   mixed      $value       Значение для валидации
	 * @param   Validation $validation  Правила валидации
	 *
	 * @return string|null Сообщение об ошибке или null если валидация прошла успешно
	 * 
	 * @internal Внутренний метод, используется для универсальной валидации
	 */
	private function validateValue(mixed $value, Validation $validation): ?string {
		// Проверка обязательности поля
		if ($validation->required && ($value === null || $value === '')) {
			return $validation->customMessage ?? 'Поле обязательно для заполнения';
		}

		// Если значение null и поле не обязательное, пропускаем остальные проверки
		if ($value === null) {
			return null;
		}

		// Проверка типа значения
		if (!is_string($value) && !is_numeric($value)) {
			return $validation->customMessage ?? 'Значение должно быть строкой или числом';
		}

		$value = (string)$value;
		$length = mb_strlen($value);

		// Проверка минимальной длины
		if ($validation->minLength !== null && $length < $validation->minLength) {
			return $validation->customMessage ?? "Минимальная длина должна быть {$validation->minLength} символов";
		}

		// Проверка максимальной длины
		if ($validation->maxLength !== null && $length > $validation->maxLength) {
			return $validation->customMessage ?? "Максимальная длина должна быть {$validation->maxLength} символов";
		}

		// Проверка регулярного выражения
		if ($validation->pattern !== null && !preg_match($validation->pattern, $value)) {
			return $validation->customMessage ?? 'Значение не соответствует требуемому формату';
		}

		// Проверка допустимых значений
		if (!empty($validation->allowedValues) && !in_array($value, $validation->allowedValues, true)) {
			$allowedValues = implode(', ', $validation->allowedValues);
			return $validation->customMessage ?? "Значение должно быть одним из: {$allowedValues}";
		}

		// Проверка числовых ограничений
		if (is_numeric($value)) {
			$numericValue = (float)$value;

			if ($validation->min !== null && $numericValue < $validation->min) {
				return $validation->customMessage ?? "Значение должно быть не меньше {$validation->min}";
			}

			if ($validation->max !== null && $numericValue > $validation->max) {
				return $validation->customMessage ?? "Значение должно быть не больше {$validation->max}";
			}
		}

		return null;
	}
}