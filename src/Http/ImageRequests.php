<?php

namespace KinopoiskDev\Http;

use KinopoiskDev\Exceptions\KinopoiskDevException;
use KinopoiskDev\Exceptions\KinopoiskResponseException;
use KinopoiskDev\Filter\MovieSearchFilter;
use KinopoiskDev\Kinopoisk;
use KinopoiskDev\Models\ApiImage;
use KinopoiskDev\Responses\Api\ImageDocsResponseDto;
use KinopoiskDev\Utils\DataManager;

/**
 * Класс для API-запросов, связанных с изображениями фильмов
 *
 * Этот класс расширяет базовый класс Kinopoisk и предоставляет специализированные
 * методы для работы с изображениями (постеры, кадры, задники) из API Kinopoisk.dev.
 *
 * @package KinopoiskDev\Http
 * @since   1.0.0
 * @author  Maxim Harder
 * @version 1.0.0
 * @link    https://kinopoiskdev.readme.io/reference/
 */
class ImageRequests extends Kinopoisk {

	/**
	 * Получает изображения для конкретного фильма
	 *
	 * @param   int     $movieId  ID фильма в Кинопоиске
	 * @param   string  $type     Тип изображения (например: 'poster', 'frame', 'backdrop')
	 * @param   int     $page     Номер страницы
	 * @param   int     $limit    Количество результатов на странице
	 *
	 * @return ImageDocsResponseDto Изображения указанного фильма
	 * @throws KinopoiskDevException При ошибках API
	 * @throws \JsonException При ошибках парсинга JSON
	 * @throws \KinopoiskDev\Exceptions\KinopoiskResponseException
	 */
	public function getImagesByMovieId(int $movieId, string $type = '', int $page = 1, int $limit = 10): ImageDocsResponseDto {
		$filters = new MovieSearchFilter();
		$filters->addFilter('movieId', $movieId);

		if (!empty($type)) {
			$filters->addFilter('type', $type);
		}

		return $this->getImages($filters, $page, $limit);
	}

	/**
	 * Получает изображения с возможностью фильтрации и пагинации
	 *
	 * Выполняет запрос к API Kinopoisk.dev для получения списка изображений фильмов
	 * с поддержкой расширенной фильтрации и постраничной навигации.
	 * Можно фильтровать по типу изображения, языку, размерам и ID фильма.
	 *
	 * @api     /v1.4/image
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @see     \KinopoiskDev\Filter\MovieSearchFilter Класс для настройки фильтрации изображений
	 * @see     \KinopoiskDev\Responses\Api\ImageDocsResponseDto Структура ответа API
	 * @see     \KinopoiskDev\Models\Image Модель отдельного изображения
	 * @link    https://kinopoiskdev.readme.io/reference/imagecontroller_findmanyv1_4
	 *
	 * @param   MovieSearchFilter|null  $filters  Объект фильтрации для поиска изображений по различным критериям
	 *                                            (тип изображения, ID фильма, язык, размеры).
	 *                                            При значении null создается новый экземпляр MovieSearchFilter без фильтров
	 * @param   int                     $page     Номер запрашиваемой страницы результатов, начиная с 1 (по умолчанию 1)
	 * @param   int                     $limit    Максимальное количество результатов на одной странице (по умолчанию 10, максимум ограничен API до
	 *                                            250)
	 *
	 * @return ImageDocsResponseDto Объект ответа, содержащий массив изображений и метаданные пагинации
	 *                              (общее количество, количество страниц, текущая страница)
	 *
	 * @throws KinopoiskDevException     При ошибках валидации данных, неправильных параметрах запроса или проблемах с инициализацией объектов
	 * @throws KinopoiskResponseException При ошибках HTTP-запроса к API (401, 403, 404)
	 * @throws \JsonException            При ошибках парсинга JSON-ответа от API, некорректном формате данных или повреждении ответа
	 */
	public function getImages(?MovieSearchFilter $filters = NULL, int $page = 1, int $limit = 10): ImageDocsResponseDto {
		if ($limit > 250) {
			throw new KinopoiskDevException('Лимит не должен превышать 250');
		}
		if ($page < 1) {
			throw new KinopoiskDevException('Номер страницы не должен быть меньше 1');
		}

		if (is_null($filters)) {
			$filters = new MovieSearchFilter();
		}

		$filters->addFilter('page', $page);
		$filters->addFilter('limit', $limit);
		$queryParams = $filters->getFilters();

		$response = $this->makeRequest('GET', 'image', $queryParams);
		$data     = $this->parseResponse($response);

		return new ImageDocsResponseDto(
			docs : DataManager::parseObjectArray($data, 'docs', ApiImage::class),
			total: $data['total'] ?? 0,
			limit: $data['limit'] ?? $limit,
			page : $data['page'] ?? $page,
			pages: $data['pages'] ?? 1,
		);
	}

	/**
	 * Получает постеры фильмов с высоким рейтингом
	 *
	 * @param   float  $minRating  Минимальный рейтинг КиноПоиска (по умолчанию 7.0)
	 * @param   int    $page       Номер страницы
	 * @param   int    $limit      Количество результатов на странице
	 *
	 * @return ImageDocsResponseDto Постеры высоко оцененных фильмов
	 * @throws KinopoiskDevException При ошибках API
	 * @throws \JsonException При ошибках парсинга JSON
	 * @throws \KinopoiskDev\Exceptions\KinopoiskResponseException
	 */
	public function getHighRatedPosters(float $minRating = 7.0, int $page = 1, int $limit = 10): ImageDocsResponseDto {
		$filters = new MovieSearchFilter();
		$filters->addFilter('type', 'poster');

		// Здесь можно было бы добавить фильтрацию по рейтингу, если API это поддерживает

		return $this->getImages($filters, $page, $limit);
	}

}