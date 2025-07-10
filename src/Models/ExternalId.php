<?php

declare(strict_types=1);

namespace KinopoiskDev\Models;

use Lombok\Getter;

/**
 * Класс для представления внешних идентификаторов фильмов
 *
 * Содержит идентификаторы фильма в различных внешних системах, таких как
 * Kinopoisk HD, IMDB и The Movie Database (TMDB). Предоставляет методы для
 * работы с идентификаторами, включая получение URL-адресов и проверку
 * существования идентификаторов.
 *
 * @package KinopoiskDev\Models
 * @since   1.0.0
 * @author  Maxim Harder
 *
 * @version 1.0.0
 * @see     \KinopoiskDev\Models\LinkedMovie Для связанных фильмов с внешними идентификаторами
 * @see     \KinopoiskDev\Models\Movie Для использования внешних идентификаторов в фильмах
 */
readonly class ExternalId implements BaseModel {

	/**
	 * Конструктор для создания объекта внешних идентификаторов
	 *
	 * Создает новый экземпляр класса ExternalId с указанными идентификаторами
	 * из внешних систем. Все параметры являются опциональными и могут быть null
	 * при отсутствии соответствующего идентификатора.
	 *
	 * @see ExternalId::fromArray() Для создания объекта из массива данных API
	 * @see ExternalId::toArray() Для преобразования объекта в массив
	 *
	 * @param   string|null  $kpHD  Идентификатор фильма в системе Kinopoisk HD (null если не указан)
	 * @param   string|null  $imdb  Идентификатор фильма в системе IMDB (null если не указан)
	 * @param   int|null     $tmdb  Идентификатор фильма в системе TMDB (null если не указан)
	 */
	public function __construct(
		#[Getter] public ?string $kpHD = NULL,
		#[Getter] public ?string $imdb = NULL,
		#[Getter] public ?int    $tmdb = NULL,
	) {}

	/**
	 * Возвращает строковое представление внешних идентификаторов
	 *
	 * Магический метод для преобразования объекта в строку. Формирует читаемое
	 * представление всех доступных внешних идентификаторов, разделенных запятыми.
	 * Если идентификаторы отсутствуют, возвращает сообщение об их отсутствии.
	 *
	 * @see ExternalId::getAvailableIds() Для получения доступных идентификаторов
	 * @see ExternalId::hasAnyId() Для проверки наличия идентификаторов
	 *
	 * @return string Строковое представление внешних идентификаторов в формате
	 *                "KP HD: {id}, IMDB: {id}, TMDB: {id}" или "No external IDs"
	 */
	public function __toString(): string {
		$parts = [];

		if ($this->kpHD) {
			$parts[] = "KP HD: {$this->kpHD}";
		}

		if ($this->imdb) {
			$parts[] = "IMDB: {$this->imdb}";
		}

		if ($this->tmdb) {
			$parts[] = "TMDB: {$this->tmdb}";
		}

		return empty($parts) ? 'No external IDs' : implode(', ', $parts);
	}

	/**
	 * Создает объект ExternalId из массива данных API
	 *
	 * Фабричный метод для создания экземпляра класса ExternalId из массива
	 * данных, полученных от API Kinopoisk.dev. Безопасно обрабатывает отсутствующие
	 * значения, устанавливая их в null. Преобразует строковое значение TMDB в целое число.
	 *
	 * @see ExternalId::toArray() Для обратного преобразования в массив
	 *
	 * @param   array<string, mixed>  $data  Массив данных о внешних идентификаторах от API, содержащий ключи:
	 *                        - kpHD: string|null - идентификатор Kinopoisk HD
	 *                        - imdb: string|null - идентификатор IMDB
	 *                        - tmdb: string|int|null - идентификатор TMDB
	 *
	 * @return self Новый экземпляр класса ExternalId с данными из массива
	 */
	public static function fromArray(array $data): static {
		return new self(
			kpHD: $data['kpHD'] ?? NULL,
			imdb: $data['imdb'] ?? NULL,
			tmdb: isset($data['tmdb']) ? (int) $data['tmdb'] : NULL,
		);
	}

	/**
	 * Преобразует объект в массив данных
	 *
	 * Конвертирует текущий экземпляр класса ExternalId в массив,
	 * совместимый с форматом API Kinopoisk.dev. Используется для сериализации
	 * данных при отправке запросов к API или для экспорта данных в JSON.
	 *
	 * @see ExternalId::fromArray() Для создания объекта из массива
	 *
	 * @return array<string, mixed> Массив с данными о внешних идентификаторах, содержащий ключи:
	 *               - kpHD: string|null - идентификатор Kinopoisk HD
	 *               - imdb: string|null - идентификатор IMDB
	 *               - tmdb: int|null - идентификатор TMDB
	 */
	public function toArray(bool $includeNulls = true): array {
		return [
			'kpHD' => $this->kpHD,
			'imdb' => $this->imdb,
			'tmdb' => $this->tmdb,
		];
	}

	/**
	 * Генерирует URL-адрес страницы фильма в системе IMDB
	 *
	 * Формирует полный URL-адрес страницы фильма в системе IMDB на основе
	 * сохраненного идентификатора. Возвращает null, если идентификатор IMDB
	 * не установлен.
	 *
	 * @see ExternalId::getImdbId() Для получения идентификатора IMDB
	 * @see ExternalId::hasImdbId() Для проверки существования идентификатора
	 *
	 * @return string|null URL-адрес страницы фильма в IMDB или null, если идентификатор не установлен
	 */
	public function getImdbUrl(): ?string {
		return $this->imdb ? "https://www.imdb.com/title/{$this->imdb}/" : NULL;
	}

	/**
	 * Генерирует URL-адрес страницы фильма в системе TMDB
	 *
	 * Формирует полный URL-адрес страницы фильма в системе The Movie Database (TMDB)
	 * на основе сохраненного идентификатора. Возвращает null, если идентификатор TMDB
	 * не установлен.
	 *
	 * @see ExternalId::getTmdbId() Для получения идентификатора TMDB
	 * @see ExternalId::hasTmdbId() Для проверки существования идентификатора
	 *
	 * @return string|null URL-адрес страницы фильма в TMDB или null, если идентификатор не установлен
	 */
	public function getTmdbUrl(): ?string {
		return $this->tmdb ? "https://www.themoviedb.org/movie/{$this->tmdb}"
			: NULL;
	}

	/**
	 * Проверяет наличие хотя бы одного внешнего идентификатора
	 *
	 * Определяет, установлен ли хотя бы один из внешних идентификаторов
	 * (Kinopoisk HD, IMDB или TMDB). Возвращает true, если найден хотя бы один
	 * не null идентификатор.
	 *
	 * @see ExternalId::hasImdbId() Для проверки конкретного идентификатора IMDB
	 * @see ExternalId::hasTmdbId() Для проверки конкретного идентификатора TMDB
	 * @see ExternalId::hasKinopoiskHdId() Для проверки конкретного идентификатора Kinopoisk HD
	 *
	 * @return bool true, если установлен хотя бы один идентификатор, false в противном случае
	 */
	public function hasAnyId(): bool {
		return $this->kpHD !== NULL || $this->imdb !== NULL
		       || $this->tmdb !== NULL;
	}

	/**
	 * Проверяет наличие идентификатора IMDB
	 *
	 * Определяет, установлен ли идентификатор фильма в системе IMDB.
	 * Возвращает true, если идентификатор не равен null.
	 *
	 * @see ExternalId::getImdbId() Для получения идентификатора IMDB
	 * @see ExternalId::hasAnyId() Для проверки любого идентификатора
	 *
	 * @return bool true, если идентификатор IMDB установлен, false в противном случае
	 */
	public function hasImdbId(): bool {
		return $this->imdb !== NULL;
	}

	/**
	 * Проверяет наличие идентификатора TMDB
	 *
	 * Определяет, установлен ли идентификатор фильма в системе The Movie Database (TMDB).
	 * Возвращает true, если идентификатор не равен null.
	 *
	 * @see ExternalId::getTmdbId() Для получения идентификатора TMDB
	 * @see ExternalId::hasAnyId() Для проверки любого идентификатора
	 *
	 * @return bool true, если идентификатор TMDB установлен, false в противном случае
	 */
	public function hasTmdbId(): bool {
		return $this->tmdb !== NULL;
	}

	/**
	 * Проверяет наличие идентификатора Kinopoisk HD
	 *
	 * Определяет, установлен ли идентификатор фильма в системе Kinopoisk HD.
	 * Возвращает true, если идентификатор не равен null.
	 *
	 * @see ExternalId::getKinopoiskHdId() Для получения идентификатора Kinopoisk HD
	 * @see ExternalId::hasAnyId() Для проверки любого идентификатора
	 *
	 * @return bool true, если идентификатор Kinopoisk HD установлен, false в противном случае
	 */
	public function hasKinopoiskHdId(): bool {
		return $this->kpHD !== NULL;
	}

	/**
	 * Возвращает все доступные идентификаторы в виде ассоциативного массива
	 *
	 * Собирает все установленные (не null) внешние идентификаторы в ассоциативный массив,
	 * где ключами являются названия систем, а значениями - соответствующие идентификаторы.
	 * Отсутствующие идентификаторы не включаются в результат.
	 *
	 * @see ExternalId::hasAnyId() Для проверки наличия идентификаторов
	 * @see ExternalId::toArray() Для получения всех идентификаторов включая null
	 *
	 * @return array<string, string|int> Ассоциативный массив с доступными идентификаторами, где:
	 *               - ключ 'kpHD' содержит идентификатор Kinopoisk HD (если установлен)
	 *               - ключ 'imdb' содержит идентификатор IMDB (если установлен)
	 *               - ключ 'tmdb' содержит идентификатор TMDB (если установлен)
	 */
	public function getAvailableIds(): array {
		$ids = [];

		if ($this->kpHD !== NULL) {
			$ids['kpHD'] = $this->kpHD;
		}

		if ($this->imdb !== NULL) {
			$ids['imdb'] = $this->imdb;
		}

		if ($this->tmdb !== NULL) {
			$ids['tmdb'] = $this->tmdb;
		}

		return $ids;
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
