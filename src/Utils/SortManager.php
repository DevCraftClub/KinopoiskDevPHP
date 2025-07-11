<?php

namespace KinopoiskDev\Utils;

use KinopoiskDev\Enums\SortDirection;
use KinopoiskDev\Enums\SortField;
use KinopoiskDev\Filter\SortCriteria;

/**
 * Trait для добавления функциональности сортировки к фильтрам
 *
 * Этот trait предоставляет методы для управления параметрами сортировки
 * при выполнении запросов к API Kinopoisk.dev. Может использоваться
 * в классах фильтрации для расширения их функциональности.
 *
 * @package KinopoiskDev\Utils
 * @since   1.0.0
 * @author  Maxim Harder
 * @version 1.0.0
 */
trait SortManager {

	/**
	 * Массив критериев сортировки
	 *
	 * @var SortCriteria[]
	 */
	protected array $sortCriteria = [];

	/**
	 * Ассоциативный массив критериев сортировки, индексированный по полю
	 * Используется для быстрого доступа к критериям по полю
	 *
	 * @var array<string, SortCriteria>
	 */
	protected array $sortCriteriaByField = [];

	/**
	 * Удаляет сортировку по указанному полю
	 *
	 * @param   SortField  $field  Поле для удаления из сортировки
	 *
	 * @return $this Возвращает текущий экземпляр для цепочки вызовов
	 */
	public function removeSortByField(SortField $field): static {
		$fieldKey = $field->value;

		// Если критерий для этого поля существует, удаляем его
		if (isset($this->sortCriteriaByField[$fieldKey])) {
			// Удаляем из ассоциативного массива
			unset($this->sortCriteriaByField[$fieldKey]);

			// Удаляем из основного массива
			$this->sortCriteria = array_filter(
				$this->sortCriteria,
				fn (SortCriteria $criteria) => $criteria->field !== $field,
			);
		}

		return $this;
	}

	/**
	 * Переключает направление сортировки для указанного поля
	 *
	 * Если сортировка по полю существует, меняет направление на противоположное.
	 * Если сортировки нет, добавляет с направлением по умолчанию.
	 *
	 * @param   SortField  $field  Поле для переключения сортировки
	 *
	 * @return $this Возвращает текущий экземпляр для цепочки вызовов
	 */
	public function toggleSort(SortField $field): static {
		$fieldKey = $field->value;

		// Если критерий для этого поля существует, меняем направление
		if (isset($this->sortCriteriaByField[$fieldKey])) {
			$criteria         = $this->sortCriteriaByField[$fieldKey];
			$reversedCriteria = $criteria->reverse();

			// Обновляем в ассоциативном массиве
			$this->sortCriteriaByField[$fieldKey] = $reversedCriteria;

			// Обновляем в основном массиве
			foreach ($this->sortCriteria as $index => $c) {
				if ($c->field === $field) {
					$this->sortCriteria[$index] = $reversedCriteria;
					break;
				}
			}

			return $this;
		}

		// Если сортировка по полю не найдена, добавляем с направлением по умолчанию
		return $this->sortBy($field);
	}

	/**
	 * Добавляет сортировку по указанному полю
	 *
	 * @param   SortField           $field      Поле для сортировки
	 * @param   SortDirection|null  $direction  Направление сортировки (по умолчанию используется рекомендуемое)
	 *
	 * @return $this Возвращает текущий экземпляр для цепочки вызовов
	 */
	public function sortBy(SortField $field, ?SortDirection $direction = NULL): static {
		$direction = $direction ?? $field->getDefaultDirection();
		$criteria  = new SortCriteria($field, $direction);

		return $this->addSortCriteria($criteria);
	}

	/**
	 * Добавляет критерий сортировки
	 *
	 * Добавляет новый критерий сортировки к текущему набору.
	 * Если критерий для указанного поля уже существует, он будет заменен.
	 *
	 * @param   SortCriteria  $criteria  Критерий сортировки
	 *
	 * @return $this Возвращает текущий экземпляр для цепочки вызовов
	 */
	public function addSortCriteria(SortCriteria $criteria): static {
		$fieldKey = $criteria->field->value;

		// Если критерий для этого поля уже существует, удаляем его из основного массива
		if (isset($this->sortCriteriaByField[$fieldKey])) {
			$this->sortCriteria = array_filter(
				$this->sortCriteria,
				fn (SortCriteria $c) => $c->field !== $criteria->field,
			);
		}

		// Добавляем новый критерий
		$this->sortCriteria[]                 = $criteria;
		$this->sortCriteriaByField[$fieldKey] = $criteria;

		return $this;
	}

