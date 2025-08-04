<?php

namespace KinopoiskDev\Models;

use KinopoiskDev\Enums\PersonProfession;
use KinopoiskDev\Enums\PersonSex;

/**
 * Сущность персоны для индексации в поисковой системе MeiliSearch
 *
 * Этот класс представляет структуру данных персоны для индексации в поисковой системе MeiliSearch.
 * Содержит основную информацию о персоне, включая биографические данные, профессиональную информацию
 * и места рождения/смерти. Все свойства являются  для обеспечения неизменности данных.
 *
 * @package   KinopoiskDev\Models
 * @since     1.0.0
 * @author    Maxim Harder
 * @version   1.0.0
 *
 * @see       \KinopoiskDev\Enums\PersonSex Enum для определения пола персоны
 * @see       \KinopoiskDev\Models\Person Основная модель персоны
 */
class MeiliPersonEntity extends AbstractBaseModel {

	/**
	 * Создает новый экземпляр сущности персоны для MeiliSearch
	 *
	 * Конструктор инициализирует все свойства персоны значениями по умолчанию.
	 * Все параметры являются именованными для удобства использования и поддержки
	 * автоматической генерации объектов из массивов данных API.
	 *
	 * @since 1.0.0
	 *
	 * @param   int                                $id          Уникальный идентификатор персоны в базе данных
	 * @param   string|null                        $name        Имя персоны на русском языке (может быть null для неизвестных персон)
	 * @param   string|null                        $enName      Имя персоны на английском языке (может быть null если отсутствует перевод)
	 * @param   string|null                        $photo       URL фотографии персоны (может быть null если фото недоступно)
	 * @param   PersonSex|null                     $sex         Пол персоны из enum PersonSex (может быть null если не определен)
	 * @param   int|null                           $growth      Рост персоны в сантиметрах (может быть null если неизвестен)
	 * @param   string|null                        $birthday    Дата рождения в формате ISO 8601 (может быть null если неизвестна)
	 * @param   string|null                        $death       Дата смерти в формате ISO 8601 (может быть null если персона жива или дата неизвестна)
	 * @param   int|null                           $age         Возраст персоны в годах (может быть null если невозможно вычислить)
	 * @param   \KinopoiskDev\Models\BirthPlace[]  $birthPlace  Массив мест рождения персоны (пустой массив по умолчанию)
	 * @param   \KinopoiskDev\Models\DeathPlace[]  $deathPlace  Массив мест смерти персоны (пустой массив по умолчанию)
	 * @param   PersonProfession[]|null            $profession  Массив профессий персоны (может быть null если профессии неизвестны)
	 *
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
		public ?array     $profession = [],
	) {}

	/**
	 * Создает объект MeiliPersonEntity из массива данных API
	 *
	 * Фабричный метод для создания экземпляра класса MeiliPersonEntity из массива данных,
	 * полученных от API Kinopoisk.dev. Безопасно обрабатывает отсутствующие
	 * значения, устанавливая их в null или пустые массивы.
	 *
	 * @see MeiliPersonEntity::toArray() Для обратного преобразования в массив
	 *
	 * @param   array<string, mixed>  $data  Массив данных персоны от API
	 *
	 * @return static Новый экземпляр класса MeiliPersonEntity
	 *
	 */
	public static function fromArray(array $data): static {
		return new self(
			id        : $data['id'] ?? 0,
			name      : $data['name'] ?? NULL,
			enName    : $data['enName'] ?? NULL,
			photo     : $data['photo'] ?? NULL,
			sex       : isset($data['sex']) ? PersonSex::tryFrom($data['sex']) : NULL,
			growth    : $data['growth'] ?? NULL,
			birthday  : $data['birthday'] ?? NULL,
			death     : $data['death'] ?? NULL,
			age       : $data['age'] ?? NULL,
			birthPlace: $data['birthPlace'] ?? [],
			deathPlace: $data['deathPlace'] ?? [],
			profession: isset($data['profession']) && is_array($data['profession']) ?
				array_map(fn ($pr) => is_string($pr) ? $pr : (is_object($pr) && property_exists($pr, 'value') ? $pr->value : $pr),
					$data['profession']) : [],
		);
	}

