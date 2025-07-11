<?php

namespace KinopoiskDev\Http;

use KinopoiskDev\Exceptions\KinopoiskDevException;
use KinopoiskDev\Filter\PersonSearchFilter;
use KinopoiskDev\Kinopoisk;
use KinopoiskDev\Models\Person;
use KinopoiskDev\Models\PersonAward;
use KinopoiskDev\Responses\Api\PersonAwardDocsResponseDto;
use KinopoiskDev\Responses\Api\PersonDocsResponseDto;

/**
 * Класс для API-запросов, связанных с персонами
 *
 * Предоставляет полный набор методов для работы с персонами через API Kinopoisk.dev.
 * Включает поиск персон, получение детальной информации, наград, фильтрацию по
 * профессиям и другим критериям. Поддерживает расширенную фильтрацию,
 * пагинацию и обработку ошибок.
 *
 * Основные возможности:
 * - Поиск персон по различным критериям
 * - Получение детальной информации о персоне
 * - Работа с наградами персон
 * - Фильтрация по профессиям (актеры, режиссеры и т.д.)
 * - Поиск по имени с поддержкой регулярных выражений
 * - Специализированные методы для популярных запросов
 *
 * @package KinopoiskDev\Http
 * @since   1.0.0
 * @author  Maxim Harder
 * @version 1.0.0
 *
 * @see     \KinopoiskDev\Filter\PersonSearchFilter Для настройки фильтрации
 * @see     \KinopoiskDev\Models\Person Модель персоны
 * @see     \KinopoiskDev\Models\PersonAward Модель награды персоны
 * @see     \KinopoiskDev\Responses\Api\PersonDocsResponseDto Ответ с персонами
 * @see     \KinopoiskDev\Responses\Api\PersonAwardDocsResponseDto Ответ с наградами
 * @link    https://kinopoiskdev.readme.io/reference/
 *
 * @example
 * ```php
 * $personRequests = new PersonRequests('your-api-token');
 *
 * // Получение персоны по ID
 * $person = $personRequests->getPersonById(123);
 *
 * // Поиск персон
 * $filter = new PersonSearchFilter();
 * $filter->profession('актер')->age(30, 50);
 * $results = $personRequests->searchPersons($filter, 1, 20);
 *
 * // Поиск по имени
 * $actors = $personRequests->searchByName('Том Круз');
 *
 * // Получение актеров
 * $actors = $personRequests->getActors(1, 50);
 * ```
 */
class PersonRequests extends Kinopoisk {

	/**
	 * Получает персону по её уникальному идентификатору
	 *
	 * Выполняет запрос к API для получения полной информации о персоне,
	 * включая биографические данные, фильмографию, награды, места
	 * рождения и смерти, и другие доступные сведения.
	 *
	 * @api     /v1.4/person/{id}
	 * @since   1.0.0
	 *
	 * @link    https://kinopoiskdev.readme.io/reference/personcontroller_findonev1_4
	 *
	 * @param   int  $personId  Уникальный идентификатор персоны в системе Kinopoisk
	 *
	 * @return Person Объект персоны со всеми доступными данными
	 * @throws KinopoiskDevException При ошибках API или проблемах с сетью
	 * @throws KinopoiskResponseException При ошибках HTTP-запроса (401, 403, 404)
	 * @throws \JsonException При ошибках парсинга JSON-ответа
	 *
	 * @example
	 * ```php
	 * $person = $personRequests->getPersonById(123);
	 * echo $person->name; // Имя персоны
	 * echo $person->profession; // Профессия
	 * ```
	 */
	public function getPersonById(int $personId): Person {
		$response = $this->makeRequest('GET', "person/{$personId}");
		$data     = $this->parseResponse($response);

		return Person::fromArray($data);
	}

