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

	/**
	 * Валидирует API токен
	 *
	 * @param string|null $token API токен для валидации
	 * @return bool
	 * @throws ValidationException
	 */
	public function validateApiToken(?string $token): bool {
		if (empty($token)) {
			throw new ValidationException('API токен не может быть пустым');
		}

		// Проверка формата токена: XXXX-XXXX-XXXX-XXXX
		if (!preg_match('/^[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}$/', $token)) {
			throw new ValidationException('API токен должен быть в формате: XXXX-XXXX-XXXX-XXXX');
		}

		return true;
	}

	/**
	 * Валидирует HTTP метод
	 *
	 * @param string $method HTTP метод
	 * @return bool
	 * @throws ValidationException
	 */
	public function validateHttpMethod(string $method): bool {
		if (empty($method)) {
			throw new ValidationException('HTTP метод не может быть пустым');
		}

		$allowedMethods = ['GET', 'POST', 'PUT', 'DELETE', 'PATCH'];
		if (!in_array($method, $allowedMethods, true)) {
			throw new ValidationException("Неподдерживаемый HTTP метод: {$method}");
		}

		return true;
	}

	/**
	 * Валидирует endpoint
	 *
	 * @param string $endpoint Endpoint для валидации
	 * @return bool
	 * @throws ValidationException
	 */
	public function validateEndpoint(string $endpoint): bool {
		if (empty(trim($endpoint))) {
			throw new ValidationException("Некорректный endpoint: {$endpoint}");
		}

		// Проверка на двойные слеши, ведущие/замыкающие слеши
		if (strpos($endpoint, '//') !== false || 
			str_starts_with($endpoint, '/') || 
			str_ends_with($endpoint, '/')) {
			throw new ValidationException("Некорректный endpoint: {$endpoint}");
		}

		// Проверка на слишком много частей
		$parts = explode('/', $endpoint);
		if (count($parts) > 3) {
			throw new ValidationException("Некорректный endpoint: {$endpoint}");
		}

		return true;
	}

	/**
	 * Валидирует год
	 *
	 * @param int $year Год для валидации
	 * @return bool
	 * @throws ValidationException
	 */
	public function validateYear(int $year): bool {
		if ($year < 1888 || $year > 2030) {
			throw new ValidationException('Год должен быть в диапазоне от 1888 до 2030');
		}

		return true;
	}

	/**
	 * Валидирует рейтинг
	 *
	 * @param float $rating Рейтинг для валидации
	 * @return bool
	 * @throws ValidationException
	 */
	public function validateRating(float $rating): bool {
		if ($rating < 0.0 || $rating > 10.0) {
			throw new ValidationException('Рейтинг должен быть в диапазоне от 0.0 до 10.0');
		}

		return true;
	}

	/**
	 * Валидирует лимит
	 *
	 * @param int $limit Лимит для валидации
	 * @return bool
	 * @throws ValidationException
	 */
	public function validateLimit(int $limit): bool {
		if ($limit < 1 || $limit > 250) {
			throw new ValidationException('Лимит должен быть в диапазоне от 1 до 250');
		}

		return true;
	}

	/**
	 * Валидирует номер страницы
	 *
	 * @param int $page Номер страницы
	 * @return bool
	 * @throws ValidationException
	 */
	public function validatePage(int $page): bool {
		if ($page < 1) {
			throw new ValidationException('Номер страницы должен быть больше 0');
		}

		return true;
	}

	/**
	 * Валидирует ID фильма
	 *
	 * @param int $movieId ID фильма
	 * @return bool
	 * @throws ValidationException
	 */
	public function validateMovieId(int $movieId): bool {
		if ($movieId <= 0) {
			throw new ValidationException('ID фильма должен быть положительным числом');
		}

		return true;
	}

	/**
	 * Валидирует ID персоны
	 *
	 * @param int $personId ID персоны
	 * @return bool
	 * @throws ValidationException
	 */
	public function validatePersonId(int $personId): bool {
		if ($personId <= 0) {
			throw new ValidationException('ID персоны должен быть положительным числом');
		}

		return true;
	}

	/**
	 * Валидирует жанр
	 *
	 * @param string $genre Жанр для валидации
	 * @return bool
	 * @throws ValidationException
	 */
	public function validateGenre(string $genre): bool {
		$allowedGenres = [
			'драма', 'комедия', 'боевик', 'триллер', 'ужасы', 'фантастика',
			'приключения', 'мелодрама', 'детектив', 'криминал', 'вестерн',
			'военный', 'история', 'биография', 'спорт', 'мультфильм', 'семейный',
			'мюзикл', 'музыка', 'новости', 'ток-шоу', 'игра', 'реальное ТВ'
		];

		if (!in_array($genre, $allowedGenres, true)) {
			throw new ValidationException("Неподдерживаемый жанр: {$genre}");
		}

		return true;
	}

	/**
	 * Валидирует страну
	 *
	 * @param string $country Страна для валидации
	 * @return bool
	 * @throws ValidationException
	 */
	public function validateCountry(string $country): bool {
		$allowedCountries = [
			'Россия', 'США', 'Великобритания', 'Германия', 'Франция', 'Италия',
			'Испания', 'Канада', 'Австралия', 'Япония', 'Китай', 'Индия',
			'Бразилия', 'Мексика', 'Аргентина', 'Южная Корея', 'Швеция',
			'Норвегия', 'Дания', 'Финляндия', 'Нидерланды', 'Бельгия'
		];

		if (!in_array($country, $allowedCountries, true)) {
			throw new ValidationException("Неподдерживаемая страна: {$country}");
		}

		return true;
	}

	/**
	 * Валидирует профессию
	 *
	 * @param string $profession Профессия для валидации
	 * @return bool
	 * @throws ValidationException
	 */
	public function validateProfession(string $profession): bool {
		$allowedProfessions = [
			'актер', 'режиссер', 'продюсер', 'сценарист', 'оператор',
			'композитор', 'художник', 'монтажер', 'звукорежиссер'
		];

		if (!in_array($profession, $allowedProfessions, true)) {
			throw new ValidationException("Неподдерживаемая профессия: {$profession}");
		}

		return true;
	}

	/**
	 * Валидирует поисковый запрос
	 *
	 * @param string $query Поисковый запрос
	 * @return bool
	 * @throws ValidationException
	 */
	public function validateSearchQuery(string $query): bool {
		if (empty(trim($query))) {
			throw new ValidationException('Поисковый запрос не может быть пустым');
		}

		if (mb_strlen($query) < 2) {
			throw new ValidationException('Поисковый запрос должен содержать минимум 2 символа');
		}

		if (mb_strlen($query) > 100) {
			throw new ValidationException('Поисковый запрос не может превышать 100 символов');
		}

		return true;
	}

	/**
	 * Валидирует дату
	 *
	 * @param string $date Дата в формате Y-m-d
	 * @return bool
	 * @throws ValidationException
	 */
	public function validateDate(string $date): bool {
		if (empty($date)) {
			throw new ValidationException('Дата не может быть пустой');
		}

		$dateTime = \DateTime::createFromFormat('Y-m-d', $date);
		if (!$dateTime || $dateTime->format('Y-m-d') !== $date) {
			throw new ValidationException("Некорректный формат даты: {$date}");
		}

		return true;
	}

	/**
	 * Валидирует диапазон дат
	 *
	 * @param string $startDate Начальная дата
	 * @param string $endDate Конечная дата
	 * @return bool
	 * @throws ValidationException
	 */
	public function validateDateRange(string $startDate, string $endDate): bool {
		$this->validateDate($startDate);
		$this->validateDate($endDate);

		$start = new \DateTime($startDate);
		$end = new \DateTime($endDate);

		if ($start > $end) {
			throw new ValidationException('Начальная дата не может быть позже конечной');
		}

		return true;
	}

	/**
	 * Валидирует непустой массив
	 *
	 * @param array $array Массив для валидации
	 * @return bool
	 * @throws ValidationException
	 */
	public function validateNotEmptyArray(array $array): bool {
		if (empty($array)) {
			throw new ValidationException('Массив не может быть пустым');
		}

		return true;
	}
}