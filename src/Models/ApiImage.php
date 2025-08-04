<?php

declare(strict_types=1);

namespace KinopoiskDev\Models;

use Lombok\Getter;

/**
 * Класс для представления изображений из API Kinopoisk.dev
 *
 * Расширенная модель изображения, которая включает дополнительные поля,
 * возвращаемые API: movieId, type, id, createdAt, updatedAt.
 *
 * @package KinopoiskDev\Models
 * @since   1.0.0
 * @author  Maxim Harder
 *
 * @version 1.0.0
 * @see     \KinopoiskDev\Models\Image Базовая модель изображения
 */
class ApiImage extends AbstractBaseModel {

	/**
	 * Конструктор модели API изображения
	 *
	 * @param   int|null     $movieId     ID фильма
	 * @param   string|null  $type        Тип изображения
	 * @param   string|null  $url         URL полноразмерного изображения
	 * @param   string|null  $previewUrl  URL превью изображения
	 * @param   int|null     $height      Высота изображения в пикселях
	 * @param   int|null     $width       Ширина изображения в пикселях
	 * @param   string|null  $createdAt   Дата создания
	 * @param   string|null  $updatedAt   Дата обновления
	 * @param   string|null  $id          Уникальный идентификатор
	 */
	public function __construct(
		#[Getter] public ?int    $movieId = NULL,
		#[Getter] public ?string $type = NULL,
		#[Getter] public ?string $url = NULL,
		#[Getter] public ?string $previewUrl = NULL,
		#[Getter] public ?int    $height = NULL,
		#[Getter] public ?int    $width = NULL,
		#[Getter] public ?string $createdAt = NULL,
		#[Getter] public ?string $updatedAt = NULL,
		#[Getter] public ?string $id = NULL,
	) {}

	/**
	 * Строковое представление изображения
	 *
	 * @return string Строковое описание изображения
	 */
	public function __toString(): string {
		if (!$this->exists()) {
			return 'Изображение недоступно';
		}

		$parts = [];
		if ($this->type) {
			$parts[] = "Тип: {$this->type}";
		}
		if ($this->getFormattedDimensions()) {
			$parts[] = $this->getFormattedDimensions();
		}

		return empty($parts) ? 'Изображение доступно' : implode(' - ', $parts);
	}

	/**
	 * Проверяет, доступно ли изображение
	 *
	 * @return bool true если изображение доступно
	 */
	public function exists(): bool {
		return $this->url !== NULL || $this->previewUrl !== NULL;
	}

	/**
	 * Возвращает размеры изображения в виде строки
	 *
	 * @return string|null Строка размеров в формате "1920x1080"
	 */
	public function getFormattedDimensions(): ?string {
		if ($this->width === NULL || $this->height === NULL) {
			return NULL;
		}

		return "{$this->width}x{$this->height}";
	}

	/**
	 * Создает объект ApiImage из массива данных API
	 *
	 * @param   array<string, mixed>  $data  Массив данных изображения от API
	 *
	 * @return static Новый экземпляр класса ApiImage с данными из массива
	 */
	public static function fromArray(array $data): static {
		return new self(
			movieId   : isset($data['movieId']) ? (int) $data['movieId'] : NULL,
			type      : $data['type'] ?? NULL,
			url       : $data['url'] ?? NULL,
			previewUrl: $data['previewUrl'] ?? NULL,
			height    : isset($data['height']) ? (int) $data['height'] : NULL,
			width     : isset($data['width']) ? (int) $data['width'] : NULL,
			createdAt : $data['createdAt'] ?? NULL,
			updatedAt : $data['updatedAt'] ?? NULL,
			id        : $data['id'] ?? NULL,
		);
	}

	/**
	 * Преобразует объект в массив
	 *
	 * @param   bool  $includeNulls  Включать ли null значения
	 *
	 * @return array<string, mixed> Массив с данными изображения
	 */
	public function toArray(bool $includeNulls = TRUE): array {
		$data = [
			'movieId'    => $this->movieId,
			'type'       => $this->type,
			'url'        => $this->url,
			'previewUrl' => $this->previewUrl,
			'height'     => $this->height,
			'width'      => $this->width,
			'createdAt'  => $this->createdAt,
			'updatedAt'  => $this->updatedAt,
			'id'         => $this->id,
		];

		if (!$includeNulls) {
			$data = array_filter($data, fn ($value) => $value !== NULL);
		}

		return $data;
	}

	/**
	 * Возвращает лучший доступный URL изображения
	 *
	 * @return string|null URL наилучшего доступного изображения
	 */
	public function getBestUrl(): ?string {
		return $this->url ?? $this->previewUrl;
	}

	/**
	 * Валидация данных
	 *
	 * @return bool true если данные валидны
	 */
	public function validate(): bool {
		return $this->exists();
	}

} 