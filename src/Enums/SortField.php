<?php

namespace KinopoiskDev\Enums;

/**
 * Enum для полей сортировки при поиске фильмов
 *
 * Этот enum содержит все возможные поля, которые можно использовать
 * для сортировки результатов поиска через API Kinopoisk.dev
 *
 * @package KinopoiskDev\Enums
 * @since   1.0.0
 * @author  Maxim Harder
 * @version 1.0.0
 * @link    https://kinopoiskdev.readme.io/reference/moviecontroller_findmanybyqueryv1_4
 */
enum SortField: string {

	// Основные поля для сортировки
	case ID               = 'id';
	case NAME             = 'name';
	case EN_NAME          = 'enName';
	case ALTERNATIVE_NAME = 'alternativeName';
	case YEAR             = 'year';
	case CREATED_AT       = 'createdAt';
	case UPDATED_AT       = 'updatedAt';

	// Рейтинги
	case RATING_KP                   = 'rating.kp';
	case RATING_IMDB                 = 'rating.imdb';
	case RATING_TMDB                 = 'rating.tmdb';
	case RATING_FILM_CRITICS         = 'rating.filmCritics';
	case RATING_RUSSIAN_FILM_CRITICS = 'rating.russianFilmCritics';
	case RATING_AWAIT                = 'rating.await';

	// Голоса
	case VOTES_KP                   = 'votes.kp';
	case VOTES_IMDB                 = 'votes.imdb';
	case VOTES_TMDB                 = 'votes.tmdb';
	case VOTES_FILM_CRITICS         = 'votes.filmCritics';
	case VOTES_RUSSIAN_FILM_CRITICS = 'votes.russianFilmCritics';
	case VOTES_AWAIT                = 'votes.await';

	// Длительность и технические параметры
	case MOVIE_LENGTH        = 'movieLength';
	case SERIES_LENGTH       = 'seriesLength';
	case TOTAL_SERIES_LENGTH = 'totalSeriesLength';
	case AGE_RATING          = 'ageRating';

	// Топы и позиции
	case TOP_10  = 'top10';
	case TOP_250 = 'top250';

	// Даты премьер
	case PREMIERE_WORLD  = 'premiere.world';
	case PREMIERE_RUSSIA = 'premiere.russia';
	case PREMIERE_USA    = 'premiere.usa';

	// Студии
	case TYPE = 'type';

	case TITLE = 'title';

	/**
	 * Возвращает все поля рейтингов
	 *
	 * Статический метод для получения всех доступных полей рейтингов.
	 * Используется для создания интерфейсов выбора рейтинговых полей.
	 *
	 * @return array Массив всех рейтинговых полей SortField
	 */
	public static function getRatingFields(): array {
		static $fields = NULL;

		if ($fields === NULL) {
			$fields = [
				self::RATING_KP,
				self::RATING_IMDB,
				self::RATING_TMDB,
				self::RATING_FILM_CRITICS,
				self::RATING_RUSSIAN_FILM_CRITICS,
				self::RATING_AWAIT,
			];
		}

		return $fields;
	}

	/**
	 * Возвращает все поля голосов
	 *
	 * Статический метод для получения всех доступных полей голосов.
	 * Используется для создания интерфейсов выбора полей голосов.
	 *
	 * @return array Массив всех полей голосов SortField
	 */
	public static function getVotesFields(): array {
		static $fields = NULL;

		if ($fields === NULL) {
			$fields = [
				self::VOTES_KP,
				self::VOTES_IMDB,
				self::VOTES_TMDB,
				self::VOTES_FILM_CRITICS,
				self::VOTES_RUSSIAN_FILM_CRITICS,
				self::VOTES_AWAIT,
			];
		}

		return $fields;
	}

