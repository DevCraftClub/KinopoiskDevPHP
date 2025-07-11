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
class Audience extends AbstractBaseModel {

	/**
	 * Конструктор модели аудитории
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
	 *
	 * @see Audience::toArray() Для обратного преобразования в массив
	 *
	 * @param   array<string, mixed>  $data  Массив данных об аудитории от API, содержащий ключи:
	 *                        - count: int|null - количество зрителей
	 *                        - country: string|null - страна сбора данных
	 *
	 * @return static Новый экземпляр класса Audience
	 *
	 */
	public static function fromArray(array $data): static {
		return new self(
			count  : $data['count'] ?? null,
			country: $data['country'] ?? null,
		);
	}

	/**
	 * Преобразует объект в массив
	 *
	 * @param   bool $includeNulls Включать ли null значения
	 *
	 * @see Audience::fromArray() Для создания объекта из массива
	 * @return array<string, mixed> Массив с данными об аудитории, содержащий ключи:
	 *               - count: int|null - количество зрителей
	 *               - country: string|null - страна сбора данных
	 *
	 */
	public function toArray(bool $includeNulls = true): array {
		$result = [];

		if ($this->count !== null || $includeNulls) {
			$result['count'] = $this->count;
		}

		if ($this->country !== null || $includeNulls) {
			$result['country'] = $this->country;
		}

		return $result;
	}

}
