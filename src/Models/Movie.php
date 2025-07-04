<?php

declare(strict_types=1);

namespace KinopoiskDev\Models;

use KinopoiskDev\Enums\MovieStatus;
use KinopoiskDev\Enums\MovieType;
use KinopoiskDev\Enums\RatingMpaa;
use KinopoiskDev\Utils\DataManager;
use Lombok\Getter;

/**
 * Класс для представления фильма/сериала из API Kinopoisk.dev
 *
 * Представляет полную информацию о фильме или сериале, включая базовые данные,
 * рейтинги, участников, изображения, связанные произведения и другую метаинформацию.
 * Используется для работы с детальной информацией о произведениях кинематографа.
 *
 * @package KinopoiskDev\Models
 * @since   1.0.0
 * @author  Maxim Harder
 * @version 1.0.0
 * @see     \KinopoiskDev\Models\SearchMovie Для поисковых результатов фильмов
 * @see     \KinopoiskDev\Models\LinkedMovie Для связанных фильмов
 * @see     \KinopoiskDev\Models\ExternalId Для внешних идентификаторов
 */
class Movie {

	/**
	 * Конструктор для создания объекта фильма/сериала
	 *
	 * Создает новый экземпляр класса Movie с полным набором данных о произведении.
	 * Большинство параметров являются опциональными и могут быть null при отсутствии
	 * соответствующей информации в источнике данных.
	 *
	 * @see Movie::fromArray() Для создания объекта из массива данных API
	 * @see Movie::toArray() Для преобразования объекта в массив
	 *
	 * @param   int|null                                 $id                  Уникальный идентификатор фильма в системе Kinopoisk
	 * @param   ExternalId|null                          $externalId          Внешние идентификаторы (IMDB, TMDB, KinopoiskHD)
	 * @param   string|null                              $name                Название фильма на русском языке
	 * @param   string|null                              $alternativeName     Альтернативное название фильма
	 * @param   string|null                              $enName              Название фильма на английском языке
	 * @param   \KinopoiskDev\Models\Name[]|null         $names               Массив всех названий фильма на разных языках
	 * @param   MovieType|null                           $type                Тип произведения (фильм, сериал, мультфильм и т.д.)
	 * @param   int|null                                 $typeNumber          Числовой код типа произведения
	 * @param   int|null                                 $year                Год выпуска произведения
	 * @param   string|null                              $description         Полное описание сюжета фильма
	 * @param   string|null                              $shortDescription    Краткое описание фильма
	 * @param   string|null                              $slogan              Рекламный слоган фильма
	 * @param   MovieStatus|null                         $status              Статус производства (анонс, производство, вышел и т.д.)
	 * @param   \KinopoiskDev\Models\FactInMovie[]|null  $facts               Массив интересных фактов о фильме
	 * @param   int|null                                 $movieLength         Длительность фильма в минутах
	 * @param   RatingMpaa|null                          $ratingMpaa          Рейтинг MPAA (G, PG, PG-13, R, NC-17)
	 * @param   int|null                                 $ageRating           Возрастной рейтинг (6+, 12+, 16+, 18+)
	 * @param   Rating|null                              $rating              Рейтинги от различных источников
	 * @param   Votes|null                               $votes               Количество голосов по рейтингам
	 * @param   \KinopoiskDev\Models\Logo|null         $logo                Логотип фильма
	 * @param   \KinopoiskDev\Models\ShortImage|null                               $poster              Постер фильма
	 * @param   \KinopoiskDev\Models\ShortImage|null                               $backdrop            Фоновое изображение фильма
	 * @param   \KinopoiskDev\Models\VideoTypes|null        $videos              Видеоматериалы (трейлеры, тизеры)
	 * @param   \KinopoiskDev\Models\ItemName[]          $genres              Жанры фильма
	 * @param   \KinopoiskDev\Models\ItemName[]          $countries           Страны производства
	 * @param   \KinopoiskDev\Models\Person[]            $persons             Участники фильма (актеры, режиссеры и т.д.)
	 * @param   \KinopoiskDev\Models\ReviewInfo|null   $reviewInfo          Информация о рецензиях
	 * @param   \KinopoiskDev\Models\SeasonInfo[]        $seasonsInfo         Информация о сезонах (для сериалов)
	 * @param   \KinopoiskDev\Models\CurrencyValue|null     $budget              Бюджет фильма
	 * @param   \KinopoiskDev\Models\Fees|null              $fees                Кассовые сборы
	 * @param   \KinopoiskDev\Models\Premiere|null          $premiere            Даты премьер в разных странах
	 * @param   \KinopoiskDev\Models\LinkedMovie[]|null  $similarMovies       Похожие фильмы
	 * @param   \KinopoiskDev\Models\LinkedMovie[]|null  $sequelsAndPrequels  Сиквелы и приквелы
	 * @param   \KinopoiskDev\Models\Watchability|null   $watchability        Информация о просмотрах
	 * @param   \KinopoiskDev\Models\YearRange[]|null    $releaseYears        Годы выпуска (для сериалов)
	 * @param   int|null                                 $top10               Позиция в топ-10 (если есть)
	 * @param   int|null                                 $top250              Позиция в топ-250 (если есть)
	 * @param   bool                                     $isSeries            Является ли произведение сериалом
	 * @param   bool|null                                $ticketsOnSale       Доступны ли билеты к покупке
	 * @param   int|null                                 $totalSeriesLength   Общая длительность всех серий
	 * @param   int|null                                 $seriesLength        Длительность одной серии
	 * @param   \KinopoiskDev\Models\Audience[]          $audience            Информация об аудитории
	 * @param   array                                    $lists               Списки, в которые входит фильм
	 * @param   \KinopoiskDev\Models\Networks|null       $networks            Телевизионные сети
	 * @param   string|null                              $createdAt           Дата создания записи
	 * @param   string|null                              $updatedAt           Дата последнего обновления записи
	 */
	public function __construct(
		#[Getter] public readonly ?int          $id = NULL,
		#[Getter] public readonly ?ExternalId   $externalId = NULL,
		#[Getter] public readonly ?string       $name = NULL,
		#[Getter] public readonly ?string       $alternativeName = NULL,
		#[Getter] public readonly ?string       $enName = NULL,
		#[Getter] public readonly ?array        $names = [],
		#[Getter] public readonly ?MovieType    $type = NULL,
		#[Getter] public readonly ?int          $typeNumber = NULL,
		#[Getter] public readonly ?int          $year = NULL,
		#[Getter] public readonly ?string       $description = NULL,
		#[Getter] public readonly ?string       $shortDescription = NULL,
		#[Getter] public readonly ?string       $slogan = NULL,
		#[Getter] public readonly ?MovieStatus  $status = NULL,
		#[Getter] public readonly ?array        $facts = [],
		#[Getter] public readonly ?int          $movieLength = NULL,
		#[Getter] public readonly ?RatingMpaa   $ratingMpaa = NULL,
		#[Getter] public readonly ?int          $ageRating = NULL,
		#[Getter] public readonly ?Rating       $rating = NULL,
		#[Getter] public readonly ?Votes        $votes = NULL,
		#[Getter] public readonly ?Logo        $logo = NULL,
		#[Getter] public readonly ?ShortImage        $poster = NULL,
		#[Getter] public readonly ?ShortImage        $backdrop = NULL,
		#[Getter] public readonly ?VideoTypes        $videos = NULL,
		#[Getter] public readonly array         $genres = [],
		#[Getter] public readonly array         $countries = [],
		#[Getter] public readonly array         $persons = [],
		#[Getter] public readonly ?ReviewInfo        $reviewInfo = NULL,
		#[Getter] public readonly ?array        $seasonsInfo = [],
		#[Getter] public readonly ?CurrencyValue         $budget = NULL,
		#[Getter] public readonly ?Fees         $fees = NULL,
		#[Getter] public readonly ?Premiere         $premiere = NULL,
		#[Getter] public readonly ?array        $similarMovies = [],
		#[Getter] public readonly ?array        $sequelsAndPrequels = [],
		#[Getter] public readonly ?Watchability $watchability = NULL,
		#[Getter] public readonly ?array        $releaseYears = [],
		#[Getter] public readonly ?int          $top10 = NULL,
		#[Getter] public readonly ?int          $top250 = NULL,
		#[Getter] public readonly bool          $isSeries = FALSE,
		#[Getter] public readonly ?bool         $ticketsOnSale = NULL,
		#[Getter] public readonly ?int          $totalSeriesLength = NULL,
		#[Getter] public readonly ?int          $seriesLength = NULL,
		#[Getter] public readonly ?array        $audience = [],
		#[Getter] public readonly array         $lists = [],
		#[Getter] public readonly ?Networks     $networks = NULL,
		#[Getter] public readonly ?string       $createdAt = NULL,
		#[Getter] public readonly ?string       $updatedAt = NULL,
	) {}

