<?php

declare(strict_types=1);

namespace KinopoiskDev\Models;

/**
 * Класс для представления данных об аудитории фильма
 *
 * Представляет информацию о количестве зрителей и стране, где собирались
 * данные об аудитории фильма. Используется для хранения статистики
 * просмотров и географического распределения зрителей.
 *
 * @package KinopoiskDev\Models
 * @since   1.0.0
 * @author  Maxim Harder
 * @version 1.0.0
 *
 * @see     \KinopoiskDev\Models\Rating Для информации о рейтингах
 * @see     \KinopoiskDev\Models\Fees Для информации о кассовых сборах
 */
readonly class Audience implements BaseModel {

	/**
	 * Конструктор для создания объекта данных об аудитории
	 *
	 * Создает новый экземпляр класса Audience с указанными параметрами
	 * количества зрителей и страны. Все параметры являются опциональными
	 * и могут быть null при отсутствии данных.
	 *
	 * @see Audience::fromArray() Для создания объекта из массива данных API
	 *
	 * @param   string|null  $country  Страна сбора данных (null если не указана)
	 *
	 * @param   int|null     $count    Количество зрителей (null если данные отсутствуют)
	 */
	public function __construct(
		public ?int    $count = NULL,
		public ?string $country = NULL,
	) {}

	/**
	 * Создает объект Audience из массива данных API
	 *
	 * Фабричный метод для создания экземпляра класса Audience из массива данных,
	 * полученных от API Kinopoisk.dev. Безопасно обрабатывает отсутствующие
	 * значения, устанавливая их в null.
	 *
	 * @see Audience::toArray() Для обратного преобразования в массив
	 *
	 * @param   array  $data  Массив данных об аудитории от API, содержащий ключи:
	 *                        - count: int|null - количество зрителей
	 *                        - country: string|null - страна сбора данных
	 *
	 * @return self Новый экземпляр класса Audience
	 *
	 */
	public static function fromArray(array $data): static {
		return new self(
			count  : $data['count'] ?? NULL,
			country: $data['country'] ?? NULL,
		);
	}

	/**
	 * Преобразует объект в массив данных
	 *
	 * Конвертирует текущий экземпляр класса Audience в массив,
	 * совместимый с форматом API Kinopoisk.dev. Используется для
	 * сериализации данных при отправке запросов к API.
	 *
	 * @see Audience::fromArray() Для создания объекта из массива
	 * @return array Массив с данными об аудитории, содержащий ключи:
	 *               - count: int|null - количество зрителей
	 *               - country: string|null - страна сбора данных
	 *
	 */
	public function toArray(bool $includeNulls = true): array {
		return [
			'count'   => $this->count,
			'country' => $this->country,
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
