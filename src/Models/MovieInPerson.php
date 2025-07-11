<?php

namespace KinopoiskDev\Models;

/**
 * Класс для представления персоны в контексте фильма
 *
 * Представляет информацию о персоне (актер, режиссер, сценарист и др.) в контексте
 * конкретного фильма или сериала. Содержит основные данные о персоне, включая
 * идентификатор, имена, рейтинг, описание роли и профессию. Используется для
 * хранения и обработки данных об участниках кинопроизводства, полученных от API
 * Kinopoisk.dev.
 *
 * @package KinopoiskDev\Models
 * @since   1.0.0
 * @author  Maxim Harder
 *
 * @version 1.0.0
 * @see     \KinopoiskDev\Models\PersonInMovie Для обратной связи (персона в фильме)
 * @see     \KinopoiskDev\Models\Person Для полной информации о персоне
 * @see     \KinopoiskDev\Models\Movie Для использования в информации о фильмах
 */
 class MovieInPerson implements BaseModel {

	/**
	 * Конструктор модели фильма в персоне
	 *
	 * Создает новый экземпляр класса MovieInPerson с указанными параметрами.
	 * Только идентификатор является обязательным параметром, остальные могут
	 * быть null при отсутствии соответствующей информации о персоне или её роли
	 * в конкретном фильме.
	 *
	 * @see MovieInPerson::fromArray() Для создания объекта из массива данных API
	 * @see MovieInPerson::toArray() Для преобразования объекта в массив
	 *
	 * @param   int          $id               Уникальный идентификатор персоны в системе Kinopoisk
	 * @param   string|null  $name             Имя персоны на русском языке (null если не указано)
	 * @param   string|null  $alternativeName  Альтернативное имя персоны (null если не указано)
	 * @param   float|null   $rating           Рейтинг персоны в контексте данного фильма (null если не указан)
	 * @param   bool|null    $general          Является ли персона главным участником фильма (null если не определено)
	 * @param   string|null  $description      Описание роли персоны в фильме (null если не указано)
	 * @param   string|null  $enProfession     Профессия персоны на английском языке (null если не указана)
	 *
	 * @example
	 * ```php
	 * $moviePerson = new MovieInPerson(
	 *     id: 123456,
	 *     name: 'Иван Петров',
	 *     alternativeName: 'Ivan Petrov',
	 *     rating: 8.5,
	 *     general: true,
	 *     description: 'Главная роль',
	 *     enProfession: 'actor'
	 * );
	 * ```
	 */
	public function __construct(
		public int     $id,
		public ?string $name = NULL,
		public ?string $alternativeName = NULL,
		public ?float  $rating = NULL,
		public ?bool   $general = NULL,
		public ?string $description = NULL,
		public ?string $enProfession = NULL,

	) {}

	/**
	 * Создает объект MovieInPerson из массива данных API
	 *
	 * Фабричный метод для создания экземпляра класса MovieInPerson из массива данных,
	 * полученных от API Kinopoisk.dev. Безопасно обрабатывает отсутствующие значения,
	 * устанавливая их в null. Идентификатор является обязательным параметром и должен
	 * присутствовать в массиве данных.
	 *
	 * @see MovieInPerson::toArray() Для обратного преобразования в массив
	 * @see BaseModel::fromArray() Реализация интерфейса BaseModel
	 *
	 * @param   array  $data  Массив данных о персоне от API, содержащий ключи:
	 *                        - id: int - уникальный идентификатор персоны (обязательный)
	 *                        - name: string|null - имя персоны на русском языке
	 *                        - alternativeName: string|null - альтернативное имя персоны
	 *                        - rating: float|null - рейтинг персоны в контексте фильма
	 *                        - general: bool|null - является ли персона главным участником
	 *                        - description: string|null - описание роли в фильме
	 *                        - enProfession: string|null - профессия на английском языке
	 *
	 * @return BaseModel Новый экземпляр класса MovieInPerson с данными из массива
	 *
	 * @example
	 * ```php
	 * $personData = [
	 *     'id' => 123456,
	 *     'name' => 'Иван Петров',
	 *     'alternativeName' => 'Ivan Petrov',
	 *     'rating' => 8.5,
	 *     'general' => true,
	 *     'description' => 'Главная роль',
	 *     'enProfession' => 'actor'
	 * ];
	 * $moviePerson = MovieInPerson::fromArray($personData);
	 * ```
	 */
	public static function fromArray(array $data): self {
		return new self(
			id             : $data['id'],
			name           : $data['name'] ?? NULL,
			alternativeName: $data['alternativeName'] ?? NULL,
			rating         : $data['rating'] ?? NULL,
			general        : $data['general'] ?? NULL,
			description    : $data['description'] ?? NULL,
			enProfession   : $data['enProfession'] ?? NULL,
		);
	}

	/**
	 * Преобразует объект в массив данных
	 *
	 * Конвертирует текущий экземпляр класса MovieInPerson в массив,
	 * совместимый с форматом API Kinopoisk.dev. Используется для сериализации
	 * данных при отправке запросов к API, экспорта данных в JSON или других
	 * операций преобразования. Все свойства объекта, включая null-значения,
	 * сохраняются в результирующем массиве.
	 *
	 * @see MovieInPerson::fromArray() Для создания объекта из массива
	 * @see BaseModel::toArray() Реализация интерфейса BaseModel
	 *
	 * @return array Массив с данными о персоне в фильме, содержащий ключи:
	 *               - id: int - уникальный идентификатор персоны
	 *               - name: string|null - имя персоны на русском языке
	 *               - alternativeName: string|null - альтернативное имя персоны
	 *               - rating: float|null - рейтинг персоны в контексте фильма
	 *               - general: bool|null - является ли персона главным участником
	 *               - description: string|null - описание роли в фильме
	 *               - enProfession: string|null - профессия на английском языке
	 *
	 * @example
	 * ```php
	 * $moviePerson = new MovieInPerson(123456, 'Иван Петров', 'Ivan Petrov');
	 * $array = $moviePerson->toArray();
	 * // Результат: ['id' => 123456, 'name' => 'Иван Петров', 'alternativeName' => 'Ivan Petrov', ...]
	 * ```
	 */
	public function toArray(): array {
		return [
			'id'              => $this->id,
			'name'            => $this->name,
			'alternativeName' => $this->alternativeName,
			'rating'          => $this->rating,
			'general'         => $this->general,
			'description'     => $this->description,
			'enProfession'    => $this->enProfession,
		];
	}

}