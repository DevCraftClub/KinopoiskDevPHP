<?php

namespace KinopoiskDev\Responses\Api;

use KinopoiskDev\Models\SearchMovie;
use KinopoiskDev\Responses\BaseDocsResponseDto;
use KinopoiskDev\Utils\DataManager;

/**
 * Объект-контейнер для ответа API с данными о фильмах и информацией о пагинации
 *
 * Представляет стандартный ответ API Kinopoisk.dev для запросов возвращающих
 * коллекцию фильмов с поддержкой пагинации. Содержит массив документов фильмов
 * и метаданные для постраничной навигации.
 *
 * @package   KinopoiskDev\Responses\Api
 * @author    Maxim Harder
 * @copyright MIT
 * @version   1.0.0
 * @see       \KinopoiskDev\Models\Movie Для структуры отдельного фильма
 * @see       \KinopoiskDev\Responses\ErrorResponseDto Для обработки ошибок API
 *
 */
class SearchPersonResponseDto extends BaseDocsResponseDto {

	/**
	 * Создает экземпляр DTO из массива данных API
	 *
	 * Фабричный метод для создания объекта DTO из ассоциативного массива,
	 * полученного от API Kinopoisk.dev. Метод использует DataManager для безопасного
	 * преобразования каждого элемента массива docs в объект SearchMovie и инициализирует
	 * все параметры пагинации значениями по умолчанию в случае их отсутствия.
	 *
	 * @see    \KinopoiskDev\Utils\DataManager::parseObjectArray() Используется для преобразования массива объектов
	 * @see    \KinopoiskDev\Models\SearchMovie::fromArray() Метод создания объектов SearchMovie из массива данных
	 *
	 * @param   array<string, mixed>  $data  Массив данных от API, содержащий ключи:
	 *                        - docs: array - массив данных поиска фильмов для преобразования
	 *                        - total: int - общее количество найденных фильмов в результате
	 *                        - limit: int - максимальное количество элементов на странице (по умолчанию 10)
	 *                        - page: int - номер текущей страницы (начиная с 1, по умолчанию 1)
	 *                        - pages: int - общее количество страниц (по умолчанию 0)
	 *
	 * @return static Новый экземпляр текущего класса DTO с преобразованными данными SearchMovie
	 * @throws \KinopoiskDev\Exceptions\KinopoiskDevException При ошибках валидации класса SearchMovie или отсутствии метода fromArray
	 */
	public static function fromArray(array $data): static {
		return new self(
			docs : DataManager::parseObjectArray($data, 'docs', SearchMovie::class),
			total: $data['total'] ?? 0,
			limit: $data['limit'] ?? 10,
			page : $data['page'] ?? 1,
			pages: $data['pages'] ?? 0,
		);
	}

}
