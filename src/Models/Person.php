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
 * @version 1.0.0
 * @see     \KinopoiskDev\Models\PersonInMovie Для информации о персоне в контексте фильма
 * @see     \KinopoiskDev\Enums\PersonProfession Для типов профессий персон
 * @see     \KinopoiskDev\Enums\PersonSex Для типов пола персон
 */
class Person {

	/**
	 * Конструктор для создания объекта персоны
	 *
	 * Создает новый экземпляр класса Person с полным набором данных о персоне.
	 * Большинство параметров являются опциональными и могут быть null при отсутствии
	 * соответствующей информации в источнике данных.
	 *
	 * @see Person::fromArray() Для создания объекта из массива данных API
	 * @see Person::toArray() Для преобразования объекта в массив
	 *
	 * @param   int                                $id           Уникальный идентификатор персоны в системе Kinopoisk
	 * @param   string|null                        $photo        URL фотографии персоны
	 * @param   string|null                        $name         Имя персоны на русском языке
	 * @param   string|null                        $enName       Имя персоны на английском языке
	 * @param   string|null                        $description  Описание или роль персоны
	 * @param   string|null                        $profession   Профессия персоны на русском языке
	 * @param   PersonProfession|null              $enProfession Профессия персоны (enum)
	 * @param   PersonSex|null                     $sex          Пол персоны (enum)
	 * @param   int|null                           $growth       Рост персоны в сантиметрах
	 * @param   string|null                        $birthday     Дата рождения в формате ISO
	 * @param   string|null                        $death        Дата смерти в формате ISO
	 * @param   int|null                           $age          Возраст персоны
	 * @param   array                              $birthPlace   Массив мест рождения
	 * @param   array                              $deathPlace   Массив мест смерти
	 * @param   array                              $spouses      Массив данных о супругах
	 * @param   int|null                           $countAwards  Количество наград
	 * @param   array                              $facts        Массив интересных фактов о персоне
	 * @param   array                              $movies       Массив фильмов с участием персоны
	 * @param   string|null                        $updatedAt    Дата последнего обновления записи
	 * @param   string|null                        $createdAt    Дата создания записи
	 */
	public function __construct(
		public readonly int               $id,
		public readonly ?string           $photo = NULL,
		public readonly ?string           $name = NULL,
		public readonly ?string           $enName = NULL,
		public readonly ?string           $description = NULL,
		public readonly ?string           $profession = NULL,
		public readonly ?PersonProfession $enProfession = NULL,
		public readonly ?PersonSex        $sex = NULL,
		public readonly ?int              $growth = NULL,
		public readonly ?string           $birthday = NULL,
		public readonly ?string           $death = NULL,
		public readonly ?int              $age = NULL,
		public readonly array             $birthPlace = [],
		public readonly array             $deathPlace = [],
		public readonly array             $spouses = [],
		public readonly ?int              $countAwards = NULL,
		public readonly array             $facts = [],
		public readonly array             $movies = [],
		public readonly ?string           $updatedAt = NULL,
		public readonly ?string           $createdAt = NULL,
	) {}

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
	 */
	public static function fromArray(array $data): self {
		return new self(
			id          : $data['id'],
			photo       : $data['photo'] ?? NULL,
			name        : $data['name'] ?? NULL,
			enName      : $data['enName'] ?? NULL,
			description : $data['description'] ?? NULL,
			profession  : $data['profession'] ?? NULL,
			enProfession: DataManager::parseEnumValue($data, 'enProfession', PersonProfession::class),
			sex         : DataManager::parseEnumValue($data, 'sex', PersonSex::class),
			growth      : $data['growth'] ?? NULL,
			birthday    : $data['birthday'] ?? NULL,
			death       : $data['death'] ?? NULL,
			age         : $data['age'] ?? NULL,
			birthPlace  : $data['birthPlace'] ?? [],
			deathPlace  : $data['deathPlace'] ?? [],
			spouses     : $data['spouses'] ?? [],
			countAwards : $data['countAwards'] ?? NULL,
			facts       : $data['facts'] ?? [],
			movies      : $data['movies'] ?? [],
			updatedAt   : $data['updatedAt'] ?? NULL,
			createdAt   : $data['createdAt'] ?? NULL,
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
	public function toArray(): array {
		return [
			'id'           => $this->id,
			'photo'        => $this->photo,
			'name'         => $this->name,
			'enName'       => $this->enName,
			'description'  => $this->description,
			'profession'   => $this->profession,
			'enProfession' => $this->enProfession?->value,
			'sex'          => $this->sex?->value,
			'growth'       => $this->growth,
			'birthday'     => $this->birthday,
			'death'        => $this->death,
			'age'          => $this->age,
			'birthPlace'   => $this->birthPlace,
			'deathPlace'   => $this->deathPlace,
			'spouses'      => $this->spouses,
			'countAwards'  => $this->countAwards,
			'facts'        => $this->facts,
			'movies'       => DataManager::getObjectsArray($this->movies),
			'updatedAt'    => $this->updatedAt,
			'createdAt'    => $this->createdAt,
		];
	}

	/**
	 * Возвращает наиболее подходящее имя персоны
	 *
	 * Определяет и возвращает наиболее подходящее имя персоны, отдавая
	 * предпочтение русскому имени, если оно доступно. Если русское имя
	 * отсутствует, возвращает английское имя.
	 *
	 * @return string|null Наиболее подходящее имя персоны или null, если имя не задано
	 */
	public function getBestName(): ?string {
		return $this->name ?? $this->enName;
	}

	/**
	 * Возвращает URL фотографии персоны
	 *
	 * Предоставляет прямой доступ к URL-адресу фотографии персоны.
	 * Может использоваться для отображения изображения персоны в интерфейсе.
	 *
	 * @return string|null URL-адрес фотографии или null, если фотография отсутствует
	 */
	public function getPhotoUrl(): ?string {
		return $this->photo;
	}

	/**
	 * Возвращает профессию персоны на русском языке
	 *
	 * Предоставляет доступ к названию профессии персоны на русском языке.
	 * Может использоваться для отображения профессии в русскоязычном интерфейсе.
	 *
	 * @see Person::getProfessionEn() Для получения профессии на английском языке
	 *
	 * @return string|null Название профессии на русском языке или null, если не задано
	 */
	public function getProfessionRu(): ?string {
		return $this->profession;
	}

	/**
	 * Возвращает профессию персоны на английском языке
	 *
	 * Предоставляет доступ к профессии персоны в виде enum значения.
	 * Может использоваться для программной обработки типа профессии.
	 *
	 * @see Person::getProfessionRu() Для получения профессии на русском языке
	 * @see PersonProfession Для списка возможных профессий
	 *
	 * @return PersonProfession|null Enum значение профессии или null, если не задано
	 */
	public function getProfessionEn(): ?PersonProfession {
		return $this->enProfession;
	}

	/**
	 * Возвращает описание роли персоны
	 *
	 * Предоставляет доступ к описанию роли или другой информации о персоне.
	 * Может содержать дополнительные сведения о роли в фильме или общую информацию.
	 *
	 * @return string|null Описание роли или null, если описание отсутствует
	 */
	public function getRoleDescription(): ?string {
		return $this->description;
	}

	/**
	 * Проверяет, является ли персона актером
	 *
	 * Определяет, является ли персона актером, на основе значения профессии.
	 * Проверяет как enum значение, так и текстовое представление профессии.
	 *
	 * @see Person::isDirector() Для проверки, является ли персона режиссером
	 * @see Person::isWriter() Для проверки, является ли персона сценаристом
	 * @see PersonProfession::ACTOR Для enum значения профессии актера
	 *
	 * @return bool true, если персона является актером, иначе false
	 */
	public function isActor(): bool {
		return $this->enProfession === PersonProfession::ACTOR ||
		       $this->profession === 'актеры' ||
		       $this->profession === 'актер';
	}

	/**
	 * Проверяет, является ли персона режиссером
	 *
	 * Определяет, является ли персона режиссером, на основе значения профессии.
	 * Проверяет как enum значение, так и текстовое представление профессии.
	 *
	 * @see Person::isActor() Для проверки, является ли персона актером
	 * @see Person::isWriter() Для проверки, является ли персона сценаристом
	 * @see PersonProfession::DIRECTOR Для enum значения профессии режиссера
	 *
	 * @return bool true, если персона является режиссером, иначе false
	 */
	public function isDirector(): bool {
		return $this->enProfession === PersonProfession::DIRECTOR ||
		       $this->profession === 'режиссеры' ||
		       $this->profession === 'режиссер';
	}

	/**
	 * Проверяет, является ли персона сценаристом
	 *
	 * Определяет, является ли персона сценаристом, на основе значения профессии.
	 * Проверяет как enum значение, так и текстовое представление профессии.
	 *
	 * @see Person::isActor() Для проверки, является ли персона актером
	 * @see Person::isDirector() Для проверки, является ли персона режиссером
	 * @see PersonProfession::WRITER Для enum значения профессии сценариста
	 *
	 * @return bool true, если персона является сценаристом, иначе false
	 */
	public function isWriter(): bool {
		return $this->enProfession === PersonProfession::WRITER ||
		       $this->profession === 'сценаристы' ||
		       $this->profession === 'сценарист';
	}

	/**
	 * Проверяет, является ли персона продюсером
	 *
	 * Определяет, является ли персона продюсером, на основе значения профессии.
	 * Проверяет как enum значение, так и текстовое представление профессии.
	 *
	 * @see Person::isActor() Для проверки, является ли персона актером
	 * @see Person::isDirector() Для проверки, является ли персона режиссером
	 * @see PersonProfession::PRODUCER Для enum значения профессии продюсера
	 *
	 * @return bool true, если персона является продюсером, иначе false
	 */
	public function isProducer(): bool {
		return $this->enProfession === PersonProfession::PRODUCER ||
		       $this->profession === 'продюсеры' ||
		       $this->profession === 'продюсер';
	}

	/**
	 * Проверяет, является ли персона композитором
	 *
	 * Определяет, является ли персона композитором, на основе значения профессии.
	 * Проверяет как enum значение, так и текстовое представление профессии.
	 *
	 * @see Person::isActor() Для проверки, является ли персона актером
	 * @see Person::isDirector() Для проверки, является ли персона режиссером
	 * @see PersonProfession::COMPOSER Для enum значения профессии композитора
	 *
	 * @return bool true, если персона является композитором, иначе false
	 */
	public function isComposer(): bool {
		return $this->enProfession === PersonProfession::COMPOSER ||
		       $this->profession === 'композиторы' ||
		       $this->profession === 'композитор';
	}

	/**
	 * Возвращает категорию роли персоны
	 *
	 * Определяет основную категорию профессии персоны на основе
	 * проверки различных типов профессий. Возвращает строковое
	 * представление категории из enum PersonProfession.
	 *
	 * @see Person::isActor() Для проверки, является ли персона актером
	 * @see Person::isDirector() Для проверки, является ли персона режиссером
	 * @see PersonProfession Для списка возможных категорий профессий
	 *
	 * @return string Строковое представление категории профессии
	 */
	public function getRoleCategory(): string {
		if ($this->isActor()) {
			return PersonProfession::ACTOR->value;
		}
		if ($this->isDirector()) {
			return PersonProfession::DIRECTOR->value;
		}
		if ($this->isWriter()) {
			return PersonProfession::WRITER->value;
		}
		if ($this->isProducer()) {
			return PersonProfession::PRODUCER->value;
		}
		if ($this->isComposer()) {
			return PersonProfession::COMPOSER->value;
		}

		return PersonProfession::OTHER->value;
	}

	/**
	 * Возвращает отформатированное имя персоны с ролью
	 *
	 * Формирует строку, содержащую имя персоны и описание роли в скобках,
	 * если описание роли доступно. Если имя персоны отсутствует, использует
	 * идентификатор персоны в качестве запасного варианта.
	 *
	 * @see Person::getBestName() Для получения наиболее подходящего имени
	 *
	 * @return string Отформатированное имя с ролью или только имя, если роль отсутствует
	 */
	public function getFormattedNameWithRole(): string {
		$name = $this->getBestName() ?? "Person #{$this->id}";
		$role = $this->description;

		if ($role) {
			return "{$name} ({$role})";
		}

		return $name;
	}

	/**
	 * Возвращает строковое представление персоны
	 *
	 * Реализует магический метод __toString для преобразования объекта
	 * в строку. Возвращает отформатированное имя персоны с ролью.
	 *
	 * @see Person::getFormattedNameWithRole() Для получения отформатированного имени с ролью
	 *
	 * @return string Строковое представление персоны
	 */
	public function __toString(): string {
		return $this->getFormattedNameWithRole();
	}

}
