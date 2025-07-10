<?php

declare(strict_types=1);

namespace KinopoiskDev\Models;

use KinopoiskDev\Utils\DataManager;

/**
 * Класс для представления номинации
 *
 * Представляет информацию о номинации на награду, включая
 * детали о самой награде и название номинации. Используется
 * в составе наград для фильмов и персон.
 *
 * @package KinopoiskDev\Models
 * @since   1.0.0
 * @author  Maxim Harder
 * @version 1.0.0
 * @see     \KinopoiskDev\Models\NominationAward Для информации о награде
 * @see     \KinopoiskDev\Models\MovieAward Для наград фильмов
 * @see     \KinopoiskDev\Models\PersonAward Для наград персон
 */
readonly class Nomination implements BaseModel {

	/**
	 * Конструктор для создания объекта номинации
	 *
	 * Создает новый экземпляр класса Nomination с указанными параметрами.
	 * Оба параметра являются опциональными и могут быть null при отсутствии
	 * соответствующей информации в источнике данных.
	 *
	 * @param   NominationAward|null  $award  Информация о награде
	 * @param   string|null           $title  Название номинации (например, "Лучший фильм", "Лучший актер")
	 */
	public function __construct(
		public ?NominationAward $award = NULL,
		public ?string          $title = NULL,
	) {}

	/**
	 * Возвращает строковое представление номинации
	 *
	 * Формирует читаемое представление номинации, включающее название
	 * номинации и информацию о награде, если они доступны.
	 *
	 * @return string Строковое представление номинации
	 */
	public function __toString(): string {
		$parts = [];

		if ($this->title) {
			$parts[] = $this->title;
		}

		if ($this->award && $this->award->hasInfo()) {
			$parts[] = "- {$this->award}";
		}

		return empty($parts) ? 'Неизвестная номинация' : implode(' ', $parts);
	}

	/**
	 * Создает объект Nomination из массива данных API
	 *
	 * Фабричный метод для создания экземпляра класса Nomination из массива данных,
	 * полученных от API Kinopoisk.dev. Безопасно обрабатывает отсутствующие
	 * значения и преобразует вложенные объекты в соответствующие классы.
	 *
	 * @param   array  $data  Массив данных о номинации от API, содержащий ключи:
	 *                        - award: array|null - данные о награде
	 *                        - title: string|null - название номинации
	 *
	 * @return \KinopoiskDev\Models\Nomination Новый экземпляр класса Nomination с данными из массива
	 * @throws \KinopoiskDev\Exceptions\KinopoiskDevException
	 */
	public static function fromArray(array $data): static {
		return new self(
			award: DataManager::parseObjectData($data, 'award', NominationAward::class),
			title: $data['title'] ?? NULL,
		);
	}

	/**
	 * Преобразует объект в массив данных
	 *
	 * Конвертирует текущий экземпляр класса Nomination в массив,
	 * совместимый с форматом API Kinopoisk.dev. Используется для сериализации
	 * данных при отправке запросов к API или для экспорта данных.
	 *
	 * @return array Массив с данными о номинации, содержащий ключи:
	 *               - award: array|null - данные о награде
	 *               - title: string|null - название номинации
	 */
	public function toArray(bool $includeNulls = true): array {
		return [
			'award' => $this->award?->toArray(),
			'title' => $this->title,
		];
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
