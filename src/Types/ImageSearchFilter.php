<?php

namespace KinopoiskDev\Types;

use KinopoiskDev\Utils\MovieFilter;
use KinopoiskDev\Utils\FilterTrait;

/**
 * Класс для фильтров при поиске изображений
 *
 * @package KinopoiskDev\Types
 * @since   1.0.0
 * @author  Maxim Harder
 * @version 1.0.0
 */
class ImageSearchFilter extends MovieFilter {

	use FilterTrait;

	/**
	 * Добавляет фильтр по языку изображения
	 *
	 * @param   string  $language  Язык изображения
	 *
	 * @return $this
	 */
	public function language(string $language): self {
		$this->addFilter('language', $language);

		return $this;
	}

	/**
	 * Фильтр только для постеров
	 *
	 * @return $this
	 */
	public function onlyPosters(): self {
		return $this->type('poster');
	}

	/**
	 * Фильтр только для кадров
	 *
	 * @return $this
	 */
	public function onlyStills(): self {
		return $this->type('still');
	}

	/**
	 * Фильтр только для фотосессий
	 *
	 * @return $this
	 */
	public function onlyShooting(): self {
		return $this->type('shooting');
	}

	/**
	 * Фильтр только для скриншотов
	 *
	 * @return $this
	 */
	public function onlyScreenshots(): self {
		return $this->type('screenshot');
	}

	/**
	 * Фильтр только для изображений высокого разрешения (Full HD+)
	 *
	 * @return $this
	 */
	public function onlyHighRes(): self {
		return $this->minResolution(1920, 1080);
	}

	/**
	 * Добавляет фильтр по минимальному разрешению
	 *
	 * @param   int  $minWidth   Минимальная ширина
	 * @param   int  $minHeight  Минимальная высота
	 *
	 * @return $this
	 */
	public function minResolution(int $minWidth, int $minHeight): self {
		$this->width($minWidth, 'gte');
		$this->height($minHeight, 'gte');

		return $this;
	}

	/**
	 * Добавляет фильтр по ширине изображения
	 *
	 * @param   int     $width     Ширина изображения в пикселях
	 * @param   string  $operator  Оператор сравнения
	 *
	 * @return $this
	 */
	public function width(int $width, string $operator = 'eq'): self {
		$this->addFilter('width', $width, $operator);

		return $this;
	}

	/**
	 * Добавляет фильтр по высоте изображения
	 *
	 * @param   int     $height    Высота изображения в пикселях
	 * @param   string  $operator  Оператор сравнения
	 *
	 * @return $this
	 */
	public function height(int $height, string $operator = 'eq'): self {
		$this->addFilter('height', $height, $operator);

		return $this;
	}

}
