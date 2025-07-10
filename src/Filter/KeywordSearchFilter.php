<?php

namespace KinopoiskDev\Filter;

use KinopoiskDev\Enums\SortDirection;
use KinopoiskDev\Enums\SortField;
use KinopoiskDev\Utils\FilterTrait;
use KinopoiskDev\Utils\MovieFilter;

/**
 * Фильтр для поиска ключевых слов
 *
 * Класс предоставляет методы для создания фильтров поиска ключевых слов
 * по различным критериям: ID, названию, связанным фильмам, датам и т.д.
 * Используется в KeywordRequests для формирования параметров запроса к API.
 *
 * @package KinopoiskDev\Filter
 * @since   1.0.0
 * @author  Maxim Harder
 * @version 1.0.0
 *
 * @see     \KinopoiskDev\Http\KeywordRequests Для использования фильтра
 * @link    https://api.kinopoisk.dev/documentation-yaml Документация API
 */
class KeywordSearchFilter extends MovieFilter {
	use FilterTrait;

	/**
	 * Добавляет фильтр по ID ключевого слова
	 *
	 * @param   int|array<int>  $id        ID ключевого слова или массив ID
	 * @param   string          $operator  Оператор сравнения (eq, ne, in, nin)
	 *
	 * @return $this
	 */
	public function id(int|array $id, string $operator = 'eq'): self {
		$this->addFilter('id', $id, $operator);
		return $this;
	}

	/**
	 * Добавляет фильтр по названию ключевого слова
	 *
	 * @param   string  $title     Название ключевого слова
	 * @param   string  $operator  Оператор сравнения (eq, ne, regex)
	 *
	 * @return $this
	 */
	public function title(string $title, string $operator = 'eq'): self {
		$this->addFilter('title', $title, $operator);
		return $this;
	}

	/**
	 * Добавляет фильтр по ID фильма
	 *
	 * Находит все ключевые слова, связанные с указанным фильмом.
	 *
	 * @param   int|array<int>  $movieId  ID фильма или массив ID фильмов
	 *
	 * @return $this
	 */
	public function movieId(int|array $movieId): self {
		$this->addFilter('movieId', $movieId);
		return $this;
	}

	/**
	 * Добавляет фильтр по дате создания
	 *
	 * @param   string  $createdAt  Дата создания в ISO формате
	 * @param   string  $operator   Оператор сравнения (eq, ne, gt, gte, lt, lte)
	 *
	 * @return $this
	 */
	public function createdAt(string $createdAt, string $operator = 'eq'): self {
		$this->addFilter('createdAt', $createdAt, $operator);
		return $this;
	}

	/**
	 * Добавляет фильтр по дате обновления
	 *
	 * @param   string  $updatedAt  Дата обновления в ISO формате
	 * @param   string  $operator   Оператор сравнения (eq, ne, gt, gte, lt, lte)
	 *
	 * @return $this
	 */
	public function updatedAt(string $updatedAt, string $operator = 'eq'): self {
		$this->addFilter('updatedAt', $updatedAt, $operator);
		return $this;
	}

	/**
	 * Поиск ключевых слов по названию с использованием регулярных выражений
	 *
	 * @param   string  $query  Поисковый запрос
	 *
	 * @return $this
	 */
	public function search(string $query): self {
		$this->addFilter('title', $query, 'regex');
		return $this;
	}

	/**
	 * Фильтр для популярных ключевых слов (связанных с большим количеством фильмов)
	 *
	 * Возвращает ключевые слова, которые встречаются в 10 и более фильмах.
	 *
	 * @param   int  $minMovieCount  Минимальное количество связанных фильмов
	 *
	 * @return $this
	 */
	public function onlyPopular(int $minMovieCount = 10): self {
		$this->addFilter('movieCount', $minMovieCount, 'gte');
		return $this;
	}

	/**
	 * Фильтр для недавно созданных ключевых слов
	 *
	 * @param   int  $daysAgo  Количество дней назад от текущей даты
	 *
	 * @return $this
	 */
	public function recentlyCreated(int $daysAgo = 30): self {
		$timestamp = strtotime("-{$daysAgo} days");
		if ($timestamp === false) {
			$timestamp = time() - ($daysAgo * 86400); // fallback calculation
		}
		$date = date('Y-m-d\TH:i:s.v\Z', $timestamp);
		$this->addFilter('createdAt', $date, 'gte');
		return $this;
	}

