<?php

namespace KinopoiskDev\Enums;

/**
 * Enum для полей фильтрации
 *
 * Этот enum содержит все возможные поля, которые можно использовать
 * при фильтрации данных через API Kinopoisk.dev
 */
enum FilterField: string {

	// Основные поля
	case ID                = 'id';
	case EXTERNAL_ID       = 'externalId';
	case NAME              = 'name';
	case EN_NAME           = 'enName';
	case ALTERNATIVE_NAME  = 'alternativeName';
	case NAMES             = 'names.name';
	case DESCRIPTION       = 'description';
	case SHORT_DESCRIPTION = 'shortDescription';
	case SLOGAN            = 'slogan';

	// Типы и статусы
	case TYPE        = 'type';
	case TYPE_NUMBER = 'typeNumber';
	case IS_SERIES   = 'isSeries';
	case STATUS      = 'status';

	// Даты и годы
	case YEAR          = 'year';
	case RELEASE_YEARS = 'releaseYears';
	case UPDATED_AT    = 'updatedAt';
	case CREATED_AT    = 'createdAt';

	// Рейтинги и оценки
	case RATING_KP                   = 'rating.kp';
	case RATING_IMDB                 = 'rating.imdb';
	case RATING_TMDB                 = 'rating.tmdb';
	case RATING_FILM_CRITICS         = 'rating.filmCritics';
	case RATING_RUSSIAN_FILM_CRITICS = 'rating.russianFilmCritics';
	case RATING_AWAIT                = 'rating.await';
	case RATING_MPAA                 = 'ratingMpaa';
	case AGE_RATING                  = 'ageRating';

	// Голоса
	case VOTES_KP                   = 'votes.kp';
	case VOTES_IMDB                 = 'votes.imdb';
	case VOTES_TMDB                 = 'votes.tmdb';
	case VOTES_FILM_CRITICS         = 'votes.filmCritics';
	case VOTES_RUSSIAN_FILM_CRITICS = 'votes.russianFilmCritics';
	case VOTES_AWAIT                = 'votes.await';

	// Длительность
	case MOVIE_LENGTH        = 'movieLength';
	case SERIES_LENGTH       = 'seriesLength';
	case TOTAL_SERIES_LENGTH = 'totalSeriesLength';

	// Жанры и страны
	case GENRES    = 'genres.name';
	case COUNTRIES = 'countries.name';

	// Изображения
	case POSTER   = 'poster';
	case BACKDROP = 'backdrop';
	case LOGO     = 'logo';

	// Дополнительные поля
	case TICKETS_ON_SALE      = 'ticketsOnSale';
	case VIDEOS               = 'videos';
	case NETWORKS             = 'networks';
	case PERSONS              = 'persons';
	case PERSONS_NAME         = 'persons.name';
	case PERSONS_ID           = 'persons.id';
	case PERSONS_PROFESSION   = 'persons.profession';
	case FACTS                = 'facts';
	case FEES                 = 'fees';
	case PREMIERE             = 'premiere';
	case PREMIERE_WORLD       = 'premiere.world';
	case PREMIERE_RUSSIA      = 'premiere.russia';
	case PREMIERE_USA         = 'premiere.usa';
	case SIMILAR_MOVIES       = 'similarMovies';
	case SEQUELS_AND_PREQUELS = 'sequelsAndPrequels';
	case WATCHABILITY         = 'watchability';
	case LISTS                = 'lists';
	case TOP_10               = 'top10';
	case TOP_250              = 'top250';
	case SEASONS_INFO         = 'seasonsInfo';
	case BUDGET               = 'budget';
	case AUDIENCE             = 'audience';

	/**
	 * Возвращает тип поля
	 */
	public function getFieldType(): string {
		return match ($this) {
			// Числовые поля
			self::ID, self::TYPE_NUMBER, self::YEAR,
			self::RATING_KP, self::RATING_IMDB, self::RATING_TMDB,
			self::RATING_FILM_CRITICS, self::RATING_RUSSIAN_FILM_CRITICS, self::RATING_AWAIT,
			self::AGE_RATING, self::VOTES_KP, self::VOTES_IMDB, self::VOTES_TMDB,
			self::VOTES_FILM_CRITICS, self::VOTES_RUSSIAN_FILM_CRITICS, self::VOTES_AWAIT,
			self::MOVIE_LENGTH, self::SERIES_LENGTH, self::TOTAL_SERIES_LENGTH,
			self::TOP_10, self::TOP_250, self::PERSONS_ID                         => 'number',

			// Булевы поля
			self::IS_SERIES, self::TICKETS_ON_SALE                                => 'boolean',

			// Текстовые поля (для regex)
			self::NAME, self::EN_NAME, self::ALTERNATIVE_NAME, self::NAMES,
			self::DESCRIPTION, self::SHORT_DESCRIPTION, self::SLOGAN,
			self::PERSONS_NAME                                                    => 'text',

			// Поля дат
			self::UPDATED_AT, self::CREATED_AT,
			self::PREMIERE_WORLD, self::PREMIERE_RUSSIA, self::PREMIERE_USA       => 'date',

			// Поля для включения/исключения
			self::GENRES, self::COUNTRIES                                         => 'include_exclude',

			// Объектные поля
			self::EXTERNAL_ID, self::RELEASE_YEARS, self::POSTER, self::BACKDROP,
			self::LOGO, self::VIDEOS, self::NETWORKS, self::PERSONS, self::FACTS,
			self::FEES, self::PREMIERE, self::SIMILAR_MOVIES, self::SEQUELS_AND_PREQUELS,
			self::WATCHABILITY, self::LISTS, self::SEASONS_INFO, self::BUDGET,
			self::AUDIENCE                                                        => 'object',

			default                                                               => 'string'
		};
	}

	/**
	 * Проверяет, поддерживает ли поле операторы включения/исключения
	 */
	public function supportsIncludeExclude(): bool {
		return $this->getFieldType() === 'include_exclude';
	}

	/**
	 * Проверяет, поддерживает ли поле диапазоны
	 */
	public function supportsRange(): bool {
		return in_array($this->getFieldType(), ['number', 'date']);
	}

	/**
	 * Возвращает оператор по умолчанию для поля
	 */
	public function getDefaultOperator(): FilterOperator {
		return FilterOperator::getDefaultForFieldType($this->getFieldType());
	}

	/**
	 * Возвращает базовое поле для составных полей (например, rating.kp -> rating)
	 */
	public function getBaseField(): string {
		$parts = explode('.', $this->value);

		return $parts[0];
	}

	/**
	 * Возвращает подполе для составных полей (например, rating.kp -> kp)
	 */
	public function getSubField(): ?string {
		$parts = explode('.', $this->value);

		return $parts[1] ?? NULL;
	}

}