	/**
	 * Выполняет поиск персон по имени
	 *
	 * Удобный метод для поиска персон по имени с использованием регулярных выражений.
	 * Поддерживает поиск как по русским, так и по английским именам. Полезен для
	 * быстрого поиска персон без сложной фильтрации.
	 *
	 * @api     /v1.4/person/search
	 * @since   1.0.0
	 *
	 * @link    https://kinopoiskdev.readme.io/reference/personcontroller_searchpersonv1_4
	 *
	 * @param   string  $name   Имя персоны для поиска (может быть русским или английским)
	 * @param   int     $page   Номер страницы результатов (начиная с 1)
	 * @param   int     $limit  Количество результатов на странице (максимум 250)
	 *
	 * @return PersonDocsResponseDto Результаты поиска с информацией о пагинации
	 * @throws KinopoiskDevException При ошибках API или валидации
	 * @throws KinopoiskResponseException При ошибках HTTP-запроса
	 * @throws \JsonException При ошибках парсинга JSON-ответа
	 *
	 * @example
	 * ```php
	 * // Поиск по русскому имени
	 * $results = $personRequests->searchByName('Том Круз');
	 *
	 * // Поиск по английскому имени
	 * $results = $personRequests->searchByName('Tom Cruise', 1, 20);
	 * ```
	 */
	public function searchByName(string $name, int $page = 1, int $limit = 10): PersonDocsResponseDto {
		$filters = new PersonSearchFilter();
		$filters->addFilter('query', $name);
		$filters->addFilter('page', $page);
		$filters->addFilter('limit', $limit);

		$response = $this->makeRequest('GET', 'person/search', $filters->getFilters());
		$data     = $this->parseResponse($response);

		return $this->searchPersons($filters, $page, $limit);
	}

	/**
	 * Выполняет поиск персон по различным критериям
	 *
	 * Основной метод для поиска персон с поддержкой сложных фильтров.
	 * Позволяет искать по имени, профессии, возрасту, полу, месту рождения и другим параметрам.
	 *
	 * @api    /v1.4/person
	 * @link   https://kinopoiskdev.readme.io/reference/personcontroller_findmanyv1_4
	 *
	 * @param   PersonSearchFilter|null  $filters  Объект фильтра для поиска персон
	 * @param   int                      $page     Номер страницы результатов (по умолчанию: 1)
	 * @param   int                      $limit    Количество результатов на странице (по умолчанию: 10, максимум: 250)
	 *
	 * @return PersonDocsResponseDto Результаты поиска с пагинацией
	 * @throws KinopoiskDevException При ошибках API
	 */
	public function searchPersons(?PersonSearchFilter $filters = NULL, int $page = 1, int $limit = 10): PersonDocsResponseDto {
		if ($limit > 250) {
			throw new KinopoiskDevException('Limit не должен превышать 250');
		}
		if ($page < 1) {
			throw new KinopoiskDevException('Номер страницы не должен быть меньше 1');
		}

		if (is_null($filters)) {
			$filters = new PersonSearchFilter();
		}

		$filters->addFilter('page', $page);
		$filters->addFilter('limit', $limit);
		$queryParams = $filters->getFilters();

		$response = $this->makeRequest('GET', '/person', $queryParams);
		$data     = $this->parseResponse($response);

		return new PersonDocsResponseDto(
			docs : array_map(fn ($personData) => Person::fromArray($personData), $data['docs'] ?? []),
			total: $data['total'] ?? 0,
			limit: $data['limit'] ?? $limit,
			page : $data['page'] ?? $page,
			pages: $data['pages'] ?? 1,
		);
	}

	/**
	 * Получает список актёров
	 *
	 * Удобный метод для получения списка персон с профессией "актёр".
	 * Является обёрткой над методом getPersonsByProfession().
	 *
	 * @see    PersonRequests::getPersonsByProfession() Для получения персон других профессий
	 *
	 * @param   int  $limit  Количество результатов на странице (максимум 250)
	 *
	 * @param   int  $page   Номер страницы результатов (начиная с 1)
	 *
	 * @return PersonDocsResponseDto Список актёров с информацией о пагинации
	 * @throws \JsonException При ошибках парсинга JSON-ответа
	 * @throws KinopoiskDevException При ошибках API
	 */
	public function getActors(int $page = 1, int $limit = 10): PersonDocsResponseDto {
		return $this->getPersonsByProfession('актер', $page, $limit);
	}

	/**
	 * Получает персон по профессии
	 *
	 * Выполняет поиск персон, которые работают в указанной профессиональной области.
	 * Поддерживает русские названия профессий из справочника Kinopoisk.
	 *
	 * @see    PersonRequests::getActors() Для получения актёров
	 * @see    PersonRequests::getDirectors() Для получения режиссёров
	 *
	 * @param   int     $limit       Количество результатов на странице (максимум 250)
	 *
	 * @param   string  $profession  Профессия (актёр, режиссёр, сценарист, продюсер и т.д.)
	 * @param   int     $page        Номер страницы результатов (начиная с 1)
	 *
	 * @return PersonDocsResponseDto Персоны указанной профессии с информацией о пагинации
	 * @throws KinopoiskDevException При ошибках API
	 * @throws \JsonException При ошибках парсинга JSON-ответа
	 */
	public function getPersonsByProfession(string $profession, int $page = 1, int $limit = 10): PersonDocsResponseDto {
		$filters = new PersonSearchFilter();
		$filters->profession($profession);

		return $this->searchPersons($filters, $page, $limit);
	}

