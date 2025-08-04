<?php

declare(strict_types=1);

namespace KinopoiskDev\Models;

/**
 * Класс для представления фильма из поисковых ключевых слов
 *
 * @package KinopoiskDev\Models
 * @since   1.0.0
 * @author  Maxim Harder
 */
class MovieFromKeyword extends AbstractBaseModel {

	/**
	 * Конструктор
	 *
	 * @param   int|null     $id               ID фильма
	 * @param   string|null  $name             Название фильма
	 * @param   string|null  $enName           Английское название
	 * @param   string|null  $alternativeName  Альтернативное название
	 * @param   string|null  $type             Тип (фильм, сериал и т.д.)
	 * @param   int|null     $year             Год выпуска
	 */
	public function __construct(
		public ?int    $id = NULL,
		public ?string $name = NULL,
		public ?string $enName = NULL,
		public ?string $alternativeName = NULL,
		public ?string $type = NULL,
		public ?int    $year = NULL,
	) {}

	/**
	 * Создает объект из массива данных API
	 *
	 * @param   array  $data  Данные от API
	 *
	 * @return static Новый экземпляр класса
	 */
	public static function fromArray(array $data): static {
		return new self(
			id             : isset($data['id']) ? (int) $data['id'] : NULL,
			name           : $data['name'] ?? NULL,
			enName         : $data['enName'] ?? NULL,
			alternativeName: $data['alternativeName'] ?? NULL,
			type           : $data['type'] ?? NULL,
			year           : isset($data['year']) ? (int) $data['year'] : NULL,
		);
	}

	/**
	 * Преобразует объект в массив
	 *
	 * @param   bool  $includeNulls  Включать ли null значения
	 *
	 * @return array Массив данных
	 */
	public function toArray(bool $includeNulls = TRUE): array {
		return [
			'id'              => $this->id,
			'name'            => $this->name,
			'enName'          => $this->enName,
			'alternativeName' => $this->alternativeName,
			'type'            => $this->type,
			'year'            => $this->year,
		];
	}

	/**
	 * Валидирует данные модели
	 *
	 * @return bool True если данные валидны
	 */
	public function validate(): bool {
		return TRUE; // Basic validation - override in specific models if needed
	}

}