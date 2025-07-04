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
 * @version 1.0.0
 * @see     \KinopoiskDev\Models\Name Для представления названий с языком и типом
 * @see     \KinopoiskDev\Models\Movie Для основной модели фильма
 * @link    https://kinopoiskdev.readme.io/reference/moviecontroller_getpossiblevaluesbyfieldname
 */
class ItemName {

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
		public readonly string $name,
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
	public function toArray(): array {
		return [
			'name' => $this->name,
		];
	}

}