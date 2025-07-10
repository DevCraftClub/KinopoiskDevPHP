<?php

declare(strict_types=1);

namespace KinopoiskDev\Models;

/**
 * Класс для представления кассовых сборов фильма по регионам
 *
 * Представляет информацию о кассовых сборах фильма в различных регионах мира,
 * включая мировые сборы, сборы в России и США. Каждый регион содержит
 * денежное значение с валютой, представленное объектом CurrencyValue.
 * Используется для хранения и обработки финансовой информации о фильмах
 * из API Kinopoisk.dev.
 *
 * @package KinopoiskDev\Models
 * @since   1.0.0
 * @author  Maxim Harder
 *
 * @version 1.0.0
 * @see     \KinopoiskDev\Models\Budget Для информации о бюджете фильма
 * @see     \KinopoiskDev\Models\CurrencyValue Для структуры денежных значений
 */
readonly class Fees implements BaseModel {

	/**
	 * Конструктор для создания объекта кассовых сборов
	 *
	 * Создает новый экземпляр класса Fees с информацией о кассовых сборах
	 * фильма по различным регионам. Все параметры являются опциональными
	 * и могут быть null при отсутствии данных о сборах в конкретном регионе.
	 *
	 * @see Fees::fromArray() Для создания объекта из массива данных API
	 * @see Fees::toArray() Для преобразования объекта в массив
	 *
	 * @param   CurrencyValue|null  $world   Мировые кассовые сборы фильма (null если не указаны)
	 * @param   CurrencyValue|null  $russia  Кассовые сборы фильма в России (null если не указаны)
	 * @param   CurrencyValue|null  $usa     Кассовые сборы фильма в США (null если не указаны)
	 */
	public function __construct(
		public ?CurrencyValue $world = NULL,
		public ?CurrencyValue $russia = NULL,
		public ?CurrencyValue $usa = NULL,
	) {}

	/**
	 * Создает объект Fees из массива данных API
	 *
	 * Фабричный метод для создания экземпляра класса Fees из массива
	 * данных о кассовых сборах, полученных от API Kinopoisk.dev.
	 * Безопасно обрабатывает отсутствующие значения, устанавливая их в null.
	 * Автоматически преобразует вложенные массивы в объекты CurrencyValue
	 * для каждого региона.
	 *
	 * @see Fees::toArray() Для обратного преобразования в массив
	 * @see CurrencyValue::fromArray() Для создания объектов денежных значений
	 *
	 * @param   array  $data  Массив данных о кассовых сборах от API, содержащий ключи:
	 *                        - world: array|null - данные о мировых сборах
	 *                        - russia: array|null - данные о сборах в России
	 *                        - usa: array|null - данные о сборах в США
	 *
	 * @return \KinopoiskDev\Models\Fees Новый экземпляр класса Fees с данными из массива
	 */
	public static function fromArray(array $data): static {
		return new self(
			world : isset($data['world']) ? CurrencyValue::fromArray($data['world']) : NULL,
			russia: isset($data['russia']) ? CurrencyValue::fromArray($data['russia']) : NULL,
			usa   : isset($data['usa']) ? CurrencyValue::fromArray($data['usa']) : NULL,
		);
	}

	/**
	 * Преобразует объект в массив данных
	 *
	 * Конвертирует текущий экземпляр класса Fees в массив,
	 * совместимый с форматом API Kinopoisk.dev. Используется для сериализации
	 * данных при отправке запросов к API или для экспорта данных в JSON.
	 * Автоматически преобразует объекты CurrencyValue в массивы для каждого региона.
	 *
	 * @see Fees::fromArray() Для создания объекта из массива
	 * @see CurrencyValue::toArray() Для преобразования денежных значений в массивы
	 *
	 * @return array Массив с данными о кассовых сборах, содержащий ключи:
	 *               - world: array|null - мировые сборы в формате массива
	 *               - russia: array|null - сборы в России в формате массива
	 *               - usa: array|null - сборы в США в формате массива
	 */
	public function toArray(bool $includeNulls = true): array {
		return [
			'world'  => $this->world?->toArray(),
			'russia' => $this->russia?->toArray(),
			'usa'    => $this->usa?->toArray(),
		];
	}

}