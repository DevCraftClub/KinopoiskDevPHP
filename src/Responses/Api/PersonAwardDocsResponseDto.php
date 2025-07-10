<?php

namespace KinopoiskDev\Responses\Api;

use KinopoiskDev\Models\PersonAward;
use KinopoiskDev\Responses\BaseDocsResponseDto;
use KinopoiskDev\Utils\DataManager;

/**
 * DTO для результатов поиска наград персон с пагинацией
 *
 * Класс предназначен для представления ответа API при поиске наград персон.
 * Наследуется от BaseDocsResponseDto и специализируется на работе с коллекцией
 * объектов PersonAward. Обеспечивает типизированный доступ к данным наград
 * с поддержкой пагинации результатов.
 *
 * @package   KinopoiskDev\Responses\Api
 * @since     1.0.0
 * @author    Maxim Harder
 * @version   1.0.0
 *
 * @see       \KinopoiskDev\Models\PersonAward
 * @see       \KinopoiskDev\Responses\BaseDocsResponseDto
 */
class PersonAwardDocsResponseDto extends BaseDocsResponseDto {

	/**
	 * Создает экземпляр DTO из массива данных API
	 *
	 * Фабричный метод для создания объекта PersonAwardDocsResponseDto из массива данных,
	 * полученных от API Kinopoisk.dev. Метод использует DataManager для безопасного
	 * преобразования каждого элемента массива docs в объект PersonAward и инициализирует
	 * все параметры пагинации значениями по умолчанию в случае их отсутствия.
	 *
	 * @see    \KinopoiskDev\Utils\DataManager::parseObjectArray() Используется для преобразования массива объектов
	 * @see    \KinopoiskDev\Models\PersonAward::fromArray() Метод создания объектов PersonAward из массива данных
	 *
	 * @param   array<string, mixed>  $data  Массив данных от API, содержащий ключи:
	 *                        - docs: array - массив данных наград персон для преобразования
	 *                        - total: int - общее количество наград в результате
	 *                        - limit: int - максимальное количество элементов на странице
	 *                        - page: int - номер текущей страницы (начиная с 1)
	 *                        - pages: int - общее количество страниц
	 *
	 * @return static Новый экземпляр PersonAwardDocsResponseDto с преобразованными данными
	 * @throws \KinopoiskDev\Exceptions\KinopoiskDevException При ошибках валидации класса PersonAward или отсутствии метода fromArray
	 */
	public static function fromArray(array $data): static {
		return new self(
			docs : DataManager::parseObjectArray($data, 'docs', PersonAward::class),
			total: $data['total'] ?? 0,
			limit: $data['limit'] ?? 10,
			page : $data['page'] ?? 1,
			pages: $data['pages'] ?? 0,
		);
	}

}
