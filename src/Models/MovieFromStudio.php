<?php

namespace KinopoiskDev\Models;

/**
 * Класс для представления фильма из студии
 *
 * Представляет минимальную информацию о фильме в контексте студии,
 * содержащую только уникальный идентификатор произведения.
 * Используется как упрощенная модель для связи между студиями и фильмами.
 *
 * @package KinopoiskDev\Models
 * @since   1.0.0
 * @see     \KinopoiskDev\Models\Studio Для основной модели студии
 * @see     \KinopoiskDev\Models\Movie Для полной информации о фильме
 */
readonly class MovieFromStudio implements BaseModel {

	/**
	 * Конструктор для создания объекта фильма из студии
	 *
	 * Создает новый экземпляр класса MovieFromStudio с указанным идентификатором.
	 * Представляет минимальную информацию о фильме, связанном со студией.
	 *
	 * @see MovieFromStudio::fromArray() Для создания объекта из массива данных API
	 * @see MovieFromStudio::toArray() Для преобразования объекта в массив
	 *
	 * @param   int  $id  Уникальный идентификатор фильма в системе Kinopoisk
	 */
	public function __construct(
		public int $id,
	) {}

	/**
	 * Создает объект MovieFromStudio из массива данных API
	 *
	 * Фабричный метод для создания экземпляра класса MovieFromStudio из массива
	 * данных, полученных от API Kinopoisk.dev. Извлекает только необходимый
	 * идентификатор фильма для создания упрощенной модели.
	 *
	 * @see MovieFromStudio::toArray() Для обратного преобразования в массив
	 * @see \KinopoiskDev\Models\BaseModel::fromArray() Для интерфейса BaseModel
	 *
	 * @param   array  $data  Массив данных от API, содержащий ключ:
	 *                        - id: int - уникальный идентификатор фильма
	 *
	 * @return \KinopoiskDev\Models\BaseModel Новый экземпляр MovieFromStudio с данными из массива
	 */
	public static function fromArray(array $data): self {
		return new self(
			id: $data['id'],
		);
	}

	/**
	 * Преобразует объект в массив данных
	 *
	 * Конвертирует текущий экземпляр класса MovieFromStudio в массив,
	 * совместимый с форматом API Kinopoisk.dev. Возвращает только
	 * идентификатор фильма как минимальный набор данных.
	 *
	 * @see MovieFromStudio::fromArray() Для создания объекта из массива
	 * @see \KinopoiskDev\Models\BaseModel::toArray() Для интерфейса BaseModel
	 *
	 * @return array Массив с данными о фильме, содержащий ключ:
	 *               - id: int - уникальный идентификатор фильма
	 */
	public function toArray(): array {
		return [
			'id' => $this->id,
		];
	}

}