	/**
	 * Создает объект Movie из массива данных API
	 *
	 * Фабричный метод для создания экземпляра класса Movie из массива данных,
	 * полученных от API Kinopoisk.dev. Безопасно обрабатывает отсутствующие
	 * значения и преобразует вложенные объекты в соответствующие классы.
	 *
	 * @see Movie::toArray() Для обратного преобразования в массив
	 * @see ExternalId::fromArray() Для создания внешних идентификаторов
	 * @see Rating::fromArray() Для создания рейтингов
	 * @see Votes::fromArray() Для создания голосов
	 * @see Image::fromArray() Для создания изображений
	 * @see Person::fromArray() Для создания участников
	 *
	 * @param   array  $data  Массив данных о фильме от API, содержащий все возможные поля фильма
	 *
	 * @return \KinopoiskDev\Models\Movie Новый экземпляр класса Movie с данными из массива
	 */
	public static function fromArray(array $data): Movie {
		return new self(
			id                : $data['id'],
			externalId        : isset($data['externalId'])
				? ExternalId::fromArray($data['externalId']) : NULL,
			name              : $data['name'] ?? NULL,
			alternativeName   : $data['alternativeName'] ?? NULL,
			enName            : $data['enName'] ?? NULL,
			names             : $data['names'] ?? [],
			type              : isset($data['type']) ? MovieType::tryFrom($data['type']) : NULL,
			typeNumber        : $data['typeNumber'] ?? NULL,
			year              : $data['year'] ?? NULL,
			description       : $data['description'] ?? NULL,
			shortDescription  : $data['shortDescription'] ?? NULL,
			slogan            : $data['slogan'] ?? NULL,
			status            : isset($data['status']) ? MovieStatus::tryFrom($data['status']) : NULL,
			facts             : $data['facts'] ?? [],
			movieLength       : $data['movieLength'] ?? NULL,
			ratingMpaa        : isset($data['ratingMpaa']) ? RatingMpaa::tryFrom($data['ratingMpaa']) : NULL,
			ageRating         : $data['ageRating'] ?? NULL,
			rating            : isset($data['rating']) ? Rating::fromArray($data['rating'])
				: NULL,
			votes             : isset($data['votes']) ? Votes::fromArray($data['votes'])
				: NULL,
			logo              : $data['logo'] ?? NULL,
			poster            : isset($data['poster']) ? ShortImage::fromArray($data['poster'])
				: NULL,
			backdrop          : isset($data['backdrop'])
				? ShortImage::fromArray($data['backdrop']) : NULL,
			videos            : $data['videos'] ?? NULL,
			genres            : isset($data['genres']) ? array_map(fn ($g) => 
				is_array($g) ? ItemName::fromArray($g) : $g, $data['genres']) : [],
			countries         : isset($data['countries']) ? array_map(fn ($c) => 
				is_array($c) ? ItemName::fromArray($c) : $c, $data['countries']) : [],
			persons           : isset($data['persons']) ? array_map(fn ($p) => 
				Person::fromArray($p), $data['persons']) : [],
			reviewInfo        : isset($data['reviewInfo']) && is_array($data['reviewInfo']) 
				? ReviewInfo::fromArray($data['reviewInfo']) : $data['reviewInfo'] ?? NULL,
			seasonsInfo       : isset($data['seasonsInfo']) ? array_map(fn ($s) => 
				is_array($s) ? SeasonInfo::fromArray($s) : $s, $data['seasonsInfo']) : [],
			budget            : isset($data['budget']) && is_array($data['budget']) 
				? CurrencyValue::fromArray($data['budget']) : $data['budget'] ?? NULL,
			fees              : isset($data['fees']) && is_array($data['fees']) 
				? Fees::fromArray($data['fees']) : $data['fees'] ?? NULL,
			premiere          : isset($data['premiere']) && is_array($data['premiere']) 
				? Premiere::fromArray($data['premiere']) : $data['premiere'] ?? NULL,
			similarMovies     : isset($data['similarMovies']) ? array_map(fn ($m) => 
				is_array($m) ? LinkedMovie::fromArray($m) : $m, $data['similarMovies']) : [],
			sequelsAndPrequels: isset($data['sequelsAndPrequels']) ? array_map(fn ($m) => 
				is_array($m) ? LinkedMovie::fromArray($m) : $m, $data['sequelsAndPrequels']) : [],
			watchability      : isset($data['watchability']) && is_array($data['watchability']) 
				? Watchability::fromArray($data['watchability']) : $data['watchability'] ?? NULL,
			releaseYears      : isset($data['releaseYears']) ? array_map(fn ($y) => 
				is_array($y) ? YearRange::fromArray($y) : $y, $data['releaseYears']) : [],
			top10             : $data['top10'] ?? NULL,
			top250            : $data['top250'] ?? NULL,
			isSeries          : $data['isSeries'] ?? FALSE,
			ticketsOnSale     : $data['ticketsOnSale'] ?? NULL,
			totalSeriesLength : $data['totalSeriesLength'] ?? NULL,
			seriesLength      : $data['seriesLength'] ?? NULL,
			audience          : $data['audience'] ?? [],
			lists             : $data['lists'] ?? [],
			networks          : $data['networks'] ?? NULL,
			createdAt         : $data['createdAt'] ?? NULL,
			updatedAt         : $data['updatedAt'] ?? NULL,
		);
	}

