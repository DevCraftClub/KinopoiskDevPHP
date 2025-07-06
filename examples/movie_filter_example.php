<?php

require_once __DIR__ . '/../vendor/autoload.php';

use KinopoiskDev\Http\MovieRequests;
use KinopoiskDev\Types\MovieSearchFilter;
use KinopoiskDev\Utils\MovieFilter;
use KinopoiskDev\Enums\SortField;
use KinopoiskDev\Enums\SortDirection;
use KinopoiskDev\Types\SortCriteria;

// Инициализация клиента API с токеном
$token = getenv('KINOPOISK_TOKEN') ?: 'YOUR_API_TOKEN';
$movieRequests = new MovieRequests($token);

// Пример 1: Использование базового MovieFilter
echo "=== Пример 1: Поиск фильмов с высоким рейтингом ===\n";
$filter = new MovieFilter();
$filter->rating(8.0, 'kp', 'gte')  // Рейтинг KP >= 8.0
       ->votes(10000, 'kp', 'gte') // Количество голосов >= 10000
       ->year(2020, 'gte')         // Год выпуска >= 2020
       ->genres('драма', 'in');    // Жанр содержит "драма"

$movies = $movieRequests->searchMovies($filter, 1, 5);
printMovies($movies->docs, "Высокорейтинговые драмы с 2020 года");

// Пример 2: Использование MovieSearchFilter для более сложного поиска
echo "\n=== Пример 2: Поиск фильмов по нескольким критериям ===\n";
$searchFilter = new MovieSearchFilter();
$searchFilter->searchByName('Властелин')                // Поиск по названию
             ->withRatingBetween(7.0, 9.0)              // Рейтинг от 7.0 до 9.0
             ->withAllGenres(['фэнтези', 'приключения']) // Оба жанра должны присутствовать
             ->onlyMovies();                            // Только фильмы, не сериалы

$movies = $movieRequests->searchMovies($searchFilter, 1, 5);
printMovies($movies->docs, "Фильмы про Властелина");

// Пример 3: Поиск фильмов с участием конкретного актера
echo "\n=== Пример 3: Поиск фильмов с участием актера ===\n";
$actorFilter = new MovieSearchFilter();
$actorFilter->withActor('Том Хэнкс')
            ->withMinRating(7.0)
            ->withMinVotes(5000);

$movies = $movieRequests->searchMovies($actorFilter, 1, 5);
printMovies($movies->docs, "Фильмы с Томом Хэнксом");

// Пример 4: Поиск фильмов из топ-250
echo "\n=== Пример 4: Поиск фильмов из топ-250 ===\n";
$topFilter = new MovieSearchFilter();
$topFilter->inTop250()
          ->withYearBetween(2000, 2022);

$movies = $movieRequests->searchMovies($topFilter, 1, 5);
printMovies($movies->docs, "Топ-250 фильмов 2000-2022");

// НОВОЕ: Пример 5: Базовая сортировка по рейтингу
echo "\n=== Пример 5: НОВОЕ - Сортировка по рейтингу Кинопоиска ===\n";
$sortFilter = new MovieSearchFilter();
$sortFilter->withMinRating(7.0, 'kp')
           ->withMinVotes(50000, 'kp')
           ->onlyMovies()
           ->sortByKinopoiskRating(); // Сортировка по рейтингу KP (по убыванию)

$movies = $movieRequests->searchMovies($sortFilter, 1, 10);
printMoviesWithSort($movies->docs, "Лучшие фильмы по рейтингу KP", $sortFilter->getSortString());

// НОВОЕ: Пример 6: Множественная сортировка
echo "\n=== Пример 6: НОВОЕ - Множественная сортировка ===\n";
$multiSortFilter = new MovieSearchFilter();
$multiSortFilter->withMinRating(6.5, 'kp')
                ->withYearBetween(2015, 2024)
                ->sortByKinopoiskRating()  // Первый критерий: рейтинг по убыванию
                ->sortByYear()             // Второй критерий: год по убыванию
                ->sortByPopularity();      // Третий критерий: популярность по убыванию

$movies = $movieRequests->searchMovies($multiSortFilter, 1, 10);
printMoviesWithSort($movies->docs, "Фильмы с множественной сортировкой", $multiSortFilter->getSortString());

