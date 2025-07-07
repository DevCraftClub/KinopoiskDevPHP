<?php

namespace KinopoiskDev\Responses;

use KinopoiskDev\Models\Person;
use KinopoiskDev\Utils\DataManager;

/**
 * DTO ответа для результатов поиска персон с пагинацией
 *
 * Класс предназначен для представления ответа API при поиске персон.
 * Наследуется от BaseDocsResponseDto и специализируется на работе с коллекцией
 * объектов Person. Обеспечивает типизированный доступ к данным персон
 * с поддержкой пагинации результатов.
 *
 * @package   KinopoiskDev\Responses
 * @since     1.0.0
 * @author    Maxim Harder
 * @version   1.0.0
 *
 * @see       \KinopoiskDev\Models\Person
 * @see       \KinopoiskDev\Responses\BaseDocsResponseDto
 */
class PersonDocsResponseDto extends BaseDocsResponseDto {

	/**
	 * Создает экземпляр DTO из массива данных API
	 *
	 * Фабричный метод для создания объекта PersonDocsResponseDto из массива данных,
	 * полученных от API Kinopoisk.dev. Метод использует DataManager для безопасного
	 * преобразования каждого элемента массива docs в объект Person и инициализирует
	 * все параметры пагинации значениями по умолчанию в случае их отсутствия.
	 *
	 * @since 1.0.0
	 * @see   \KinopoiskDev\Utils\DataManager::parseObjectArray() Используется для преобразования массива объектов
	 * @see   \KinopoiskDev\Models\Person::fromArray() Метод создания объектов Person из массива данных
	 *
	 * @param   array  $data  Массив данных от API, содержащий ключи:
	 *                        - docs: array - массив данных персон для преобразования
	 *                        - total: int - общее количество персон в результате
	 *                        - limit: int - максимальное количество элементов на странице
	 *                        - page: int - номер текущей страницы (начиная с 1)
	 *                        - pages: int - общее количество страниц
	 *
	 * @return static Новый экземпляр PersonDocsResponseDto с преобразованными данными
	 * @throws \KinopoiskDev\Exceptions\KinopoiskDevException При ошибках валидации класса Person или отсутствии метода fromArray
	 */
	public static function fromArray(array $data): static {
		return new static(
			docs : DataManager::parseObjectArray($data, 'docs', Person::class),
			total: $data['total'] ?? 0,
			limit: $data['limit'] ?? 10,
			page : $data['page'] ?? 1,
			pages: $data['pages'] ?? 0,
		);
	}

}
