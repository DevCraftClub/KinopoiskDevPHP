<?php

declare(strict_types=1);

namespace KinopoiskDev\Models;

use KinopoiskDev\Enums\PersonProfession;
use KinopoiskDev\Enums\PersonSex;
use KinopoiskDev\Utils\DataManager;

/**
 * Класс для представления персоны из API Kinopoisk.dev
 *
 * Представляет информацию об актере, режиссере, сценаристе или другом участнике
 * кинопроизводства. Содержит биографические данные, профессиональную информацию,
 * фильмографию и другие связанные сведения.
 *
 * @package KinopoiskDev\Models
 * @since   1.0.0
 * @author  Maxim Harder
 *
 * @version 1.0.0
 * @see     \KinopoiskDev\Models\PersonInMovie Для информации о персоне в контексте фильма
 * @see     \KinopoiskDev\Enums\PersonProfession Для типов профессий персон
 * @see     \KinopoiskDev\Enums\PersonSex Для типов пола персон
 * @see     \KinopoiskDev\Models\MeiliPersonEntity Родительский класс
 * @link    https://kinopoiskdev.readme.io/reference/personcontroller_findonev1_4
 */
readonly class Person extends MeiliPersonEntity {

	/**
	 * Конструктор для создания объекта персоны
	 *
	 * Создает новый экземпляр класса Person с полным набором данных о персоне.
	 * Все свойства класса являются для обеспечения неизменности данных.
	 * Конструктор также вызывает родительский конструктор для инициализации
	 * базовых свойств наследуемых от MeiliPersonEntity.
	 *
	 * @see Person::fromArray() Для создания объекта из массива данных API
	 * @see Person::toArray() Для преобразования объекта в массив
	 * @see MeiliPersonEntity::__construct() Конструктор родительского класса
	 *
	 * @param   int                                   $id           Уникальный идентификатор персоны в системе Kinopoisk
	 * @param   string|null                           $name         Имя персоны на русском языке
	 * @param   string|null                           $enName       Имя персоны на английском языке
	 * @param   string|null                           $photo        URL фотографии персоны
	 * @param   PersonSex|null                        $sex          Пол персоны (enum значение)
	 * @param   int|null                              $growth       Рост персоны в сантиметрах
	 * @param   string|null                           $birthday     Дата рождения в формате ISO 8601
	 * @param   string|null                           $death        Дата смерти в формате ISO 8601
	 * @param   int|null                              $age          Возраст персоны в годах
	 * @param   \KinopoiskDev\Models\BirthPlace[]     $birthPlace   Массив мест рождения персоны (пустой массив по умолчанию)
	 * @param   \KinopoiskDev\Models\DeathPlace[]     $deathPlace   Массив мест смерти персоны (пустой массив по умолчанию)
	 * @param   PersonProfession[]|null               $profession   Массив профессий персоны (может быть null если профессии неизвестны)
	 * @param   \KinopoiskDev\Models\Spouses[]        $spouses      Массив данных о супругах персоны
	 * @param   int                                   $countAwards  Количество наград персоны (по умолчанию 0)
	 * @param   \KinopoiskDev\Models\FactInPerson[]   $facts        Массив интересных фактов о персоне
	 * @param   \KinopoiskDev\Models\MovieInPerson[]  $movies       Массив фильмов с участием персоны
	 * @param   string|null                           $updatedAt    Дата последнего обновления записи в формате ISO 8601
	 * @param   string|null                           $createdAt    Дата создания записи в формате ISO 8601
	 */

	public function __construct(
		public int        $id,
		public ?string    $name = NULL,
		public ?string    $enName = NULL,
		public ?string    $photo = NULL,
		public ?PersonSex $sex = NULL,
		public ?int       $growth = NULL,
		public ?string    $birthday = NULL,
		public ?string    $death = NULL,
		public ?int       $age = NULL,
		public array      $birthPlace = [],
		public array      $deathPlace = [],
		public array      $spouses = [],
		public int        $countAwards = 0,
		public ?array     $profession = [],
		public array      $facts = [],
		public array      $movies = [],
		public ?string    $updatedAt = NULL,
		public ?string    $createdAt = NULL,
	) {
		parent::__construct($id, $name, $name, $photo, $sex, $growth, $birthday, $death, $age, $birthPlace, $deathPlace, $profession);
	}

	/**
	 * Создает объект Person из массива данных API
	 *
	 * Фабричный метод для создания экземпляра класса Person из массива данных,
	 * полученных от API Kinopoisk.dev. Безопасно обрабатывает отсутствующие
	 * значения и преобразует вложенные объекты в соответствующие классы.
	 *
	 * @see Person::toArray() Для обратного преобразования в массив
	 * @see DataManager::parseEnumValue() Для преобразования enum значений
	 *
	 * @param   array  $data  Массив данных о персоне от API, содержащий все возможные поля персоны
	 *
	 * @return \KinopoiskDev\Models\Person Новый экземпляр класса Person с данными из массива
	 * @throws \KinopoiskDev\Exceptions\KinopoiskDevException
	 */
	public static function fromArray(array $data): static {
		return new self(
			id         : $data['id'],
			name       : $data['name'] ?? NULL,
			enName     : $data['enName'] ?? NULL,
			photo      : $data['photo'] ?? NULL,
			sex        : DataManager::parseEnumValue($data, 'sex', PersonSex::class),
			growth     : $data['growth'] ?? NULL,
			birthday   : $data['birthday'] ?? NULL,
			death      : $data['death'] ?? NULL,
			age        : $data['age'] ?? NULL,
			birthPlace : $data['birthPlace'] ?? [],
			deathPlace : $data['deathPlace'] ?? [],
			spouses    : $data['spouses'] ?? [],
			countAwards: $data['countAwards'] ?? 0,
			profession : $data['profession'] ? array_map(fn (PersonProfession $pr) => $pr->value, $data['profession']) : [],
			facts      : $data['facts'] ?? [],
			movies     : $data['movies'] ?? [],
			updatedAt  : $data['updatedAt'] ?? NULL,
			createdAt  : $data['createdAt'] ?? NULL,
		);
	}

	/**
	 * Преобразует объект в массив данных
	 *
	 * Конвертирует текущий экземпляр класса Person в массив,
	 * совместимый с форматом API Kinopoisk.dev. Используется для сериализации
	 * данных при отправке запросов к API или для экспорта данных.
	 *
	 * @see Person::fromArray() Для создания объекта из массива
	 * @see DataManager::getObjectsArray() Для преобразования массива объектов в массив массивов
	 *
	 * @return array Массив с полными данными о персоне, содержащий все поля объекта
	 */
	public function toArray(bool $includeNulls = true): array {
		return [
			'id'          => $this->id,
			'photo'       => $this->photo,
			'name'        => $this->name,
			'enName'      => $this->enName,
			'profession'  => $this->profession,
			'sex'         => $this->sex?->value,
			'growth'      => $this->growth,
			'birthday'    => $this->birthday,
			'death'       => $this->death,
			'age'         => $this->age,
			'birthPlace'  => $this->birthPlace,
			'deathPlace'  => $this->deathPlace,
			'spouses'     => $this->spouses,
			'countAwards' => $this->countAwards,
			'facts'       => $this->facts,
			'movies'      => DataManager::getObjectsArray($this->movies),
			'updatedAt'   => $this->updatedAt,
			'createdAt'   => $this->createdAt,
		];
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
