<?php

declare(strict_types=1);

namespace KinopoiskDev\Models;

/**
 * Класс для представления рейтингов фильма из различных источников
 *
 * Содержит рейтинги фильма/сериала из различных источников, включая
 * Кинопоиск, IMDB, TMDB, а также оценки кинокритиков и ожидания зрителей.
 * Используется для отображения и анализа популярности и качества произведения.
 *
 * @package KinopoiskDev\Models
 * @since   1.0.0
 * @author  Maxim Harder
 *
 * @version 1.0.0
 * @see     \KinopoiskDev\Models\Movie::getRating() Для получения рейтинга фильма
 * @see     \KinopoiskDev\Models\Votes Для информации о количестве голосов
 */
class Rating {

	/**
	 * Конструктор для создания объекта рейтингов
	 *
	 * Создает новый экземпляр класса Rating с рейтингами из различных источников.
	 * Все параметры являются опциональными и могут быть null при отсутствии
	 * соответствующей информации в источнике данных.
	 *
	 * @see Rating::fromArray() Для создания объекта из массива данных API
	 * @see Rating::toArray() Для преобразования объекта в массив
	 *
	 * @param   float|null  $kp                  Рейтинг на Кинопоиске (от 0.0 до 10.0)
	 * @param   float|null  $imdb                Рейтинг на IMDB (от 0.0 до 10.0)
	 * @param   float|null  $tmdb                Рейтинг на The Movie Database (от 0.0 до 10.0)
	 * @param   float|null  $filmCritics         Рейтинг кинокритиков (от 0.0 до 100.0)
	 * @param   float|null  $russianFilmCritics  Рейтинг российских кинокритиков (от 0.0 до 100.0)
	 * @param   float|null  $await               Рейтинг ожидания (от 0.0 до 100.0)
	 */
	public function __construct(
		public readonly ?float $kp = NULL,
		public readonly ?float $imdb = NULL,
		public readonly ?float $tmdb = NULL,
		public readonly ?float $filmCritics = NULL,
		public readonly ?float $russianFilmCritics = NULL,
		public readonly ?float $await = NULL,
	) {}

	/**
	 * Возвращает строковое представление рейтингов
	 *
	 * Реализует магический метод __toString для преобразования объекта
	 * в строку. Формирует строку, содержащую основные рейтинги в удобочитаемом
	 * формате, разделенные запятыми.
	 *
	 * @return string Строковое представление рейтингов или 'No ratings', если рейтинги отсутствуют
	 */
	public function __toString(): string {
		$parts = [];

		if ($this->kp) {
			$parts[] = "KP: {$this->kp}";
		}
		if ($this->imdb) {
			$parts[] = "IMDB: {$this->imdb}";
		}
		if ($this->tmdb) {
			$parts[] = "TMDB: {$this->tmdb}";
		}
		if ($this->filmCritics) {
			$parts[] = "Critics: {$this->filmCritics}";
		}

		return empty($parts) ? 'No ratings' : implode(', ', $parts);
	}

	/**
	 * Создает объект Rating из массива данных API
	 *
	 * Фабричный метод для создания экземпляра класса Rating из массива данных,
	 * полученных от API Kinopoisk.dev. Безопасно обрабатывает отсутствующие
	 * значения и преобразует строковые значения в числовые.
	 *
	 * @see Rating::toArray() Для обратного преобразования в массив
	 *
	 * @param   array  $data  Массив данных о рейтингах от API
	 *
	 * @return \KinopoiskDev\Models\Rating Новый экземпляр класса Rating с данными из массива
	 */
	public static function fromArray(array $data): self {
		return new self(
			kp                : isset($data['kp']) ? (float) $data['kp'] : NULL,
			imdb              : isset($data['imdb']) ? (float) $data['imdb'] : NULL,
			tmdb              : isset($data['tmdb']) ? (float) $data['tmdb'] : NULL,
			filmCritics       : isset($data['filmCritics'])
				? (float) $data['filmCritics'] : NULL,
			russianFilmCritics: isset($data['russianFilmCritics'])
				? (float) $data['russianFilmCritics'] : NULL,
			await             : isset($data['await']) ? (float) $data['await'] : NULL,
		);
	}