	/**
	 * Валидирует данные модели
	 *
	 * @return bool True если данные валидны
	 */
	public function validate(): bool {
		return TRUE; // Basic validation - override in specific models if needed
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
	 * Возвращает категории ролей персоны
	 *
	 * Определяет все категории профессий персоны на основе проверки различных типов профессий.
	 * Использует современный подход с array_filter для оптимизации производительности
	 * и избежания повторяющихся if-конструкций. Метод создает карту соответствия между
	 * значениями enum профессий и результатами методов проверки, затем фильтрует только
	 * те профессии, которые присутствуют у данной персоны.
	 *
	 * @since   1.0.0
	 * @version 1.0.0
	 * @see     PersonProfession Enum с возможными категориями профессий
	 * @see     self::isActor() Проверка, является ли персона актером
	 * @see     self::isDirector() Проверка, является ли персона режиссером
	 * @see     self::isWriter() Проверка, является ли персона сценаристом
	 * @see     self::isProducer() Проверка, является ли персона продюсером
	 * @see     self::isComposer() Проверка, является ли персона композитором
	 * @see     self::isOperator() Проверка, является ли персона оператором
	 * @see     self::isDesigner() Проверка, является ли персона художником
	 * @see     self::isEditor() Проверка, является ли персона монтажером
	 * @see     self::isVoiceActor() Проверка, является ли персона актером дубляжа
	 * @see     self::isOtherProfession() Проверка других профессий персоны
	 *
	 * @return array<string> Массив строковых значений (value) enum PersonProfession для активных профессий персоны
	 */
	public function getRoleCategory(): array {
		$professionMap = [
			PersonProfession::ACTOR->value       => $this->isActor(),
			PersonProfession::DIRECTOR->value    => $this->isDirector(),
			PersonProfession::WRITER->value      => $this->isWriter(),
			PersonProfession::PRODUCER->value    => $this->isProducer(),
			PersonProfession::COMPOSER->value    => $this->isComposer(),
			PersonProfession::OPERATOR->value    => $this->isOperator(),
			PersonProfession::DESIGN->value      => $this->isDesigner(),
			PersonProfession::EDITOR->value      => $this->isEditor(),
			PersonProfession::VOICE_ACTOR->value => $this->isVoiceActor(),
			PersonProfession::OTHER->value       => $this->isOtherProfession(),
		];

		return array_keys(array_filter($professionMap, static fn (bool $hasRole): bool => $hasRole));
	}

	/**
	 * Проверяет, является ли персона актером
	 *
	 * Определяет, является ли данная персона актером на основе значений в массиве профессий.
	 * Метод выполняет строгую проверку (с использованием оператора ===) наличия строкового
	 * значения enum PersonProfession::ACTOR в массиве profession. Возвращает true, если
	 * среди профессий персоны найдена профессия актера.
	 *
	 * @see PersonProfession::ACTOR Enum значение профессии актера
	 * @see self::isDirector() Проверка, является ли персона режиссером
	 * @see self::isWriter() Проверка, является ли персона сценаристом
	 * @see self::isProducer() Проверка, является ли персона продюсером
	 * @see self::isComposer() Проверка, является ли персона композитором
	 * @see self::isOperator() Проверка, является ли персона оператором
	 * @see self::isDesigner() Проверка, является ли персона художником
	 * @see self::isEditor() Проверка, является ли персона монтажером
	 * @see self::isVoiceActor() Проверка, является ли персона актером дубляжа
	 * @see self::isOtherProfession() Проверка других профессий персоны
	 *
	 * @return bool true, если персона является актером, false в противном случае
	 */
	public function isActor(): bool {
		return $this->profession !== NULL && in_array(PersonProfession::ACTOR->value, $this->profession, TRUE);
	}

	/**
	 * Проверяет, является ли персона режиссером
	 *
	 * Метод проверяет наличие профессии "режиссер" в массиве профессий персоны.
	 * Использует строгое сравнение для точного соответствия значения enum
	 * PersonProfession::DIRECTOR среди всех профессий персоны.
	 *
	 * @see PersonProfession::DIRECTOR Константа для профессии режиссера
	 * @see self::isActor() Проверка, является ли персона актером
	 * @see self::isWriter() Проверка, является ли персона сценаристом
	 * @see self::isProducer() Проверка, является ли персона продюсером
	 * @see self::isComposer() Проверка, является ли персона композитором
	 * @see self::isOperator() Проверка, является ли персона оператором
	 * @see self::isDesigner() Проверка, является ли персона художником
	 * @see self::isEditor() Проверка, является ли персона монтажером
	 * @see self::isVoiceActor() Проверка, является ли персона актером дубляжа
	 * @see self::isOtherProfession() Проверка других профессий персоны
	 *
	 * @return bool true, если персона является режиссером, false в противном случае
	 */
	public function isDirector(): bool {
		return $this->profession !== NULL && in_array(PersonProfession::DIRECTOR->value, $this->profession, TRUE);
	}

	/**
	 * Проверяет, является ли персона сценаристом
	 *
	 * Метод проверяет наличие профессии "сценарист" в массиве профессий персоны.
	 * Использует строгое сравнение для точного соответствия значения enum
	 * PersonProfession::WRITER среди всех профессий персоны.
	 *
	 * @see PersonProfession::WRITER Константа для профессии сценариста
	 * @see self::isActor() Проверка, является ли персона актером
	 * @see self::isDirector() Проверка, является ли персона режиссером
	 * @see self::isProducer() Проверка, является ли персона продюсером
	 * @see self::isComposer() Проверка, является ли персона композитором
	 * @see self::isOperator() Проверка, является ли персона оператором
	 * @see self::isDesigner() Проверка, является ли персона художником
	 * @see self::isEditor() Проверка, является ли персона монтажером
	 * @see self::isVoiceActor() Проверка, является ли персона актером дубляжа
	 * @see self::isOtherProfession() Проверка других профессий персоны
	 *
	 * @return bool true, если персона является сценаристом, false в противном случае
	 */
	public function isWriter(): bool {
		return $this->profession !== NULL && in_array(PersonProfession::WRITER->value, $this->profession, TRUE);
	}

	/**
	 * Проверяет, является ли персона продюсером
	 *
	 * Метод проверяет наличие профессии "продюсер" в массиве профессий персоны.
	 * Использует строгое сравнение для точного соответствия значения enum
	 * PersonProfession::PRODUCER среди всех профессий персоны.
	 *
	 * @see PersonProfession::PRODUCER Константа для профессии продюсера
	 * @see self::isActor() Проверка, является ли персона актером
	 * @see self::isDirector() Проверка, является ли персона режиссером
	 * @see self::isWriter() Проверка, является ли персона сценаристом
	 * @see self::isComposer() Проверка, является ли персона композитором
	 * @see self::isOperator() Проверка, является ли персона оператором
	 * @see self::isDesigner() Проверка, является ли персона художником
	 * @see self::isEditor() Проверка, является ли персона монтажером
	 * @see self::isVoiceActor() Проверка, является ли персона актером дубляжа
	 * @see self::isOtherProfession() Проверка других профессий персоны
	 *
	 * @return bool true, если персона является продюсером, false в противном случае
	 */
	public function isProducer(): bool {
		return $this->profession !== NULL && in_array(PersonProfession::PRODUCER->value, $this->profession, TRUE);
	}

	/**
	 * Проверяет, является ли персона композитором
	 *
	 * Метод проверяет наличие профессии "композитор" в массиве профессий персоны.
	 * Использует строгое сравнение для точного соответствия значения enum
	 * PersonProfession::COMPOSER среди всех профессий персоны.
	 *
	 * @see PersonProfession::COMPOSER Константа для профессии композитора
	 * @see self::isActor() Проверка, является ли персона актером
	 * @see self::isDirector() Проверка, является ли персона режиссером
	 * @see self::isWriter() Проверка, является ли персона сценаристом
	 * @see self::isProducer() Проверка, является ли персона продюсером
	 * @see self::isOperator() Проверка, является ли персона оператором
	 * @see self::isDesigner() Проверка, является ли персона художником
	 * @see self::isEditor() Проверка, является ли персона монтажером
	 * @see self::isVoiceActor() Проверка, является ли персона актером дубляжа
	 * @see self::isOtherProfession() Проверка других профессий персоны
	 *
	 * @return bool true, если персона является композитором, false в противном случае
	 */
	public function isComposer(): bool {
		return $this->profession !== NULL && in_array(PersonProfession::COMPOSER->value, $this->profession, TRUE);
	}

	/**
	 * Проверяет, является ли персона оператором
	 *
	 * Метод проверяет наличие профессии "оператор" в массиве профессий персоны.
	 * Использует строгое сравнение для точного соответствия значения enum
	 * PersonProfession::OPERATOR среди всех профессий персоны.
	 *
	 * @see PersonProfession::OPERATOR Константа для профессии оператора
	 * @see self::isActor() Проверка, является ли персона актером
	 * @see self::isDirector() Проверка, является ли персона режиссером
	 * @see self::isWriter() Проверка, является ли персона сценаристом
	 * @see self::isProducer() Проверка, является ли персона продюсером
	 * @see self::isComposer() Проверка, является ли персона композитором
	 * @see self::isDesigner() Проверка, является ли персона художником
	 * @see self::isEditor() Проверка, является ли персона монтажером
	 * @see self::isVoiceActor() Проверка, является ли персона актером дубляжа
	 * @see self::isOtherProfession() Проверка других профессий персоны
	 *
	 * @return bool true, если персона является оператором, false в противном случае
	 */
	public function isOperator(): bool {
		return $this->profession !== NULL && in_array(PersonProfession::OPERATOR->value, $this->profession, TRUE);
	}

	/**
	 * Проверяет, является ли персона художником (постановщиком)
	 *
	 * Метод проверяет наличие профессии "художник" в массиве профессий персоны.
	 * Использует строгое сравнение для точного соответствия значения enum
	 * PersonProfession::DESIGN среди всех профессий персоны.
	 *
	 * @see PersonProfession::DESIGN Константа для профессии художника
	 * @see self::isActor() Проверка, является ли персона актером
	 * @see self::isDirector() Проверка, является ли персона режиссером
	 * @see self::isWriter() Проверка, является ли персона сценаристом
	 * @see self::isProducer() Проверка, является ли персона продюсером
	 * @see self::isComposer() Проверка, является ли персона композитором
	 * @see self::isOperator() Проверка, является ли персона оператором
	 * @see self::isEditor() Проверка, является ли персона монтажером
	 * @see self::isVoiceActor() Проверка, является ли персона актером дубляжа
	 * @see self::isOtherProfession() Проверка других профессий персоны
	 *
	 * @return bool true, если персона является художником, false в противном случае
	 */
	public function isDesigner(): bool {
		return $this->profession !== NULL && in_array(PersonProfession::DESIGN->value, $this->profession, TRUE);
	}

	/**
	 * Проверяет, является ли персона монтажёром
	 *
	 * Метод проверяет наличие профессии "монтажер" в массиве профессий персоны.
	 * Использует строгое сравнение для точного соответствия значения enum
	 * PersonProfession::EDITOR среди всех профессий персоны.
	 *
	 * @see PersonProfession::EDITOR Константа для профессии монтажёра
	 * @see self::isActor() Проверка, является ли персона актером
	 * @see self::isDirector() Проверка, является ли персона режиссером
	 * @see self::isWriter() Проверка, является ли персона сценаристом
	 * @see self::isProducer() Проверка, является ли персона продюсером
	 * @see self::isComposer() Проверка, является ли персона композитором
	 * @see self::isOperator() Проверка, является ли персона оператором
	 * @see self::isDesigner() Проверка, является ли персона художником
	 * @see self::isVoiceActor() Проверка, является ли персона актером дубляжа
	 * @see self::isOtherProfession() Проверка других профессий персоны
	 *
	 * @return bool true, если персона является монтажёром, false в противном случае
	 */
	public function isEditor(): bool {
		return $this->profession !== NULL && in_array(PersonProfession::EDITOR->value, $this->profession, TRUE);
	}

	/**
	 * Проверяет, является ли персона актёром дубляжа
	 *
	 * Метод проверяет наличие профессии "актер дубляжа" в массиве профессий персоны.
	 * Использует строгое сравнение для точного соответствия значения enum
	 * PersonProfession::VOICE_ACTOR среди всех профессий персоны.
	 *
	 * @see PersonProfession::VOICE_ACTOR Константа для актёра дубляжа
	 * @see self::isActor() Проверка, является ли персона актером
	 * @see self::isDirector() Проверка, является ли персона режиссером
	 * @see self::isWriter() Проверка, является ли персона сценаристом
	 * @see self::isProducer() Проверка, является ли персона продюсером
	 * @see self::isComposer() Проверка, является ли персона композитором
	 * @see self::isOperator() Проверка, является ли персона оператором
	 * @see self::isDesigner() Проверка, является ли персона художником
	 * @see self::isEditor() Проверка, является ли персона монтажером
	 * @see self::isOtherProfession() Проверка других профессий персоны
	 *
	 * @return bool true, если персона является актером дубляжа, false в противном случае
	 */
	public function isVoiceActor(): bool {
		return $this->profession !== NULL && in_array(PersonProfession::VOICE_ACTOR->value, $this->profession, TRUE);
	}

	/**
	 * Проверяет, является ли персона иной профессии
	 *
	 * Метод проверяет наличие профессии "другой" в массиве профессий персоны.
	 * Использует строгое сравнение для точного соответствия значения enum
	 * PersonProfession::DIRECTOR среди всех профессий персоны.
	 *
	 * @see PersonProfession::DIRECTOR Константа для других профессий персоны
	 * @see self::isActor() Проверка, является ли персона актером
	 * @see self::isDirector() Проверка, является ли персона режиссером
	 * @see self::isWriter() Проверка, является ли персона сценаристом
	 * @see self::isProducer() Проверка, является ли персона продюсером
	 * @see self::isComposer() Проверка, является ли персона композитором
	 * @see self::isOperator() Проверка, является ли персона оператором
	 * @see self::isDesigner() Проверка, является ли персона художником
	 * @see self::isEditor() Проверка, является ли персона монтажером
	 * @see self::isVoiceActor() Проверка, является ли персона актером дубляжа
	 *
	 * @return bool true, если персона является другой профессии, false в противном случае
	 */
	public function isOtherProfession(): bool {
		return $this->profession !== NULL && in_array(PersonProfession::OTHER->value, $this->profession, TRUE);
	}

	/**
	 * Преобразует объект сущности персоны в массив данных
	 *
	 * Конвертирует все свойства сущности персоны в ассоциативный массив
	 * для сериализации, передачи в API или сохранения в хранилище данных.
	 * Метод выполняет безопасное преобразование nullable enum значений
	 * в их строковые представления через использование null-safe оператора.
	 *
	 * Возвращаемый массив содержит как базовые свойства персоны (id, имена,
	 * фото), так и дополнительные данные (профессии на русском и английском
	 * языках, полученные через соответствующие методы).
	 *
	 * @since 1.0.0
	 * @see   getBestName() Для получения наиболее подходящего имени персоны
	 * @see   getProfessionRu() Для получения массива профессий на русском языке
	 * @see   getProfessionEn() Для получения массива профессий на английском языке
	 * @see   \KinopoiskDev\Enums\PersonSex Enum для значений пола персоны
	 * @see   \KinopoiskDev\Enums\PersonProfession Enum для значений профессий персоны
	 *
	 * @return array<string, mixed> Ассоциативный массив с данными персоны, содержащий ключи:
	 *               - id: int - уникальный идентификатор персоны
	 *               - photo: string|null - URL фотографии персоны
	 *               - name: string|null - русское имя персоны
	 *               - enName: string|null - английское имя персоны
	 *               - profession: array|null - массив объектов профессий персоны
	 *               - professionRu: array - массив профессий на русском языке
	 *               - professionEn: array - массив профессий на английском языке
	 *               - sex: string|null - пол персоны (значение enum или null)
	 *               - growth: int|null - рост персоны в сантиметрах
	 *               - birthday: string|null - дата рождения в формате строки
	 *               - death: string|null - дата смерти в формате строки
	 *               - age: int|null - возраст персоны в годах
	 *               - birthPlace: array - массив мест рождения
	 *               - deathPlace: array - массив мест смерти
	 */
	public function toArray(bool $includeNulls = TRUE): array {
		return [
			'id'           => $this->id,
			'photo'        => $this->photo,
			'name'         => $this->name,
			'enName'       => $this->enName,
			'profession'   => $this->profession,
			'professionRu' => $this->getProfessionRu(),
			'professionEn' => $this->getProfessionEn(),
			'sex'          => $this->sex?->value,
			'growth'       => $this->growth,
			'birthday'     => $this->birthday,
			'death'        => $this->death,
			'age'          => $this->age,
			'birthPlace'   => $this->birthPlace,
			'deathPlace'   => $this->deathPlace,
		];
	}

	/**
	 * Возвращает профессию персоны на русском языке
	 *
	 * Предоставляет доступ к названию профессии персоны на русском языке.
	 * Может использоваться для отображения профессии в русскоязычном интерфейсе.
	 *
	 * @see Person::getProfessionEn() Для получения профессии на английском языке
	 *
	 * @return array<string> Название профессии на русском языке или null, если не задано
	 */
	public function getProfessionRu(): array {
		return array_map(function ($professionValue) {
			$profession = is_string($professionValue)
				? PersonProfession::tryFrom($professionValue)
				: $professionValue;

			return $profession?->getRussianName() ?? $professionValue;
		}, $this->profession ?? []);
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
	 * @return array<string> Enum значение профессии или null, если не задано
	 */
	public function getProfessionEn(): array {
		return array_map(function ($professionValue) {
			$profession = is_string($professionValue)
				? PersonProfession::tryFrom($professionValue)
				: $professionValue;

			return $profession?->getEnglishName() ?? $professionValue;
		}, $this->profession ?? []);
	}

}
