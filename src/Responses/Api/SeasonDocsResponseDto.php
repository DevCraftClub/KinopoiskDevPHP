<?php

namespace KinopoiskDev\Responses\Api;

use KinopoiskDev\Models\Season;
use KinopoiskDev\Responses\BaseDocsResponseDto;
use KinopoiskDev\Utils\DataManager;

/**
 * DTO ответа для результатов поиска сезонов с пагинацией
 *
 * Класс представляет типизированный ответ API при поиске сезонов сериалов.
 * Наследуется от BaseDocsResponseDto и специализируется на работе с коллекцией
 * объектов Season. Обеспечивает безопасное преобразование данных API в типизированные
 * объекты PHP с поддержкой пагинации результатов поиска.
 *
 * @package   KinopoiskDev\Responses\Api
 * @since     1.0.0
 * @author    Maxim Harder
 * @version   1.0.0
 *
 * @see       \KinopoiskDev\Models\Season Класс модели сезона для элементов массива docs
 * @see       \KinopoiskDev\Responses\BaseDocsResponseDto Базовый класс для ответов с пагинацией
 */
class SeasonDocsResponseDto extends BaseDocsResponseDto {

	/**
	 * Создает экземпляр DTO из массива данных API
	 *
	 * Фабричный метод для создания объекта SeasonDocsResponseDto из массива данных,
	 * полученных от API Kinopoisk.dev. Использует DataManager для безопасного
	 * преобразования каждого элемента массива docs в объект Season и устанавливает
	 * параметры пагинации с значениями по умолчанию при их отсутствии.
	 *
	 * @see    \KinopoiskDev\Utils\DataManager::parseObjectArray() Используется для преобразования массива объектов
	 * @see    \KinopoiskDev\Models\Season::fromArray() Метод создания объектов Season из массива данных
	 * @see    \KinopoiskDev\Responses\BaseResponseDto::fromArray() Родительский абстрактный метод
	 *
	 * @param   array<string, mixed>  $data  Ассоциативный массив данных от API, содержащий ключи:
	 *                        - docs: array - массив данных сезонов для преобразования в объекты Season
	 *                        - total: int - общее количество сезонов в результате поиска (по умолчанию 0)
	 *                        - limit: int - максимальное количество элементов на странице (по умолчанию 10)
	 *                        - page: int - номер текущей страницы, начиная с 1 (по умолчанию 1)
	 *                        - pages: int - общее количество страниц в результате (по умолчанию 0)
	 *
	 * @return static Новый экземпляр SeasonDocsResponseDto с преобразованными данными сезонов
	 * @throws \KinopoiskDev\Exceptions\KinopoiskDevException При ошибках валидации класса Season или отсутствии метода fromArray
	 */
	public static function fromArray(array $data): static {
		return new self(
			docs : DataManager::parseObjectArray($data, 'docs', Season::class),
			total: $data['total'] ?? 0,
			limit: $data['limit'] ?? 10,
			page : $data['page'] ?? 1,
			pages: $data['pages'] ?? 0,
		);
	}

}
