<?php

namespace KinopoiskDev\Models;

/**
 * Модель фактов из фильма
 *
 * Представляет интересный факт о фильме, сериале или другом произведении.
 * Может содержать как обычную информацию, так и спойлеры, а также
 * имеет определенный тип (например, "блупер", "ошибка" и т.д.).
 *
 * @package KinopoiskDev\Models
 * @since   1.0.0
 * @author  Maxim Harder
 *
 * @version 1.0.0
 * @see     \KinopoiskDev\Models\Movie Основная модель фильма
 * @see     \KinopoiskDev\Models\SearchMovie Поисковая модель фильма
 */
 class FactInMovie implements BaseModel {

	/**
	 * Конструктор для создания объекта факта о фильме
	 *
	 * Создает новый экземпляр FactInMovie с указанным содержимым факта
	 * и дополнительными метаданными о типе и наличии спойлеров.
	 *
	 * @param   string       $value    Текст факта - основное содержимое информации о фильме
	 * @param   string|null  $type     Тип факта (например, "блупер", "ошибка", "интересный факт")
	 * @param   bool|null    $spoiler  Содержит ли факт спойлеры (true - да, false - нет, null - неизвестно)
	 *
	 * @example
	 * ```php
	 * $fact = new FactInMovie(
	 *     value: 'Во время съёмок актёр травмировал руку',
	 *     type: 'блупер',
	 *     spoiler: false
	 * );
	 * ```
	 */
	public function __construct(
		public string  $value,
		public ?string $type = NULL,
		public ?bool   $spoiler = NULL,
	) {}

	/**
	 * Создает объект факта о фильме из массива данных API
	 *
	 * Фабричный метод для создания экземпляра класса FactInMovie из массива данных,
	 * полученных от API Kinopoisk.dev. Метод безопасно обрабатывает отсутствующие
	 * значения полей type и spoiler, устанавливая их в null при отсутствии в исходных данных.
	 * Используется для десериализации данных фактов о фильмах, полученных от API.
	 *
	 * @see FactInMovie::toArray() Для обратного преобразования объекта в массив
	 * @see FactInMovie::__construct() Конструктор класса с описанием параметров
	 *
	 * @param   array  $data  Ассоциативный массив с данными факта от API, содержащий ключи:
	 *                        - value: string - обязательное поле с текстом факта
	 *                        - type: string|null - опциональный тип факта (по умолчанию null)
	 *                        - spoiler: bool|null - опциональный флаг спойлера (по умолчанию null)
	 *
	 * @return self Новый экземпляр FactInMovie с данными из массива
	 *
	 * @throws \TypeError Если поле 'value' отсутствует в массиве или имеет неправильный тип
	 *
	 * @example
	 * ```php
	 * $data = [
	 *     'value' => 'Актёр получил травму во время съёмок',
	 *     'type' => 'блупер',
	 *     'spoiler' => false
	 * ];
	 * $fact = FactInMovie::fromArray($data);
	 * ```
	 */
	public static function fromArray(array $data): self {
		return new self(
			value  : $data['value'],
			type   : $data['type'] ?? NULL,
			spoiler: $data['spoiler'] ?? NULL,
		);
	}

	/**
	 * Преобразует объект в массив данных
	 *
	 * Конвертирует текущий экземпляр класса FactInMovie в ассоциативный массив,
	 * содержащий все основные свойства объекта. Используется для сериализации
	 * данных при отправке запросов к API или для экспорта данных в JSON.
	 * Возвращает массив с тремя основными полями: значение факта, тип и статус спойлера.
	 *
	 * @see FactInMovie::fromArray() Для создания объекта из массива данных
	 * @see FactInMovie::__construct() Для инициализации объекта с данными
	 *
	 * @return array Ассоциативный массив с данными факта о фильме, содержащий ключи:
	 *               - value: string - текстовое содержимое факта
	 *               - type: string|null - тип факта (null если не определен)
	 *               - spoiler: bool|null - признак спойлера (null если не определен)
	 */
	public function toArray(bool $includeNulls = true): array {
		return [
			'value'   => $this->value,
			'type'    => $this->type,
			'spoiler' => $this->spoiler,
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