	/**
	 * Преобразует объект в массив данных
	 *
	 * Конвертирует текущий экземпляр класса Rating в массив,
	 * совместимый с форматом API Kinopoisk.dev. Используется для сериализации
	 * данных при отправке запросов к API или для экспорта данных.
	 *
	 * @see Rating::fromArray() Для создания объекта из массива
	 *
	 * @return array Массив с данными о рейтингах из различных источников
	 */
	public function toArray(): array {
		return [
			'kp'                 => $this->kp,
			'imdb'               => $this->imdb,
			'tmdb'               => $this->tmdb,
			'filmCritics'        => $this->filmCritics,
			'russianFilmCritics' => $this->russianFilmCritics,
			'await'              => $this->await,
		];
	}

	/**
	 * Возвращает рейтинг фильма на Кинопоиске
	 *
	 * Предоставляет доступ к рейтингу фильма в системе Кинопоиск.
	 * Рейтинг представлен в виде числа с плавающей точкой в диапазоне от 0.0 до 10.0.
	 *
	 * @see Rating::getImdbRating() Для получения рейтинга IMDB
	 * @see Rating::getTmdbRating() Для получения рейтинга TMDB
	 *
	 * @return float|null Рейтинг на Кинопоиске или null, если рейтинг отсутствует
	 */
	public function getKinopoiskRating(): ?float {
		return $this->kp;
	}

	/**
	 * Возвращает рейтинг фильма на IMDB
	 *
	 * Предоставляет доступ к рейтингу фильма в системе Internet Movie Database (IMDB).
	 * Рейтинг представлен в виде числа с плавающей точкой в диапазоне от 0.0 до 10.0.
	 *
	 * @see Rating::getKinopoiskRating() Для получения рейтинга Кинопоиска
	 * @see Rating::getTmdbRating() Для получения рейтинга TMDB
	 *
	 * @return float|null Рейтинг на IMDB или null, если рейтинг отсутствует
	 */
	public function getImdbRating(): ?float {
		return $this->imdb;
	}

	/**
	 * Возвращает рейтинг фильма на TMDB
	 *
	 * Предоставляет доступ к рейтингу фильма в системе The Movie Database (TMDB).
	 * Рейтинг представлен в виде числа с плавающей точкой в диапазоне от 0.0 до 10.0.
	 *
	 * @see Rating::getKinopoiskRating() Для получения рейтинга Кинопоиска
	 * @see Rating::getImdbRating() Для получения рейтинга IMDB
	 *
	 * @return float|null Рейтинг на TMDB или null, если рейтинг отсутствует
	 */
	public function getTmdbRating(): ?float {
		return $this->tmdb;
	}

	/**
	 * Возвращает рейтинг фильма от кинокритиков
	 *
	 * Предоставляет доступ к рейтингу фильма от международных кинокритиков.
	 * Рейтинг представлен в виде числа с плавающей точкой в диапазоне от 0.0 до 100.0.
	 *
	 * @see Rating::getRussianFilmCriticsRating() Для получения рейтинга российских кинокритиков
	 *
	 * @return float|null Рейтинг кинокритиков или null, если рейтинг отсутствует
	 */
	public function getFilmCriticsRating(): ?float {
		return $this->filmCritics;
	}

	/**
	 * Возвращает рейтинг фильма от российских кинокритиков
	 *
	 * Предоставляет доступ к рейтингу фильма от российских кинокритиков.
	 * Рейтинг представлен в виде числа с плавающей точкой в диапазоне от 0.0 до 100.0.
	 *
	 * @see Rating::getFilmCriticsRating() Для получения рейтинга международных кинокритиков
	 *
	 * @return float|null Рейтинг российских кинокритиков или null, если рейтинг отсутствует
	 */
	public function getRussianFilmCriticsRating(): ?float {
		return $this->russianFilmCritics;
	}

