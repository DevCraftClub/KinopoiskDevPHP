<?php

namespace KinopoiskDev\Http;

use KinopoiskDev\Enums\StudioType;
use KinopoiskDev\Exceptions\KinopoiskDevException;
use KinopoiskDev\Filter\StudioSearchFilter;
use KinopoiskDev\Kinopoisk;
use KinopoiskDev\Models\Studio;
use KinopoiskDev\Responses\Api\StudioDocsResponseDto;

/**
 * Класс для API-запросов, связанных со студиями
 *
 * Этот класс предоставляет методы для всех конечных точек студий API Kinopoisk.dev.
 * Позволяет получать информацию о кинокомпаниях, студиях дубляжа, производителях
 * и других организациях, участвующих в создании фильмов.
 *
 * @package KinopoiskDev\Http
 * @since   1.0.0
 * @author  Maxim Harder
 * @version 1.0.0
 * @see     \KinopoiskDev\Models\Studio Для структуры данных студии
 * @see     \KinopoiskDev\Filter\StudioSearchFilter Для фильтрации запросов
 */
class StudioRequests extends Kinopoisk {

	/**
	 * Получает производственные студии
	 *
	 * Удобный метод для получения студий типа "Производство".
	 *
	 * @param   int  $page   Номер страницы результатов
	 * @param   int  $limit  Количество результатов на странице
	 *
	 * @return StudioDocsResponseDto Производственные студии
	 * @throws KinopoiskDevException При ошибках API
	 * @throws \JsonException|\KinopoiskDev\Exceptions\KinopoiskResponseException При ошибках парсинга JSON
	 */
	public function getProductionStudios(int $page = 1, int $limit = 10): StudioDocsResponseDto {
		return $this->getStudiosByType(StudioType::PRODUCTION->value, $page, $limit);
	}

	/**
	 * Получает студии по типу
	 *
	 * Удобный метод для получения студий определенного типа:
	 * "Производство", "Спецэффекты", "Прокат", "Студия дубляжа"
	 *
	 * @param   string  $type   Тип студии
	 * @param   int     $page   Номер страницы результатов
	 * @param   int     $limit  Количество результатов на странице
	 *
	 * @return StudioDocsResponseDto Студии указанного типа
	 * @throws KinopoiskDevException При ошибках API
	 * @throws \JsonException|\KinopoiskDev\Exceptions\KinopoiskResponseException При ошибках парсинга JSON
	 */
	public function getStudiosByType(string $type, int $page = 1, int $limit = 10): StudioDocsResponseDto {
		$filters = new StudioSearchFilter();
		$filters->type($type);

		return $this->searchStudios($filters, $page, $limit);
	}

	/**
	 * Ищет студии по различным критериям
	 *
	 * Основной метод для поиска студий с поддержкой сложных фильтров.
	 * Позволяет искать по названию, типу студии, подтипу и связанным фильмам.
	 *
	 * @api    /v1.4/studio
	 * @link   https://kinopoiskdev.readme.io/reference/studiocontroller_findmanyv1_4
	 *
	 * @param   StudioSearchFilter|null  $filters  Объект фильтра для поиска студий
	 * @param   int                      $page     Номер страницы результатов (по умолчанию: 1)
	 * @param   int                      $limit    Количество результатов на странице (по умолчанию: 10, максимум: 250)
	 *
	 * @return StudioDocsResponseDto Результаты поиска с пагинацией
	 * @throws KinopoiskDevException При ошибках API
	 */
	public function searchStudios(?StudioSearchFilter $filters = NULL, int $page = 1, int $limit = 10): StudioDocsResponseDto {
		if ($limit > 250) {
			throw new KinopoiskDevException('Лимит не должен превышать 250');
		}
		if ($page < 1) {
			throw new KinopoiskDevException('Номер страницы не должен быть меньше 1');
		}

		if (is_null($filters)) {
			$filters = new StudioSearchFilter();
		}

		$filters->addFilter('page', $page);
		$filters->addFilter('limit', $limit);
		$queryParams = $filters->getFilters();

		$response = $this->makeRequest('GET', 'studio', $queryParams);
		$data     = $this->parseResponse($response);

		return new StudioDocsResponseDto(
			docs : array_map(fn ($studioData) => Studio::fromArray($studioData), $data['docs'] ?? []),
			total: $data['total'] ?? 0,
			limit: $data['limit'] ?? $limit,
			page : $data['page'] ?? $page,
			pages: $data['pages'] ?? 1,
		);
	}

	/**
	 * Получает студии дубляжа
	 *
	 * Удобный метод для получения студий типа "Студия дубляжа".
	 *
	 * @param   int  $page   Номер страницы результатов
	 * @param   int  $limit  Количество результатов на странице
	 *
	 * @return StudioDocsResponseDto Студии дубляжа
	 * @throws KinopoiskDevException При ошибках API
	 * @throws \JsonException|\KinopoiskDev\Exceptions\KinopoiskResponseException При ошибках парсинга JSON
	 */
	public function getDubbingStudios(int $page = 1, int $limit = 10): StudioDocsResponseDto {
		return $this->getStudiosByType(StudioType::DUBBING_STUDIO->value, $page, $limit);
	}

