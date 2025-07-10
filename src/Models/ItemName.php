<?php

declare(strict_types=1);

namespace KinopoiskDev\Models;

/**
 * Класс для представления названия элемента
 *
 * Простая модель для хранения названий различных элементов системы
 * Kinopoisk.dev. Используется для представления наименований фильмов,
 * персон, жанров и других сущностей, когда требуется только строковое
 * значение названия без дополнительных атрибутов.
 *
 * @package KinopoiskDev\Models
 * @api     /v1/movie/possible-values-by-field
 * @since   1.0.0
 * @author  Maxim Harder
 *
 * @version 1.0.0
 * @see     \KinopoiskDev\Models\Name Для представления названий с языком и типом
 * @see     \KinopoiskDev\Models\Movie Для основной модели фильма
 * @link    https://kinopoiskdev.readme.io/reference/moviecontroller_getpossiblevaluesbyfieldname
 */
readonly class ItemName implements BaseModel {

	/**
	 * Конструктор для создания объекта названия элемента
	 *
	 * Создает новый экземпляр класса ItemName с указанным названием.
	 * Используется для инициализации простых строковых названий без
	 * дополнительных метаданных о языке или типе.
	 *
	 * @see ItemName::fromArray() Для создания объекта из массива данных API
	 * @see ItemName::toArray() Для преобразования объекта в массив
	 *
	 * @param   string  $name  Строковое представление названия элемента
	 */
	public function __construct(
		public string $name,
	) {}

	/**
	 * Создает объект ItemName из массива данных API
	 *
	 * Фабричный метод для создания экземпляра класса ItemName из массива
	 * данных, полученных от API Kinopoisk.dev. Извлекает значение названия
	 * из ключа 'name' входного массива и создает новый объект.
	 *
	 * @see ItemName::toArray() Для обратного преобразования в массив
	 *
	 * @param   array  $data  Массив данных от API, содержащий ключ:
	 *                        - name: string - название элемента
	 *
	 * @return \KinopoiskDev\Models\ItemName Новый экземпляр класса ItemName с данными из массива
	 */
	public static function fromArray(array $data): self {
		return new self(
			name: $data['name'],
		);
	}

	/**
	 * Преобразует объект в массив данных
	 *
	 * Конвертирует текущий экземпляр класса ItemName в массив,
	 * совместимый с форматом API Kinopoisk.dev. Используется для
	 * сериализации данных при отправке запросов к API или для
	 * экспорта данных в JSON формат.
	 *
	 * @see ItemName::fromArray() Для создания объекта из массива
	 *
	 * @return array Массив с данными о названии элемента, содержащий ключи:
	 *               - name: string - название элемента
	 */
	public function toArray(bool $includeNulls = true): array {
		return [
			'name' => $this->name,
		];
	}


	/**
	 * Валидирует данные модели
	 *
	 * @return bool True если данные валидны
	 * @throws \KinopoiskDev\Exceptions\ValidationException При ошибке валидации
	 */
	public function validate(): bool {
		return true; // Basic validation - override in specific models if needed
	}

	/**
	 * Возвращает JSON представление объекта
	 *
	 * @param int $flags Флаги для json_encode
	 * @return string JSON строка
	 * @throws \JsonException При ошибке сериализации
	 */
	public function toJson(int $flags = JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE): string {
		return json_encode($this->toArray(), $flags);
	}

	/**
	 * Создает объект из JSON строки
	 *
	 * @param string $json JSON строка
	 * @return static Экземпляр модели
	 * @throws \JsonException При ошибке парсинга
	 * @throws \KinopoiskDev\Exceptions\ValidationException При некорректных данных
	 */
	public static function fromJson(string $json): static {
		$data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
		$instance = static::fromArray($data);
		$instance->validate();
		return $instance;
	}


}
