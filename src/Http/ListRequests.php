<?php

namespace KinopoiskDev\Http;

use KinopoiskDev\Exceptions\KinopoiskDevException;
use KinopoiskDev\Exceptions\KinopoiskResponseException;
use KinopoiskDev\Kinopoisk;
use KinopoiskDev\Models\Movie;
use KinopoiskDev\Responses\Api\ListDocsResponseDto;
use KinopoiskDev\Filter\MovieSearchFilter;
use KinopoiskDev\Utils\DataManager;

/**
 * Класс для API-запросов, связанных с коллекциями и списками фильмов
 *
 * Этот класс расширяет базовый класс Kinopoisk и предоставляет специализированные
 * методы для работы с коллекциями фильмов (топ-250, жанровые подборки, тематические списки) из API Kinopoisk.dev.
 *
 * @package KinopoiskDev\Http
 * @since   1.0.0
 * @author  Maxim Harder
 * @version 1.0.0
 * @link    https://kinopoiskdev.readme.io/reference/
 */
class ListRequests extends Kinopoisk {

	/**
	 * Получает все доступные коллекции с возможностью фильтрации и пагинации
	 *
	 * Выполняет запрос к API Kinopoisk.dev для получения списка всех коллекций фильмов
	 * с поддержкой расширенной фильтрации и постраничной навигации.
	 * Можно фильтровать по категориям, названиям и другим параметрам коллекций.
	 *
	 * @api     /v1.4/list
	 * @since   1.0.0
	 * @version 1.0.0
	 *
	 * @see     \KinopoiskDev\Filter\MovieSearchFilter Класс для настройки фильтрации коллекций
	 * @see     \KinopoiskDev\Responses\Api\ListDocsResponseDto Структура ответа API
	 * @link    https://kinopoiskdev.readme.io/reference/listcontroller_findmanyv1_4
	 *
	 * @param   MovieSearchFilter|null  $filters  Объект фильтрации для поиска коллекций по различным критериям
	 *                                            (категория, название, количество фильмов).
	 *                                            При значении null создается новый экземпляр MovieSearchFilter без фильтров
	 * @param   int                     $page     Номер запрашиваемой страницы результатов, начиная с 1 (по умолчанию 1)
	 * @param   int                     $limit    Максимальное количество результатов на одной странице (по умолчанию 10, максимум ограничен API до 250)
	 *
	 * @return ListDocsResponseDto Объект ответа, содержащий массив коллекций и метаданные пагинации
	 *                             (общее количество, количество страниц, текущая страница)
	 *
	 * @throws KinopoiskDevException     При ошибках валидации данных, неправильных параметрах запроса или проблемах с инициализацией объектов
	 * @throws KinopoiskResponseException При ошибках HTTP-запроса к API (401, 403, 404)
	 * @throws \JsonException            При ошибках парсинга JSON-ответа от API, некорректном формате данных или повреждении ответа
	 */
	public function getAllLists(?MovieSearchFilter $filters = null, int $page = 1, int $limit = 10): ListDocsResponseDto {
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

		$response = $this->makeRequest('GET', 'list', $queryParams);
		$data     = $this->parseResponse($response);

		return new ListDocsResponseDto(
			docs : DataManager::parseObjectArray($data, 'docs', \KinopoiskDev\Models\Lists::class),
			total: $data['total'] ?? 0,
			limit: $data['limit'] ?? $limit,
			page : $data['page'] ?? $page,
			pages: $data['pages'] ?? 1,
		);
	}

	/**
	 * Получает конкретную коллекцию по её slug
	 *
	 * @api  /v1.4/list/{slug}
	 * @link https://kinopoiskdev.readme.io/reference/listcontroller_findonev1_4
	 *
	 * @param   string  $slug  Уникальный идентификатор коллекции (например: 'top250', 'popular-films')
	 *
	 * @return \KinopoiskDev\Models\Lists Коллекция фильмов со всеми доступными данными
	 * @throws KinopoiskDevException При ошибках API или проблемах с сетью
	 * @throws \JsonException При ошибках парсинга JSON
	 */
	public function getListBySlug(string $slug): \KinopoiskDev\Models\Lists {
		$response = $this->makeRequest('GET', "/list/{$slug}");
		$data     = $this->parseResponse($response);

		return \KinopoiskDev\Models\Lists::fromArray($data);
	}

	/**
	 * Получает популярные коллекции
	 *
	 * @param   int  $page   Номер страницы
	 * @param   int  $limit  Количество результатов на странице
	 *
	 * @return ListDocsResponseDto Популярные коллекции
	 * @throws KinopoiskDevException При ошибках API
	 * @throws \JsonException При ошибках парсинга JSON
	 * @throws \KinopoiskDev\Exceptions\KinopoiskResponseException
	 */
	public function getPopularLists(int $page = 1, int $limit = 10): ListDocsResponseDto {
		$filters = new MovieSearchFilter();
		// Можно добавить сортировку по популярности, если API это поддерживает
		
		return $this->getAllLists($filters, $page, $limit);
	}

	/**
	 * Получает коллекции по категории
	 *
	 * @param   string  $category  Категория коллекций
	 * @param   int     $page      Номер страницы
	 * @param   int     $limit     Количество результатов на странице
	 *
	 * @return ListDocsResponseDto Коллекции указанной категории
	 * @throws KinopoiskDevException При ошибках API
	 * @throws \JsonException При ошибках парсинга JSON
	 * @throws \KinopoiskDev\Exceptions\KinopoiskResponseException
	 */
	public function getListsByCategory(string $category, int $page = 1, int $limit = 10): ListDocsResponseDto {
		$filters = new MovieSearchFilter();
		$filters->addFilter('category', $category);
		
		return $this->getAllLists($filters, $page, $limit);
	}

}