	/**
	 * Возвращает рейтинг ожидания фильма
	 *
	 * Предоставляет доступ к рейтингу ожидания фильма, который отражает
	 * интерес аудитории к еще не вышедшему фильму.
	 * Рейтинг представлен в виде числа с плавающей точкой в диапазоне от 0.0 до 100.0.
	 *
	 * @return float|null Рейтинг ожидания или null, если рейтинг отсутствует
	 */
	public function getAwaitRating(): ?float {
		return $this->await;
	}

	/**
	 * Возвращает наивысший доступный рейтинг
	 *
	 * Анализирует все доступные рейтинги и возвращает наивысший из них.
	 * Учитывает только основные рейтинги (Кинопоиск, IMDB, TMDB, кинокритики),
	 * игнорируя рейтинги ожидания и российских кинокритиков.
	 *
	 * @see Rating::getAverageRating() Для получения среднего рейтинга
	 *
	 * @return float|null Наивысший рейтинг или null, если все рейтинги отсутствуют
	 */
	public function getHighestRating(): ?float {
		$ratings = array_filter([
			$this->kp,
			$this->imdb,
			$this->tmdb,
			$this->filmCritics,
		]);

		return empty($ratings) ? NULL : max($ratings);
	}

	/**
	 * Возвращает средний рейтинг из всех доступных
	 *
	 * Вычисляет среднее арифметическое всех доступных рейтингов.
	 * Учитывает только основные рейтинги (Кинопоиск, IMDB, TMDB, кинокритики),
	 * игнорируя рейтинги ожидания и российских кинокритиков.
	 *
	 * @see Rating::getHighestRating() Для получения наивысшего рейтинга
	 *
	 * @return float|null Средний рейтинг или null, если все рейтинги отсутствуют
	 */
	public function getAverageRating(): ?float {
		$ratings = array_filter([
			$this->kp,
			$this->imdb,
			$this->tmdb,
			$this->filmCritics,
		]);

		return empty($ratings) ? NULL : array_sum($ratings) / count($ratings);
	}

	/**
	 * Проверяет наличие хотя бы одного рейтинга
	 *
	 * Определяет, существует ли хотя бы один рейтинг из любого источника.
	 * Учитывает все возможные рейтинги, включая рейтинги ожидания и критиков.
	 *
	 * @see Rating::getAvailableRatings() Для получения всех доступных рейтингов
	 *
	 * @return bool true, если существует хотя бы один рейтинг, иначе false
	 */
	public function hasAnyRating(): bool {
		return $this->kp !== NULL || $this->imdb !== NULL
		       || $this->tmdb !== NULL
		       || $this->filmCritics !== NULL
		       || $this->russianFilmCritics !== NULL
		       || $this->await !== NULL;
	}

	/**
	 * Возвращает все доступные рейтинги в виде ассоциативного массива
	 *
	 * Собирает все ненулевые рейтинги в ассоциативный массив, где ключи
	 * соответствуют источникам рейтингов, а значения - самим рейтингам.
	 * Используется для получения полного набора рейтингов в удобном формате.
	 *
	 * @see Rating::hasAnyRating() Для проверки наличия хотя бы одного рейтинга
	 *
	 * @return array Ассоциативный массив доступных рейтингов
	 */
	public function getAvailableRatings(): array {
		$ratings = [];

		if ($this->kp !== NULL) {
			$ratings['kp'] = $this->kp;
		}
		if ($this->imdb !== NULL) {
			$ratings['imdb'] = $this->imdb;
		}
		if ($this->tmdb !== NULL) {
			$ratings['tmdb'] = $this->tmdb;
		}
		if ($this->filmCritics !== NULL) {
			$ratings['filmCritics'] = $this->filmCritics;
		}
		if ($this->russianFilmCritics !== NULL) {
			$ratings['russianFilmCritics'] = $this->russianFilmCritics;
		}
		if ($this->await !== NULL) {
			$ratings['await'] = $this->await;
		}

		return $ratings;
	}

}