	/**
	 * Преобразует объект в массив данных
	 *
	 * Конвертирует текущий экземпляр класса Movie в массив,
	 * совместимый с форматом API Kinopoisk.dev. Используется для сериализации
	 * данных при отправке запросов к API или для экспорта данных.
	 *
	 * @see Movie::fromArray() Для создания объекта из массива
	 *
	 * @return array Массив с полными данными о фильме, содержащий все поля объекта
	 */
	public function toArray(): array {
		return [
			'id'                 => $this->id,
			'externalId'         => $this->externalId?->toArray(),
			'name'               => $this->name,
			'alternativeName'    => $this->alternativeName,
			'enName'             => $this->enName,
			'names'              => DataManager::getObjectsArray($this->names),
			'type'               => $this->type?->value,
			'typeNumber'         => $this->typeNumber,
			'year'               => $this->year,
			'description'        => $this->description,
			'shortDescription'   => $this->shortDescription,
			'slogan'             => $this->slogan,
			'status'             => $this->status?->value,
			'facts'              => DataManager::getObjectsArray($this->facts),
			'movieLength'        => $this->movieLength,
			'ratingMpaa'         => $this->ratingMpaa?->value,
			'ageRating'          => $this->ageRating,
			'rating'             => $this->rating?->toArray(),
			'votes'              => $this->votes?->toArray(),
			'logo'               => DataManager::getObjectsArray($this->logo),
			'poster'             => $this->poster?->toArray(),
			'backdrop'           => $this->backdrop?->toArray(),
			'videos'             => DataManager::getObjectsArray($this->videos),
			'genres'             => DataManager::getObjectsArray($this->genres),
			'countries'          => DataManager::getObjectsArray($this->countries),
			'persons'            => DataManager::getObjectsArray($this->persons),
			'reviewInfo'         => $this->reviewInfo?->toArray(),
			'seasonsInfo'        => DataManager::getObjectsArray($this->seasonsInfo),
			'budget'             => $this->budget?->toArray(),
			'fees'               => $this->fees?->toArray(),
			'premiere'           => $this->premiere?->toArray(),
			'similarMovies'      => DataManager::getObjectsArray($this->similarMovies),
			'sequelsAndPrequels' => DataManager::getObjectsArray($this->sequelsAndPrequels),
			'watchability'       => $this->watchability?->toArray(),
			'releaseYears'       => DataManager::getObjectsArray($this->releaseYears),
			'top10'              => $this->top10,
			'top250'             => $this->top250,
			'isSeries'           => $this->isSeries,
			'ticketsOnSale'      => $this->ticketsOnSale,
			'totalSeriesLength'  => $this->totalSeriesLength,
			'seriesLength'       => $this->seriesLength,
			'audience'           => DataManager::getObjectsArray($this->audience),
			'lists'              => $this->lists,
			'networks'           => $this->networks?->toArray(),
			'createdAt'          => $this->createdAt,
			'updatedAt'          => $this->updatedAt,
		];
	}

