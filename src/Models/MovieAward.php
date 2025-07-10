<?php

declare(strict_types=1);

namespace KinopoiskDev\Models;

use KinopoiskDev\Utils\DataManager;

/**
 * Класс для представления награды фильма
 *
 * Представляет информацию о награде, полученной фильмом или сериалом,
 * включая номинацию, статус победы и временные метки.
 * Используется для отображения наградной истории произведения.
 *
 * @package KinopoiskDev\Models
 * @since   1.0.0
 * @author  Maxim Harder
 * @version 1.0.0
 * @see     \KinopoiskDev\Models\Nomination Для информации о номинации
 * @see     \KinopoiskDev\Models\Movie Для основной модели фильма
 * @link    https://kinopoiskdev.readme.io/reference/moviecontroller_findmanyawardsv1_4
 */
readonly class MovieAward implements BaseModel {

	/**
	 * Конструктор для создания объекта награды фильма
	 *
	 * Создает новый экземпляр класса MovieAward с указанными параметрами.
	 * Большинство параметров являются опциональными и могут быть null при отсутствии
	 * соответствующей информации в источнике данных.
	 *
	 * @param   Nomination|null  $nomination  Информация о номинации
	 * @param   bool|null        $winning     Статус победы (true - победа, false - номинация)
	 * @param   string|null      $updatedAt   Дата последнего обновления записи
	 * @param   string|null      $createdAt   Дата создания записи
	 * @param   int|null         $movieId     ID фильма (может отсутствовать в некоторых контекстах)
	 */
	public function __construct(
		public ?Nomination $nomination = NULL,
		public ?bool       $winning = NULL,
		public ?string     $updatedAt = NULL,
		public ?string     $createdAt = NULL,
		public ?int        $movieId = NULL,
	) {}

	/**
	 * Возвращает строковое представление награды
	 *
	 * Формирует читаемое представление награды, включающее информацию
	 * о номинации и статусе победы.
	 *
	 * @return string Строковое представление награды
	 */
	public function __toString(): string {
		$parts = [];

		if ($this->nomination && $this->nomination->hasInfo()) {
			$parts[] = (string) $this->nomination;
		}

		$parts[] = "[{$this->getWinningStatus()}]";

		return implode(' ', $parts);
	}

	/**
	 * Проверяет, установлена ли информация о награде
	 *
	 * @return bool true если есть информация о номинации или статусе победы, иначе false
	 */
	public function hasInfo(): bool {
		return ($this->nomination !== NULL && $this->nomination->hasInfo()) || $this->winning !== NULL;
	}

	/**
	 * Возвращает статус награды в текстовом виде
	 *
	 * @return string Статус награды ("Победа", "Номинация", "Неизвестно")
	 */
	public function getWinningStatus(): string {
		if ($this->winning === TRUE) {
			return 'Победа';
		} elseif ($this->winning === FALSE) {
			return 'Номинация';
		}

		return 'Неизвестно';
	}

	/**
	 * Создает объект MovieAward из массива данных API
	 *
	 * Фабричный метод для создания экземпляра класса MovieAward из массива данных,
	 * полученных от API Kinopoisk.dev. Безопасно обрабатывает отсутствующие
	 * значения и преобразует вложенные объекты в соответствующие классы.
	 *
	 * @param   array  $data  Массив данных о награде фильма от API, содержащий ключи:
	 *                        - nomination: array|null - данные о номинации
	 *                        - winning: bool|null - статус победы
	 *                        - updatedAt: string|null - дата обновления
	 *                        - createdAt: string|null - дата создания
	 *                        - movieId: int|null - ID фильма
	 *
	 * @return \KinopoiskDev\Models\MovieAward Новый экземпляр класса MovieAward с данными из массива
	 * @throws \KinopoiskDev\Exceptions\KinopoiskDevException
	 */
	public static function fromArray(array $data): static {
		return new self(
			nomination: DataManager::parseObjectData($data, 'nomination', Nomination::class),
			winning   : $data['winning'] ?? NULL,
			updatedAt : $data['updatedAt'] ?? NULL,
			createdAt : $data['createdAt'] ?? NULL,
			movieId   : $data['movieId'] ?? NULL,
		);
	}

	/**
	 * Преобразует объект в массив данных
	 *
	 * Конвертирует текущий экземпляр класса MovieAward в массив,
	 * совместимый с форматом API Kinopoisk.dev. Используется для сериализации
	 * данных при отправке запросов к API или для экспорта данных.
	 *
	 * @return array Массив с данными о награде фильма
	 */
	public function toArray(bool $includeNulls = true): array {
		return [
			'nomination' => $this->nomination?->toArray(),
			'winning'    => $this->winning,
			'updatedAt'  => $this->updatedAt,
			'createdAt'  => $this->createdAt,
			'movieId'    => $this->movieId,
		];
	}

	/**
	 * Проверяет, является ли награда победной
	 *
	 * @return bool true если фильм победил в номинации, иначе false
	 */
	public function isWinning(): bool {
		return $this->winning === TRUE;
	}

	/**
	 * Проверяет, является ли запись только номинацией
	 *
	 * @return bool true если фильм был только номинирован, иначе false
	 */
	public function isNominationOnly(): bool {
		return $this->winning === FALSE;
	}


	/**
	 * Валидирует данные модели
	 *
	 * @return bool True если данные валидны
	 * @throws \KinopoiskDev\Exceptions\ValidationException При ошибке валидации
	 */
	public function validate(): bool {
		return true; // Basic validation - override in specific models if needed
	}

	/**
	 * Возвращает JSON представление объекта
	 *
	 * @param int $flags Флаги для json_encode
	 * @return string JSON строка
	 * @throws \JsonException При ошибке сериализации
	 */
	public function toJson(int $flags = JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE): string {
		return json_encode($this->toArray(), $flags);
	}

	/**
	 * Создает объект из JSON строки
	 *
	 * @param string $json JSON строка
	 * @return static Экземпляр модели
	 * @throws \JsonException При ошибке парсинга
	 * @throws \KinopoiskDev\Exceptions\ValidationException При некорректных данных
	 */
	public static function fromJson(string $json): static {
		$data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
		$instance = static::fromArray($data);
		$instance->validate();
		return $instance;
	}


}
