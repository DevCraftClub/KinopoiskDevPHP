<?php

namespace KinopoiskDev\Models;

use KinopoiskDev\Enums\PersonSex;

/**
 * Класс для представления супруга/супруги персоны
 *
 * Представляет информацию о супруге или супруге персоны, включая персональные данные,
 * статус отношений, количество детей и причины развода. Используется для хранения
 * и обработки семейной информации персон из API Kinopoisk.dev.
 *
 * @package KinopoiskDev\Models
 * @since   1.0.0
 * @author  Maxim Harder
 * @version 1.0.0
 *
 * @see     \KinopoiskDev\Enums\PersonSex Для определения пола супруга
 * @see     \KinopoiskDev\Models\Person Для основной модели персоны
 */
readonly class Spouses implements BaseModel {

	/**
	 * Конструктор для создания объекта супруга
	 *
	 * Создает новый экземпляр класса Spouses с информацией о супруге персоны.
	 * Все свойства являются для обеспечения неизменности данных.
	 * Параметр divorced имеет значение по умолчанию false.
	 *
	 * @see Spouses::fromArray() Для создания объекта из массива данных API
	 * @see Spouses::toArray() Для преобразования объекта в массив
	 *
	 * @param   int        $id              Уникальный идентификатор супруга в базе данных
	 * @param   string     $name            Полное имя супруга
	 * @param   bool       $divorced        Статус развода (true - в разводе, false - в браке, по умолчанию false)
	 * @param   string     $divorcedReason  Причина развода (пустая строка если развода не было)
	 * @param   PersonSex  $sex             Пол супруга (мужской или женский)
	 * @param   int        $children        Количество детей в браке
	 * @param   string     $relation        Описание типа отношений или дополнительная информация
	 */
	public function __construct(
		public int       $id,
		public string    $name,
		public bool      $divorced = FALSE,
		public string    $divorcedReason,
		public PersonSex $sex,
		public int       $children,
		public string    $relation,
	) {}

	/**
	 * Создает объект Spouses из массива данных API
	 *
	 * Фабричный метод для создания экземпляра класса Spouses из массива данных,
	 * полученных от API Kinopoisk.dev. Безопасно обрабатывает все поля массива
	 * и автоматически преобразует строковое значение пола в enum PersonSex
	 * с помощью метода tryFrom().
	 *
	 * @see Spouses::toArray() Для обратного преобразования в массив
	 * @see PersonSex::tryFrom() Для безопасного преобразования строки в enum
	 *
	 * @param   array  $data  Массив данных о супруге от API, содержащий ключи:
	 *                        - id: int - уникальный идентификатор супруга
	 *                        - name: string - полное имя супруга
	 *                        - divorced: bool - статус развода
	 *                        - divorcedReason: string - причина развода
	 *                        - sex: string - пол супруга ('male' или 'female')
	 *                        - children: int - количество детей
	 *                        - relation: string - тип отношений
	 *
	 * @return self Новый экземпляр класса Spouses с данными из массива
	 */
	public static function fromArray(array $data): self {
		return new static(
			id            : $data['id'],
			name          : $data['name'],
			divorced      : $data['divorced'],
			divorcedReason: $data['divorcedReason'],
			sex           : PersonSex::tryFrom($data['sex']),
			children      : $data['children'],
			relation      : $data['relation'],
		);
	}

	/**
	 * Преобразует объект в массив данных
	 *
	 * Конвертирует текущий экземпляр класса Spouses в массив,
	 * совместимый с форматом API Kinopoisk.dev. Используется для сериализации
	 * данных при отправке запросов к API или для экспорта данных в JSON.
	 * Enum PersonSex автоматически преобразуется в строковое значение.
	 *
	 * @see Spouses::fromArray() Для создания объекта из массива
	 * @see PersonSex::value Для получения строкового значения enum
	 *
	 * @return array Массив с данными о супруге, содержащий ключи:
	 *               - id: int - уникальный идентификатор супруга
	 *               - name: string - полное имя супруга
	 *               - divorced: bool - статус развода
	 *               - divorcedReason: string - причина развода
	 *               - sex: PersonSex - пол супруга (enum объект)
	 *               - children: int - количество детей
	 *               - relation: string - тип отношений
	 */
	public function toArray(): array {
		return [
			'id'             => $this->id,
			'name'           => $this->name,
			'divorced'       => $this->divorced,
			'divorcedReason' => $this->divorcedReason,
			'sex'            => $this->sex,
			'children'       => $this->children,
			'relation'       => $this->relation,
		];
	}

}