<?php

declare(strict_types=1);

namespace KinopoiskDev\Models;

use KinopoiskDev\Utils\DataManager;

/**
 * Класс для работы с коллекцией сетей/телеканалов
 *
 * Представляет коллекцию сетей и телеканалов, связанных с фильмом или сериалом.
 * Используется для группировки информации о производителях контента,
 * таких как Netflix, HBO, BBC и других телевизионных сетях и стриминговых платформах.
 * Содержит массив элементов NetworkItem с данными о каждой сети.
 *
 * @package KinopoiskDev\Models
 * @since   1.0.0
 * @author  Maxim Harder
 *
 * @version 1.0.0
 * @see     \KinopoiskDev\Models\NetworkItem Для отдельных элементов сети
 * @see     \KinopoiskDev\Models\Movie Для основной модели фильма
 */
readonly class Networks implements BaseModel {

	/**
	 * Конструктор для создания объекта коллекции сетей
	 *
	 * Создает новый экземпляр класса Networks с указанным массивом элементов сетей.
	 * Параметр является опциональным и может быть null при отсутствии данных
	 * о сетях и телеканалах для данного фильма или сериала.
	 *
	 * @see Networks::fromArray() Для создания объекта из массива данных API
	 * @see Networks::toArray() Для преобразования объекта в массив
	 * @see NetworkItem Для структуры отдельного элемента сети
	 *
	 * @param   NetworkItem[]|null  $items  Массив элементов сетей или null если данные отсутствуют
	 *
	 * @example
	 * ```php
	 * // Создание коллекции с несколькими сетями
	 * $networks = new Networks([
	 *     new NetworkItem('Netflix', new Logo('https://example.com/netflix.png')),
	 *     new NetworkItem('HBO', new Logo('https://example.com/hbo.png'))
	 * ]);
	 *
	 * // Создание пустой коллекции
	 * $emptyNetworks = new Networks();
	 * ```
	 */
	public function __construct(
		public ?array $items = NULL,
	) {}

	/**
	 * Создает объект Networks из массива данных API
	 *
	 * Статический фабричный метод для создания экземпляра класса Networks
	 * из массива данных, полученных от API Kinopoisk.dev. Безопасно обрабатывает
	 * отсутствующие значения и автоматически создает массив объектов NetworkItem
	 * из данных API. Используется для десериализации ответов API в объекты модели.
	 *
	 * @see    Networks::toArray() Для обратного преобразования в массив
	 * @see    NetworkItem::fromArray() Для создания отдельных элементов сети
	 *
	 * @param   array  $data  Массив данных о сетях от API, содержащий ключи:
	 *                        - items: array|null - массив данных об элементах сетей
	 *
	 * @return self Новый экземпляр класса Networks с данными из массива
	 *
	 * @throws \KinopoiskDev\Exceptions\KinopoiskDevException
	 * @example
	 *         ```php
	 *         // Создание из полных данных API
	 *         $apiData = [
	 *         'items' => [
	 *         ['name' => 'Netflix', 'logo' => ['url' => 'https://example.com/netflix.png']],
	 *         ['name' => 'HBO', 'logo' => ['url' => 'https://example.com/hbo.png']]
	 *         ]
	 *         ];
	 *         $networks = Networks::fromArray($apiData);
	 *
	 * // Создание из пустых данных
	 * $emptyNetworks = Networks::fromArray([]);
	 * ```
	 */
	public static function fromArray(array $data): static {
		return new self(
			items: DataManager::parseObjectArray($data, 'items', NetworkItem::class, NULL),
		);
	}

	/**
	 * Преобразует объект в массив данных
	 *
	 * Конвертирует текущий экземпляр класса Networks в массив,
	 * совместимый с форматом API Kinopoisk.dev. Автоматически преобразует
	 * все вложенные объекты NetworkItem в массивы. Используется для сериализации
	 * данных при отправке запросов к API или для экспорта в JSON формат.
	 *
	 * @see Networks::fromArray() Для создания объекта из массива
	 * @see NetworkItem::toArray() Для преобразования элементов сети в массивы
	 *
	 * @return array Массив с данными о сетях, содержащий ключи:
	 *               - items: array|null - массив данных об элементах сетей или null
	 *
	 * @example
	 * ```php
	 *
	 * $networks = new Networks([
	 *     new NetworkItem('Netflix', new Logo('https://example.com/netflix.png'))
	 * ]);
	 *
	 * $array = $networks->toArray();
	 * // ['items' => [['name' => 'Netflix', 'logo' => ['url' => 'https://example.com/netflix.png']]]]
	 * ```
	 */
	public function toArray(bool $includeNulls = true): array {
		return [
			'items' => DataManager::getObjectsArray($this->items),
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