	/**
	 * Возвращает рейтинг фильма на Кинопоиске
	 *
	 * Извлекает рейтинг фильма из системы Кинопоиск. Возвращает null,
	 * если рейтинг не установлен или объект рейтинга отсутствует.
	 *
	 * @see Movie::getImdbRating() Для получения рейтинга IMDB
	 * @see Rating::getKp() Для альтернативного способа получения рейтинга
	 *
	 * @return float|null Рейтинг на Кинопоиске (от 0.0 до 10.0) или null, если не установлен
	 */
	public function getKinopoiskRating(): ?float {
		return $this->rating?->kp;
	}

	/**
	 * Возвращает рейтинг фильма на IMDB
	 *
	 * Извлекает рейтинг фильма из системы IMDB. Возвращает null,
	 * если рейтинг не установлен или объект рейтинга отсутствует.
	 *
	 * @see Movie::getKinopoiskRating() Для получения рейтинга Кинопоиска
	 * @see Rating::getImdb() Для альтернативного способа получения рейтинга
	 *
	 * @return float|null Рейтинг на IMDB (от 0.0 до 10.0) или null, если не установлен
	 */
	public function getImdbRating(): ?float {
		return $this->rating?->imdb;
	}

	/**
	 * Возвращает URL постера фильма
	 *
	 * Извлекает URL-адрес постера фильма из объекта изображения.
	 * Возвращает null, если постер не установлен или URL отсутствует.
	 *
	 * @see Image::getUrl() Для получения URL из объекта изображения
	 * @see Movie::getBackdropUrl() Для получения URL фонового изображения
	 *
	 * @return string|null URL-адрес постера или null, если не установлен
	 */
	public function getPosterUrl(): ?string {
		return $this->poster?->url;
	}