// НОВОЕ: Пример 7: Ручная настройка сортировки с enum
echo "\n=== Пример 7: НОВОЕ - Ручная настройка сортировки ===\n";
$manualSortFilter = new MovieSearchFilter();
$manualSortFilter->withIncludedGenres(['комедия'])
                 ->withMinRating(6.0, 'kp')
                 ->sortBy(SortField::VOTES_KP, SortDirection::DESC)     // По голосам по убыванию
                 ->sortBy(SortField::YEAR, SortDirection::ASC)          // По году по возрастанию
                 ->sortBy(SortField::MOVIE_LENGTH, SortDirection::ASC); // По длительности по возрастанию

$movies = $movieRequests->searchMovies($manualSortFilter, 1, 8);
printMoviesWithSort($movies->docs, "Комедии с ручной сортировкой", $manualSortFilter->getSortString());

// НОВОЕ: Пример 8: Сортировка с использованием SortCriteria
echo "\n=== Пример 8: НОВОЕ - Использование SortCriteria ===\n";
$criteriaFilter = new MovieSearchFilter();
$criteriaFilter->withIncludedGenres(['триллер', 'драма'])
               ->withMinRating(7.5, 'kp');

// Создание критериев сортировки
$ratingCriteria = SortCriteria::descending(SortField::RATING_IMDB);
$yearCriteria = SortCriteria::ascending(SortField::YEAR);
$votesCriteria = SortCriteria::create(SortField::VOTES_KP); // Использует направление по умолчанию

$criteriaFilter->addSortCriteria($ratingCriteria)
               ->addSortCriteria($yearCriteria)
               ->addSortCriteria($votesCriteria);

$movies = $movieRequests->searchMovies($criteriaFilter, 1, 8);
printMoviesWithSort($movies->docs, "Драма-триллеры с SortCriteria", $criteriaFilter->getSortString());

// НОВОЕ: Пример 9: Сортировка из строковых параметров
echo "\n=== Пример 9: НОВОЕ - Сортировка из строковых параметров ===\n";
$stringParamsFilter = new MovieSearchFilter();
$stringParamsFilter->withIncludedGenres(['фантастика'])
                   ->withYearBetween(2010, 2024)
                   ->addMultipleSort([
	                   'rating.kp:desc',     // По рейтингу KP по убыванию
	                   'votes.imdb:desc',    // По голосам IMDB по убыванию
	                   'year:asc',           // По году по возрастанию
	                   'name'                // По названию (направление по умолчанию)
                   ]);

$movies = $movieRequests->searchMovies($stringParamsFilter, 1, 8);
printMoviesWithSort($movies->docs, "Фантастика со строковыми параметрами", $stringParamsFilter->getSortString());

// НОВОЕ: Пример 10: Динамическое управление сортировкой
echo "\n=== Пример 10: НОВОЕ - Динамическое управление сортировкой ===\n";
$dynamicFilter = new MovieSearchFilter();
$dynamicFilter->withIncludedGenres(['боевик'])
              ->withMinRating(6.0, 'kp');

// Проверяем и добавляем сортировку динамически
if (!$dynamicFilter->hasSortBy(SortField::RATING_KP)) {
	echo "Добавляем сортировку по рейтингу KP\n";
	$dynamicFilter->sortByKinopoiskRating();
}

// Переключаем направление сортировки
echo "Переключаем направление сортировки по году\n";
$dynamicFilter->toggleSort(SortField::YEAR);

// Получаем информацию о сортировке
$sortCount = $dynamicFilter->getSortCount();
$hasRatingSort = $dynamicFilter->hasSortBy(SortField::RATING_KP);
$ratingDirection = $dynamicFilter->getSortDirection(SortField::RATING_KP);

echo "Количество критериев сортировки: $sortCount\n";
echo "Есть сортировка по рейтингу: " . ($hasRatingSort ? 'Да' : 'Нет') . "\n";
echo "Направление сортировки по рейтингу: " . ($ratingDirection?->value ?? 'Не установлено') . "\n";

$movies = $movieRequests->searchMovies($dynamicFilter, 1, 8);
printMoviesWithSort($movies->docs, "Боевики с динамической сортировкой", $dynamicFilter->getSortString());