	/**
	 * Получает награды персон с возможностью фильтрации и пагинации
	 *
	 * @api     /v1.4/person/awards
	 * @see     \KinopoiskDev\Filter\PersonSearchFilter Для параметров фильтрации
	 * @see     \KinopoiskDev\Models\PersonAward Для структуры данных наград персон
	 * @see     \KinopoiskDev\Responses\PersonAwardDocsResponseDto Для структуры ответа
	 * @link    https://kinopoiskdev.readme.io/reference/personcontroller_findawardsv1_4
	 *
	 * @link    https://kinopoiskdev.readme.io/reference/personcontroller_findmanyawardsv1_4
	 *
	 * @param   PersonSearchFilter|null  $filters  Объект фильтрации для поиска наград
	 * @param   int                      $page     Номер страницы (по умолчанию: 1)
	 * @param   int                      $limit    Количество результатов на странице (по умолчанию: 10)
	 *
	 * @param   PersonSearchFilter|null  $filters  Фильтры для поиска наград персон.
	 *                                             Если null, создается пустой фильтр.
	 *                                             Поддерживает фильтрацию по возрасту, полу,
	 *                                             месту рождения, профессии и другим параметрам.
	 * @param   int                      $page     Номер страницы для пагинации (начиная с 1).
	 *                                             Значение должно быть положительным числом.
	 * @param   int                      $limit    Максимальное количество элементов на странице.
	 *                                             Значение не должно превышать 250.
	 *
	 * @return  PersonAwardDocsResponseDto Объект ответа, содержащий:
	 *                                    - docs: массив объектов PersonAward с данными о наградах
	 *                                    - total: общее количество наград в результате
	 *                                    - limit: примененное ограничение на количество элементов
	 *                                    - page: текущая страница
	 *                                    - pages: общее количество страниц
	 *
	 * @throws  \KinopoiskDev\Exceptions\KinopoiskDevException|\KinopoiskDev\Exceptions\KinopoiskResponseException|\JsonException
	 *          - Если параметр $limit превышает 250
	 *          - Если параметр $page меньше 1
	 *          - При ошибках HTTP-запроса к API
	 *          - При ошибках парсинга ответа от API
	 *          - При ошибках создания объектов PersonAward из данных API
	 *
	 * @example
	 * ```php
	 * // Получение первых 10 наград персон
	 * $awards = $kinopoisk->getPersonAwards();
	 *
	 * // Получение наград с фильтрацией по профессии
	 * $filter = new PersonSearchFilter();
	 * $filter->profession('актер');
	 * $awards = $kinopoisk->getPersonAwards($filter, 1, 20);
	 *
	 * // Получение наград живых персон с ограничением по возрасту
	 * $filter = new PersonSearchFilter();
	 * $filter->onlyAlive()->age(30, 'gte');
	 * $awards = $kinopoisk->getPersonAwards($filter, 2, 50);
	 * ```
	 */
	public function getPersonAwards(?PersonSearchFilter $filters, int $page = 1, int $limit = 10): PersonAwardDocsResponseDto {
		if ($limit > 250) {
			throw new KinopoiskDevException('Limit не должен превышать 250');
		}
		if ($page < 1) {
			throw new KinopoiskDevException('Номер страницы не должен быть меньше 1');
		}

		if (is_null($filters)) {
			$filters = new PersonSearchFilter();
		}

		$response = $this->makeRequest('GET', 'person/awards', $filters->getFilters());
		$data     = $this->parseResponse($response);

		return new PersonAwardDocsResponseDto(
			docs : array_map(fn ($awardData) => PersonAward::fromArray($awardData), $data['docs'] ?? []),
			total: $data['total'] ?? 0,
			limit: $data['limit'] ?? $limit,
			page : $data['page'] ?? $page,
			pages: $data['pages'] ?? 1,
		);
	}

}
