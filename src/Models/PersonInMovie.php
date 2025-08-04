<?php

declare(strict_types=1);

namespace KinopoiskDev\Models;

/**
 * Класс для представления персоны в контексте фильма
 *
 * Представляет информацию о персоне (актере, режиссере и т.д.) в контексте
 * конкретного фильма или сериала. Содержит основные данные о персоне,
 * включая идентификатор, имя, фото и профессиональную информацию.
 *
 * @package KinopoiskDev\Models
 * @since   1.0.0
 * @author  Maxim Harder
 *
 * @version 1.0.0
 * @see     \KinopoiskDev\Models\Person Для полной информации о персоне
 * @see     \KinopoiskDev\Models\Movie Для информации о фильме
 */
class PersonInMovie extends AbstractBaseModel {

	/**
	 * Конструктор для создания объекта персоны в фильме
	 *
	 * Создает новый экземпляр класса PersonInMovie с указанными параметрами.
	 * Только идентификатор является обязательным параметром, остальные могут
	 * быть null при отсутствии соответствующей информации.
	 *
	 * @see PersonInMovie::fromArray() Для создания объекта из массива данных API
	 * @see PersonInMovie::toArray() Для преобразования объекта в массив
	 *
	 * @param   int          $id            Уникальный идентификатор персоны
	 * @param   string|null  $photo         URL фотографии персоны
	 * @param   string|null  $name          Имя персоны на русском языке
	 * @param   string|null  $enName        Имя персоны на английском языке
	 * @param   string|null  $description   Описание роли персоны в фильме
	 * @param   string|null  $profession    Профессия персоны на русском языке
	 * @param   string|null  $enProfession  Профессия персоны на английском языке
	 */
	public function __construct(
		public int     $id,
		public ?string $photo = NULL,
		public ?string $name = NULL,
		public ?string $enName = NULL,
		public ?string $description = NULL,
		public ?string $profession = NULL,
		public ?string $enProfession = NULL,
	) {}

	/**
	 * Создает объект PersonInMovie из массива данных API
	 *
	 * Фабричный метод для создания экземпляра класса PersonInMovie из массива данных,
	 * полученных от API Kinopoisk.dev. Безопасно обрабатывает отсутствующие
	 * значения, устанавливая их в null.
	 *
	 * @see PersonInMovie::toArray() Для обратного преобразования в массив
	 *
	 * @param   array  $data  Массив данных о персоне от API, содержащий ключи:
	 *                        - id: int - уникальный идентификатор персоны
	 *                        - photo: string|null - URL фотографии персоны
	 *                        - name: string|null - имя персоны на русском языке
	 *                        - enName: string|null - имя персоны на английском языке
	 *                        - description: string|null - описание роли персоны
	 *                        - profession: string|null - профессия персоны на русском
	 *                        - enProfession: string|null - профессия персоны на английском
	 *
	 * @return \KinopoiskDev\Models\PersonInMovie Новый экземпляр класса PersonInMovie с данными из массива
	 */
	public static function fromArray(array $data): static {
		return new self(
			id          : $data['id'],
			photo       : $data['photo'] ?? NULL,
			name        : $data['name'] ?? NULL,
			enName      : $data['enName'] ?? NULL,
			description : $data['description'] ?? NULL,
			profession  : $data['profession'] ?? NULL,
			enProfession: $data['enProfession'] ?? NULL,
		);
	}

	/**
	 * Преобразует объект в массив данных
	 *
	 * Конвертирует текущий экземпляр класса PersonInMovie в массив,
	 * совместимый с форматом API Kinopoisk.dev. Используется для сериализации
	 * данных при отправке запросов к API или для экспорта данных.
	 *
	 * @see PersonInMovie::fromArray() Для создания объекта из массива
	 *
	 * @return array Массив с данными о персоне в фильме, содержащий все поля объекта
	 */
	public function toArray(bool $includeNulls = TRUE): array {
		return [
			'id'           => $this->id,
			'photo'        => $this->photo,
			'name'         => $this->name,
			'enName'       => $this->enName,
			'description'  => $this->description,
			'profession'   => $this->profession,
			'enProfession' => $this->enProfession,
		];
	}

	/**
	 * Валидирует данные модели
	 *
	 * @return bool True если данные валидны
	 */
	public function validate(): bool {
		return TRUE; // Basic validation - override in specific models if needed
	}

}
