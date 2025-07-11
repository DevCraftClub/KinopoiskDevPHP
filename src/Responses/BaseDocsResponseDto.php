<?php

namespace KinopoiskDev\Responses;

use KinopoiskDev\Utils\DataManager;

/**
 * Базовый класс для всех DTO ответов с пагинацией документов
 *
 * Предоставляет общую функциональность для пагинированных ответов API,
 * включая навигацию по страницам и получение элементов коллекции.
 * Этот абстрактный класс расширяет BaseResponseDto и добавляет
 * специфичные для пагинации методы.
 *
 * @package   KinopoiskDev\Responses
 * @since     1.0.0
 * @author    Maxim Harder
 *
 * @version   1.0.0
 * @see       \KinopoiskDev\Responses\Api\MovieDocsResponseDto
 * @see       \KinopoiskDev\Responses\Api\PersonDocsResponseDto
 * @see       \KinopoiskDev\Responses\BaseResponseDto
 */
abstract class BaseDocsResponseDto extends BaseResponseDto {

	/**
	 * Конструктор для создания DTO пагинированного ответа
	 *
	 * Инициализирует все необходимые параметры пагинации со значениями по умолчанию.
	 * Все свойства являются  для обеспечения неизменности данных.
	 *
	 * @since 1.0.0
	 *
	 * @param   int    $total  Общее количество доступных документов в коллекции
	 * @param   int    $limit  Максимальное количество документов на одной странице
	 * @param   int    $page   Номер текущей страницы (начинается с 1)
	 * @param   int    $pages  Общее количество страниц в коллекции
	 *
	 * @param   array<int, mixed>  $docs   Массив документов текущей страницы
	 */
	public function __construct(
		public  array $docs = [],
		public  int   $total = 0,
		public  int   $limit = 10,
		public  int   $page = 1,
		public  int   $pages = 0,
	) {}

	/**
	 * Возвращает номер следующей страницы
	 *
	 * Вычисляет номер следующей страницы на основе текущей позиции.
	 * Возвращает null, если текущая страница является последней.
	 *
	 * @since 1.0.0
	 * @see   hasNextPage() Для проверки существования следующей страницы
	 * @return int|null Номер следующей страницы или null если следующей страницы нет
	 *
	 */
	public function getNextPage(): ?int {
		return $this->hasNextPage() ? $this->page + 1 : NULL;
	}

	/**
	 * Проверяет наличие следующей страницы
	 *
	 * Определяет, есть ли еще страницы после текущей на основе
	 * сравнения номера текущей страницы с общим количеством страниц.
	 *
	 * @since 1.0.0
	 * @see   getNextPage() Для получения номера следующей страницы
	 * @return bool true если есть следующая страница, false в противном случае
	 *
	 */
	public function hasNextPage(): bool {
		return $this->page < $this->pages;
	}

	/**
	 * Возвращает номер предыдущей страницы
	 *
	 * Вычисляет номер предыдущей страницы на основе текущей позиции.
	 * Возвращает null, если текущая страница является первой.
	 *
	 * @since 1.0.0
	 * @see   hasPreviousPage() Для проверки существования предыдущей страницы
	 * @return int|null Номер предыдущей страницы или null если предыдущей страницы нет
	 *
	 */
	public function getPreviousPage(): ?int {
		return $this->hasPreviousPage() ? $this->page - 1 : NULL;
	}

	/**
	 * Проверяет наличие предыдущей страницы
	 *
	 * Определяет, есть ли страницы перед текущей на основе
	 * сравнения номера текущей страницы с единицей.
	 *
	 * @since 1.0.0
	 * @see   getPreviousPage() Для получения номера предыдущей страницы
	 * @return bool true если есть предыдущая страница, false в противном случае
	 *
	 */
	public function hasPreviousPage(): bool {
		return $this->page > 1;
	}

	/**
	 * Возвращает первый элемент коллекции
	 *
	 * Получает первый документ из массива docs текущей страницы.
	 * Возвращает null, если коллекция пуста.
	 *
	 * @since 1.0.0
	 * @see   getLast() Для получения последнего элемента
	 * @see   isEmpty() Для проверки пустоты коллекции
	 * @return mixed Первый документ или null если коллекция пуста
	 *
	 */
	public function getFirst(): mixed {
		return $this->docs[0] ?? NULL;
	}

	/**
	 * Возвращает последний элемент коллекции
	 *
	 * Получает последний документ из массива docs текущей страницы.
	 * Создает копию массива для избежания изменения  свойства.
	 * Возвращает null, если коллекция пуста.
	 *
	 * @since 1.0.0
	 * @see   getFirst() Для получения первого элемента
	 * @see   isEmpty() Для проверки пустоты коллекции
	 * @return mixed Последний документ или null если коллекция пуста
	 *
	 */
	public function getLast(): mixed {
		if (empty($this->docs)) {
			return NULL;
		}

		$docs = $this->docs;

		return end($docs) ? : NULL;
	}

	/**
	 * Проверяет пустоту коллекции результатов
	 *
	 * Определяет, содержит ли текущая страница какие-либо документы.
	 *
	 * @since 1.0.0
	 * @see   getCurrentPageCount() Для получения точного количества элементов
	 * @return bool true если коллекция пуста, false в противном случае
	 *
	 */
	public function isEmpty(): bool {
		return empty($this->docs);
	}

	/**
	 * Возвращает количество результатов на текущей странице
	 *
	 * Подсчитывает фактическое количество документов в массиве docs
	 * для текущей страницы.
	 *
	 * @since 1.0.0
	 * @see   isEmpty() Для проверки пустоты коллекции
	 * @return int Количество документов на текущей странице
	 *
	 */
	public function getCurrentPageCount(): int {
		return count($this->docs);
	}

	/**
	 * {@inheritDoc}
	 *
	 * Конвертирует объект в массив для сериализации
	 *
	 * Преобразует все свойства пагинации в ассоциативный массив,
	 * подходящий для JSON-сериализации или передачи в API.
	 *
	 * @since 1.0.0
	 * @return array Ассоциативный массив со всеми данными пагинации
	 *
	 */
	public function toArray(): array {
		return [
			'docs'  => DataManager::getObjectsArray($this->docs),
			'total' => $this->total,
			'limit' => $this->limit,
			'page'  => $this->page,
			'pages' => $this->pages,
		];
	}

}