	/**
	 * Возвращает человекочитаемое описание поля
	 *
	 * Предоставляет описательное название поля сортировки на русском языке
	 * для использования в пользовательских интерфейсах и документации.
	 *
	 * @return string Описательное название поля на русском языке
	 */
	public function getDescription(): string {
		// Используем статический кеш для оптимизации
		static $cache = [];

		if (isset($cache[$this->value])) {
			return $cache[$this->value];
		}

		$result = match ($this) {
			self::ID                          => 'ID фильма',
			self::NAME                        => 'Название (русское)',
			self::EN_NAME                     => 'Название (английское)',
			self::ALTERNATIVE_NAME            => 'Альтернативное название',
			self::YEAR                        => 'Год выпуска',
			self::CREATED_AT                  => 'Дата создания',
			self::UPDATED_AT                  => 'Дата обновления',
			self::RATING_KP                   => 'Рейтинг Кинопоиска',
			self::RATING_IMDB                 => 'Рейтинг IMDB',
			self::RATING_TMDB                 => 'Рейтинг TMDB',
			self::RATING_FILM_CRITICS         => 'Рейтинг кинокритиков',
			self::RATING_RUSSIAN_FILM_CRITICS => 'Рейтинг российских кинокритиков',
			self::RATING_AWAIT                => 'Рейтинг ожидания',
			self::VOTES_KP                    => 'Голоса Кинопоиска',
			self::VOTES_IMDB                  => 'Голоса IMDB',
			self::VOTES_TMDB                  => 'Голоса TMDB',
			self::VOTES_FILM_CRITICS          => 'Голоса кинокритиков',
			self::VOTES_RUSSIAN_FILM_CRITICS  => 'Голоса российских кинокритиков',
			self::VOTES_AWAIT                 => 'Голоса ожидания',
			self::MOVIE_LENGTH                => 'Длительность фильма',
			self::SERIES_LENGTH               => 'Длительность серии',
			self::TOTAL_SERIES_LENGTH         => 'Общая длительность сериала',
			self::AGE_RATING                  => 'Возрастной рейтинг',
			self::TOP_10                      => 'Позиция в топ-10',
			self::TOP_250                     => 'Позиция в топ-250',
			self::PREMIERE_WORLD              => 'Дата мировой премьеры',
			self::PREMIERE_RUSSIA             => 'Дата премьеры в России',
			self::PREMIERE_USA                => 'Дата премьеры в США',
			self::TITLE                       => 'Название',
			self::TYPE                        => 'Тип',
		};

		$cache[$this->value] = $result;

		return $result;
	}

	/**
	 * Проверяет, является ли поле рейтинговым
	 *
	 * Определяет, относится ли поле сортировки к категории рейтингов.
	 * Используется для группировки и специальной обработки рейтинговых полей.
	 *
	 * @return bool true, если поле является рейтинговым, false в противном случае
	 */
	public function isRatingField(): bool {
		static $cache = [];

		if (!isset($cache[$this->value])) {
			$cache[$this->value] = str_starts_with($this->value, 'rating.');
		}

		return $cache[$this->value];
	}

	/**
	 * Проверяет, является ли поле полем голосов
	 *
	 * Определяет, относится ли поле сортировки к категории голосов.
	 * Используется для группировки и специальной обработки полей голосов.
	 *
	 * @return bool true, если поле является полем голосов, false в противном случае
	 */
	public function isVotesField(): bool {
		static $cache = [];

		if (!isset($cache[$this->value])) {
			$cache[$this->value] = str_starts_with($this->value, 'votes.');
		}

		return $cache[$this->value];
	}

	/**
	 * Проверяет, является ли поле полем даты
	 *
	 * Определяет, относится ли поле сортировки к категории дат.
	 * Используется для валидации и специальной обработки временных полей.
	 *
	 * @return bool true, если поле является полем даты, false в противном случае
	 */
	public function isDateField(): bool {
		static $cache = [];

		if (!isset($cache[$this->value])) {
			$cache[$this->value] = $this->getDataType() === 'date';
		}

		return $cache[$this->value];
	}

