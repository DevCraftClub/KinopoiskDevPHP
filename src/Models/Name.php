<?php

namespace KinopoiskDev\Models;

/**
 * Класс для представления названий фильмов
 *
 * Представляет информацию о названии фильма, включая само название, язык
 * и тип названия. Используется для хранения различных вариантов названий
 * фильмов в разных языках и форматах (официальное название, рабочее название,
 * альтернативное название и т.д.).
 *
 * @package KinopoiskDev\Models
 * @since   1.0.0
 * @author  Maxim Harder
 *
 * @version 1.0.0
 * @see     \KinopoiskDev\Models\Movie Для использования в информации о фильмах
 * @see     \KinopoiskDev\Models\LinkedMovie Для связанных фильмов с названиями
 */
readonly class Name implements BaseModel {

	/**
	 * Конструктор для создания объекта названия фильма
	 *
	 * Создает новый экземпляр класса Name с указанными параметрами названия,
	 * языка и типа. Параметры язык и тип являются опциональными и могут быть null
	 * при отсутствии соответствующих данных.
	 *
	 * @see Name::fromArray() Для создания объекта из массива данных API
	 * @see Name::toArray() Для преобразования объекта в массив
	 *
	 * @param   string       $name      Название фильма (основное значение)
	 * @param   string|null  $language  Язык названия в формате ISO 639-1 (например, "ru", "en") или null
	 * @param   string|null  $type      Тип названия (например, "официальное", "рабочее", "альтернативное") или null
	 */
	public function __construct(
		public string  $name,
		public ?string $language = NULL,
		public ?string $type = NULL,
	) {}

	/**
	 * Создает объект Name из массива данных API
	 *
	 * Статический фабричный метод для создания экземпляра класса Name из массива
	 * данных, полученных от API Kinopoisk.dev. Безопасно обрабатывает отсутствующие
	 * значения для опциональных параметров, устанавливая их в null. Используется
	 * для десериализации данных о названиях фильмов из ответов API.
	 *
	 * @see Name::toArray() Для обратного преобразования в массив
	 *
	 * @param   array  $data  Массив данных о названии от API, содержащий ключи:
	 *                        - name: string - само название фильма (обязательно)
	 *                        - language: string|null - язык названия (опционально)
	 *                        - type: string|null - тип названия (опционально)
	 *
	 * @return self Новый экземпляр класса Name с данными из массива
	 */
	public static function fromArray(array $data): self {
		return new self(
			name    : $data['name'],
			language: $data['language'] ?? NULL,
			type    : $data['type'] ?? NULL,
		);
	}

	/**
	 * Преобразует объект в массив данных
	 *
	 * Конвертирует текущий экземпляр класса Name в массив, совместимый с форматом
	 * API Kinopoisk.dev. Используется для сериализации данных при отправке
	 * запросов к API или для экспорта данных в JSON. Включает все свойства
	 * объекта, включая null-значения.
	 *
	 * @see Name::fromArray() Для создания объекта из массива
	 *
	 * @return array Массив с данными о названии, содержащий ключи:
	 *               - name: string - название фильма
	 *               - language: string|null - язык названия
	 *               - type: string|null - тип названия
	 */
	public function toArray(bool $includeNulls = true): array {
		return [
			'name'     => $this->name,
			'language' => $this->language,
			'type'     => $this->type,
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
