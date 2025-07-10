<?php

namespace KinopoiskDev\Responses\Api;

use KinopoiskDev\Models\Image;
use KinopoiskDev\Responses\BaseDocsResponseDto;
use KinopoiskDev\Utils\DataManager;

/**
 * DTO ответа для результатов поиска изображений с пагинацией
 *
 * Класс предоставляет структурированный доступ к результатам поиска изображений
 * от API Kinopoisk.dev с поддержкой пагинации и специализированными методами
 * для фильтрации, сортировки и группировки изображений по различным критериям.
 *
 * @package   KinopoiskDev\Responses\Api
 * @since     1.0.0
 * @author    Maxim Harder
 *
 * @version   1.0.0
 * @see       \KinopoiskDev\Models\Image
 * @see       \KinopoiskDev\Responses\BaseDocsResponseDto
 */
class ImageDocsResponseDto extends BaseDocsResponseDto {

	/**
	 * Создает экземпляр DTO из массива данных API
	 *
	 * Фабричный метод для создания объекта ImageDocsResponseDto из массива данных,
	 * полученных от API Kinopoisk.dev. Метод использует DataManager для безопасного
	 * преобразования каждого элемента массива docs в объект Image и инициализирует
	 * все параметры пагинации значениями по умолчанию в случае их отсутствия.
	 *
	 * @see    \KinopoiskDev\Utils\DataManager::parseObjectArray() Используется для преобразования массива объектов
	 * @see    \KinopoiskDev\Models\Image::fromArray() Метод создания объектов Image из массива данных
	 *
	 * @param   array<string, mixed>  $data  Массив данных от API, содержащий ключи:
	 *                        - docs: array - массив данных изображений для преобразования
	 *                        - total: int - общее количество изображений в результате
	 *                        - limit: int - максимальное количество элементов на странице
	 *                        - page: int - номер текущей страницы (начиная с 1)
	 *                        - pages: int - общее количество страниц
	 *
	 * @return static Новый экземпляр ImageDocsResponseDto с преобразованными данными
	 * @throws \KinopoiskDev\Exceptions\KinopoiskDevException При ошибках валидации класса Image или отсутствии метода fromArray
	 */
	public static function fromArray(array $data): static {
		return new self(
			docs : DataManager::parseObjectArray($data, 'docs', Image::class),
			total: $data['total'] ?? 0,
			limit: $data['limit'] ?? 10,
			page : $data['page'] ?? 1,
			pages: $data['pages'] ?? 0,
		);
	}

	/**
	 * Преобразует объект в массив данных
	 *
	 * Конвертирует весь объект DTO в массив, включая преобразование
	 * всех объектов Image в массивы. Полезно для сериализации,
	 * кэширования или передачи данных в другие системы.
	 *
	 * @return array Массив данных, содержащий:
	 *               - docs: array - массив данных изображений
	 *               - total: int - общее количество изображений
	 *               - limit: int - лимит на страницу
	 *               - page: int - номер текущей страницы
	 *               - pages: int - общее количество страниц
	 */
	public function toArray(): array {
		return [
			'docs'  => DataManager::getObjectsArray($this->docs),
			'total' => $this->total,
			'limit' => $this->limit,
			'page'  => $this->page,
			'pages' => $this->pages,
		];
	}

}
