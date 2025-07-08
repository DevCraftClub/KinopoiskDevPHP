<?php

namespace KinopoiskDev\Models;

use KinopoiskDev\Enums\MovieStatus;
use KinopoiskDev\Enums\MovieType;
use KinopoiskDev\Enums\RatingMpaa;

/**
 * Класс для представления результатов поиска фильмов
 *
 * Представляет данные о фильме, полученные при выполнении поиска через API Kinopoisk.dev.
 * Содержит основную информацию о фильме, включая идентификатор, названия, рейтинги,
 * постеры, жанры и другие метаданные. Используется для отображения результатов поиска
 * без необходимости загрузки полной информации о фильме.
 *
 * @package KinopoiskDev\Models
 * @since   1.0.0
 * @author  Maxim Harder
 *
 * @version 1.0.0
 * @see     \KinopoiskDev\Models\Movie Для полной информации о фильме
 * @see     \KinopoiskDev\Models\Name Для названий фильмов
 * @see     \KinopoiskDev\Models\ExternalId Для внешних идентификаторов
 * @see     \KinopoiskDev\Models\Rating Для рейтингов
 * @see     \KinopoiskDev\Models\ShortImage Для изображений
 * @see     \KinopoiskDev\Models\ItemName Для жанров и стран
 * @see     \KinopoiskDev\Models\YearRange Для годов выпуска
 * @see     \KinopoiskDev\Models\Logo Для логотипов
 * @see     \KinopoiskDev\Models\Votes Для голосов
 * @link    https://kinopoiskdev.readme.io/reference/moviecontroller_searchmoviesv1_4
 */
