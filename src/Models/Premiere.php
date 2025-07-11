<?php

declare(strict_types=1);

namespace KinopoiskDev\Models;

/**
 * Класс для представления информации о премьерах фильма
 *
 * Содержит даты премьер фильма или сериала в различных странах и форматах,
 * включая мировую премьеру, премьеру в России, цифровой релиз, релиз на DVD и Blu-ray.
 * Используется для отображения информации о датах выхода произведения.
 *
 * @package KinopoiskDev\Models
 * @since   1.0.0
 * @author  Maxim Harder
 *
 * @version 1.0.0
 * @see     \KinopoiskDev\Models\Movie Для использования в информации о фильмах
 */
 class Premiere implements BaseModel {

	/**
	 * Конструктор для создания объекта информации о премьерах
	 *
	 * Создает новый экземпляр класса Premiere с указанными параметрами.
	 * Все параметры являются опциональными и могут быть null при отсутствии
	 * соответствующей информации в источнике данных.
	 *
	 * @see Premiere::fromArray() Для создания объекта из массива данных API
	 * @see Premiere::toArray() Для преобразования объекта в массив
	 *
	 * @param   string|null  $country  Страна премьеры
	 * @param   string|null  $world    Дата мировой премьеры в формате ISO
	 * @param   string|null  $russia   Дата премьеры в России в формате ISO
	 * @param   string|null  $digital  Дата цифрового релиза в формате ISO
	 * @param   string|null  $cinema   Дата премьеры в кинотеатрах в формате ISO
	 * @param   string|null  $bluray   Дата релиза на Blu-ray в формате ISO
	 * @param   string|null  $dvd      Дата релиза на DVD в формате ISO
	 */
	public function __construct(
		public ?string $country = NULL,
		public ?string $world = NULL,
		public ?string $russia = NULL,
		public ?string $digital = NULL,
		public ?string $cinema = NULL,
		public ?string $bluray = NULL,
		public ?string $dvd = NULL,
	) {}

	/**
	 * Создает объект Premiere из массива данных API
	 *
	 * Фабричный метод для создания экземпляра класса Premiere из массива данных,
	 * полученных от API Kinopoisk.dev. Безопасно обрабатывает отсутствующие
	 * значения, устанавливая их в null.
	 *
	 * @see Premiere::toArray() Для обратного преобразования в массив
	 *
	 * @param   array  $data  Массив данных о премьерах от API, содержащий ключи:
	 *                        - country: string|null - страна премьеры
	 *                        - world: string|null - дата мировой премьеры
	 *                        - russia: string|null - дата премьеры в России
	 *                        - digital: string|null - дата цифрового релиза
	 *                        - cinema: string|null - дата премьеры в кинотеатрах
	 *                        - bluray: string|null - дата релиза на Blu-ray
	 *                        - dvd: string|null - дата релиза на DVD
	 *
	 * @return \KinopoiskDev\Models\Premiere Новый экземпляр класса Premiere с данными из массива
	 */
	public static function fromArray(array $data): static {
		return new self(
			country: $data['country'] ?? NULL,
			world  : $data['world'] ?? NULL,
			russia : $data['russia'] ?? NULL,
			digital: $data['digital'] ?? NULL,
			cinema : $data['cinema'] ?? NULL,
			bluray : $data['bluray'] ?? NULL,
			dvd    : $data['dvd'] ?? NULL,
		);
	}

	/**
	 * Преобразует объект в массив данных
	 *
	 * Конвертирует текущий экземпляр класса Premiere в массив,
	 * совместимый с форматом API Kinopoisk.dev. Используется для сериализации
	 * данных при отправке запросов к API или для экспорта данных.
	 *
	 * @see Premiere::fromArray() Для создания объекта из массива
	 *
	 * @return array Массив с данными о премьерах, содержащий все поля объекта
	 */
	public function toArray(bool $includeNulls = true): array {
		return [
			'country' => $this->country,
			'world'   => $this->world,
			'russia'  => $this->russia,
			'digital' => $this->digital,
			'cinema'  => $this->cinema,
			'bluray'  => $this->bluray,
			'dvd'     => $this->dvd,
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
