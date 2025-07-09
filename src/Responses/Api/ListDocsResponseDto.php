<?php

namespace KinopoiskDev\Responses\Api;

use KinopoiskDev\Models\Lists;
use KinopoiskDev\Responses\BaseDocsResponseDto;

/**
 * DTO для ответа API с коллекциями фильмов
 *
 * Этот класс представляет структурированный ответ от API Kinopoisk.dev
 * при запросе списка коллекций фильмов с поддержкой пагинации.
 *
 * @package KinopoiskDev\Responses\Api
 * @since   1.0.0
 * @author  Maxim Harder
 * @version 1.0.0
 * @link    https://kinopoiskdev.readme.io/reference/listcontroller_findmanyv1_4
 *
 * @extends BaseDocsResponseDto<Lists>
 */
class ListDocsResponseDto extends BaseDocsResponseDto {

	/**
	 * Массив коллекций фильмов
	 *
	 * @var Lists[] Список объектов коллекций, полученных от API
	 */
	public array $docs;

	/**
	 * Конструктор DTO ответа с коллекциями
	 *
	 * @param Lists[] $docs  Массив объектов коллекций фильмов
	 * @param int     $total Общее количество коллекций, соответствующих запросу
	 * @param int     $limit Максимальное количество коллекций на странице
	 * @param int     $page  Номер текущей страницы
	 * @param int     $pages Общее количество страниц
	 */
	public function __construct(
		array $docs,
		int $total,
		int $limit,
		int $page,
		int $pages
	) {
		parent::__construct($docs, $total, $limit, $page, $pages);
	}

	/**
	 * Получает все названия коллекций
	 *
	 * @return string[] Массив названий коллекций
	 */
	public function getListNames(): array {
		return array_map(fn(Lists $list) => $list->name, $this->docs);
	}

	/**
	 * Фильтрует коллекции по категории
	 *
	 * @param string $category Название категории для фильтрации
	 *
	 * @return Lists[] Массив коллекций указанной категории
	 */
	public function filterByCategory(string $category): array {
		return array_filter(
			$this->docs,
			fn(Lists $list) => $list->category === $category
		);
	}

	/**
	 * Получает популярные коллекции (с большим количеством фильмов)
	 *
	 * @param int $threshold Минимальное количество фильмов для считания коллекции популярной
	 *
	 * @return Lists[] Массив популярных коллекций
	 */
	public function getPopularLists(int $threshold = 100): array {
		return array_filter(
			$this->docs,
			fn(Lists $list) => $list->isPopular($threshold)
		);
	}

}