// НОВОЕ: Пример 11: Предустановленные комбинации сортировки
echo "\n=== Пример 11: НОВОЕ - Предустановленные комбинации ===\n";

// Лучшие фильмы (комбинированная сортировка)
$bestFilter = new MovieSearchFilter();
$bestFilter->withMinRating(7.0, 'kp')
           ->withMinVotes(10000, 'kp')
           ->onlyMovies()
           ->sortByBest(); // Комбинированная сортировка: рейтинг + год

$movies = $movieRequests->searchMovies($bestFilter, 1, 8);
printMoviesWithSort($movies->docs, "Лучшие фильмы (комбинированная сортировка)", $bestFilter->getSortString());

// НОВОЕ: Пример 12: Работа с информацией о полях сортировки
echo "\n=== Пример 12: НОВОЕ - Информация о полях сортировки ===\n";

// Получение информации о различных полях
$fields = [
	SortField::RATING_KP,
	SortField::YEAR,
	SortField::VOTES_IMDB,
	SortField::MOVIE_LENGTH
];

echo "Информация о полях сортировки:\n";
foreach ($fields as $field) {
	echo sprintf(
		"- %s: тип=%s, направление по умолчанию=%s, рейтинговое=%s\n",
		$field->getDescription(),
		$field->getDataType(),
		$field->getDefaultDirection()->getDescription(),
		$field->isRatingField() ? 'Да' : 'Нет'
	);
}

// НОВОЕ: Пример 13: Экспорт и импорт критериев сортировки
echo "\n=== Пример 13: НОВОЕ - Экспорт/импорт критериев ===\n";

$exportFilter = new MovieSearchFilter();
$exportFilter->sortByKinopoiskRating()
             ->sortByYear()
             ->sortByPopularity();

// Экспорт критериев
$exportedCriteria = $exportFilter->exportSortCriteria();
echo "Экспортированные критерии:\n";
print_r($exportedCriteria);

// Создание нового фильтра и импорт критериев
$importFilter = new MovieSearchFilter();
$importFilter->withIncludedGenres(['мелодрама'])
             ->importSortCriteria($exportedCriteria);

echo "Импортированная строка сортировки: " . $importFilter->getSortString() . "\n";

// НОВОЕ: Пример 14: Сложный поиск с фильтрацией и сортировкой
echo "\n=== Пример 14: НОВОЕ - Сложный поиск российских фильмов ===\n";
$complexFilter = new MovieSearchFilter();
$complexFilter->withIncludedCountries(['Россия', 'СССР'])
              ->withExcludedGenres(['ужасы', 'эротика'])
              ->withRatingBetween(6.5, 10.0, 'kp')
              ->withMinVotes(1000, 'kp')
              ->withYearBetween(1990, 2024)
              ->onlyMovies()
              ->sortByKinopoiskRating()
              ->sortByYear()
              ->sortByPopularity();

$movies = $movieRequests->searchMovies($complexFilter, 1, 10);
printMoviesWithSort($movies->docs, "Лучшие российские фильмы", $complexFilter->getSortString());

// НОВОЕ: Пример 15: Поиск сериалов с сортировкой по дате премьеры
echo "\n=== Пример 15: НОВОЕ - Сериалы с сортировкой по премьере ===\n";
$seriesFilter = new MovieSearchFilter();
$seriesFilter->onlySeries()
             ->withMinRating(7.5, 'kp')
             ->withIncludedGenres(['драма'])
             ->sortBy(SortField::PREMIERE_WORLD, SortDirection::DESC)
             ->sortByKinopoiskRating();

$movies = $movieRequests->searchMovies($seriesFilter, 1, 8);
printMoviesWithSort($movies->docs, "Драматические сериалы по дате премьеры", $seriesFilter->getSortString());

// Функция для вывода информации о фильмах (оригинальная)
function printMovies(array $movies, string $title = ""): void {
	if ($title) {
		echo "--- $title ---\n";
	}

	if (empty($movies)) {
		echo "Фильмы не найдены.\n";
		return;
	}

	foreach ($movies as $index => $movie) {
		echo ($index + 1) . ". {$movie->name} ({$movie->year})\n";
		echo "   Рейтинг KP: " . ($movie->rating?->kp ?? 'N/A') . "\n";
		echo "   Жанры: " . implode(', ', array_map(fn($genre) => $genre->name, $movie->genres ?? [])) . "\n";
		echo "\n";
	}
}

