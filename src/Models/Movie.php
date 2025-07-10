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
 *
 * @version 1.0.0
 * @see     \KinopoiskDev\Models\SearchMovie Для поисковых результатов фильмов
 * @see     \KinopoiskDev\Models\LinkedMovie Для связанных фильмов
 * @see     \KinopoiskDev\Models\ExternalId Для внешних идентификаторов
 * @link    https://kinopoiskdev.readme.io/reference/moviecontroller_findonev1_4
 */
readonly class Movie implements BaseModel {

	private const int MIN_YEAR = 1888;
	private const int MAX_YEAR = 2030;

	/**
	 * Конструктор для создания объекта фильма/сериала
	 *
	 * Создает новый экземпляр класса Movie с указанными параметрами.
	 * Все параметры являются опциональными и могут быть null при отсутствии данных.
	 *
	 * @see Movie::fromArray() Для создания объекта из массива данных API
	 *
	 * @param   int|null           $id                    Уникальный идентификатор фильма
	 * @param   ExternalId|null    $externalId            Внешние идентификаторы (IMDB, TMDB и т.д.)
	 * @param   string|null        $name                  Название фильма на русском языке
	 * @param   string|null        $alternativeName       Альтернативное название
	 * @param   string|null        $enName                Название фильма на английском языке
	 * @param   array<Name>|null   $names                 Массив названий на разных языках
	 * @param   MovieType|null     $type                  Тип фильма (фильм, сериал, мини-сериал)
	 * @param   int|null           $typeNumber            Номер типа
	 * @param   int|null           $year                  Год выпуска
	 * @param   string|null        $description           Полное описание фильма
	 * @param   string|null        $shortDescription      Краткое описание
	 * @param   string|null        $slogan                Слоган фильма
	 * @param   MovieStatus|null   $status                Статус фильма
	 * @param   array<FactInMovie>|null $facts            Массив фактов о фильме
	 * @param   int|null           $movieLength           Длительность фильма в минутах
	 * @param   RatingMpaa|null    $ratingMpaa            Рейтинг MPAA
	 * @param   int|null           $ageRating             Возрастной рейтинг
	 * @param   Rating|null        $rating                Рейтинги фильма
	 * @param   Votes|null         $votes                 Голоса за фильм
	 * @param   Logo|null          $logo                  Логотип фильма
	 * @param   ShortImage|null    $poster                Постер фильма
	 * @param   ShortImage|null    $backdrop              Фоновое изображение
	 * @param   VideoTypes|null    $videos                Видео материалы
	 * @param   array<ItemName>    $genres                Массив жанров
	 * @param   array<ItemName>    $countries             Массив стран производства
	 * @param   array<PersonInMovie> $persons             Массив участников съемочной группы
	 * @param   ReviewInfo|null    $reviewInfo            Информация о рецензиях
	 * @param   array|null         $seasonsInfo           Информация о сезонах
	 * @param   CurrencyValue|null $budget                Бюджет фильма
	 * @param   Fees|null          $fees                  Кассовые сборы
	 * @param   Premiere|null      $premiere              Информация о премьере
	 * @param   array|null         $similarMovies         Похожие фильмы
	 * @param   array|null         $sequelsAndPrequels    Сиквелы и приквелы
	 * @param   Watchability|null  $watchability          Где посмотреть фильм
	 * @param   array|null         $releaseYears          Годы выпуска
	 * @param   int|null           $top10                 Позиция в топ-10
	 * @param   int|null           $top250                Позиция в топ-250
	 * @param   bool               $isSeries              Является ли сериалом
	 * @param   bool|null          $ticketsOnSale         Продаются ли билеты
	 * @param   int|null           $totalSeriesLength     Общая длительность сериала
	 * @param   int|null           $seriesLength          Длительность серии
	 * @param   array<Audience>|null $audience            Аудитория фильма
	 * @param   array<Lists>       $lists                 Списки фильмов
	 * @param   Networks|null      $networks              Сети вещания
	 * @param   string|null        $createdAt             Дата создания записи
	 * @param   string|null        $updatedAt             Дата обновления записи
	 */
	public function __construct(
		#[Getter] public ?int           $id = NULL,
		#[Getter] public ?ExternalId    $externalId = NULL,
		#[Getter] public ?string        $name = NULL,
		#[Getter] public ?string        $alternativeName = NULL,
		#[Getter] public ?string        $enName = NULL,
		#[Getter] public ?array         $names = [],
		#[Getter] public ?MovieType     $type = NULL,
		#[Getter] public ?int           $typeNumber = NULL,
		#[Getter] public ?int           $year = NULL,
		#[Getter] public ?string        $description = NULL,
		#[Getter] public ?string        $shortDescription = NULL,
		#[Getter] public ?string        $slogan = NULL,
		#[Getter] public ?MovieStatus   $status = NULL,
		#[Getter] public ?array         $facts = [],
		#[Getter] public ?int           $movieLength = NULL,
		#[Getter] public ?RatingMpaa    $ratingMpaa = NULL,
		#[Getter] public ?int           $ageRating = NULL,
		#[Getter] public ?Rating        $rating = NULL,
		#[Getter] public ?Votes         $votes = NULL,
		#[Getter] public ?Logo          $logo = NULL,
		#[Getter] public ?ShortImage    $poster = NULL,
		#[Getter] public ?ShortImage    $backdrop = NULL,
		#[Getter] public ?VideoTypes    $videos = NULL,
		#[Getter] public array          $genres = [],
		#[Getter] public array          $countries = [],
		#[Getter] public array          $persons = [],
		#[Getter] public ?ReviewInfo    $reviewInfo = NULL,
		#[Getter] public ?array         $seasonsInfo = [],
		#[Getter] public ?CurrencyValue $budget = NULL,
		#[Getter] public ?Fees          $fees = NULL,
		#[Getter] public ?Premiere      $premiere = NULL,
		#[Getter] public ?array         $similarMovies = [],
		#[Getter] public ?array         $sequelsAndPrequels = [],
		#[Getter] public ?Watchability  $watchability = NULL,
		#[Getter] public ?array         $releaseYears = [],
		#[Getter] public ?int           $top10 = NULL,
		#[Getter] public ?int           $top250 = NULL,
		#[Getter] public bool           $isSeries = FALSE,
		#[Getter] public ?bool          $ticketsOnSale = NULL,
		#[Getter] public ?int           $totalSeriesLength = NULL,
		#[Getter] public ?int           $seriesLength = NULL,
		#[Getter] public ?array         $audience = [],
		#[Getter] public array          $lists = [],
		#[Getter] public ?Networks      $networks = NULL,
		#[Getter] public ?string        $createdAt = NULL,
		#[Getter] public ?string        $updatedAt = NULL,
	) {}

	/**
	 * Создает объект Movie из массива данных API
	 *
	 * Фабричный метод для создания экземпляра класса Movie из массива данных,
	 * полученных от API Kinopoisk.dev. Безопасно обрабатывает отсутствующие
	 * значения, устанавливая их в null или пустые массивы.
	 *
	 * @see Movie::toArray() Для обратного преобразования в массив
	 *
	 * @param   array<string, mixed>  $data  Массив данных фильма от API
	 *
	 * @return static Новый экземпляр класса Movie
	 *
	 */
	public static function fromArray(array $data): static {
		return new static(
			id: $data['id'] ?? NULL,
			externalId: isset($data['externalId']) ? ExternalId::fromArray($data['externalId']) : NULL,
			name: $data['name'] ?? NULL,
			alternativeName: $data['alternativeName'] ?? NULL,
			enName: $data['enName'] ?? NULL,
			names: isset($data['names']) ? DataManager::parseObjectArray($data['names'], Name::class) : [],
			type: isset($data['type']) ? MovieType::tryFrom($data['type']) : NULL,
			typeNumber: $data['typeNumber'] ?? NULL,
			year: $data['year'] ?? NULL,
			description: $data['description'] ?? NULL,
			shortDescription: $data['shortDescription'] ?? NULL,
			slogan: $data['slogan'] ?? NULL,
			status: isset($data['status']) ? MovieStatus::tryFrom($data['status']) : NULL,
			facts: isset($data['facts']) ? DataManager::parseObjectArray($data['facts'], FactInMovie::class) : [],
			movieLength: $data['movieLength'] ?? NULL,
			ratingMpaa: isset($data['ratingMpaa']) ? RatingMpaa::tryFrom($data['ratingMpaa']) : NULL,
			ageRating: $data['ageRating'] ?? NULL,
			rating: isset($data['rating']) ? Rating::fromArray($data['rating']) : NULL,
			votes: isset($data['votes']) ? Votes::fromArray($data['votes']) : NULL,
			logo: isset($data['logo']) ? Logo::fromArray($data['logo']) : NULL,
			poster: isset($data['poster']) ? ShortImage::fromArray($data['poster']) : NULL,
			backdrop: isset($data['backdrop']) ? ShortImage::fromArray($data['backdrop']) : NULL,
			videos: isset($data['videos']) ? VideoTypes::fromArray($data['videos']) : NULL,
			genres: isset($data['genres']) ? DataManager::parseObjectArray($data['genres'], ItemName::class) : [],
			countries: isset($data['countries']) ? DataManager::parseObjectArray($data['countries'], ItemName::class) : [],
			persons: isset($data['persons']) ? DataManager::parseObjectArray($data['persons'], PersonInMovie::class) : [],
			reviewInfo: isset($data['reviewInfo']) ? ReviewInfo::fromArray($data['reviewInfo']) : NULL,
			seasonsInfo: $data['seasonsInfo'] ?? [],
			budget: isset($data['budget']) ? CurrencyValue::fromArray($data['budget']) : NULL,
			fees: isset($data['fees']) ? Fees::fromArray($data['fees']) : NULL,
			premiere: isset($data['premiere']) ? Premiere::fromArray($data['premiere']) : NULL,
			similarMovies: $data['similarMovies'] ?? [],
			sequelsAndPrequels: $data['sequelsAndPrequels'] ?? [],
			watchability: isset($data['watchability']) ? Watchability::fromArray($data['watchability']) : NULL,
			releaseYears: $data['releaseYears'] ?? [],
			top10: $data['top10'] ?? NULL,
			top250: $data['top250'] ?? NULL,
			isSeries: $data['isSeries'] ?? FALSE,
			ticketsOnSale: $data['ticketsOnSale'] ?? NULL,
			totalSeriesLength: $data['totalSeriesLength'] ?? NULL,
			seriesLength: $data['seriesLength'] ?? NULL,
			audience: isset($data['audience']) ? DataManager::parseObjectArray($data['audience'], Audience::class) : [],
			lists: isset($data['lists']) ? DataManager::parseObjectArray($data['lists'], Lists::class) : [],
			networks: isset($data['networks']) ? Networks::fromArray($data['networks']) : NULL,
			createdAt: $data['createdAt'] ?? NULL,
			updatedAt: $data['updatedAt'] ?? NULL,
		);
	}

	/**
	 * Преобразует объект в массив данных
	 *
	 * Конвертирует текущий экземпляр класса Movie в массив,
	 * совместимый с форматом API Kinopoisk.dev. Используется для
	 * сериализации данных при отправке запросов к API.
	 *
	 * @see Movie::fromArray() Для создания объекта из массива
	 * @return array<string, mixed> Массив с данными фильма
	 *
	 */
	public function toArray(bool $includeNulls = true): array {
		$data = [
			'id'                  => $this->id,
			'externalId'          => $this->externalId?->toArray(),
			'name'                => $this->name,
			'alternativeName'     => $this->alternativeName,
			'enName'              => $this->enName,
			'names'               => DataManager::getObjectsArray($this->names),
			'type'                => $this->type?->value,
			'typeNumber'          => $this->typeNumber,
			'year'                => $this->year,
			'description'         => $this->description,
			'shortDescription'    => $this->shortDescription,
			'slogan'              => $this->slogan,
			'status'              => $this->status?->value,
			'facts'               => DataManager::getObjectsArray($this->facts),
			'movieLength'         => $this->movieLength,
			'ratingMpaa'          => $this->ratingMpaa?->value,
			'ageRating'           => $this->ageRating,
			'rating'              => $this->rating?->toArray(),
			'votes'               => $this->votes?->toArray(),
			'logo'                => $this->logo?->toArray(),
			'poster'              => $this->poster?->toArray(),
			'backdrop'            => $this->backdrop?->toArray(),
			'videos'              => $this->videos?->toArray(),
			'genres'              => DataManager::getObjectsArray($this->genres),
			'countries'           => DataManager::getObjectsArray($this->countries),
			'persons'             => DataManager::getObjectsArray($this->persons),
			'reviewInfo'          => $this->reviewInfo?->toArray(),
			'seasonsInfo'         => $this->seasonsInfo,
			'budget'              => $this->budget?->toArray(),
			'fees'                => $this->fees?->toArray(),
			'premiere'            => $this->premiere?->toArray(),
			'similarMovies'       => $this->similarMovies,
			'sequelsAndPrequels'  => $this->sequelsAndPrequels,
			'watchability'        => $this->watchability?->toArray(),
			'releaseYears'        => $this->releaseYears,
			'top10'               => $this->top10,
			'top250'              => $this->top250,
			'isSeries'            => $this->isSeries,
			'ticketsOnSale'       => $this->ticketsOnSale,
			'totalSeriesLength'   => $this->totalSeriesLength,
			'seriesLength'        => $this->seriesLength,
			'audience'            => DataManager::getObjectsArray($this->audience),
			'lists'               => $this->lists,
			'networks'            => $this->networks?->toArray(),
			'createdAt'           => $this->createdAt,
			'updatedAt'           => $this->updatedAt,
		];

		// Удаляем null значения если не нужно их включать
		if (!$includeNulls) {
			return array_filter($data, fn($value) => $value !== null);
		}

		return $data;
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
	 * @return array<string> Массив строк с названиями жанров
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
	 * @return array<string> Массив строк с названиями стран производства
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

	/**
	 * Валидирует данные модели
	 *
	 * Проверяет корректность основных полей объекта Movie.
	 * Проверяет наличие обязательного идентификатора и валидность опциональных полей.
	 *
	 * @return bool True если данные валидны
	 * @throws \KinopoiskDev\Exceptions\ValidationException При ошибке валидации
	 */
	public function validate(): bool {
		// Основная валидация - должен быть ID
		if ($this->id === null || $this->id <= 0) {
			throw new \KinopoiskDev\Exceptions\ValidationException('Movie ID is required and must be positive');
		}

		// Валидация года
		if ($this->year !== null && ($this->year < self::MIN_YEAR || $this->year > self::MAX_YEAR)) {
			throw new \KinopoiskDev\Exceptions\ValidationException('Movie year must be between ' . self::MIN_YEAR . ' and ' . self::MAX_YEAR);
		}

		// Валидация рейтингов
		if ($this->rating?->kp !== null && ($this->rating->kp < 0 || $this->rating->kp > 10)) {
			throw new \KinopoiskDev\Exceptions\ValidationException('Kinopoisk rating must be between 0 and 10');
		}

		if ($this->rating?->imdb !== null && ($this->rating->imdb < 0 || $this->rating->imdb > 10)) {
			throw new \KinopoiskDev\Exceptions\ValidationException('IMDB rating must be between 0 and 10');
		}

		// Валидация возрастного рейтинга
		if ($this->ageRating !== null && ($this->ageRating < 0 || $this->ageRating > 21)) {
			throw new \KinopoiskDev\Exceptions\ValidationException('Age rating must be between 0 and 21');
		}

		return true;
	}

	/**
	 * Возвращает JSON представление объекта
	 *
	 * Сериализует объект Movie в JSON строку с использованием указанных флагов.
	 * По умолчанию включает поддержку Unicode и выбрасывает исключения при ошибках.
	 *
	 * @param int $flags Флаги для json_encode
	 * @return string JSON строка
	 * @throws \JsonException При ошибке сериализации
	 */
	public function toJson(int $flags = JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE): string {
		$json = json_encode($this->toArray(), $flags);
		if ($json === false) {
			throw new \JsonException('Ошибка кодирования JSON');
		}
		return $json;
	}

	/**
	 * Создает объект из JSON строки
	 *
	 * Парсит JSON строку и создает из неё объект Movie.
	 * Автоматически валидирует полученные данные.
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
