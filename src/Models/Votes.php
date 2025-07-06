<?php

declare(strict_types=1);

namespace KinopoiskDev\Models;

/**
 * Класс для представления количества голосов из различных источников
 *
 * Содержит информацию о количестве голосов для фильма/сериала из различных
 * источников, включая Кинопоиск, IMDB, TMDB, а также голоса кинокритиков
 * и ожидания зрителей. Используется для анализа популярности произведения.
 *
 * @package KinopoiskDev\Models
 * @since   1.0.0
 * @author  Maxim Harder
 * @version 1.0.0
 * @see     \KinopoiskDev\Models\Movie::getVotes() Для получения голосов фильма
 * @see     \KinopoiskDev\Models\Rating Для информации о рейтингах
 */
class Votes {

	/**
	 * Конструктор для создания объекта голосов
	 *
	 * Создает новый экземпляр класса Votes с количеством голосов из различных источников.
	 * Все параметры являются опциональными и могут быть NULL при отсутствии
	 * соответствующей информации в источнике данных.
	 *
	 * @see Votes::fromArray() Для создания объекта из массива данных API
	 * @see Votes::toArray() Для преобразования объекта в массив
	 *
	 * @param   int|null  $kp                 Количество голосов на Кинопоиске
	 * @param   int|null  $imdb               Количество голосов на IMDB
	 * @param   int|null  $tmdb               Количество голосов на The Movie Database
	 * @param   int|null  $filmCritics        Количество голосов кинокритиков
	 * @param   int|null  $russianFilmCritics Количество голосов российских кинокритиков
	 * @param   int|null  $await              Количество голосов ожидания
	 */
	public function __construct(
		public readonly ?int $kp = NULL,
		public readonly ?int $imdb = NULL,
		public readonly ?int $tmdb = NULL,
		public readonly ?int $filmCritics = NULL,
		public readonly ?int $russianFilmCritics = NULL,
		public readonly ?int $await = NULL,
	) {}

	/**
	 * Создает объект Votes из массива данных API
	 *
	 * Фабричный метод для создания экземпляра класса Votes из массива данных,
	 * полученных от API Kinopoisk.dev. Безопасно обрабатывает отсутствующие
	 * значения и преобразует строковые значения в числовые.
	 *
	 * @see Votes::toArray() Для обратного преобразования в массив
	 *
	 * @param   array  $data  Массив данных о голосах от API, содержащий ключи:
	 *                        - kp: int|null - количество голосов на Кинопоиске
	 *                        - imdb: int|null - количество голосов на IMDB
	 *                        - tmdb: int|null - количество голосов на TMDB
	 *                        - filmCritics: int|null - количество голосов кинокритиков
	 *                        - russianFilmCritics: int|null - количество голосов российских кинокритиков
	 *                        - await: int|null - количество голосов ожидания
	 *
	 * @return \KinopoiskDev\Models\Votes Новый экземпляр класса Votes с данными из массива
	 */
	public static function fromArray(array $data): self {
		return new self(
			kp: isset($data['kp']) ? (int) $data['kp'] : NULL,
			imdb: isset($data['imdb']) ? (int) $data['imdb'] : NULL,
			tmdb: isset($data['tmdb']) ? (int) $data['tmdb'] : NULL,
			filmCritics: isset($data['filmCritics'])
				? (int) $data['filmCritics'] : NULL,
			russianFilmCritics: isset($data['russianFilmCritics'])
				? (int) $data['russianFilmCritics'] : NULL,
			await: isset($data['await']) ? (int) $data['await'] : NULL,
		);
	}

	/**
	 * Преобразует объект в массив данных
	 *
	 * Конвертирует текущий экземпляр класса Votes в массив,
	 * совместимый с форматом API Kinopoisk.dev. Используется для сериализации
	 * данных при отправке запросов к API или для экспорта данных.
	 *
	 * @see Votes::fromArray() Для создания объекта из массива
	 *
	 * @return array Массив с данными о количестве голосов из различных источников
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
	 * Возвращает общее количество голосов со всех платформ
	 *
	 * Суммирует количество голосов из всех доступных источников,
	 * включая Кинопоиск, IMDB, TMDB, голоса кинокритиков и ожидания.
	 * Игнорирует отсутствующие (null) значения при подсчете.
	 *
	 * @see Votes::getAvailableVotes() Для получения голосов в виде ассоциативного массива
	 * @see Votes::getMostVotedPlatform() Для определения платформы с наибольшим количеством голосов
	 *
	 * @return int Общее количество голосов со всех платформ
	 */
	public function getTotalVotes(): int {
		return array_sum(array_filter([
			$this->kp,
			$this->imdb,
			$this->tmdb,
			$this->filmCritics,
			$this->russianFilmCritics,
			$this->await,
		]));
	}

	/**
	 * Возвращает платформу с наибольшим количеством голосов
	 *
	 * Определяет, какая из платформ (Кинопоиск, IMDB, TMDB и т.д.) имеет
	 * наибольшее количество голосов. Используется для определения наиболее
	 * популярного источника оценок для данного фильма или сериала.
	 *
	 * @see Votes::getAvailableVotes() Для получения всех доступных голосов
	 * @see Votes::getTotalVotes() Для получения общего количества голосов
	 *
	 * @return string|null Ключ платформы с наибольшим количеством голосов или null, если голоса отсутствуют
	 */
	public function getMostVotedPlatform(): ?string {
		$votes = $this->getAvailableVotes();
		if (empty($votes)) {
			return NULL;
		}

		$maxVotes = max($votes);

		return array_search($maxVotes, $votes) ? : NULL;
	}

