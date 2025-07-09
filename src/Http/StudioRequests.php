<?php

namespace KinopoiskDev\Http;

use KinopoiskDev\Enums\StudioType;
use KinopoiskDev\Exceptions\KinopoiskDevException;
use KinopoiskDev\Kinopoisk;
use KinopoiskDev\Models\Studio;
use KinopoiskDev\Responses\Api\StudioDocsResponseDto;
use KinopoiskDev\Filter\StudioSearchFilter;

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
	 * @return StudioDocsResponseDto Результаты поиска с информацией о пагинации
	 * @throws KinopoiskDevException При ошибках API или превышении лимитов
	 * @throws \JsonException|\KinopoiskDev\Exceptions\KinopoiskResponseException При ошибках парсинга JSON-ответа
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