	/**
	 * Возвращает тип данных поля для валидации
	 *
	 * Определяет тип данных поля сортировки для обеспечения корректной
	 * валидации и обработки параметров сортировки.
	 *
	 * @return string Тип данных поля ('number', 'string', 'date')
	 */
	public function getDataType(): string {
		// Используем статический кеш для оптимизации
		static $cache = [];

		if (isset($cache[$this->value])) {
			return $cache[$this->value];
		}

		$result = match ($this) {
			// Числовые поля
			self::ID, self::YEAR, self::MOVIE_LENGTH, self::SERIES_LENGTH,
			self::TOTAL_SERIES_LENGTH, self::AGE_RATING, self::TOP_10, self::TOP_250,
			self::RATING_KP, self::RATING_IMDB, self::RATING_TMDB,
			self::RATING_FILM_CRITICS, self::RATING_RUSSIAN_FILM_CRITICS, self::RATING_AWAIT,
			self::VOTES_KP, self::VOTES_IMDB, self::VOTES_TMDB,
			self::VOTES_FILM_CRITICS, self::VOTES_RUSSIAN_FILM_CRITICS, self::VOTES_AWAIT => 'number',

			// Поля дат
			self::CREATED_AT, self::UPDATED_AT, self::PREMIERE_WORLD,
			self::PREMIERE_RUSSIA, self::PREMIERE_USA                                     => 'date',
			default                                                                       => 'string',
		};

		$cache[$this->value] = $result;

		return $result;
	}

	/**
	 * Проверяет, является ли поле числовым
	 *
	 * Определяет, относится ли поле сортировки к числовому типу данных.
	 * Используется для валидации и обработки числовых значений.
	 *
	 * @return bool true, если поле является числовым, false в противном случае
	 */
	public function isNumericField(): bool {
		static $cache = [];

		if (!isset($cache[$this->value])) {
			$cache[$this->value] = $this->getDataType() === 'number';
		}

		return $cache[$this->value];
	}

	/**
	 * Возвращает рекомендуемое направление сортировки по умолчанию
	 *
	 * Определяет наиболее логичное направление сортировки для каждого поля
	 * на основе его семантики и обычных пользовательских ожиданий.
	 *
	 * @return SortDirection Рекомендуемое направление сортировки
	 */
	public function getDefaultDirection(): SortDirection {
		static $cache = [];

		if (isset($cache[$this->value])) {
			return $cache[$this->value];
		}

		$result = match ($this) {
			// По убыванию для рейтингов (сначала лучшие)
			self::RATING_KP, self::RATING_IMDB, self::RATING_TMDB,
			self::RATING_FILM_CRITICS, self::RATING_RUSSIAN_FILM_CRITICS, self::RATING_AWAIT,
				// По убыванию для голосов (сначала популярные)
			self::VOTES_KP, self::VOTES_IMDB, self::VOTES_TMDB,
			self::VOTES_FILM_CRITICS, self::VOTES_RUSSIAN_FILM_CRITICS, self::VOTES_AWAIT,
				// По убыванию для года (сначала новые)
			self::YEAR,
				// По убыванию для ID (сначала новые записи)
			self::ID,
				// По убыванию для дат (сначала свежие)
			self::CREATED_AT, self::UPDATED_AT, self::PREMIERE_WORLD,
			self::PREMIERE_RUSSIA, self::PREMIERE_USA                        => SortDirection::DESC,

			// По возрастанию для топов (меньше = лучше позиция)
			self::TOP_10, self::TOP_250, self::MOVIE_LENGTH,
			self::SERIES_LENGTH, self::TOTAL_SERIES_LENGTH, self::AGE_RATING => SortDirection::ASC,

			// По возрастанию для названий (алфавитный порядок)
			self::NAME, self::EN_NAME, self::ALTERNATIVE_NAME                => SortDirection::ASC,
			// По возрастанию для технических параметров
			default                                                           => SortDirection::DESC,
		};

		$cache[$this->value] = $result;

		return $result;
	}

}
