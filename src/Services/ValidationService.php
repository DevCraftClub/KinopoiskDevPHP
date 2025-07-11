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
 *
 * @package KinopoiskDev\Services
 * @since   1.0.0
 * @author  Maxim Harder
 * @version 1.0.0
 */
final class ValidationService {

	/**
	 * Валидирует объект на основе атрибутов свойств
	 *
	 * @param   object $object Объект для валидации
	 *
	 * @return bool True если валидация прошла успешно
	 * @throws ValidationException При ошибках валидации
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
	 * @param   object             $object   Объект
	 * @param   ReflectionProperty $property Свойство для валидации
	 *
	 * @return array<string, string> Массив ошибок валидации
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
	 * @param   string     $value        Значение для валидации
	 * @param   Validation $validation   Правила валидации
	 * @param   string     $propertyName Название свойства
	 *
	 * @return array<string, string> Массив ошибок
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
	 * @param   float|int  $value        Значение для валидации
	 * @param   Validation $validation   Правила валидации
	 * @param   string     $propertyName Название свойства
	 *
	 * @return array<string, string> Массив ошибок
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
	 * @param   array<string, mixed> $data  Данные для валидации
	 * @param   array<string, mixed> $rules Правила валидации
	 *
	 * @return bool True если валидация прошла успешно
	 * @throws ValidationException При ошибках валидации
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
	 * @param   mixed  $value     Значение
	 * @param   array<string, mixed>  $rules     Правила
	 * @param   string $fieldName Название поля
	 *
	 * @return array<string, string> Массив ошибок
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
}