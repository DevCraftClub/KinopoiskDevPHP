<?php

declare(strict_types=1);

namespace KinopoiskDev\Models;

/**
 * Класс для представления информации о рецензиях на фильм
 *
 * Содержит статистические данные о рецензиях на фильм или сериал,
 * включая общее количество рецензий, количество положительных рецензий
 * и процентное соотношение положительных отзывов.
 *
 * @package KinopoiskDev\Models
 * @since   1.0.0
 * @author  Maxim Harder
 *
 * @version 1.0.0
 * @see     \KinopoiskDev\Models\Movie Для использования в информации о фильмах
 */
readonly class ReviewInfo implements BaseModel {

	/**
	 * Конструктор для создания объекта информации о рецензиях
	 *
	 * Создает новый экземпляр класса ReviewInfo с указанными параметрами.
	 * Все параметры являются опциональными и могут быть NULL при отсутствии
	 * соответствующей информации в источнике данных.
	 *
	 * @see ReviewInfo::fromArray() Для создания объекта из массива данных API
	 * @see ReviewInfo::toArray() Для преобразования объекта в массив
	 *
	 * @param   int|null     $count          Общее количество рецензий
	 * @param   int|null     $positiveCount  Количество положительных рецензий
	 * @param   string|null  $percentage     Процент положительных рецензий в виде строки
	 */
	public function __construct(
		public ?int    $count = NULL,
		public ?int    $positiveCount = NULL,
		public ?string $percentage = NULL,
	) {}

	/**
	 * Создает объект ReviewInfo из массива данных API
	 *
	 * Фабричный метод для создания экземпляра класса ReviewInfo из массива данных,
	 * полученных от API Kinopoisk.dev. Безопасно обрабатывает отсутствующие
	 * значения, устанавливая их в NULL.
	 *
	 * @see ReviewInfo::toArray() Для обратного преобразования в массив
	 *
	 * @param   array  $data  Массив данных о рецензиях от API, содержащий ключи:
	 *                        - count: int|null - общее количество рецензий
	 *                        - positiveCount: int|null - количество положительных рецензий
	 *                        - percentage: string|null - процент положительных рецензий
	 *
	 * @return \KinopoiskDev\Models\ReviewInfo Новый экземпляр класса ReviewInfo с данными из массива
	 */
	public static function fromArray(array $data): self {
		return new self(
			count        : $data['count'] ?? NULL,
			positiveCount: $data['positiveCount'] ?? NULL,
			percentage   : $data['percentage'] ?? NULL,
		);
	}

	/**
	 * Преобразует объект в массив данных
	 *
	 * Конвертирует текущий экземпляр класса ReviewInfo в массив,
	 * совместимый с форматом API Kinopoisk.dev. Используется для сериализации
	 * данных при отправке запросов к API или для экспорта данных.
	 *
	 * @see ReviewInfo::fromArray() Для создания объекта из массива
	 *
	 * @return array Массив с данными о рецензиях, содержащий ключи:
	 *               - count: int|null - общее количество рецензий
	 *               - positiveCount: int|null - количество положительных рецензий
	 *               - percentage: string|null - процент положительных рецензий
	 */
	public function toArray(): array {
		return [
			'count'         => $this->count,
			'positiveCount' => $this->positiveCount,
			'percentage'    => $this->percentage,
		];
	}

}