	/**
	 * Возвращает массив названий жанров фильма
	 *
	 * Извлекает названия жанров из массива объектов жанров и возвращает их
	 * в виде простого массива строк. Если поле 'name' отсутствует у жанра,
	 * возвращается пустая строка.
	 *
	 * @see Movie::getCountryNames() Для получения названий стран
	 * @see Movie::getGenres() Для получения полной информации о жанрах
	 *
	 * @return array Массив строк с названиями жанров
	 */
	public function getGenreNames(): array {
		return $this->genres ? array_map(fn ($genre) => $genre->name ?? '', $this->genres) : [];
	}

	/**
	 * Возвращает массив названий стран производства
	 *
	 * Извлекает названия стран из массива объектов стран и возвращает их
	 * в виде простого массива строк. Если поле 'name' отсутствует у страны,
	 * возвращается пустая строка.
	 *
	 * @see Movie::getGenreNames() Для получения названий жанров
	 * @see Movie::getCountries() Для получения полной информации о странах
	 *
	 * @return array Массив строк с названиями стран производства
	 */
	public function getCountryNames(): array {
		return $this->countries ? array_map(fn ($country) => $country->name ?? '', $this->countries) : [];
	}

	/**
	 * Возвращает URL страницы фильма в системе IMDB
	 *
	 * Формирует URL-адрес страницы фильма в системе IMDB на основе
	 * внешних идентификаторов. Возвращает null, если внешние идентификаторы
	 * отсутствуют или идентификатор IMDB не установлен.
	 *
	 * @see ExternalId::getImdbUrl() Для получения URL из внешних идентификаторов
	 * @see Movie::getTmdbUrl() Для получения URL TMDB
	 *
	 * @return string|null URL-адрес страницы фильма в IMDB или null, если не доступен
	 */
	public function getImdbUrl(): ?string {
		return $this->externalId?->getImdbUrl();
	}

	/**
	 * Возвращает URL страницы фильма в системе TMDB
	 *
	 * Формирует URL-адрес страницы фильма в системе The Movie Database (TMDB)
	 * на основе внешних идентификаторов. Возвращает null, если внешние
	 * идентификаторы отсутствуют или идентификатор TMDB не установлен.
	 *
	 * @see ExternalId::getTmdbUrl() Для получения URL из внешних идентификаторов
	 * @see Movie::getImdbUrl() Для получения URL IMDB
	 *
	 * @return string|null URL-адрес страницы фильма в TMDB или null, если не доступен
	 */
	public function getTmdbUrl(): ?string {
		return $this->externalId?->getTmdbUrl();
	}


}
