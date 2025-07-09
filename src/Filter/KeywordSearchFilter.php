<?php

namespace KinopoiskDev\Filter;

/**
 * Фильтр для поиска ключевых слов
 *
 * Этот класс предоставляет fluent interface для построения фильтров
 * при поиске ключевых слов через API Kinopoisk.dev.
 * Позволяет фильтровать по названию, ID фильмов, датам создания и обновления.
 *
 * @package KinopoiskDev\Filter
 * @since   1.0.0
 * @author  Maxim Harder
 * @version 1.0.0
 * @link    https://kinopoiskdev.readme.io/reference/keywordcontroller_findmanyv1_4
 */
class KeywordSearchFilter {

	/**
	 * Массив фильтров для API запроса
	 *
	 * @var array<string, mixed> Ассоциативный массив параметров фильтрации
	 */
	private array $filters = [];

	/**
	 * Добавляет фильтр к запросу
	 *
	 * @param   string  $key    Ключ фильтра
	 * @param   mixed   $value  Значение фильтра
	 *
	 * @return self Текущий экземпляр для цепочки вызовов
	 */
	public function addFilter(string $key, mixed $value): self {
		$this->filters[$key] = $value;
		return $this;
	}

	/**
	 * Возвращает все установленные фильтры
	 *
	 * @return array<string, mixed> Массив фильтров для API запроса
	 */
	public function getFilters(): array {
		return $this->filters;
	}

	/**
	 * Очищает все фильтры
	 *
	 * @return self Текущий экземпляр для цепочки вызовов
	 */
	public function clearFilters(): self {
		$this->filters = [];
		return $this;
	}

	// ==================== ПОИСК ПО ОСНОВНЫМ ПОЛЯМ ====================

	/**
	 * Фильтрация по ID ключевого слова
	 *
	 * @param   int|string|array  $id  ID ключевого слова (можно использовать "!" для исключения)
	 *
	 * @return self Текущий экземпляр для цепочки вызовов
	 * @example $filter->id(123) // поиск по ID 123
	 * @example $filter->id('!123') // исключить ID 123
	 * @example $filter->id([123, 456]) // поиск по нескольким ID
	 */
	public function id(int|string|array $id): self {
		$this->filters['id'] = is_array($id) ? $id : [$id];
		return $this;
	}

	/**
	 * Фильтрация по названию ключевого слова
	 *
	 * @param   string|array  $title  Название ключевого слова (можно использовать "!" для исключения)
	 *
	 * @return self Текущий экземпляр для цепочки вызовов
	 * @example $filter->title('1980-е') // поиск слов содержащих "1980-е"
	 * @example $filter->title('!ужасы') // исключить слово "ужасы"
	 * @example $filter->title(['комедия', 'драма']) // поиск нескольких слов
	 */
	public function title(string|array $title): self {
		$this->filters['title'] = is_array($title) ? $title : [$title];
		return $this;
	}

	/**
	 * Фильтрация по ID фильмов, связанных с ключевыми словами
	 *
	 * @param   int|string|array  $movieId  ID фильма (можно использовать "!" для исключения)
	 *
	 * @return self Текущий экземпляр для цепочки вызовов
	 * @example $filter->movieId(666) // ключевые слова фильма с ID 666
	 * @example $filter->movieId('!123') // исключить ключевые слова фильма 123
	 * @example $filter->movieId([666, 777]) // ключевые слова нескольких фильмов
	 */
	public function movieId(int|string|array $movieId): self {
		$this->filters['movies.id'] = is_array($movieId) ? $movieId : [$movieId];
		return $this;
	}

	// ==================== ФИЛЬТРАЦИЯ ПО ДАТАМ ====================

	/**
	 * Фильтрация по дате обновления записи
	 *
	 * @param   string  $date  Дата в формате dd.mm.yyyy или диапазон dd.mm.yyyy-dd.mm.yyyy
	 *
	 * @return self Текущий экземпляр для цепочки вызовов
	 * @example $filter->updatedAt('01.01.2023') // обновлено 1 января 2023
	 * @example $filter->updatedAt('01.01.2023-31.12.2023') // обновлено в 2023 году
	 */
	public function updatedAt(string $date): self {
		$this->filters['updatedAt'] = [$date];
		return $this;
	}

	/**
	 * Фильтрация по дате создания записи
	 *
	 * @param   string  $date  Дата в формате dd.mm.yyyy или диапазон dd.mm.yyyy-dd.mm.yyyy
	 *
	 * @return self Текущий экземпляр для цепочки вызовов
	 * @example $filter->createdAt('01.01.2020') // создано 1 января 2020
	 * @example $filter->createdAt('01.01.2020-31.12.2020') // создано в 2020 году
	 */
	public function createdAt(string $date): self {
		$this->filters['createdAt'] = [$date];
		return $this;
	}

