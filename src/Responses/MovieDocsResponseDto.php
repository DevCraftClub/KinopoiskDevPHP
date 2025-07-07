<?php

namespace KinopoiskDev\Responses;

use KinopoiskDev\Models\Movie;
use KinopoiskDev\Utils\DataManager;

/**
 * Объект-контейнер для ответа API с данными о фильмах и информацией о пагинации
 *
 * Представляет стандартный ответ API Kinopoisk.dev для запросов возвращающих
 * коллекцию фильмов с поддержкой пагинации. Содержит массив документов фильмов
 * и метаданные для постраничной навигации.
 *
 * @package   KinopoiskDev\Responses
 * @author    Maxim Harder
 * @copyright MIT
 * @version   1.0.0
 * @see       \KinopoiskDev\Models\Movie Для структуры отдельного фильма
 * @see       \KinopoiskDev\Responses\ErrorResponseDto Для обработки ошибок API
 *
 */
class MovieDocsResponseDto extends BaseDocsResponseDto {

	/**
	 * Создает экземпляр DTO из массива данных API
	 *
	 * Фабричный метод для создания объекта MovieDocsResponseDto из массива данных,
	 * полученных от API Kinopoisk.dev. Метод использует значения по умолчанию
	 * для всех параметров пагинации в случае их отсутствия в исходных данных.
	 * Массив docs остается без преобразования и передается как есть.
	 *
	 * @since  1.0.0
	 * @see    \KinopoiskDev\Models\Movie Класс модели фильма для элементов массива docs
	 * @see    \KinopoiskDev\Responses\BaseDocsResponseDto::__construct() Конструктор с параметрами
	 *
	 * @see    \KinopoiskDev\Responses\BaseResponseDto::fromArray() Родительский абстрактный метод
	 *
	 * @param   array  $data  Ассоциативный массив с данными от API, содержащий ключи:
	 *                        - docs: array - массив данных фильмов (остается без преобразования)
	 *                        - total: int - общее количество фильмов в результате (по умолчанию 0)
	 *                        - limit: int - максимальное количество элементов на странице (по умолчанию 10)
	 *                        - page: int - номер текущей страницы, начиная с 1 (по умолчанию 1)
	 *                        - pages: int - общее количество страниц (по умолчанию 0)
	 *
	 * @return static Новый экземпляр MovieDocsResponseDto с установленными данными пагинации
	 *
	 * @throws \KinopoiskDev\Exceptions\KinopoiskDevException
	 */
	public static function fromArray(array $data): static {
		return new static(
			docs : DataManager::parseObjectArray($data, 'docs', Movie::class),
			total: $data['total'] ?? 0,
			limit: $data['limit'] ?? 10,
			page : $data['page'] ?? 1,
			pages: $data['pages'] ?? 0,
		);
	}

}
