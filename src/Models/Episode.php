<?php

declare(strict_types=1);

namespace KinopoiskDev\Models;

use KinopoiskDev\Utils\DataManager;

/**
 * Класс для представления эпизода сериала (версия API 1.4)
 *
 * Представляет информацию об отдельном эпизоде сериала согласно схеме Episode,
 * включая номер, название, описание, дату выхода и кадр из эпизода.
 * Используется в составе сезонов для детальной информации о структуре сериалов.
 *
 * @package KinopoiskDev\Models
 * @since   1.0.0
 * @author  Maxim Harder
 * @version 1.0.0
 * @see     \KinopoiskDev\Models\Season Для информации о сезонах
 * @see     \KinopoiskDev\Models\ShortImage Для кадров из эпизодов
 */
class Episode {

	/**
	 * Конструктор для создания объекта эпизода
	 *
	 * Создает новый экземпляр класса Episode с указанными параметрами.
	 * Большинство параметров являются опциональными и могут быть null при отсутствии
	 * соответствующей информации в источнике данных.
	 *
	 * @param   int|null         $number         Номер эпизода
	 * @param   string|null      $name           Название эпизода на русском языке
	 * @param   string|null      $enName         Название эпизода на английском языке
	 * @param   string|null      $date           Дата выхода эпизода (deprecated)
	 * @param   string|null      $description    Описание эпизода на русском языке
	 * @param   ShortImage|null  $still          Кадр из эпизода
	 * @param   string|null      $airDate        Дата выхода эпизода
	 * @param   string|null      $enDescription  Описание эпизода на английском языке
	 */
	public function __construct(
		public readonly ?int        $number = null,
		public readonly ?string     $name = null,
		public readonly ?string     $enName = null,
		public readonly ?string     $date = null, // deprecated
		public readonly ?string     $description = null,
		public readonly ?ShortImage $still = null,
		public readonly ?string     $airDate = null,
		public readonly ?string     $enDescription = null,
	) {}

	/**
	 * Создает объект Episode из массива данных API
	 *
	 * Фабричный метод для создания экземпляра класса Episode из массива данных,
	 * полученных от API Kinopoisk.dev. Безопасно обрабатывает отсутствующие
	 * значения и преобразует вложенные объекты в соответствующие классы.
	 *
	 * @param   array  $data  Массив данных об эпизоде от API
	 *
	 * @return \KinopoiskDev\Models\Episode Новый экземпляр класса Episode с данными из массива
	 * @throws \KinopoiskDev\Exceptions\KinopoiskDevException
	 */
	public static function fromArray(array $data): self {
		return new self(
			number        : $data['number'] ?? null,
			name          : $data['name'] ?? null,
			enName        : $data['enName'] ?? null,
			date          : $data['date'] ?? null,
			description   : $data['description'] ?? null,
			still         : DataManager::parseObjectData($data, 'still', ShortImage::class),
			airDate       : $data['airDate'] ?? null,
			enDescription : $data['enDescription'] ?? null,
		);
	}

	/**
	 * Преобразует объект в массив данных
	 *
	 * Конвертирует текущий экземпляр класса Episode в массив,
	 * совместимый с форматом API Kinopoisk.dev.
	 *
	 * @return array Массив с данными об эпизоде
	 */
	public function toArray(): array {
		return [
			'number'        => $this->number,
			'name'          => $this->name,
			'enName'        => $this->enName,
			'date'          => $this->date,
			'description'   => $this->description,
			'still'         => $this->still?->toArray(),
			'airDate'       => $this->airDate,
			'enDescription' => $this->enDescription,
		];
	}

	/**
	 * Возвращает наилучшее доступное название эпизода
	 *
	 * @return string|null Название эпизода или null если не задано
	 */
	public function getBestName(): ?string {
		return $this->name ?? $this->enName;
	}

	/**
	 * Возвращает наилучшее доступное описание эпизода
	 *
	 * @return string|null Описание эпизода или null если не задано
	 */
	public function getBestDescription(): ?string {
		return $this->description ?? $this->enDescription;
	}

	/**
	 * Возвращает актуальную дату выхода эпизода
	 *
	 * Приоритет отдается airDate над deprecated полем date
	 *
	 * @return string|null Дата выхода эпизода
	 */
	public function getActualAirDate(): ?string {
		return $this->airDate ?? $this->date;
	}

	/**
	 * Проверяет наличие кадра из эпизода
	 *
	 * @return bool true если кадр доступен, иначе false
	 */
	public function hasStill(): bool {
		return $this->still !== null && ($this->still->url !== null || $this->still->previewUrl !== null);
	}
}