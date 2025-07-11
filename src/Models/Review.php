<?php

declare(strict_types=1);

namespace KinopoiskDev\Models;

/**
 * Класс для представления рецензии на фильм
 *
 * Представляет информацию о рецензии пользователя на фильм или сериал,
 * включая текст рецензии, тип (позитивная/негативная/нейтральная),
 * автора и статистику лайков/дизлайков.
 *
 * @package KinopoiskDev\Models
 * @since   1.0.0
 * @author  Maxim Harder
 * @version 1.0.0
 * @see     \KinopoiskDev\Models\Movie Для основной модели фильма
 * @link    https://kinopoiskdev.readme.io/reference/reviewcontroller_findmanyv1_4
 */
 class Review implements BaseModel {

	/**
	 * Конструктор для создания объекта рецензии
	 *
	 * Создает новый экземпляр класса Review с указанными параметрами.
	 * Все обязательные поля должны быть переданы при создании объекта.
	 *
	 * @param   int          $id              Уникальный идентификатор рецензии
	 * @param   int          $movieId         ID фильма к которому относится рецензия
	 * @param   int          $authorId        ID автора рецензии
	 * @param   int          $reviewLikes     Количество лайков рецензии
	 * @param   int          $reviewDislikes  Количество дизлайков рецензии
	 * @param   string       $updatedAt       Дата последнего обновления
	 * @param   string       $createdAt       Дата создания рецензии
	 * @param   string|null  $title           Заголовок рецензии
	 * @param   string|null  $type            Тип рецензии (Позитивный/Негативный/Нейтральный)
	 * @param   string|null  $review          Текст рецензии
	 * @param   string|null  $date            Дата создания рецензии (альтернативное поле)
	 * @param   string|null  $author          Имя автора рецензии
	 * @param   int|null     $userRating      Пользовательский рейтинг
	 */
	public function __construct(
		public int     $id,
		public int     $movieId,
		public int     $authorId,
		public int     $reviewLikes,
		public int     $reviewDislikes,
		public string  $updatedAt,
		public string  $createdAt,
		public ?string $title = NULL,
		public ?string $type = NULL,
		public ?string $review = NULL,
		public ?string $date = NULL,
		public ?string $author = NULL,
		public ?int    $userRating = NULL,
	) {}

	/**
	 * Создает объект Review из массива данных API
	 *
	 * Фабричный метод для создания экземпляра класса Review из массива данных,
	 * полученных от API Kinopoisk.dev. Безопасно обрабатывает отсутствующие
	 * значения для опциональных полей.
	 *
	 * @param   array  $data  Массив данных о рецензии от API
	 *
	 * @return \KinopoiskDev\Models\Review Новый экземпляр класса Review с данными из массива
	 */
	public static function fromArray(array $data): static {
		return new self(
			id            : $data['id'],
			movieId       : $data['movieId'],
			authorId      : $data['authorId'],
			reviewLikes   : $data['reviewLikes'],
			reviewDislikes: $data['reviewDislikes'],
			updatedAt     : $data['updatedAt'],
			createdAt     : $data['createdAt'],
			title         : $data['title'] ?? NULL,
			type          : $data['type'] ?? NULL,
			review        : $data['review'] ?? NULL,
			date          : $data['date'] ?? NULL,
			author        : $data['author'] ?? NULL,
			userRating    : $data['userRating'] ?? NULL,
		);
	}

	/**
	 * Преобразует объект в массив данных
	 *
	 * Конвертирует текущий экземпляр класса Review в массив,
	 * совместимый с форматом API Kinopoisk.dev.
	 *
	 * @return array Массив с данными о рецензии
	 */
	public function toArray(bool $includeNulls = true): array {
		return [
			'id'             => $this->id,
			'movieId'        => $this->movieId,
			'title'          => $this->title,
			'type'           => $this->type,
			'review'         => $this->review,
			'date'           => $this->date,
			'author'         => $this->author,
			'userRating'     => $this->userRating,
			'authorId'       => $this->authorId,
			'reviewLikes'    => $this->reviewLikes,
			'reviewDislikes' => $this->reviewDislikes,
			'updatedAt'      => $this->updatedAt,
			'createdAt'      => $this->createdAt,
		];
	}

	/**
	 * Проверяет, является ли рецензия позитивной
	 *
	 * @return bool true если рецензия позитивная, иначе false
	 */
	public function isPositive(): bool {
		return $this->type === 'Позитивный';
	}

	/**
	 * Проверяет, является ли рецензия негативной
	 *
	 * @return bool true если рецензия негативная, иначе false
	 */
	public function isNegative(): bool {
		return $this->type === 'Негативный';
	}

	/**
	 * Проверяет, является ли рецензия нейтральной
	 *
	 * @return bool true если рецензия нейтральная, иначе false
	 */
	public function isNeutral(): bool {
		return $this->type === 'Нейтральный';
	}

	/**
	 * Возвращает общий рейтинг рецензии (лайки - дизлайки)
	 *
	 * @return int Разность между лайками и дизлайками
	 */
	public function getNetRating(): int {
		return $this->reviewLikes - $this->reviewDislikes;
	}

	/**
	 * Возвращает процент позитивных оценок рецензии
	 *
	 * @return float Процент лайков от общего количества оценок
	 */
	public function getPositivePercentage(): float {
		$total = $this->reviewLikes + $this->reviewDislikes;
		if ($total === 0) {
			return 0.0;
		}

		return ($this->reviewLikes / $total) * 100;
	}

	/**
	 * Возвращает актуальную дату рецензии
	 *
	 * Приоритет отдается createdAt над полем date
	 *
	 * @return string Дата рецензии
	 */
	public function getActualDate(): string {
		return $this->createdAt ?? $this->date ?? '';
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
