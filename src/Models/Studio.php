<?php

namespace KinopoiskDev\Models;

use KinopoiskDev\Enums\StudioType;
use KinopoiskDev\Utils\DataManager;

/**
 * Класс для представления студии кинопроизводства
 *
 * Представляет информацию о студии кинопроизводства, включая тип студии,
 * название, подтип и связанные с ней фильмы. Используется для хранения
 * и обработки данных о студиях, полученных от API Kinopoisk.dev.
 *
 * @package KinopoiskDev\Models
 * @since   1.0.0
 * @version 1.0.0
 * @see     \KinopoiskDev\Enums\StudioType Для типов студий
 * @see     \KinopoiskDev\Models\MovieFromStudio Для фильмов, связанных со студией
 * @see     \KinopoiskDev\Models\BaseModel Базовый интерфейс для всех моделей
 */
 class Studio implements BaseModel {

	/**
	 * Конструктор для создания объекта студии
	 *
	 * Создает новый экземпляр класса Studio с указанными параметрами.
	 * Только идентификатор, дата обновления и дата создания являются обязательными,
	 * остальные параметры могут быть null при отсутствии соответствующей информации.
	 *
	 * @see Studio::fromArray() Для создания объекта из массива данных API
	 * @see Studio::toArray() Для преобразования объекта в массив
	 * @see \KinopoiskDev\Enums\StudioType Для возможных типов студий
	 *
	 * @param   string                                  $id         Уникальный идентификатор студии
	 * @param   string|null                             $subType    Подтип студии или null если не определен
	 * @param   string|null                             $title      Название студии или null если не определено
	 * @param   StudioType|null                         $type       Тип студии или null если не определен
	 * @param   \KinopoiskDev\Models\MovieFromStudio[]  $movies     Массив фильмов, связанных со студией
	 * @param   string                                  $updateAt   Дата последнего обновления в формате ISO 8601
	 * @param   string                                  $createdAt  Дата создания записи в формате ISO 8601
	 */
	public function __construct(
		public string      $id,
		public ?string     $subType = NULL,
		public ?string     $title = NULL,
		public ?StudioType $type = NULL,
		public array       $movies = [],
		public ?string     $updateAt = NULL,
		public ?string     $createdAt = NULL,
	) {}

	/**
	 * Создает объект Studio из массива данных API
	 *
	 * Фабричный метод для создания экземпляра класса Studio из массива данных,
	 * полученных от API Kinopoisk.dev. Метод использует значения по умолчанию
	 * для опциональных параметров и безопасно обрабатывает отсутствующие ключи.
	 *
	 * @see Studio::toArray() Для обратного преобразования в массив
	 * @see \KinopoiskDev\Models\BaseModel::fromArray() Для интерфейса BaseModel
	 * @see \KinopoiskDev\Enums\StudioType Для преобразования типа студии
	 *
	 * @param   array  $data  Массив данных от API, содержащий ключи:
	 *                        - id: string - уникальный идентификатор студии (обязательный)
	 *                        - subType: string|null - подтип студии (опциональный)
	 *                        - title: string|null - название студии (опциональный)
	 *                        - type: StudioType|null - тип студии (опциональный)
	 *                        - movies: MovieFromStudio[] - массив связанных фильмов (опциональный)
	 *                        - updateAt: string - дата последнего обновления (обязательный)
	 *                        - createdAt: string - дата создания (обязательный)
	 *
	 * @return \KinopoiskDev\Models\BaseModel Новый экземпляр Studio с данными из массива
	 * @throws \KinopoiskDev\Exceptions\KinopoiskDevException
	 */
	public static function fromArray(array $data): static {
		return new self(
			id       : $data['id'],
			subType  : $data['subType'] ?? NULL,
			title    : $data['title'] ?? NULL,
			type     : DataManager::parseEnumValue($data, 'type', StudioType::class),
			movies   : $data['movies'] ?? [],
			updateAt : $data['updateAt'],
			createdAt: $data['createdAt'],
		);
	}

	/**
	 * Преобразует объект Studio в массив данных
	 *
	 * Метод для преобразования экземпляра класса Studio в ассоциативный массив
	 * данных. Используется для сериализации объекта в формат, совместимый с API,
	 * или для передачи данных в другие части системы.
	 *
	 * @see Studio::fromArray() Для создания объекта из массива
	 * @see \KinopoiskDev\Models\BaseModel::toArray() Для интерфейса BaseModel
	 *
	 * @param   bool  $includeNulls  Включать ли null значения в результат (по умолчанию true)
	 *
	 * @return array Ассоциативный массив с данными студии, содержащий ключи:
	 *               - id: string - уникальный идентификатор студии
	 *               - subType: string|null - подтип студии
	 *               - title: string|null - название студии
	 *               - type: StudioType|null - тип студии
	 *               - movies: array - массив связанных фильмов
	 *               - updateAt: string - дата последнего обновления
	 *               - createdAt: string - дата создания
	 */
	public function toArray(bool $includeNulls = TRUE): array {
		$data = [
			'id'        => $this->id,
			'subType'   => $this->subType,
			'title'     => $this->title,
			'type'      => $this->type,
			'movies'    => $this->movies,
			'updateAt'  => $this->updateAt,
			'createdAt' => $this->createdAt,
		];

		if (!$includeNulls) {
			$data = array_filter($data, fn($value) => $value !== NULL);
		}

		return $data;
	}

	/**
	 * Создает объект Studio из JSON строки
	 *
	 * @param string $json JSON строка с данными студии
	 * @return static
	 * @throws \JsonException
	 */
	public static function fromJson(string $json): static {
		$data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
		return static::fromArray($data);
	}

	/**
	 * Преобразует объект Studio в JSON строку
	 *
	 * @param int $flags Флаги для json_encode
	 * @return string
	 * @throws \JsonException
	 */
	public function toJson(int $flags = JSON_THROW_ON_ERROR|JSON_UNESCAPED_UNICODE): string {
		return json_encode($this->toArray(), $flags);
	}

	/**
	 * Валидирует данные студии
	 *
	 * @return bool
	 */
	public function validate(): bool {
		// Простая валидация: id, updateAt, createdAt не пустые
		return !empty($this->id) && !empty($this->updateAt) && !empty($this->createdAt);
	}

}