readonly class SearchMovie implements BaseModel {

	/**
	 * Конструктор для создания объекта результата поиска фильма
	 *
	 * Создает новый экземпляр класса SearchMovie с указанными параметрами.
	 * Большинство параметров являются опциональными и могут быть null при отсутствии
	 * соответствующей информации в источнике данных. Только идентификатор является
	 * обязательным параметром.
	 *
	 * @see SearchMovie::fromArray() Для создания объекта из массива данных API
	 * @see SearchMovie::toArray() Для преобразования объекта в массив
	 *
	 * @param   int                                    $id                 Уникальный идентификатор фильма в системе Kinopoisk
	 * @param   string|null                            $name               Название фильма на русском языке
	 * @param   string|null                            $alternativeName    Альтернативное название фильма
	 * @param   string|null                            $enName             Название фильма на английском языке
	 * @param   \KinopoiskDev\Enums\MovieType|null     $type               Тип фильма (фильм, сериал, мультфильм и т.д.)
	 * @param   int|null                               $year               Год выпуска фильма
	 * @param   string|null                            $description        Полное описание сюжета фильма
	 * @param   string|null                            $shortDescription   Краткое описание фильма
	 * @param   int|null                               $movieLength        Длительность фильма в минутах
	 * @param   \KinopoiskDev\Models\Name[]|null       $names              Массив всех названий фильма на разных языках
	 * @param   ExternalId|null                        $externalId         Внешние идентификаторы (IMDB, TMDB, KinopoiskHD)
	 * @param   Logo|null                              $logo               Логотип фильма
	 * @param   ShortImage|null                        $poster             Постер фильма
	 * @param   ShortImage|null                        $backdrop           Фоновое изображение фильма
	 * @param   Rating|null                            $rating             Рейтинг фильма
	 * @param   Votes|null                             $votes              Информация о голосах
	 * @param   \KinopoiskDev\Models\ItemName[]|null   $genres             Массив жанров фильма
	 * @param   \KinopoiskDev\Models\ItemName[]|null   $countries          Массив стран производства фильма
	 * @param   \KinopoiskDev\Models\YearRange[]|null  $releaseYears       Массив годов выпуска для разных стран
	 * @param   bool|null                              $isSeries           Является ли произведение сериалом
	 * @param   bool|null                              $ticketsOnSale      Доступны ли билеты к покупке
	 * @param   int|null                               $totalSeriesLength  Общее количество серий
	 * @param   int|null                               $seriesLength       Количество серий в сезоне
	 * @param   \KinopoiskDev\Enums\RatingMpaa|null    $ratingMpaa         Рейтинг MPAA (G, PG, PG-13, R, NC-17)
	 * @param   int|null                               $ageRating          Возрастной рейтинг
	 * @param   int|null                               $top10              Позиция в топ-10 (null если не входит)
	 * @param   int|null                               $top250             Позиция в топ-250 (null если не входит)
	 * @param   int|null                               $typeNumber         Числовой код типа фильма
	 * @param   \KinopoiskDev\Enums\MovieStatus|null   $status             Статус производства фильма
	 */
	public function __construct(
		public int          $id,
		public ?string      $name = NULL,
		public ?string      $alternativeName = NULL,
		public ?string      $enName = NULL,
		public ?MovieType   $type = NULL,
		public ?int         $year = NULL,
		public ?string      $description = NULL,
		public ?string      $shortDescription = NULL,
		public ?int         $movieLength = NULL,
		public ?array       $names = NULL,
		public ?ExternalId  $externalId = NULL,
		public ?Logo        $logo = NULL,
		public ?ShortImage  $poster = NULL,
		public ?ShortImage  $backdrop = NULL,
		public ?Rating      $rating = NULL,
		public ?Votes       $votes = NULL,
		public ?array       $genres = NULL,
		public ?array       $countries = NULL,
		public ?array       $releaseYears = NULL,
		public ?bool        $isSeries = NULL,
		public ?bool        $ticketsOnSale = NULL,
		public ?int         $totalSeriesLength = NULL,
		public ?int         $seriesLength = NULL,
		public ?RatingMpaa  $ratingMpaa = NULL,
		public ?int         $ageRating = NULL,
		public ?int         $top10 = NULL,
		public ?int         $top250 = NULL,
		public ?int         $typeNumber = NULL,
		public ?MovieStatus $status = NULL,

	) {}

	/**
	 * Создает объект SearchMovie из массива данных API
	 *
	 * Фабричный метод для создания экземпляра класса SearchMovie из массива данных,
	 * полученных от API Kinopoisk.dev. Безопасно обрабатывает отсутствующие значения,
	 * устанавливая их в null. Автоматически преобразует массивы данных в соответствующие
	 * объекты модели при их наличии.
	 *
	 * @see SearchMovie::toArray() Для обратного преобразования в массив
	 * @see SearchMovie::__construct() Для создания объекта через конструктор
	 *
	 * @param   array  $data  Массив данных от API, содержащий информацию о фильме
	 *
	 * @return  self  Новый экземпляр SearchMovie с данными из массива
	 *
	 * @throws  \TypeError  Если обязательный параметр 'id' отсутствует или имеет неверный тип
	 */
	public static function fromArray(array $data): self {
		return new self(
			id               : $data['id'],
			name             : $data['name'] ?? NULL,
			alternativeName  : $data['alternativeName'] ?? NULL,
			enName           : $data['enName'] ?? NULL,
			type             : $data['type'] ?? NULL,
			year             : $data['year'] ?? NULL,
			description      : $data['description'] ?? NULL,
			shortDescription : $data['shortDescription'] ?? NULL,
			movieLength      : $data['movieLength'] ?? NULL,
			names            : is_array($data['names']) ? Name::fromArray($data['names']) : NULL,
			externalId       : is_array($data['externalId']) ? ExternalId::fromArray($data['externalId']) : NULL,
			logo             : is_array($data['logo']) ? Logo::fromArray($data['logo']) : NULL,
			poster           : is_array($data['poster']) ? ShortImage::fromArray($data['poster']) : NULL,
			backdrop         : is_array($data['backdrop']) ? ShortImage::fromArray($data['backdrop']) : NULL,
			rating           : is_array($data['rating']) ? Rating::fromArray($data['rating']) : NULL,
			votes            : is_array($data['votes']) ? Votes::fromArray($data['votes']) : NULL,
			genres           : is_array($data['genres']) ? ItemName::fromArray($data['genres']) : NULL,
			countries        : is_array($data['countries']) ? ItemName::fromArray($data['countries']) : NULL,
			releaseYears     : is_array($data['releaseYears']) ? YearRange::fromArray($data['releaseYears']) : NULL,
			isSeries         : $data['isSeries'] ?? NULL,
			ticketsOnSale    : $data['ticketsOnSale'] ?? NULL,
			totalSeriesLength: $data['totalSeriesLength'] ?? NULL,
			seriesLength     : $data['seriesLength'] ?? NULL,
			ratingMpaa       : $data['ratingMpaa'] ?? NULL,
			ageRating        : $data['ageRating'] ?? NULL,
			top10            : $data['top10'] ?? NULL,
			top250           : $data['top250'] ?? NULL,
			typeNumber       : $data['typeNumber'] ?? NULL,
			status           : $data['status'] ?? NULL,
		);
	}

	/**
	 * Преобразует объект SearchMovie в массив
	 *
	 * Конвертирует текущий экземпляр SearchMovie в ассоциативный массив,
	 * сохраняя все свойства объекта. Полезно для сериализации данных,
	 * передачи в API или сохранения в базе данных.
	 *
	 * @see SearchMovie::fromArray() Для создания объекта из массива
	 * @see SearchMovie::__construct() Для создания объекта через конструктор
	 *
	 * @return  array  Ассоциативный массив с данными объекта, где ключи соответствуют
	 *                 именам свойств, а значения - их содержимому
	 */
	public function toArray(): array {
		return [
			'id'                => $this->id,
			'name'              => $this->name,
			'alternativeName'   => $this->alternativeName,
			'enName'            => $this->enName,
			'type'              => $this->type,
			'year'              => $this->year,
			'description'       => $this->description,
			'shortDescription'  => $this->shortDescription,
			'movieLength'       => $this->movieLength,
			'names'             => $this->names,
			'externalId'        => $this->externalId,
			'logo'              => $this->logo,
			'poster'            => $this->poster,
			'backdrop'          => $this->backdrop,
			'rating'            => $this->rating,
			'votes'             => $this->votes,
			'genres'            => $this->genres,
			'countries'         => $this->countries,
			'releaseYears'      => $this->releaseYears,
			'isSeries'          => $this->isSeries,
			'ticketsOnSale'     => $this->ticketsOnSale,
			'totalSeriesLength' => $this->totalSeriesLength,
			'seriesLength'      => $this->seriesLength,
			'ratingMpaa'        => $this->ratingMpaa,
			'ageRating'         => $this->ageRating,
			'top10'             => $this->top10,
			'top250'            => $this->top250,
			'typeNumber'        => $this->typeNumber,
			'status'            => $this->status,
		];
	}

}
