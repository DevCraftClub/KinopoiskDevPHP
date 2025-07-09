<?php

namespace KinopoiskDev\Filter;

use KinopoiskDev\Enums\SortDirection;
use KinopoiskDev\Enums\SortField;
use KinopoiskDev\Enums\StudioType;
use KinopoiskDev\Utils\FilterTrait;
use KinopoiskDev\Utils\MovieFilter;

/**
 * Фильтр для поиска студий
 *
 * Класс предоставляет методы для создания фильтров поиска студий
 * по различным критериям: названию, типу, подтипу, связанным фильмам и т.д.
 * Используется в StudioRequests для формирования параметров запроса к API.
 *
 * @package KinopoiskDev\Filter
 * @since   1.0.0
 * @author  Maxim Harder
 * @version 1.0.0
 *
 * @see     \KinopoiskDev\Http\StudioRequests Для использования фильтра
 * @see     \KinopoiskDev\Enums\StudioType Для типов студий
 */
class StudioSearchFilter extends MovieFilter {
	use FilterTrait;

	/**
	 * Фильтр по идентификатору фильма
	 *
	 * Находит студии, которые участвовали в создании указанного фильма.
	 *
	 * @param   int|array  $movieIds  ID фильма или массив ID фильмов
	 *
	 * @return self Текущий экземпляр для цепочки методов
	 */
	public function movieId(int|array $movieIds): self {
		$this->addFilter('movies.id', $movieIds);
		return $this;
	}

	/**
	 * Фильтр по типу студии
	 *
	 * @param   string|StudioType|array  $types  Тип студии, enum или массив типов
	 *
	 * @return self Текущий экземпляр для цепочки методов
	 */
	public function studioType(string|StudioType|array $types): self {
		if ($types instanceof StudioType) {
			$types = $types->value;
		} elseif (is_array($types)) {
			$types = array_map(fn($type) => $type instanceof StudioType ? $type->value : $type, $types);
		}

		$this->addFilter('type', $types);
		return $this;
	}

	/**
	 * Фильтр по подтипу студии
	 *
	 * @param   string|array  $subTypes  Подтип студии или массив подтипов
	 *
	 * @return self Текущий экземпляр для цепочки методов
	 */
	public function subType(string|array $subTypes): self {
		$this->addFilter('subType', $subTypes);
		return $this;
	}

	/**
	 * Фильтр по названию студии
	 *
	 * Поиск по точному или частичному совпадению названия.
	 *
	 * @param   string|array  $titles  Название студии или массив названий
	 *
	 * @return self Текущий экземпляр для цепочки методов
	 */
	public function title(string|array $titles): self {
		$this->addFilter('title', $titles);
		return $this;
	}

	/**
	 * Удобный метод для фильтрации производственных студий
	 *
	 * @return self Текущий экземпляр для цепочки методов
	 */
	public function productionStudios(): self {
		return $this->studioType(StudioType::PRODUCTION);
	}

	/**
	 * Удобный метод для фильтрации студий спецэффектов
	 *
	 * @return self Текущий экземпляр для цепочки методов
	 */
	public function specialEffectsStudios(): self {
		return $this->studioType(StudioType::SPECIAL_EFFECTS);
	}

	/**
	 * Удобный метод для фильтрации прокатных компаний
	 *
	 * @return self Текущий экземпляр для цепочки методов
	 */
	public function distributionCompanies(): self {
		return $this->studioType(StudioType::DISTRIBUTION);
	}

	/**
	 * Удобный метод для фильтрации студий дубляжа
	 *
	 * @return self Текущий экземпляр для цепочки методов
	 */
	public function dubbingStudios(): self {
		return $this->studioType(StudioType::DUBBING_STUDIO);
	}

	/**
	 * Исключить определенные типы студий
	 *
	 * @param   string|StudioType|array  $types  Типы для исключения
	 *
	 * @return self Текущий экземпляр для цепочки методов
	 */
	public function excludeTypes(string|StudioType|array $types): self {
		if (!is_array($types)) {
			$types = [$types];
		}

		$excludeTypes = array_map(function($type) {
			$value = $type instanceof StudioType ? $type->value : $type;
			return "!{$value}";
		}, $types);

		$this->addFilter('type', $excludeTypes);
		return $this;
	}

	/**
	 * Поиск студий, участвовавших в нескольких фильмах
	 *
	 * @param   array  $movieIds  Массив ID фильмов (студия должна участвовать во всех)
	 *
	 * @return self Текущий экземпляр для цепочки методов
	 */
	public function participatedInAllMovies(array $movieIds): self {
		foreach ($movieIds as $movieId) {
			$this->addFilter('movies.id', "+{$movieId}");
		}
		return $this;
	}

	/**
	 * Сортировка по названию студии
	 *
	 * @param   string  $direction  Направление сортировки ('asc' или 'desc')
	 *
	 * @return self Текущий экземпляр для цепочки методов
	 */
	public function sortByTitle(string $direction = 'asc'): self {
		$this->sortBy(SortField::TITLE, SortDirection::fromString($direction));
		return $this;
	}

	/**
	 * Сортировка по типу студии
	 *
	 * @param   string  $direction  Направление сортировки ('asc' или 'desc')
	 *
	 * @return self Текущий экземпляр для цепочки методов
	 */
	public function sortByType(string $direction = 'asc'): self {
		$this->sortBy(SortField::TYPE, SortDirection::fromString($direction));
		return $this;
	}
}