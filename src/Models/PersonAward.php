<?php

declare(strict_types=1);

namespace KinopoiskDev\Models;

use KinopoiskDev\Utils\DataManager;

/**
 * Класс для представления награды персоны
 *
 * Представляет информацию о награде, полученной персоной (актером, режиссером и т.д.),
 * включая номинацию, статус победы, связанный фильм и временные метки.
 * Используется для отображения наградной истории персоны.
 *
 * @package KinopoiskDev\Models
 * @since   1.0.0
 * @author  Maxim Harder
 * @version 1.0.0
 * @see     \KinopoiskDev\Models\Nomination Для информации о номинации
 * @see     \KinopoiskDev\Models\Movie Для связанного фильма
 * @see     \KinopoiskDev\Models\Person Для основной модели персоны
 * @link    https://kinopoiskdev.readme.io/reference/personcontroller_findmanyawardsv1_4
 */
readonly class PersonAward implements BaseModel {

	/**
	 * Конструктор для создания объекта награды персоны
	 *
	 * Создает новый экземпляр класса PersonAward с указанными параметрами.
	 * Только personId является обязательным, остальные параметры опциональны.
	 *
	 * @param   int              $personId    ID персоны (обязательный параметр)
	 * @param   Nomination|null  $nomination  Информация о номинации
	 * @param   bool|null        $winning     Статус победы (true - победа, false - номинация)
	 * @param   string|null      $updatedAt   Дата последнего обновления записи
	 * @param   string|null      $createdAt   Дата создания записи
	 * @param   Movie|null       $movie       Связанный фильм за который получена награда
	 */
	public function __construct(
		public int         $personId,
		public ?Nomination $nomination = NULL,
		public ?bool       $winning = NULL,
		public ?string     $updatedAt = NULL,
		public ?string     $createdAt = NULL,
		public ?Movie      $movie = NULL,
	) {}

	/**
	 * Возвращает строковое представление награды
	 *
	 * Формирует читаемое представление награды, включающее информацию
	 * о номинации, статусе победы и связанном фильме.
	 *
	 * @return string Строковое представление награды
	 */
	public function __toString(): string {
		$parts = [];

		if ($this->nomination && $this->nomination->hasInfo()) {
			$parts[] = (string) $this->nomination;
		}

		$parts[] = "[{$this->getWinningStatus()}]";

		if ($this->movie) {
			$movieTitle = $this->getMovieTitle();
			if ($movieTitle) {
				$parts[] = "за \"{$movieTitle}\"";
			}
		}

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
	 * Возвращает название связанного фильма
	 *
	 * @return string|null Название фильма за который получена награда или null
	 */
	public function getMovieTitle(): ?string {
		return $this->movie?->getBestName();
	}

	/**
	 * Создает объект PersonAward из массива данных API
	 *
	 * Фабричный метод для создания экземпляра класса PersonAward из массива данных,
	 * полученных от API Kinopoisk.dev. Безопасно обрабатывает отсутствующие
	 * значения и преобразует вложенные объекты в соответствующие классы.
	 *
	 * @param   array  $data  Массив данных о награде персоны от API, содержащий ключи:
	 *                        - personId: int - ID персоны (обязательно)
	 *                        - nomination: array|null - данные о номинации
	 *                        - winning: bool|null - статус победы
	 *                        - updatedAt: string|null - дата обновления
	 *                        - createdAt: string|null - дата создания
	 *                        - movie: array|null - данные о связанном фильме
	 *
	 * @return \KinopoiskDev\Models\PersonAward Новый экземпляр класса PersonAward с данными из массива
	 * @throws \KinopoiskDev\Exceptions\KinopoiskDevException
	 */
	public static function fromArray(array $data): self {
		return new self(
			personId  : $data['personId'],
			nomination: DataManager::parseObjectData($data, 'nomination', Nomination::class),
			winning   : $data['winning'] ?? NULL,
			updatedAt : $data['updatedAt'] ?? NULL,
			createdAt : $data['createdAt'] ?? NULL,
			movie     : DataManager::parseObjectData($data, 'movie', Movie::class),
		);
	}

	/**
	 * Преобразует объект в массив данных
	 *
	 * Конвертирует текущий экземпляр класса PersonAward в массив,
	 * совместимый с форматом API Kinopoisk.dev. Используется для сериализации
	 * данных при отправке запросов к API или для экспорта данных.
	 *
	 * @return array Массив с данными о награде персоны
	 */
	public function toArray(bool $includeNulls = true): array {
		return [
			'personId'   => $this->personId,
			'nomination' => $this->nomination?->toArray(),
			'winning'    => $this->winning,
			'updatedAt'  => $this->updatedAt,
			'createdAt'  => $this->createdAt,
			'movie'      => $this->movie?->toArray(),
		];
	}

	/**
	 * Проверяет, является ли награда победной
	 *
	 * @return bool true если персона победила в номинации, иначе false
	 */
	public function isWinning(): bool {
		return $this->winning === TRUE;
	}

	/**
	 * Проверяет, является ли запись только номинацией
	 *
	 * @return bool true если персона была только номинирована, иначе false
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