	/**
	 * Фильтр для недавно обновленных ключевых слов
	 *
	 * @param   int  $daysAgo  Количество дней назад от текущей даты
	 *
	 * @return $this
	 */
	public function recentlyUpdated(int $daysAgo = 7): self {
		$timestamp = strtotime("-{$daysAgo} days");
		if ($timestamp === false) {
			$timestamp = time() - ($daysAgo * 86400); // fallback calculation
		}
		$date = date('Y-m-d\TH:i:s.v\Z', $timestamp);
		$this->addFilter('updatedAt', $date, 'gte');
		return $this;
	}

	/**
	 * Фильтр по диапазону дат создания
	 *
	 * @param   string  $startDate  Начальная дата в ISO формате
	 * @param   string  $endDate    Конечная дата в ISO формате
	 *
	 * @return $this
	 */
	public function createdBetween(string $startDate, string $endDate): self {
		$this->addFilter('createdAt', $startDate, 'gte')
			 ->addFilter('createdAt', $endDate, 'lte');
		return $this;
	}

	/**
	 * Фильтр по диапазону дат обновления
	 *
	 * @param   string  $startDate  Начальная дата в ISO формате
	 * @param   string  $endDate    Конечная дата в ISO формате
	 *
	 * @return $this
	 */
	public function updatedBetween(string $startDate, string $endDate): self {
		$this->addFilter('updatedAt', $startDate, 'gte')
			 ->addFilter('updatedAt', $endDate, 'lte');
		return $this;
	}

	/**
	 * Выбор определенных полей для возвращения
	 *
	 * @param   array<string>  $fields  Массив названий полей
	 *
	 * @return $this
	 */
	public function selectFields(array $fields): self {
		$this->filters['selectFields'] = implode(' ', $fields);
		return $this;
	}

	/**
	 * Исключение записей с пустыми значениями в указанных полях
	 *
	 * @param   array<string>  $fields  Массив названий полей
	 *
	 * @return $this
	 */
	public function notNullFields(array $fields): self {
		foreach ($fields as $field) {
			$this->addFilter($field, null, 'ne');
		}
		return $this;
	}

	// Методы сортировки

	/**
	 * Сортировка по ID ключевого слова
	 *
	 * @param   string  $direction  Направление сортировки ('asc' или 'desc')
	 *
	 * @return $this
	 */
	public function sortById(string $direction = 'asc'): self {
		$this->sortBy(SortField::ID, SortDirection::fromString($direction));
		return $this;
	}

	/**
	 * Сортировка по названию ключевого слова
	 *
	 * @param   string  $direction  Направление сортировки ('asc' или 'desc')
	 *
	 * @return $this
	 */
	public function sortByTitle(string $direction = 'asc'): self {
		$this->sortBy(SortField::TITLE, SortDirection::fromString($direction));
		return $this;
	}

	/**
	 * Сортировка по дате создания
	 *
	 * @param   string  $direction  Направление сортировки ('asc' или 'desc')
	 *
	 * @return $this
	 */
	public function sortByCreatedAt(string $direction = 'desc'): self {
		$this->sortBy(SortField::CREATED_AT, SortDirection::fromString($direction));
		return $this;
	}

	/**
	 * Сортировка по дате обновления
	 *
	 * @param   string  $direction  Направление сортировки ('asc' или 'desc')
	 *
	 * @return $this
	 */
	public function sortByUpdatedAt(string $direction = 'desc'): self {
		$this->sortBy(SortField::UPDATED_AT, SortDirection::fromString($direction));
		return $this;
	}

	/**
	 * Сортировка по популярности (количеству связанных фильмов)
	 *
	 * @param   string  $direction  Направление сортировки ('desc' для самых популярных)
	 *
	 * @return static
	 */
	public function sortByPopularity(string $direction = 'desc'): static {
		$this->addFilter('sort', "movieCount:{$direction}");
		return $this;
	}
}