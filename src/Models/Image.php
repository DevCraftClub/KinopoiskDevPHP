<?php

declare(strict_types=1);

namespace KinopoiskDev\Models;

use Lombok\Getter;

/**
 * Класс для представления изображений фильмов
 *
 * Представляет изображение фильма, включая постеры, фоны, логотипы и другие
 * визуальные элементы. Содержит URL-адреса изображений в полном размере
 * и их уменьшенные версии для предварительного просмотра, а также
 * информацию о размерах и разрешении. Предоставляет методы для анализа
 * соотношения сторон и категории качества изображения.
 *
 * @package KinopoiskDev\Models
 * @since   1.0.0
 * @author  Maxim Harder
 *
 * @version 1.0.0
 * @see     \KinopoiskDev\Models\ShortImage Для упрощенной модели изображений
 * @see     \KinopoiskDev\Models\Logo Для логотипов
 */
 class Image implements BaseModel {

	/**
	 * Конструктор для создания объекта изображения
	 *
	 * Создает новый экземпляр класса Image с указанными параметрами.
	 * Все параметры являются опциональными и позволяют создавать
	 * объекты с частичной информацией об изображении.
	 *
	 * @param   string|null  $url         URL полноразмерного изображения (null если недоступно)
	 * @param   string|null  $previewUrl  URL превью изображения (null если недоступно)
	 * @param   int|null     $height      Высота изображения в пикселях (null если неизвестна)
	 * @param   int|null     $width       Ширина изображения в пикселях (null если неизвестна)
	 */
	public function __construct(
		#[Getter] public ?string $url = NULL,
		#[Getter] public ?string $previewUrl = NULL,
		#[Getter] public ?int    $height = NULL,
		#[Getter] public ?int    $width = NULL,
	) {}

	/**
	 * Строковое представление изображения
	 *
	 * Магический метод для получения строкового представления объекта.
	 * Возвращает описательную информацию об изображении, включая размеры
	 * и категорию разрешения. Если изображение недоступно, возвращает
	 * соответствующее сообщение.
	 *
	 * @return string Строковое описание изображения в формате "WIDTHxHEIGHT - CATEGORY"
	 *                или изображение недоступно
	 */
	public function __toString(): string {
		if (!$this->exists()) {
			return 'Изображение недоступно';
		}

		$parts = [];
		if ($this->getFormattedDimensions()) {
			$parts[] = $this->getFormattedDimensions();
		}
		if ($this->getResolutionCategory()) {
			$parts[] = $this->getResolutionCategory();
		}

		return empty($parts) ? 'Изображение доступно' : implode(' - ', $parts);
	}

	/**
	 * Проверяет наличие изображения
	 *
	 * Определяет, доступно ли изображение, проверяя наличие хотя бы одного
	 * из URL-адресов (полноразмерного или превью).
	 *
	 * @return bool true если изображение доступно, false в противном случае
	 */
	public function exists(): bool {
		return $this->url !== NULL || $this->previewUrl !== NULL;
	}

	/**
	 * Получает форматированную строку размеров
	 *
	 * Возвращает размеры изображения в стандартном формате "ширина x высота".
	 * Если размеры неизвестны, возвращает null.
	 *
	 * @return string|null Строка размеров в формате "1920x1080" или null если размеры неизвестны
	 */
	public function getFormattedDimensions(): ?string {
		if ($this->width === NULL || $this->height === NULL) {
			return NULL;
		}

		return "{$this->width}x{$this->height}";
	}

	/**
	 * Определяет категорию разрешения изображения
	 *
	 * Анализирует количество пикселей в изображении и возвращает
	 * соответствующую категорию качества: 4K, Full HD, HD, SD или Low.
	 * Использует стандартные пороговые значения для классификации.
	 *
	 * @return string|null Категория разрешения ('4K', 'Full HD', 'HD', 'SD', 'Low') или null если размеры неизвестны
	 */
	public function getResolutionCategory(): ?string {
		if ($this->width === NULL || $this->height === NULL) {
			return NULL;
		}

		$pixels = $this->width * $this->height;

		if ($pixels >= 8294400) { // 4K (3840x2160)
			return '4K';
		}

		if ($pixels >= 2073600) { // Full HD (1920x1080)
			return 'Full HD';
		}

		if ($pixels >= 921600) { // HD (1280x720)
			return 'HD';
		}

		if ($pixels >= 307200) { // SD (640x480)
			return 'SD';
		}

		return 'Low';
	}

	/**
	 * Создает объект Image из массива данных API
	 *
	 * Фабричный метод для создания экземпляра класса Image из массива
	 * данных, полученных от API Kinopoisk.dev. Безопасно обрабатывает
	 * отсутствующие значения, устанавливая их в null.
	 * Автоматически преобразует строковые значения размеров в целые числа.
	 *
	 * @param   array  $data  Массив данных изображения от API, содержащий ключи:
	 *                        - url: string|null - URL полноразмерного изображения
	 *                        - previewUrl: string|null - URL превью изображения
	 *                        - height: int|string|null - высота изображения
	 *                        - width: int|string|null - ширина изображения
	 *
	 * @return \KinopoiskDev\Models\Image Новый экземпляр класса Image с данными из массива
	 */
	public static function fromArray(array $data): self {
		return new self(
			url       : $data['url'] ?? NULL,
			previewUrl: $data['previewUrl'] ?? NULL,
			height    : isset($data['height']) ? (int) $data['height'] : NULL,
			width     : isset($data['width']) ? (int) $data['width'] : NULL,
		);
	}

	/**
	 * Преобразует объект в массив данных
	 *
	 * Конвертирует текущий экземпляр класса Image в массив,
	 * совместимый с форматом API Kinopoisk.dev. Используется для
	 * сериализации данных при отправке запросов к API или экспорте в JSON.
	 *
	 * @return array Массив с данными изображения, содержащий ключи:
	 *               - url: string|null - URL полноразмерного изображения
	 *               - previewUrl: string|null - URL превью изображения
	 *               - height: int|null - высота изображения
	 *               - width: int|null - ширина изображения
	 */
	public function toArray(bool $includeNulls = true): array {
		return [
			'url'        => $this->url,
			'previewUrl' => $this->previewUrl,
			'height'     => $this->height,
			'width'      => $this->width,
		];
	}

	/**
	 * Получает наилучший доступный URL изображения
	 *
	 * Возвращает URL полноразмерного изображения, если доступно,
	 * иначе возвращает URL превью. Предпочитает качество перед скоростью загрузки.
	 *
	 * @return string|null URL наилучшего доступного изображения или null если изображения недоступны
	 */
	public function getBestUrl(): ?string {
		return $this->url ?? $this->previewUrl;
	}

	/**
	 * Получает размеры изображения
	 *
	 * Возвращает массив с размерами изображения, содержащий ширину и высоту.
	 * Если размеры неизвестны, возвращает null.
	 *
	 * @return array|null Массив размеров с ключами 'width' и 'height' или null если размеры неизвестны
	 */
	public function getDimensions(): ?array {
		if ($this->width === NULL || $this->height === NULL) {
			return NULL;
		}

		return [
			'width'  => $this->width,
			'height' => $this->height,
		];
	}

	/**
	 * Проверяет, является ли изображение портретным
	 *
	 * Определяет ориентацию изображения на основе соотношения сторон.
	 * Портретным считается изображение с соотношением сторон меньше 1.
	 *
	 * @return bool|null true если изображение портретное, false если альбомное или квадратное, null если размеры неизвестны
	 */
	public function isPortrait(): ?bool {
		$ratio = $this->getAspectRatio();

		return $ratio !== NULL ? $ratio < 1 : NULL;
	}

	/**
	 * Вычисляет соотношение сторон изображения
	 *
	 * Рассчитывает соотношение ширины к высоте изображения.
	 * Используется для определения ориентации и пропорций изображения.
	 *
	 * @return float|null Соотношение сторон (ширина/высота) или null если размеры неизвестны или высота равна 0
	 */
	public function getAspectRatio(): ?float {
		if ($this->width === NULL || $this->height === NULL || $this->height === 0) {
			return NULL;
		}

		return $this->width / $this->height;
	}

	/**
	 * Проверяет, является ли изображение альбомным
	 *
	 * Определяет ориентацию изображения на основе соотношения сторон.
	 * Альбомным считается изображение с соотношением сторон больше 1.
	 *
	 * @return bool|null true если изображение альбомное, false если портретное или квадратное, null если размеры неизвестны
	 */
	public function isLandscape(): ?bool {
		$ratio = $this->getAspectRatio();

		return $ratio !== NULL ? $ratio > 1 : NULL;
	}

	/**
	 * Проверяет, является ли изображение квадратным
	 *
	 * Определяет, является ли изображение квадратным, сравнивая соотношение сторон
	 * с 1 с допуском 0.01 для учета погрешностей вычислений.
	 *
	 * @return bool|null true если изображение квадратное, false в противном случае, null если размеры неизвестны
	 */
	public function isSquare(): ?bool {
		$ratio = $this->getAspectRatio();

		return $ratio !== NULL ? abs($ratio - 1) < 0.01 : NULL;
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
