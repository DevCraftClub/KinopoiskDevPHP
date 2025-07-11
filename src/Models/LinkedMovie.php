<?php

declare(strict_types=1);

namespace KinopoiskDev\Models;

use KinopoiskDev\Enums\MovieType;

/**
 * Класс для представления связанного фильма
 *
 * Представляет упрощенную информацию о фильме, используемую в связанных
 * записях и ассоциациях. Содержит основные данные о фильме: идентификатор,
 * названия, тип, постер, рейтинг и год выпуска. Используется для отображения
 * связанных фильмов (похожие фильмы, сиквелы, приквелы и т.д.) без
 * необходимости загрузки полной информации.
 *
 * @package KinopoiskDev\Models
 * @since   1.0.0
 * @author  Maxim Harder
 *
 * @version 1.0.0
 * @see     \KinopoiskDev\Models\Movie Для полной информации о фильме
 * @see     \KinopoiskDev\Enums\MovieType Для типов фильмов
 * @see     \KinopoiskDev\Models\ShortImage Для изображений
 * @see     \KinopoiskDev\Models\Rating Для рейтингов
 */
 class LinkedMovie implements BaseModel {

	/**
	 * Конструктор для создания экземпляра связанного фильма
	 *
	 * Создает новый объект LinkedMovie с указанными параметрами.
	 * Все параметры, кроме идентификатора, являются опциональными и могут
	 * быть null при отсутствии соответствующих данных. Используется для
	 * инициализации объекта с данными о связанном фильме.
	 *
	 * @param   int              $id               Уникальный идентификатор фильма в базе данных
	 * @param   string|null      $name             Русское название фильма (null если не указано)
	 * @param   string|null      $enName           Английское название фильма (null если не указано)
	 * @param   string|null      $alternativeName  Альтернативное название фильма (null если не указано)
	 * @param   MovieType|null   $type             Тип фильма (фильм, сериал, мультфильм и т.д.) или null
	 * @param   ShortImage|null  $poster           Постер фильма или null если отсутствует
	 * @param   Rating|null      $rating           Рейтинги фильма или null если отсутствуют
	 * @param   int|null         $year             Год выпуска фильма или null если не указан
	 */
	public function __construct(
		public int         $id,
		public ?string     $name = NULL,
		public ?string     $enName = NULL,
		public ?string     $alternativeName = NULL,
		public ?MovieType  $type = NULL,
		public ?ShortImage $poster = NULL,
		public ?Rating     $rating = NULL,
		public ?int        $year = NULL,
	) {}

	/**
	 * Создает объект LinkedMovie из массива данных API
	 *
	 * Статический фабричный метод для создания экземпляра класса LinkedMovie
	 * из массива данных, полученных от API Kinopoisk.dev. Безопасно обрабатывает
	 * отсутствующие значения, устанавливая их в null. Автоматически конвертирует
	 * вложенные объекты (тип, постер, рейтинг) в соответствующие классы.
	 *
	 * @see ShortImage::fromArray() Для создания объекта постера
	 * @see Rating::fromArray() Для создания объекта рейтинга
	 * @see MovieType::tryFrom() Для создания enum типа фильма
	 *
	 * @param   array  $data  Массив данных о связанном фильме от API, содержащий ключи:
	 *                        - id: int - уникальный идентификатор
	 *                        - name: string|null - русское название
	 *                        - enName: string|null - английское название
	 *                        - alternativeName: string|null - альтернативное название
	 *                        - type: string|null - тип фильма
	 *                        - poster: array|null - данные о постере
	 *                        - rating: array|null - данные о рейтинге
	 *                        - year: int|null - год выпуска
	 *
	 * @return self Новый экземпляр класса LinkedMovie с данными из массива
	 */
	public static function fromArray(array $data): static {
		return new self(
			id             : $data['id'],
			name           : $data['name'] ?? NULL,
			enName         : $data['enName'] ?? NULL,
			alternativeName: $data['alternativeName'] ?? NULL,
			type           : isset($data['type']) ? MovieType::tryFrom($data['type']) : NULL,
			poster         : isset($data['poster']) ? ShortImage::fromArray($data['poster']) : NULL,
			rating         : isset($data['rating']) ? Rating::fromArray($data['rating']) : NULL,
			year           : $data['year'] ?? NULL,
		);
	}

	/**
	 * Преобразует объект LinkedMovie в массив данных
	 *
	 * Конвертирует текущий экземпляр класса LinkedMovie в массив,
	 * совместимый с форматом API Kinopoisk.dev. Автоматически обрабатывает
	 * вложенные объекты, преобразуя их в соответствующие массивы.
	 * Используется для сериализации данных при отправке запросов к API
	 * или для экспорта данных в JSON.
	 *
	 * @see ShortImage::toArray() Для преобразования постера в массив
	 * @see Rating::toArray() Для преобразования рейтинга в массив
	 * @return array Массив с данными о связанном фильме, содержащий ключи:
	 *               - id: int - уникальный идентификатор
	 *               - name: string|null - русское название
	 *               - enName: string|null - английское название
	 *               - alternativeName: string|null - альтернативное название
	 *               - type: string|null - значение типа фильма
	 *               - poster: array|null - данные о постере
	 *               - rating: array|null - данные о рейтинге
	 *               - year: int|null - год выпуска
	 */
	public function toArray(bool $includeNulls = true): array {
		return [
			'id'              => $this->id,
			'name'            => $this->name,
			'enName'          => $this->enName,
			'alternativeName' => $this->alternativeName,
			'type'            => $this->type?->value,
			'poster'          => $this->poster?->toArray(),
			'rating'          => $this->rating?->toArray(),
			'year'            => $this->year,
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