	/**
	 * Проверяет, установлена ли сортировка по указанному полю
	 *
	 * @param   SortField  $field  Поле для проверки
	 *
	 * @return bool true, если сортировка по полю установлена, false в противном случае
	 */
	public function hasSortBy(SortField $field): bool {
		return isset($this->sortCriteriaByField[$field->value]);
	}

	/**
	 * Возвращает направление сортировки для указанного поля
	 *
	 * @param   SortField  $field  Поле для получения направления
	 *
	 * @return SortDirection|null Направление сортировки или null, если сортировка не установлена
	 */
	public function getSortDirection(SortField $field): ?SortDirection {
		return $this->sortCriteriaByField[$field->value]->direction ?? NULL;
	}

	/**
	 * Возвращает все критерии сортировки
	 *
	 * @return SortCriteria[] Массив критериев сортировки
	 */
	public function getSortCriteria(): array {
		return $this->sortCriteria;
	}

	/**
	 * Устанавливает множественные критерии сортировки
	 *
	 * Заменяет текущие критерии сортировки новым набором.
	 *
	 * @param   SortCriteria[]  $criteria  Массив критериев сортировки
	 *
	 * @return $this Возвращает текущий экземпляр для цепочки вызовов
	 */
	public function setSortCriteria(array $criteria): static {
		// Очищаем текущие критерии
		$this->clearSort();

		// Добавляем новые критерии
		foreach ($criteria as $criterion) {
			if ($criterion instanceof SortCriteria) {
				$this->addSortCriteria($criterion);
			}
		}

		return $this;
	}

	/**
	 * Очищает все критерии сортировки
	 *
	 * @return $this Возвращает текущий экземпляр для цепочки вызовов
	 */
	public function clearSort(): static {
		$this->sortCriteria        = [];
		$this->sortCriteriaByField = [];

		return $this;
	}

	/**
	 * Добавляет множественные критерии сортировки из массива строк
	 *
	 * @param   array<string|SortCriteria>  $sorts  Массив строк в формате "field:direction" или просто "field"
	 *
	 * @return $this Возвращает текущий экземпляр для цепочки вызовов
	 *
	 * @example
	 * ```php
	 * $filter->addMultipleSort([
	 *     'rating.kp:desc',
	 *     'year:asc',
	 *     'name' // будет использовано направление по умолчанию
	 * ]);
	 * ```
	 */
	public function addMultipleSort(array $sorts): static {
		foreach ($sorts as $sort) {
			if ($sort instanceof SortCriteria) {
				$this->addSortCriteria($sort);
			} elseif (is_string($sort)) {
				$parts     = explode(':', $sort, 2);
				$field     = $parts[0];
				$direction = $parts[1] ?? NULL;

				$criteria = SortCriteria::fromStrings($field, $direction);
				if ($criteria) {
					$this->addSortCriteria($criteria);
				}
			}
		}

		return $this;
	}

	/**
	 * Преобразует критерии сортировки в параметры для API
	 *
	 * Формирует строку сортировки в формате, ожидаемом API Kinopoisk.dev.
	 * Множественные критерии объединяются запятыми.
	 *
	 * @return string|null Строка сортировки для API или null, если критерии не установлены
	 */
	public function getSortString(): ?string {
		if (empty($this->sortCriteria)) {
			return NULL;
		}

		$sortStrings = array_map(
			fn (SortCriteria $criteria) => $criteria->toApiString(),
			$this->sortCriteria,
		);

		return implode(',', $sortStrings);
	}

	/**
	 * Возвращает количество установленных критериев сортировки
	 *
	 * @return int Количество критериев сортировки
	 */
	public function getSortCount(): int {
		return count($this->sortCriteria);
	}

	/**
	 * Проверяет, установлены ли какие-либо критерии сортировки
	 *
	 * @return bool true, если есть хотя бы один критерий сортировки, false в противном случае
	 */
	public function hasAnySorting(): bool {
		return !empty($this->sortCriteria);
	}

