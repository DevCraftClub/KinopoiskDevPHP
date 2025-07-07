<?php

namespace KinopoiskDev\Responses\Api;

use KinopoiskDev\Models\Review;
use KinopoiskDev\Responses\BaseDocsResponseDto;
use KinopoiskDev\Utils\DataManager;

/**
 * DTO ответа для результатов поиска рецензий с пагинацией
 *
 * Класс предназначен для представления ответа API при поиске рецензий.
 * Наследуется от BaseDocsResponseDto и специализируется на работе с коллекцией
 * объектов Review. Обеспечивает типизированный доступ к данным рецензий
 * с поддержкой пагинации результатов.
 *
 * @package   KinopoiskDev\Responses\Api
 * @since     1.0.0
 * @author    Maxim Harder
 * @version   1.0.0
 * @see       \KinopoiskDev\Models\Review
 * @see       \KinopoiskDev\Responses\BaseDocsResponseDto
 */
class ReviewDocsResponseDto extends BaseDocsResponseDto {

	/**
	 * Создает экземпляр DTO из массива данных API
	 *
	 * Фабричный метод для создания объекта ReviewDocsResponseDto из массива данных,
	 * полученных от API Kinopoisk.dev. Метод использует DataManager для безопасного
	 * преобразования каждого элемента массива docs в объект Review и инициализирует
	 * все параметры пагинации значениями по умолчанию в случае их отсутствия.
	 *
	 * @since 1.0.0
	 * @see   \KinopoiskDev\Utils\DataManager::parseObjectArray() Используется для преобразования массива объектов
	 * @see   \KinopoiskDev\Models\Review::fromArray() Метод создания объектов Review из массива данных
	 *
	 * @param   array  $data  Массив данных от API, содержащий ключи:
	 *                        - docs: array - массив данных рецензий для преобразования
	 *                        - total: int - общее количество рецензий в результате (по умолчанию 0)
	 *                        - limit: int - максимальное количество элементов на странице (по умолчанию 10)
	 *                        - page: int - номер текущей страницы, начиная с 1 (по умолчанию 1)
	 *                        - pages: int - общее количество страниц (по умолчанию 0)
	 *
	 * @return static Новый экземпляр ReviewDocsResponseDto с преобразованными данными
	 * @throws \KinopoiskDev\Exceptions\KinopoiskDevException При ошибках валидации класса Review или отсутствии метода fromArray
	 */
	public static function fromArray(array $data): static {
		return new static(
			docs : DataManager::parseObjectArray($data, 'docs', Review::class),
			total: $data['total'] ?? 0,
			limit: $data['limit'] ?? 10,
			page : $data['page'] ?? 1,
			pages: $data['pages'] ?? 0,
		);
	}

}
