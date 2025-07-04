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
 * @author  Maxim Harder
 * @version 1.0.0
 *
 * @see     \KinopoiskDev\Models\Fees Для информации о кассовых сборах
 * @see     \KinopoiskDev\Models\Rating Для информации о рейтингах
 * @since   1.0.0
 */
class Audience {

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
		public readonly ?int    $count = NULL,
		public readonly ?string $country = NULL,
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
	public static function fromArray(array $data): self {
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
	public function toArray(): array {
		return [
			'count'   => $this->count,
			'country' => $this->country,
		];
	}

}