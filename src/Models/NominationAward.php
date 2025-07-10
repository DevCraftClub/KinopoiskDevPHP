<?php

declare(strict_types=1);

namespace KinopoiskDev\Models;

/**
 * Класс для представления награды в номинации
 *
 * Представляет информацию о конкретной награде в рамках номинации,
 * включая название награды и год ее вручения. Используется как часть
 * более крупной структуры номинаций для фильмов и персон.
 *
 * @package KinopoiskDev\Models
 * @since   1.0.0
 * @author  Maxim Harder
 * @version 1.0.0
 * @see     \KinopoiskDev\Models\Nomination Для полной информации о номинации
 * @see     \KinopoiskDev\Models\MovieAward Для наград фильмов
 * @see     \KinopoiskDev\Models\PersonAward Для наград персон
 */
readonly class NominationAward implements BaseModel {

	/**
	 * Конструктор для создания объекта награды номинации
	 *
	 * Создает новый экземпляр класса NominationAward с указанными параметрами.
	 * Оба параметра являются опциональными и могут быть null при отсутствии
	 * соответствующей информации в источнике данных.
	 *
	 * @param   string|null  $title  Название награды (например, "Оскар", "Золотой глобус")
	 * @param   int|null     $year   Год вручения награды
	 */
	public function __construct(
		public ?string $title = null,
		public ?int    $year = null,
	) {}

	/**
	 * Создает объект NominationAward из массива данных API
	 *
	 * Фабричный метод для создания экземпляра класса NominationAward из массива данных,
	 * полученных от API Kinopoisk.dev. Безопасно обрабатывает отсутствующие
	 * значения, устанавливая их в null.
	 *
	 * @param   array  $data  Массив данных о награде от API, содержащий ключи:
	 *                        - title: string|null - название награды
	 *                        - year: int|null - год вручения награды
	 *
	 * @return \KinopoiskDev\Models\NominationAward Новый экземпляр класса NominationAward с данными из массива
	 */
	public static function fromArray(array $data): static {
		return new self(
			title: $data['title'] ?? null,
			year: $data['year'] ?? null,
		);
	}

	/**
	 * Преобразует объект в массив данных
	 *
	 * Конвертирует текущий экземпляр класса NominationAward в массив,
	 * совместимый с форматом API Kinopoisk.dev. Используется для сериализации
	 * данных при отправке запросов к API или для экспорта данных.
	 *
	 * @return array Массив с данными о награде, содержащий ключи:
	 *               - title: string|null - название награды
	 *               - year: int|null - год вручения награды
	 */
	public function toArray(bool $includeNulls = true): array {
		return [
			'title' => $this->title,
			'year'  => $this->year,
		];
	}

	/**
	 * Возвращает строковое представление награды
	 *
	 * Формирует читаемое представление награды, включающее название
	 * и год вручения, если они доступны.
	 *
	 * @return string Строковое представление награды
	 */
	public function __toString(): string {
		$parts = [];

		if ($this->title) {
			$parts[] = $this->title;
		}

		if ($this->year) {
			$parts[] = "({$this->year})";
		}

		return empty($parts) ? 'Неизвестная награда' : implode(' ', $parts);
	}

	/**
	 * Проверяет, установлена ли информация о награде
	 *
	 * @return bool true если есть название или год, иначе false
	 */
	public function hasInfo(): bool {
		return $this->title !== null || $this->year !== null;
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
