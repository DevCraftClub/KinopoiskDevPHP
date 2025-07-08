<?php

namespace KinopoiskDev\Responses\Api;

use KinopoiskDev\Models\MovieAward;
use KinopoiskDev\Responses\BaseDocsResponseDto;
use KinopoiskDev\Utils\DataManager;

/**
 * DTO для представления ответа API с наградами фильмов и информацией о пагинации
 *
 * Расширяет базовый класс BaseDocsResponseDto для специализированной работы с коллекциями
 * наград фильмов, полученных от API Kinopoisk.dev. Предоставляет стандартный интерфейс
 * для работы с постраничными результатами поиска наград фильмов, включая метаданные
 * о количестве результатов и навигации по страницам.
 *
 * @package   KinopoiskDev\Responses\Api
 * @since     1.0.0
 * @author    Maxim Harder
 * @version   1.0.0
 *
 * @see       \KinopoiskDev\Models\MovieAward Класс модели награды фильма
 * @see       \KinopoiskDev\Responses\BaseDocsResponseDto Базовый класс для ответов с документами
 * @see       \KinopoiskDev\Utils\DataManager Утилита для преобразования данных
 */
class MovieAwardDocsResponseDto extends BaseDocsResponseDto {

	/**
	 * Создает экземпляр DTO из массива данных API
	 *
	 * Фабричный метод для создания объекта MovieAwardDocsResponseDto из массива данных,
	 * полученных от API Kinopoisk.dev. Использует DataManager для безопасного преобразования
	 * элементов массива docs в объекты MovieAward и устанавливает значения по умолчанию
	 * для всех параметров пагинации в случае их отсутствия в исходных данных.
	 *
	 * @see    \KinopoiskDev\Utils\DataManager::parseObjectArray() Метод для преобразования массива в объекты
	 * @see    \KinopoiskDev\Models\MovieAward::fromArray() Фабричный метод создания объекта награды
	 *
	 * @param   array  $data  Ассоциативный массив с данными от API, содержащий ключи:
	 *                        - docs: array - массив данных наград фильмов для преобразования в объекты MovieAward
	 *                        - total: int - общее количество наград в результате поиска (по умолчанию 0)
	 *                        - limit: int - максимальное количество элементов на странице (по умолчанию 10)
	 *                        - page: int - номер текущей страницы, начиная с 1 (по умолчанию 1)
	 *                        - pages: int - общее количество доступных страниц (по умолчанию 0)
	 *
	 * @return static Новый экземпляр MovieAwardDocsResponseDto с преобразованными данными наград
	 *
	 * @throws \KinopoiskDev\Exceptions\KinopoiskDevException При ошибках валидации класса MovieAward,
	 *                                                        отсутствии метода fromArray в классе MovieAward,
	 *                                                        или некорректной структуре данных
	 *
	 */
	public static function fromArray(array $data): static {
		return new static(
			docs : DataManager::parseObjectArray($data, 'docs', MovieAward::class),
			total: $data['total'] ?? 0,
			limit: $data['limit'] ?? 10,
			page : $data['page'] ?? 1,
			pages: $data['pages'] ?? 0,
		);
	}

}