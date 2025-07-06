<?php

declare(strict_types=1);

namespace KinopoiskDev\Models;

/**
 * Класс для представления элемента доступности просмотра
 *
 * Представляет информацию об отдельной платформе или сервисе, где доступен
 * просмотр фильма или сериала. Содержит название сервиса, логотип и URL
 * для перехода на страницу просмотра. Используется в составе коллекции
 * Watchability для отображения всех доступных вариантов просмотра.
 *
 * @package KinopoiskDev\Models
 * @since   1.0.0
 * @author  Maxim Harder
 *
 * @version 1.0.0
 * @see     \KinopoiskDev\Models\Watchability Для коллекции элементов доступности
 * @see     \KinopoiskDev\Models\Logo Для работы с логотипами сервисов
 */
class WatchabilityItem {

	/**
	 * Конструктор для создания объекта элемента доступности просмотра
	 *
	 * Создает новый экземпляр класса WatchabilityItem с указанными параметрами.
	 * Содержит информацию о конкретном сервисе для просмотра фильма или сериала,
	 * включая название, логотип и URL для перехода.
	 *
	 * @see WatchabilityItem::fromArray() Для создания объекта из массива данных API
	 * @see WatchabilityItem::toArray() Для преобразования объекта в массив
	 * @see Logo Для структуры объекта логотипа
	 *
	 * @param   string|null  $name  Название сервиса или платформы (может быть null)
	 * @param   Logo         $logo  Логотип сервиса (обязательный параметр)
	 * @param   string       $url   URL для перехода на страницу просмотра (обязательный параметр)
	 */
	public function __construct(
		public readonly ?string $name = null,
		public readonly Logo $logo,
		public readonly string $url,
	) {}

	/**
	 * Создает объект WatchabilityItem из массива данных API
	 *
	 * Фабричный метод для создания экземпляра класса WatchabilityItem из массива данных,
	 * полученных от API Kinopoisk.dev. Безопасно обрабатывает отсутствующие
	 * значения и создает вложенный объект Logo из соответствующих данных.
	 *
	 * @see WatchabilityItem::toArray() Для обратного преобразования в массив
	 * @see Logo::fromArray() Для создания объекта логотипа
	 *
	 * @param   array  $data  Массив данных о сервисе просмотра от API, содержащий ключи:
	 *                        - name: string|null - название сервиса (опционально)
	 *                        - logo: array - данные о логотипе сервиса (обязательно)
	 *                        - url: string - URL для перехода на страницу просмотра (обязательно)
	 *
	 * @return \KinopoiskDev\Models\WatchabilityItem Новый экземпляр класса WatchabilityItem с данными из массива
	 */
	public static function fromArray(array $data): self {
		return new self(
			name: $data['name'] ?? null,
			logo: Logo::fromArray($data['logo']),
			url: $data['url'],
		);
	}

	/**
	 * Преобразует объект в массив данных
	 *
	 * Конвертирует текущий экземпляр класса WatchabilityItem в массив,
	 * совместимый с форматом API Kinopoisk.dev. Преобразует вложенный
	 * объект Logo в массив. Используется для сериализации данных
	 * при отправке запросов к API или для экспорта данных.
	 *
	 * @see WatchabilityItem::fromArray() Для создания объекта из массива
	 * @see Logo::toArray() Для преобразования логотипа в массив
	 *
	 * @return array Массив с данными о сервисе просмотра, содержащий ключи:
	 *               - name: string|null - название сервиса
	 *               - logo: array - данные о логотипе сервиса
	 *               - url: string - URL для перехода на страницу просмотра
	 */
	public function toArray(): array {
		return [
			'name' => $this->name,
			'logo' => $this->logo->toArray(),
			'url' => $this->url,
		];
	}
}
