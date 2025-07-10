<?php

declare(strict_types=1);

namespace KinopoiskDev\Models;

use KinopoiskDev\Utils\DataManager;

/**
 * Класс для представления сезона сериала (версия API 1.4)
 *
 * Представляет информацию о сезоне сериала согласно схеме Season,
 * включая номер сезона, количество эпизодов, постер, название, описание
 * и массив эпизодов. Используется для детальной информации о структуре сериалов.
 *
 * @package KinopoiskDev\Models
 * @since   1.0.0
 * @author  Maxim Harder
 * @version 1.0.0
 * @see     \KinopoiskDev\Models\EpisodeV1_4 Для информации об отдельных эпизодах
 * @see     \KinopoiskDev\Models\Movie Для основной модели фильма/сериала
 * @link    https://kinopoiskdev.readme.io/reference/seasoncontroller_findmanyv1_4
 */
readonly class Season implements BaseModel {

	/**
	 * Конструктор для создания объекта сезона
	 *
	 * Создает новый экземпляр класса Season с полным набором данных о сезоне.
	 * Большинство параметров являются опциональными и могут быть null при отсутствии
	 * соответствующей информации в источнике данных.
	 *
	 * @param   int                  $movieId        ID фильма/сериала к которому относится сезон
	 * @param   int|null             $number         Номер сезона
	 * @param   int|null             $episodesCount  Количество эпизодов в сезоне
	 * @param   Episode[]        $episodes       Массив эпизодов сезона
	 * @param   ShortImage|null      $poster         Постер сезона
	 * @param   string|null          $name           Название сезона на русском языке
	 * @param   string|null          $enName         Название сезона на английском языке
	 * @param   int|null             $duration       Длительность сезона в минутах
	 * @param   string|null          $description    Описание сезона на русском языке
	 * @param   string|null          $enDescription  Описание сезона на английском языке
	 * @param   string|null          $airDate        Дата выхода сезона
	 * @param   string|null          $updatedAt      Дата последнего обновления записи
	 * @param   string|null          $createdAt      Дата создания записи
	 */
	public function __construct(
		public int          $movieId,
		public ?int         $number = null,
		public ?int         $episodesCount = null,
		public array        $episodes = [],
		public ?ShortImage  $poster = null,
		public ?string      $name = null,
		public ?string      $enName = null,
		public ?int         $duration = null,
		public ?string      $description = null,
		public ?string      $enDescription = null,
		public ?string      $airDate = null,
		public ?string      $updatedAt = null,
		public ?string      $createdAt = null,
	) {}

	/**
	 * Создает объект Season из массива данных API
	 *
	 * Фабричный метод для создания экземпляра класса Season из массива данных,
	 * полученных от API Kinopoisk.dev. Безопасно обрабатывает отсутствующие
	 * значения и преобразует вложенные объекты в соответствующие классы.
	 *
	 * @param   array  $data  Массив данных о сезоне от API
	 *
	 * @return \KinopoiskDev\Models\Season Новый экземпляр класса Season с данными из массива
	 * @throws \KinopoiskDev\Exceptions\KinopoiskDevException
	 */
	public static function fromArray(array $data): static {
		return new self(
			movieId       : $data['movieId'],
			number        : $data['number'] ?? null,
			episodesCount : $data['episodesCount'] ?? null,
			episodes      : DataManager::parseObjectArray($data, 'episodes', EpisodeV1_4::class),
			poster        : DataManager::parseObjectData($data, 'poster', ShortImage::class),
			name          : $data['name'] ?? null,
			enName        : $data['enName'] ?? null,
			duration      : $data['duration'] ?? null,
			description   : $data['description'] ?? null,
			enDescription : $data['enDescription'] ?? null,
			airDate       : $data['airDate'] ?? null,
			updatedAt     : $data['updatedAt'] ?? null,
			createdAt     : $data['createdAt'] ?? null,
		);
	}

	/**
	 * Преобразует объект в массив данных
	 *
	 * Конвертирует текущий экземпляр класса Season в массив,
	 * совместимый с форматом API Kinopoisk.dev.
	 *
	 * @return array Массив с полными данными о сезоне
	 */
	public function toArray(bool $includeNulls = true): array {
		return [
			'movieId'       => $this->movieId,
			'number'        => $this->number,
			'episodesCount' => $this->episodesCount,
			'episodes'      => DataManager::getObjectsArray($this->episodes),
			'poster'        => $this->poster?->toArray(),
			'name'          => $this->name,
			'enName'        => $this->enName,
			'duration'      => $this->duration,
			'description'   => $this->description,
			'enDescription' => $this->enDescription,
			'airDate'       => $this->airDate,
			'updatedAt'     => $this->updatedAt,
			'createdAt'     => $this->createdAt,
		];
	}

	/**
	 * Возвращает наилучшее доступное название сезона
	 *
	 * @return string|null Название сезона или null если не задано
	 */
	public function getBestName(): ?string {
		return $this->name ?? $this->enName;
	}

	/**
	 * Возвращает наилучшее доступное описание сезона
	 *
	 * @return string|null Описание сезона или null если не задано
	 */
	public function getBestDescription(): ?string {
		return $this->description ?? $this->enDescription;
	}

	/**
	 * Проверяет, есть ли эпизоды в сезоне
	 *
	 * @return bool true если есть эпизоды, иначе false
	 */
	public function hasEpisodes(): bool {
		return !empty($this->episodes);
	}

	/**
	 * Возвращает количество доступных эпизодов
	 *
	 * @return int Количество эпизодов в массиве episodes
	 */
	public function getAvailableEpisodesCount(): int {
		return count($this->episodes);
	}
}