	/**
	 * Возвращает все доступные голоса в виде ассоциативного массива
	 *
	 * Собирает все ненулевые значения голосов в ассоциативный массив, где ключи
	 * соответствуют источникам голосов, а значения - количеству голосов.
	 * Используется для получения полного набора голосов в удобном формате.
	 *
	 * @see Votes::hasAnyVotes() Для проверки наличия хотя бы одного голоса
	 * @see Votes::getTotalVotes() Для получения общего количества голосов
	 *
	 * @return array Ассоциативный массив доступных голосов
	 */
	public function getAvailableVotes(): array {
		$votes = [];

		if ($this->kp !== NULL) {
			$votes['kp'] = $this->kp;
		}
		if ($this->imdb !== NULL) {
			$votes['imdb'] = $this->imdb;
		}
		if ($this->tmdb !== NULL) {
			$votes['tmdb'] = $this->tmdb;
		}
		if ($this->filmCritics !== NULL) {
			$votes['filmCritics'] = $this->filmCritics;
		}
		if ($this->russianFilmCritics !== NULL) {
			$votes['russianFilmCritics'] = $this->russianFilmCritics;
		}
		if ($this->await !== NULL) {
			$votes['await'] = $this->await;
		}

		return $votes;
	}

	/**
	 * Проверяет наличие хотя бы одного голоса
	 *
	 * Определяет, существует ли хотя бы один голос из любого источника.
	 * Учитывает все возможные источники голосов, включая голоса ожидания и критиков.
	 *
	 * @see Votes::getAvailableVotes() Для получения всех доступных голосов
	 *
	 * @return bool true, если существует хотя бы один голос, иначе false
	 */
	public function hasAnyVotes(): bool {
		return $this->kp !== NULL || $this->imdb !== NULL
		       || $this->tmdb !== NULL
		       || $this->filmCritics !== NULL
		       || $this->russianFilmCritics !== NULL
		       || $this->await !== NULL;
	}

	/**
	 * Форматирует количество голосов с суффиксами K/M
	 *
	 * Преобразует числовое значение количества голосов в удобочитаемый формат
	 * с использованием суффиксов K (тысячи) и M (миллионы). Например, 1500 будет
	 * отображаться как "1.5K", а 2000000 как "2.0M".
	 *
	 * @see Votes::getFormattedKpVotes() Для получения отформатированных голосов Кинопоиска
	 * @see Votes::getFormattedImdbVotes() Для получения отформатированных голосов IMDB
	 *
	 * @param   int  $count  Количество голосов для форматирования
	 *
	 * @return string Отформатированное строковое представление количества голосов
	 */
	public function formatVoteCount(int $count): string {
		if ($count >= 1000000) {
			return round($count / 1000000, 1) . 'M';
		} elseif ($count >= 1000) {
			return round($count / 1000, 1) . 'K';
		}

		return (string) $count;
	}

	/**
	 * Возвращает отформатированное количество голосов Кинопоиска
	 *
	 * Предоставляет количество голосов с Кинопоиска в удобочитаемом формате
	 * с использованием суффиксов K/M. Возвращает null, если голоса отсутствуют.
	 *
	 * @see Votes::formatVoteCount() Для форматирования количества голосов
	 * @see Votes::getFormattedImdbVotes() Для получения отформатированных голосов IMDB
	 *
	 * @return string|null Отформатированное количество голосов или null, если голоса отсутствуют
	 */
	public function getFormattedKpVotes(): ?string {
		return $this->kp ? $this->formatVoteCount($this->kp) : NULL;
	}

	/**
	 * Возвращает отформатированное количество голосов IMDB
	 *
	 * Предоставляет количество голосов с IMDB в удобочитаемом формате
	 * с использованием суффиксов K/M. Возвращает null, если голоса отсутствуют.
	 *
	 * @see Votes::formatVoteCount() Для форматирования количества голосов
	 * @see Votes::getFormattedKpVotes() Для получения отформатированных голосов Кинопоиска
	 *
	 * @return string|null Отформатированное количество голосов или null, если голоса отсутствуют
	 */
	public function getFormattedImdbVotes(): ?string {
		return $this->imdb ? $this->formatVoteCount($this->imdb) : NULL;
	}

	/**
	 * Возвращает строковое представление голосов
	 *
	 * Реализует магический метод __toString для преобразования объекта
	 * в строку. Формирует строку, содержащую основные голоса в удобочитаемом
	 * формате, разделенные запятыми.
	 *
	 * @see Votes::formatVoteCount() Для форматирования количества голосов
	 * @see Votes::getFormattedKpVotes() Для получения отформатированных голосов Кинопоиска
	 * @see Votes::getFormattedImdbVotes() Для получения отформатированных голосов IMDB
	 *
	 * @return string Строковое представление голосов или 'No votes', если голоса отсутствуют
	 */
	public function __toString(): string {
		$parts = [];

		if ($this->kp) {
			$parts[] = "KP: " . $this->formatVoteCount($this->kp);
		}
		if ($this->imdb) {
			$parts[] = "IMDB: " . $this->formatVoteCount($this->imdb);
		}
		if ($this->tmdb) {
			$parts[] = "TMDB: " . $this->formatVoteCount($this->tmdb);
		}

		return empty($parts) ? 'No votes' : implode(', ', $parts);
	}

}
