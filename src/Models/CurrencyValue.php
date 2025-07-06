<?php

declare(strict_types=1);

namespace KinopoiskDev\Models;

/**
 * Класс для представления денежных значений с валютой
 *
 * Представляет денежное значение с указанием валюты, используемое для
 * хранения информации о кассовых сборах фильмов, бюджете и других
 * финансовых данных в системе Kinopoisk.dev. Поддерживает различные
 * валюты и обрабатывает отсутствующие значения.
 *
 * @package KinopoiskDev\Models
 * @since   1.0.0
 * @author  Maxim Harder
 *
 * @version 1.0.0
 * @see     \KinopoiskDev\Models\Budget Для информации о бюджете фильма
 * @see     \KinopoiskDev\Models\Fees Для информации о кассовых сборах
 */
class CurrencyValue {

	/**
	 * Конструктор для создания объекта денежного значения
	 *
	 * Создает новый экземпляр класса CurrencyValue с указанными значением
	 * и валютой. Все параметры являются опциональными и могут быть null
	 * при отсутствии данных о денежном значении или валюте.
	 *
	 * @see CurrencyValue::fromArray() Для создания объекта из массива данных API
	 * @see CurrencyValue::toArray() Для преобразования объекта в массив
	 *
	 * @param   int|null     $value     Денежное значение в указанной валюте (null если не указано)
	 * @param   string|null  $currency  Код валюты (например, USD, RUB, EUR) или null если не указана
	 */
	public function __construct(
		public readonly ?int    $value = NULL,
		public readonly ?string $currency = NULL,
	) {}

	/**
	 * Создает объект CurrencyValue из массива данных API
	 *
	 * Фабричный метод для создания экземпляра класса CurrencyValue из массива
	 * данных, полученных от API Kinopoisk.dev. Безопасно обрабатывает отсутствующие
	 * значения, устанавливая их в null. Используется для десериализации данных
	 * о денежных значениях из ответов API.
	 *
	 * @see CurrencyValue::toArray() Для обратного преобразования в массив
	 *
	 * @param   array  $data  Массив данных о денежном значении от API, содержащий ключи:
	 *                        - value: int|null - денежное значение
	 *                        - currency: string|null - код валюты
	 *
	 * @return self Новый экземпляр класса CurrencyValue с данными из массива
	 */
	public static function fromArray(array $data): self {
		return new self(
			value   : $data['value'] ?? NULL,
			currency: $data['currency'] ?? NULL,
		);
	}

	/**
	 * Преобразует объект в массив данных
	 *
	 * Конвертирует текущий экземпляр класса CurrencyValue в массив,
	 * совместимый с форматом API Kinopoisk.dev. Используется для сериализации
	 * данных при отправке запросов к API или для экспорта данных в JSON.
	 *
	 * @see CurrencyValue::fromArray() Для создания объекта из массива
	 *
	 * @return array Массив с данными о денежном значении, содержащий ключи:
	 *               - value: int|null - денежное значение
	 *               - currency: string|null - код валюты
	 */
	public function toArray(): array {
		return [
			'value'    => $this->value,
			'currency' => $this->currency,
		];
	}

}