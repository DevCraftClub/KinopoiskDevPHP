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
 * @version 1.0.0
 * @see     \KinopoiskDev\Models\Movie Для использования в информации о фильмах
 */
class Premiere {

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
		public readonly ?string $country = null,
		public readonly ?string $world = null,
		public readonly ?string $russia = null,
		public readonly ?string $digital = null,
		public readonly ?string $cinema = null,
		public readonly ?string $bluray = null,
		public readonly ?string $dvd = null,
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
	public static function fromArray(array $data): self {
		return new self(
			country: $data['country'] ?? null,
			world: $data['world'] ?? null,
			russia: $data['russia'] ?? null,
			digital: $data['digital'] ?? null,
			cinema: $data['cinema'] ?? null,
			bluray: $data['bluray'] ?? null,
			dvd: $data['dvd'] ?? null,
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
	public function toArray(): array {
		return [
			'country' => $this->country,
			'world' => $this->world,
			'russia' => $this->russia,
			'digital' => $this->digital,
			'cinema' => $this->cinema,
			'bluray' => $this->bluray,
			'dvd' => $this->dvd,
		];
	}
}
