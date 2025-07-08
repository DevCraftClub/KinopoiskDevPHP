<?php

declare(strict_types=1);

namespace KinopoiskDev\Models;

use KinopoiskDev\Utils\DataManager;

/**
 * Класс для представления элемента сети/телеканала
 *
 * Представляет информацию об элементе сети или телеканала, включая название и логотип.
 * Используется для хранения данных о телевизионных сетях, стриминговых платформах
 * и других вещательных каналах, связанных с фильмами и сериалами.
 *
 * @package KinopoiskDev\Models
 * @since   1.0.0
 * @author  Maxim Harder
 *
 * @version 1.0.0
 * @see     \KinopoiskDev\Models\Logo Для работы с логотипами
 * @see     \KinopoiskDev\Models\Movie Для использования в фильмах
 */
readonly class NetworkItem implements BaseModel {

	/**
	 * Конструктор для создания объекта элемента сети
	 *
	 * Создает новый экземпляр класса NetworkItem с указанными параметрами
	 * названия и логотипа. Все параметры являются опциональными и могут быть
	 * null при отсутствии соответствующих данных.
	 *
	 * @see NetworkItem::fromArray() Для создания объекта из массива данных API
	 * @see NetworkItem::toArray() Для преобразования объекта в массив
	 *
	 * @param   string|null  $name  Название сети или телеканала (null если не указано)
	 * @param   Logo|null    $logo  Логотип сети или null если отсутствует
	 *
	 * @example
	 * ```php
	 * $network = new NetworkItem(
	 *     name: 'Netflix',
	 *     logo: new Logo('https://example.com/netflix-logo.png')
	 * );
	 * ```
	 */
	public function __construct(
		public ?string $name = NULL,
		public ?Logo   $logo = NULL,
	) {}

	/**
	 * Создает объект NetworkItem из массива данных API
	 *
	 * Фабричный метод для создания экземпляра класса NetworkItem из массива данных,
	 * полученных от API Kinopoisk.dev. Безопасно обрабатывает отсутствующие значения,
	 * устанавливая их в null. Автоматически создает вложенный объект Logo при наличии
	 * соответствующих данных через класс DataManager.
	 *
	 * @see NetworkItem::toArray() Для обратного преобразования в массив
	 * @see Logo::fromArray() Для создания объекта логотипа
	 * @see DataManager::parseObjectData() Для парсинга вложенных объектов
	 *
	 * @param   array  $data  Массив данных об элементе сети от API, содержащий ключи:
	 *                        - name: string|null - название сети или телеканала
	 *                        - logo: array|null - данные о логотипе с URL изображения
	 *
	 * @return self Новый экземпляр класса NetworkItem с данными из массива
	 *
	 * @throws \KinopoiskDev\Exceptions\KinopoiskDevException Если указанный класс Logo не существует
	 *                                                        или не имеет метода fromArray()
	 *
	 * @example
	 * ```php
	 * // Создание элемента сети с полными данными
	 * $networkData = [
	 *     'name' => 'Netflix',
	 *     'logo' => ['url' => 'https://example.com/netflix-logo.png']
	 * ];
	 * $network = NetworkItem::fromArray($networkData);
	 *
	 * // Создание элемента сети с частичными данными
	 * $partialData = ['name' => 'HBO'];
	 * $network = NetworkItem::fromArray($partialData); // logo будет null
	 *
	 * // Создание из пустого массива
	 * $emptyNetwork = NetworkItem::fromArray([]); // все поля будут null
	 * ```
	 */
	public static function fromArray(array $data): self {
		return new self(
			name: $data['name'] ?? NULL,
			logo: DataManager::parseObjectData($data, 'logo', Logo::class),
		);
	}

	/**
	 * Преобразует объект в массив данных
	 *
	 * Конвертирует текущий экземпляр класса NetworkItem в массив,
	 * совместимый с форматом API Kinopoisk.dev. Используется для сериализации
	 * данных при отправке запросов к API или для экспорта данных в JSON формат.
	 * Автоматически преобразует вложенный объект Logo в массив.
	 *
	 * @see NetworkItem::fromArray() Для создания объекта из массива
	 * @see Logo::toArray() Для преобразования логотипа в массив
	 *
	 * @return array Массив с данными об элементе сети, содержащий ключи:
	 *               - name: string|null - название сети
	 *               - logo: array|null - данные о логотипе в формате массива
	 *
	 * @example
	 * ```php
	 * $network = new NetworkItem('Netflix', new Logo('https://example.com/logo.png'));
	 * $array = $network->toArray();
	 * // ['name' => 'Netflix', 'logo' => ['url' => 'https://example.com/logo.png']]
	 * ```
	 */
	public function toArray(): array {
		return [
			'name' => $this->name,
			'logo' => $this->logo?->toArray(),
		];
	}

}