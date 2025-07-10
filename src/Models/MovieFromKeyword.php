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
readonly class MovieFromKeyword implements BaseModel {

	/**
	 * Конструктор
	 *
	 * @param int|null    $id          ID фильма
	 * @param string|null $name        Название фильма
	 * @param string|null $enName      Английское название
	 * @param string|null $alternativeName Альтернативное название
	 * @param string|null $type        Тип (фильм, сериал и т.д.)
	 * @param int|null    $year        Год выпуска
	 */
	public function __construct(
		public readonly ?int $id = null,
		public readonly ?string $name = null,
		public readonly ?string $enName = null,
		public readonly ?string $alternativeName = null,
		public readonly ?string $type = null,
		public readonly ?int $year = null,
	) {}

	/**
	 * Создает объект из массива данных API
	 *
	 * @param array $data Данные от API
	 * @return static Новый экземпляр класса
	 */
	public static function fromArray(array $data): self {
		return new self(
			id: isset($data['id']) ? (int) $data['id'] : null,
			name: $data['name'] ?? null,
			enName: $data['enName'] ?? null,
			alternativeName: $data['alternativeName'] ?? null,
			type: $data['type'] ?? null,
			year: isset($data['year']) ? (int) $data['year'] : null,
		);
	}

	/**
	 * Преобразует объект в массив
	 *
	 * @param bool $includeNulls Включать ли null значения
	 * @return array Массив данных
	 */
	public function toArray(bool $includeNulls = true): array {
		return [
			'id' => $this->id,
			'name' => $this->name,
			'enName' => $this->enName,
			'alternativeName' => $this->alternativeName,
			'type' => $this->type,
			'year' => $this->year,
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