	// ==================== СОРТИРОВКА ====================

	/**
	 * Сортировка по ID ключевого слова
	 *
	 * @param   string  $direction  Направление сортировки: 'asc' или 'desc'
	 *
	 * @return self Текущий экземпляр для цепочки вызовов
	 */
	public function sortById(string $direction = 'asc'): self {
		$this->filters['sortField'] = ['id'];
		$this->filters['sortType'] = [$direction === 'desc' ? '-1' : '1'];
		return $this;
	}

	/**
	 * Сортировка по названию ключевого слова
	 *
	 * @param   string  $direction  Направление сортировки: 'asc' или 'desc'
	 *
	 * @return self Текущий экземпляр для цепочки вызовов
	 */
	public function sortByTitle(string $direction = 'asc'): self {
		$this->filters['sortField'] = ['title'];
		$this->filters['sortType'] = [$direction === 'desc' ? '-1' : '1'];
		return $this;
	}

	/**
	 * Сортировка по дате создания
	 *
	 * @param   string  $direction  Направление сортировки: 'asc' или 'desc'
	 *
	 * @return self Текущий экземпляр для цепочки вызовов
	 */
	public function sortByCreatedAt(string $direction = 'desc'): self {
		$this->filters['sortField'] = ['createdAt'];
		$this->filters['sortType'] = [$direction === 'desc' ? '-1' : '1'];
		return $this;
	}

	/**
	 * Сортировка по дате обновления
	 *
	 * @param   string  $direction  Направление сортировки: 'asc' или 'desc'
	 *
	 * @return self Текущий экземпляр для цепочки вызовов
	 */
	public function sortByUpdatedAt(string $direction = 'desc'): self {
		$this->filters['sortField'] = ['updatedAt'];
		$this->filters['sortType'] = [$direction === 'desc' ? '-1' : '1'];
		return $this;
	}

	// ==================== ДОПОЛНИТЕЛЬНЫЕ НАСТРОЙКИ ====================

	/**
	 * Указывает, какие поля должны быть возвращены в ответе
	 *
	 * @param   array<string>  $fields  Список полей для выборки
	 *
	 * @return self Текущий экземпляр для цепочки вызовов
	 * @example $filter->selectFields(['id', 'title', 'movies'])
	 */
	public function selectFields(array $fields): self {
		$this->filters['selectFields'] = $fields;
		return $this;
	}

	/**
	 * Указывает, какие поля не должны быть null или пустыми
	 *
	 * @param   array<string>  $fields  Список полей, которые должны содержать значения
	 *
	 * @return self Текущий экземпляр для цепочки вызовов
	 * @example $filter->notNullFields(['title', 'movies.id'])
	 */
	public function notNullFields(array $fields): self {
		$this->filters['notNullFields'] = $fields;
		return $this;
	}

	// ==================== УДОБНЫЕ МЕТОДЫ ====================

	/**
	 * Поиск ключевых слов, содержащих указанный текст
	 *
	 * @param   string  $searchText  Текст для поиска в названиях ключевых слов
	 *
	 * @return self Текущий экземпляр для цепочки вызовов
	 */
	public function search(string $searchText): self {
		return $this->title($searchText);
	}

	/**
	 * Поиск только популярных ключевых слов (с указанием полей)
	 *
	 * @return self Текущий экземпляр для цепочки вызовов
	 */
	public function onlyPopular(): self {
		return $this->notNullFields(['movies.id'])
		            ->sortByCreatedAt('desc');
	}

	/**
	 * Поиск ключевых слов за последний период
	 *
	 * @param   int  $days  Количество дней назад от текущей даты
	 *
	 * @return self Текущий экземпляр для цепочки вызовов
	 */
	public function recentlyCreated(int $days = 30): self {
		$fromDate = date('d.m.Y', strtotime("-{$days} days"));
		$toDate = date('d.m.Y');
		
		return $this->createdAt("{$fromDate}-{$toDate}")
		            ->sortByCreatedAt('desc');
	}

	/**
	 * Поиск ключевых слов, недавно обновленных
	 *
	 * @param   int  $days  Количество дней назад от текущей даты
	 *
	 * @return self Текущий экземпляр для цепочки вызовов
	 */
	public function recentlyUpdated(int $days = 7): self {
		$fromDate = date('d.m.Y', strtotime("-{$days} days"));
		$toDate = date('d.m.Y');
		
		return $this->updatedAt("{$fromDate}-{$toDate}")
		            ->sortByUpdatedAt('desc');
	}

}