// НОВАЯ функция для вывода с информацией о сортировке
function printMoviesWithSort(array $movies, string $title = "", ?string $sortString = null): void {
	if ($title) {
		echo "--- $title ---\n";
	}

	if ($sortString) {
		echo "Параметры сортировки: $sortString\n";
	}

	if (empty($movies)) {
		echo "Фильмы не найдены.\n";
		return;
	}

	foreach ($movies as $index => $movie) {
		$movieLength = $movie->movieLength ? " | {$movie->movieLength} мин" : "";
		$imdbRating = $movie->rating?->imdb ? " | IMDB: {$movie->rating->imdb}" : "";
		$votesKp = $movie->votes?->kp ? " | Голосов: " . formatNumber($movie->votes->kp) : "";

		echo sprintf(
			"%d. %s (%d)%s\n",
			$index + 1,
			$movie->name,
			$movie->year,
			$movieLength
		);

		echo sprintf(
			"   Рейтинг KP: %s%s%s\n",
			$movie->rating?->kp ?? 'N/A',
			$imdbRating,
			$votesKp
		);

		$genres = array_map(fn($genre) => $genre->name, $movie->genres ?? []);
		$countries = array_map(fn($country) => $country->name, $movie->countries ?? []);

		echo "   Жанры: " . implode(', ', $genres) . "\n";
		if (!empty($countries)) {
			echo "   Страны: " . implode(', ', $countries) . "\n";
		}

		// Показываем позицию в топах, если есть
		if ($movie->top250) {
			echo "   Позиция в топ-250: {$movie->top250}\n";
		}
		if ($movie->top10) {
			echo "   Позиция в топ-10: {$movie->top10}\n";
		}

		echo "\n";
	}
}

// Вспомогательная функция для форматирования чисел
function formatNumber(int $number): string {
	if ($number >= 1000000) {
		return round($number / 1000000, 1) . 'M';
	} elseif ($number >= 1000) {
		return round($number / 1000, 1) . 'K';
	}
	return (string)$number;
}

// НОВАЯ функция для демонстрации различных операций с сортировкой
function demonstrateSortOperations(): void {
	echo "\n=== ДЕМОНСТРАЦИЯ: Операции с сортировкой ===\n";

	$filter = new MovieSearchFilter();

	echo "1. Добавление сортировки по рейтингу:\n";
	$filter->sortByKinopoiskRating();
	echo "   Результат: " . $filter->getSortString() . "\n";

	echo "2. Добавление сортировки по году:\n";
	$filter->sortByYear();
	echo "   Результат: " . $filter->getSortString() . "\n";

	echo "3. Переключение направления сортировки по году:\n";
	$filter->toggleSort(SortField::YEAR);
	echo "   Результат: " . $filter->getSortString() . "\n";

	echo "4. Удаление сортировки по рейтингу:\n";
	$filter->removeSortByField(SortField::RATING_KP);
	echo "   Результат: " . $filter->getSortString() . "\n";

	echo "5. Очистка всей сортировки:\n";
	$filter->clearSort();
	echo "   Результат: " . ($filter->getSortString() ?? 'Сортировка отсутствует') . "\n";

	echo "6. Добавление множественной сортировки:\n";
	$filter->addMultipleSort(['rating.kp:desc', 'votes.kp:desc', 'year:asc']);
	echo "   Результат: " . $filter->getSortString() . "\n";
}

// Запуск демонстрации операций с сортировкой
demonstrateSortOperations();

echo "\n=== СПРАВКА: Доступные поля сортировки ===\n";
echo "Рейтинговые поля:\n";
foreach (SortField::getRatingFields() as $field) {
	echo "  - {$field->value} ({$field->getDescription()})\n";
}

echo "\nПоля голосов:\n";
foreach (SortField::getVotesFields() as $field) {
	echo "  - {$field->value} ({$field->getDescription()})\n";
}

echo "\nДругие популярные поля:\n";
$otherFields = [
	SortField::YEAR, SortField::NAME, SortField::MOVIE_LENGTH,
	SortField::TOP_250, SortField::CREATED_AT
];
foreach ($otherFields as $field) {
	echo "  - {$field->value} ({$field->getDescription()})\n";
}