	/**
	 * Получает студии по названию
	 *
	 * Выполняет поиск студий по точному или частичному совпадению названия.
	 *
	 * @param   string  $title  Название студии для поиска
	 * @param   int     $page   Номер страницы результатов
	 * @param   int     $limit  Количество результатов на странице
	 *
	 * @return StudioDocsResponseDto Студии с подходящими названиями
	 * @throws KinopoiskDevException При ошибках API
	 * @throws \JsonException|\KinopoiskDev\Exceptions\KinopoiskResponseException При ошибках парсинга JSON
	 */
	public function getStudiosByTitle(string $title, int $page = 1, int $limit = 10): StudioDocsResponseDto {
		$filters = new StudioSearchFilter();
		$filters->title($title);

		return $this->searchStudios($filters, $page, $limit);
	}

	/**
	 * Получает студию по её уникальному идентификатору
	 *
	 * @param int $studioId Уникальный идентификатор студии
	 * @return Studio Объект студии
	 * @throws KinopoiskDevException При ошибках API
	 */
	public function getStudioById(int $studioId): Studio {
		$filters = new StudioSearchFilter();
		$filters->id($studioId);
		$filters->addFilter('limit', 1);
		$response = $this->makeRequest('GET', "studio", $filters->getFilters());
		$data     = $this->parseResponse($response);

		return Studio::fromArray($data['docs'][0] ?? []);
	}

	/**
	 * Получает случайную студию
	 *
	 * @param StudioSearchFilter|null $filters Фильтры для поиска
	 * @return Studio Случайная студия
	 */
	public function getRandomStudio(?StudioSearchFilter $filters = null): Studio {
		if (is_null($filters)) {
			$filters = new StudioSearchFilter();
		}
		
		$results = $this->searchStudios($filters, 1, 1);
		if (empty($results->docs)) {
			throw new KinopoiskDevException('Не найдено студий, соответствующих фильтрам');
		}
		
		return $results->docs[0];
	}

	/**
	 * Выполняет поиск студий по названию (алиас для getStudiosByTitle)
	 *
	 * @param string $name Название для поиска
	 * @param int $page Номер страницы
	 * @param int $limit Количество результатов
	 * @return StudioDocsResponseDto Результаты поиска
	 */
	public function searchStudiosByName(string $name, int $page = 1, int $limit = 10): StudioDocsResponseDto {
		return $this->getStudiosByTitle($name, $page, $limit);
	}

	/**
	 * Получает студии по стране
	 *
	 * @param string $country Страна
	 * @param int $page Номер страницы
	 * @param int $limit Количество результатов
	 * @return StudioDocsResponseDto Результаты поиска
	 */
	public function getStudiosByCountry(string $country, int $page = 1, int $limit = 10): StudioDocsResponseDto {
		$filters = new StudioSearchFilter();
		$filters->country($country);
		
		return $this->searchStudios($filters, $page, $limit);
	}

	/**
	 * Получает студии по году основания
	 *
	 * @param int $year Год основания
	 * @param int $page Номер страницы
	 * @param int $limit Количество результатов
	 * @return StudioDocsResponseDto Результаты поиска
	 */
	public function getStudiosByYear(int $year, int $page = 1, int $limit = 10): StudioDocsResponseDto {
		$filters = new StudioSearchFilter();
		$filters->year($year);
		
		return $this->searchStudios($filters, $page, $limit);
	}

	/**
	 * Получает студии по диапазону годов основания
	 *
	 * @param int $fromYear Начальный год
	 * @param int $toYear Конечный год
	 * @param int $page Номер страницы
	 * @param int $limit Количество результатов
	 * @return StudioDocsResponseDto Результаты поиска
	 */
	public function getStudiosByYearRange(int $fromYear, int $toYear, int $page = 1, int $limit = 10): StudioDocsResponseDto {
		$filters = new StudioSearchFilter();
		$filters->year($fromYear, $toYear);
		
		return $this->searchStudios($filters, $page, $limit);
	}

	/**
	 * Получает студии, связанные с определенным фильмом
	 *
	 * Находит все студии, которые принимали участие в создании указанного фильма.
	 *
	 * @param   int  $movieId  Идентификатор фильма
	 * @param   int  $page     Номер страницы результатов
	 * @param   int  $limit    Количество результатов на странице
	 *
	 * @return StudioDocsResponseDto Студии, связанные с фильмом
	 * @throws KinopoiskDevException При ошибках API
	 * @throws \JsonException|\KinopoiskDev\Exceptions\KinopoiskResponseException При ошибках парсинга JSON
	 */
	public function getStudiosForMovie(int $movieId, int $page = 1, int $limit = 10): StudioDocsResponseDto {
		$filters = new StudioSearchFilter();
		$filters->movieId($movieId);

		return $this->searchStudios($filters, $page, $limit);
	}

}