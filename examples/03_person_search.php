<?php

/**
 * Поиск и работа с персонами в KinopoiskDev
 * 
 * Этот пример демонстрирует:
 * - Поиск актеров, режиссеров и других персон
 * - Получение детальной информации о персоне
 * - Поиск фильмов с участием конкретной персоны
 * - Фильтрация по возрасту, полу, профессии
 * - Работа с наградами и фактами
 */

require_once __DIR__ . '/../vendor/autoload.php';

use KinopoiskDev\Http\PersonRequests;
use KinopoiskDev\Http\MovieRequests;
use KinopoiskDev\Filter\PersonSearchFilter;
use KinopoiskDev\Filter\MovieSearchFilter;
use KinopoiskDev\Enums\PersonProfession;
use KinopoiskDev\Enums\PersonSex;
use KinopoiskDev\Exceptions\KinopoiskDevException;

// Проверяем наличие токена
if (!getenv('KINOPOISK_TOKEN')) {
    echo "❌ Ошибка: Не найден токен API. Установите переменную окружения KINOPOISK_TOKEN\n";
    exit(1);
}

echo "👥 KinopoiskDev - Поиск и работа с персонами\n";
echo "==============================================\n\n";

try {
    $personRequests = new \KinopoiskDev\Http\PersonRequests(useCache: true);
    $movieRequests = new \KinopoiskDev\Http\MovieRequests(useCache: true);
    
    // 1. Поиск актеров по имени
    echo "🔍 1. Поиск актеров по имени\n";
    echo "-----------------------------\n";
    
    $actorFilter = new PersonSearchFilter();
    $actorFilter->searchByName('Том Хэнкс')
                ->onlyActors();
    
    $actors = $personRequests->searchPersons($actorFilter, 1, 3);
    
    echo "📊 Найдено актеров: {$actors->total}\n\n";
    
    foreach ($actors->docs as $index => $person) {
        echo sprintf("%d. %s (возраст: %d)\n", 
            $index + 1, 
            $person->name, 
            $person->age ?? 'N/A'
        );
        if (!empty($person->birthPlace)) {
            echo "   📍 Место рождения: {$person->birthPlace->value}\n";
        }
    }
    
    echo "\n";

    // 2. Получение детальной информации о персоне
    echo "👤 2. Детальная информация о персоне\n";
    echo "------------------------------------\n";
    
    $personId = 1; // ID персоны (можно изменить)
    $person = $personRequests->getPersonById($personId);
    
    echo "👤 Имя: {$person->name}\n";
    echo "📅 Возраст: " . ($person->age ?? 'N/A') . "\n";
    echo "🎭 Профессии: " . implode(', ', $person->profession) . "\n";
    
    if (!empty($person->birthPlace)) {
        echo "📍 Место рождения: {$person->birthPlace->value}\n";
    }
    
    if (!empty($person->deathPlace)) {
        echo "💀 Место смерти: {$person->deathPlace->value}\n";
    }
    
    if (!empty($person->spouses)) {
        echo "💕 Семейное положение: " . implode(', ', array_map(fn($s) => $s->name, $person->spouses)) . "\n";
    }
    
    if (!empty($person->facts)) {
        echo "📝 Интересные факты:\n";
        foreach (array_slice($person->facts, 0, 3) as $fact) {
            echo "   • {$fact->value}\n";
        }
    }
    
    echo "\n";

    // 3. Поиск режиссеров определенного возраста
    echo "🎬 3. Поиск режиссеров 40-60 лет\n";
    echo "--------------------------------\n";
    
    $directorFilter = new PersonSearchFilter();
    $directorFilter->onlyDirectors()
                   ->age(40, 'gte')
                   ->age(60, 'lte');
    
    $directors = $personRequests->searchPersons($directorFilter, 1, 5);
    
    echo "📊 Найдено режиссеров: {$directors->total}\n\n";
    
    foreach ($directors->docs as $index => $person) {
        echo sprintf("%d. %s (возраст: %d)\n", 
            $index + 1, 
            $person->name, 
            $person->age ?? 'N/A'
        );
    }
    
    echo "\n";

    // 4. Поиск актрис по полу и возрасту
    echo "👩 4. Поиск актрис 25-35 лет\n";
    echo "-----------------------------\n";
    
    $actressFilter = new PersonSearchFilter();
    $actressFilter->onlyActors()
                  ->sex(PersonSex::FEMALE)
                  ->age(25, 'gte')
                  ->age(35, 'lte');
    
    $actresses = $personRequests->searchPersons($actressFilter, 1, 5);
    
    echo "📊 Найдено актрис: {$actresses->total}\n\n";
    
    foreach ($actresses->docs as $index => $person) {
        echo sprintf("%d. %s (возраст: %d)\n", 
            $index + 1, 
            $person->name, 
            $person->age ?? 'N/A'
        );
    }
    
    echo "\n";

    // 5. Поиск фильмов с участием конкретной персоны
    echo "🎭 5. Фильмы с участием Леонардо Ди Каприо\n";
    echo "-------------------------------------------\n";
    
    $actorMoviesFilter = new MovieSearchFilter();
    $actorMoviesFilter->withActor('Леонардо Ди Каприо')
                      ->withMinRating(7.0, 'kp')
                      ->withMinVotes(1000, 'kp');
    
    $actorMovies = $movieRequests->searchMovies($actorMoviesFilter, 1, 5);
    
    echo "📊 Найдено фильмов: {$actorMovies->total}\n\n";
    
    foreach ($actorMovies->docs as $index => $movie) {
        $rating = $movie->rating->kp ?? 'N/A';
        echo sprintf("%d. %s (%d) - ⭐ %s\n", 
            $index + 1, 
            $movie->name, 
            $movie->year, 
            $rating
        );
    }
    
    echo "\n";

    // 6. Поиск персон по профессии и стране
    echo "🌍 6. Американские актеры\n";
    echo "-------------------------\n";
    
    $americanFilter = new PersonSearchFilter();
    $americanFilter->onlyActors()
                   ->birthPlace('США')
                   ->age(30, 'gte');
    
    $americans = $personRequests->searchPersons($americanFilter, 1, 5);
    
    echo "📊 Найдено американских актеров: {$americans->total}\n\n";
    
    foreach ($americans->docs as $index => $person) {
        echo sprintf("%d. %s (возраст: %d)\n", 
            $index + 1, 
            $person->name, 
            $person->age ?? 'N/A'
        );
    }
    
    echo "\n";

    // 7. Поиск персон с наградами
    echo "🏆 7. Поиск персон с наградами\n";
    echo "-----------------------------\n";
    
    $awardFilter = new PersonSearchFilter();
    $awardFilter->onlyActors()
                ->age(40, 'gte')
                ->age(70, 'lte');
    
    $awardWinners = $personRequests->searchPersons($awardFilter, 1, 5);
    
    echo "📊 Найдено актеров 40-70 лет: {$awardWinners->total}\n\n";
    
    foreach ($awardWinners->docs as $index => $person) {
        echo sprintf("%d. %s (возраст: %d)\n", 
            $index + 1, 
            $person->name, 
            $person->age ?? 'N/A'
        );
        
        // Получаем детальную информацию о наградах
        if (!empty($person->awards)) {
            echo "   🏆 Награды: " . count($person->awards) . " наград\n";
        }
    }
    
    echo "\n";

    // 8. Комплексный поиск персон
    echo "🔍 8. Комплексный поиск персон\n";
    echo "------------------------------\n";
    
    $complexFilter = new PersonSearchFilter();
    $complexFilter->searchByName('Кристофер')
                  ->onlyDirectors()
                  ->age(40, 'gte');
    
    $complexResults = $personRequests->searchPersons($complexFilter, 1, 5);
    
    echo "📊 Найдено режиссеров с именем 'Кристофер' (40+): {$complexResults->total}\n\n";
    
    foreach ($complexResults->docs as $index => $person) {
        echo sprintf("%d. %s (возраст: %d)\n", 
            $index + 1, 
            $person->name, 
            $person->age ?? 'N/A'
        );
        
        if (!empty($person->birthPlace)) {
            echo "   📍 {$person->birthPlace->value}\n";
        }
    }
    
    echo "\n";

    echo "✅ Все примеры работы с персонами выполнены успешно!\n";

} catch (KinopoiskDevException $e) {
    echo "❌ Ошибка: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "❌ Неожиданная ошибка: " . $e->getMessage() . "\n";
}

echo "\n🎉 Поиск персон завершен!\n"; 