	/**
	 * Возвращает первый критерий сортировки
	 *
	 * @return SortCriteria|null Первый критерий или null, если критерии отсутствуют
	 */
	public function getFirstSortCriteria(): ?SortCriteria {
		return $this->sortCriteria[0] ?? NULL;
	}

	/**
	 * Возвращает последний критерий сортировки
	 *
	 * @return SortCriteria|null Последний критерий или null, если критерии отсутствуют
	 */
	public function getLastSortCriteria(): ?SortCriteria {
		return end($this->sortCriteria) ? : NULL;
	}

	/**
	 * Сортировка по рейтингу IMDB (по убыванию)
	 *
	 * @return $this
	 */
	public function sortByImdbRating(): static {
		return $this->sortByDesc(SortField::RATING_IMDB);
	}

	/**
	 * Добавляет сортировку по убыванию
	 *
	 * @param   SortField  $field  Поле для сортировки
	 *
	 * @return $this Возвращает текущий экземпляр для цепочки вызовов
	 */
	public function sortByDesc(SortField $field): static {
		return $this->sortBy($field, SortDirection::DESC);
	}

	/**
	 * Предустановленные методы сортировки для популярных случаев
	 */

	/**
	 * Сортировка по году выпуска (по возрастанию - сначала старые)
	 *
	 * @return $this
	 */
	public function sortByYearOldFirst(): static {
		return $this->sortByAsc(SortField::YEAR);
	}

	/**
	 * Добавляет сортировку по возрастанию
	 *
	 * @param   SortField  $field  Поле для сортировки
	 *
	 * @return $this Возвращает текущий экземпляр для цепочки вызовов
	 */
	public function sortByAsc(SortField $field): static {
		return $this->sortBy($field, SortDirection::ASC);
	}

	/**
	 * Сортировка по названию (по алфавиту)
	 *
	 * @return $this
	 */
	public function sortByName(): static {
		return $this->sortByAsc(SortField::NAME);
	}

	/**
	 * Сортировка по популярности (количество голосов Кинопоиска)
	 *
	 * @return $this
	 */
	public function sortByPopularity(): static {
		return $this->sortByDesc(SortField::VOTES_KP);
	}

	/**
	 * Сортировка по дате создания записи (сначала новые)
	 *
	 * @return $this
	 */
	public function sortByCreated(): static {
		return $this->sortByDesc(SortField::CREATED_AT);
	}

	/**
	 * Сортировка по дате обновления записи (сначала обновленные)
	 *
	 * @return $this
	 */
	public function sortByUpdated(): static {
		return $this->sortByDesc(SortField::UPDATED_AT);
	}

	/**
	 * Комбинированная сортировка по рейтингу и году
	 *
	 * Сначала по рейтингу Кинопоиска (по убыванию), затем по году (по убыванию).
	 *
	 * @return $this
	 */
	public function sortByBest(): static {
		return $this
			->sortByKinopoiskRating()
			->sortByYear();
	}

	/**
	 * Сортировка по году выпуска (по убыванию - сначала новые)
	 *
	 * @return $this
	 */
	public function sortByYear(): static {
		return $this->sortByDesc(SortField::YEAR);
	}

	/**
	 * Сортировка по рейтингу Кинопоиска (по убыванию)
	 *
	 * @return $this
	 */
	public function sortByKinopoiskRating(): static {
		return $this->sortByDesc(SortField::RATING_KP);
	}

	/**
	 * Экспорт критериев сортировки в массив для сериализации
	 *
	 * @return array<array<string, string>> Массив с данными о критериях сортировки
	 */
	public function exportSortCriteria(): array {
		return array_map(
			fn (SortCriteria $criteria) => $criteria->toArray(),
			$this->sortCriteria,
		);
	}

	/**
	 * Импорт критериев сортировки из массива
	 *
	 * @param   array<string, mixed>  $data  Массив с данными о критериях сортировки
	 *
	 * @return $this
	 */
	public function importSortCriteria(array $data): static {
		// Используем clearSort для очистки обоих массивов
		$this->clearSort();

		foreach ($data as $criteriaData) {
			if (is_array($criteriaData)) {
				$criteria = SortCriteria::fromArray($criteriaData);
				if ($criteria) {
					$this->addSortCriteria($criteria);
				}
			}
		}

		return $this;
	}

}
