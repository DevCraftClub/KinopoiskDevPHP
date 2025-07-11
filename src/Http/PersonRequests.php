<?php

namespace KinopoiskDev\Http;

use KinopoiskDev\Exceptions\KinopoiskDevException;
use KinopoiskDev\Kinopoisk;
use KinopoiskDev\Models\Person;
use KinopoiskDev\Models\PersonAward;
use KinopoiskDev\Responses\Api\PersonAwardDocsResponseDto;
use KinopoiskDev\Responses\Api\PersonDocsResponseDto;
use KinopoiskDev\Filter\PersonSearchFilter;

/**
 * Класс для API-запросов, связанных с персонами
 *
 * Этот класс расширяет базовый класс Kinopoisk и предоставляет специализированные
 * методы для всех конечных точек персон API Kinopoisk.dev, включая поиск по персонам,
 * получение детальной информации о персоне и управление данными фильмографии.
 *
 * @package KinopoiskDev\Http
 * @since   1.0.0
 * @author  Maxim Harder
 *
 * @version 1.0.0
 * @see     \KinopoiskDev\Models\Person Для структуры данных персоны
 * @see     \KinopoiskDev\Filter\PersonSearchFilter Для фильтрации запросов
 */
class PersonRequests extends Kinopoisk {

	/**
	 * Получает персону по её уникальному идентификатору
	 *
	 * Выполняет запрос к API для получения полной информации о персоне,
	 * включая биографические данные, фильмографию и другие доступные сведения.
	 *
	 * @api    /v1.4/person/{id}
	 * @link   https://kinopoiskdev.readme.io/reference/personcontroller_findonev1_4
	 *
	 * @param   int  $personId  Уникальный идентификатор персоны в системе Kinopoisk
	 *
	 * @return Person Объект персоны со всеми доступными данными
	 * @throws KinopoiskDevException При ошибках API или проблемах с сетью
	 * @throws \JsonException При ошибках парсинга JSON-ответа
	 * @throws \KinopoiskDev\Exceptions\KinopoiskResponseException
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
	 * Поддерживает поиск как по русским, так и по английским именам.
	 *
	 * @api  /v1.4/person/search
	 * @link https://kinopoiskdev.readme.io/reference/personcontroller_searchpersonv1_4
	 *
	 * @param   int     $limit  Количество результатов на странице (максимум 250)
	 *
	 * @param   string  $name   Имя персоны для поиска (может быть русским или английским)
	 * @param   int     $page   Номер страницы результатов (начиная с 1)
	 *
	 * @return PersonDocsResponseDto Результаты поиска с информацией о пагинации
	 * @throws KinopoiskDevException При ошибках API
	 * @throws \JsonException При ошибках парсинга JSON-ответа
	 * @throws \KinopoiskDev\Exceptions\KinopoiskResponseException
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
	 * @link    https://kinopoiskdev.readme.io/reference/personcontroller_findawardsv1_4
	 *
	 * @param   PersonSearchFilter|null  $filters  Объект фильтрации для поиска наград
	 * @param   int                      $page     Номер страницы (по умолчанию: 1)
	 * @param   int                      $limit    Количество результатов на странице (по умолчанию: 10)
	 *
	 * @see    \KinopoiskDev\Filter\PersonSearchFilter Для параметров фильтрации
	 * @see    \KinopoiskDev\Models\PersonAward Для структуры данных наград персон
	 * @see    \KinopoiskDev\Responses\PersonAwardDocsResponseDto Для структуры ответа
	 * @link   https://kinopoiskdev.readme.io/reference/personcontroller_findmanyawardsv1_4
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
