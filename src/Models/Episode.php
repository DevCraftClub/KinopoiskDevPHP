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
 class Episode implements BaseModel {

	/**
	 * Конструктор модели эпизода
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
		public ?int        $number = NULL,
		public ?string     $name = NULL,
		public ?string     $enName = NULL,
		public ?string     $date = NULL, // deprecated
		public ?string     $description = NULL,
		public ?ShortImage $still = NULL,
		public ?string     $airDate = NULL,
		public ?string     $enDescription = NULL,
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
	public static function fromArray(array $data): static {
		return new self(
			number       : $data['number'] ?? NULL,
			name         : $data['name'] ?? NULL,
			enName       : $data['enName'] ?? NULL,
			date         : $data['date'] ?? NULL,
			description  : $data['description'] ?? NULL,
			still        : DataManager::parseObjectData($data, 'still', ShortImage::class),
			airDate      : $data['airDate'] ?? NULL,
			enDescription: $data['enDescription'] ?? NULL,
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
	public function toArray(bool $includeNulls = true): array {
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
		return $this->still !== NULL && ($this->still->url !== NULL || $this->still->previewUrl !== NULL);
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
