<?php

namespace KinopoiskDev\Responses;

/**
 * Объект-контейнер для ответа API с данными о фильмах и информацией о пагинации
 *
 * Представляет стандартный ответ API Kinopoisk.dev для запросов возвращающих
 * коллекцию фильмов с поддержкой пагинации. Содержит массив документов фильмов
 * и метаданные для постраничной навигации.
 *
 * @package   KinopoiskDev\Responses
 * @author    Maxim Harder
 * @copyright MIT
 * @version   1.0.0
 * @see       \KinopoiskDev\Models\Movie Для структуры отдельного фильма
 * @see       \KinopoiskDev\Responses\ErrorResponseDto Для обработки ошибок API
 *
 */
class MovieDocsResponseDto {

	/**
	 * Конструктор для создания объекта ответа с данными о фильмах
	 *
	 * Создает новый экземпляр MovieDocsResponseDto с параметрами пагинации
	 * и массивом документов фильмов, полученных из API Kinopoisk.dev
	 *
	 * @see MovieDocsResponseDto::fromArray() Для создания объекта из массива
	 *      данных API
	 * @see MovieDocsResponseDto::toArray() Для преобразования объекта в массив
	 *
	 * @param   int                                 $limit  Максимальное количество фильмов на одной странице
	 * @param   int                                 $page   Текущий номер страницы (начинается с 1)
	 * @param   int                                 $pages  Общее количество страниц с результатами
	 *
	 * @param   \KinopoiskDev\Models\SearchMovie[]  $docs   Массив документов фильмов, полученных из API
	 * @param   int                                 $total  Общее количество фильмов в базе данных по заданным критериям
	 */
	public function __construct(
		public readonly array $docs = [],
		public readonly int   $total = 0,
		public readonly int   $limit = 10,
		public readonly int   $page = 1,
		public readonly int   $pages = 0,
	) {}

	public static function fromArray(array $data): MovieDocsResponseDto {
		return new self(
			docs : $data['docs'],
			total: $data['total'],
			limit: $data['limit'],
			page : $data['page'],
			pages: $data['pages'],
		);
	}

	public function toArray(): array {
		return [
			'docs'  => $this->docs,
			'total' => $this->total,
			'limit' => $this->limit,
			'page'  => $this->page,
			'pages' => $this->pages,
		];
	}

}