<?php

declare(strict_types=1);

namespace KinopoiskDev\Models;

/**
 * Класс для представления информации о доступности просмотра
 *
 * Содержит информацию о платформах и сервисах, где доступен просмотр
 * фильма или сериала. Используется для отображения списка стриминговых
 * сервисов, онлайн-кинотеатров и других площадок для просмотра контента.
 *
 * @package KinopoiskDev\Models
 * @since   1.0.0
 * @author  Maxim Harder
 *
 * @version 1.0.0
 * @see     \KinopoiskDev\Models\WatchabilityItem Для отдельных элементов доступности
 * @see     \KinopoiskDev\Models\Movie Для использования в информации о фильмах
 */
readonly class Watchability implements BaseModel {

	/**
	 * Конструктор для создания объекта доступности просмотра
	 *
	 * Создает новый экземпляр класса Watchability с указанным массивом элементов.
	 * Параметр является опциональным и может быть пустым массивом при отсутствии
	 * информации о доступности просмотра для данного фильма или сериала.
	 *
	 * @see Watchability::fromArray() Для создания объекта из массива данных API
	 * @see Watchability::toArray() Для преобразования объекта в массив
	 * @see WatchabilityItem Для структуры отдельного элемента доступности
	 *
	 * @param   array  $items  Массив объектов WatchabilityItem с информацией о платформах
	 */
	public function __construct(
		public array $items = [],
	) {}

	/**
	 * Создает объект Watchability из массива данных API
	 *
	 * Фабричный метод для создания экземпляра класса Watchability из массива данных,
	 * полученных от API Kinopoisk.dev. Безопасно обрабатывает отсутствующие
	 * значения и преобразует вложенные массивы элементов в объекты WatchabilityItem.
	 *
	 * @see Watchability::toArray() Для обратного преобразования в массив
	 * @see WatchabilityItem::fromArray() Для создания отдельных элементов доступности
	 *
	 * @param   array  $data  Массив данных о доступности просмотра от API, содержащий ключи:
	 *                        - items: array|null - массив данных о платформах просмотра
	 *
	 * @return \KinopoiskDev\Models\Watchability Новый экземпляр класса Watchability с данными из массива
	 */
	public static function fromArray(array $data): static {
		$items = [];
		if (isset($data['items']) && is_array($data['items'])) {
			$items = array_map(fn($item) => WatchabilityItem::fromArray($item), $data['items']);
		}

		return new self(
			items: $items,
		);
	}

	/**
	 * Преобразует объект в массив данных
	 *
	 * Конвертирует текущий экземпляр класса Watchability в массив,
	 * совместимый с форматом API Kinopoisk.dev. Преобразует все вложенные
	 * объекты WatchabilityItem в массивы. Используется для сериализации данных
	 * при отправке запросов к API или для экспорта данных.
	 *
	 * @see Watchability::fromArray() Для создания объекта из массива
	 * @see WatchabilityItem::toArray() Для преобразования отдельных элементов в массивы
	 *
	 * @return array Массив с данными о доступности просмотра, содержащий ключи:
	 *               - items: array - массив данных о платформах просмотра
	 */
	public function toArray(bool $includeNulls = true): array {
		return [
			'items' => array_map(fn($item) => $item->toArray(), $this->items),
		];
	}
}
