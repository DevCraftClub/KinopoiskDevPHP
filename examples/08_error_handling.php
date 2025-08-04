<?php

/**
 * Обработка ошибок в KinopoiskDev
 * 
 * Этот пример демонстрирует:
 * - Обработку различных типов ошибок
 * - Валидацию параметров
 * - Обработку сетевых ошибок
 * - Логирование ошибок
 * - Graceful degradation
 */

require_once __DIR__ . '/../vendor/autoload.php';

use KinopoiskDev\Http\MovieRequests;
use KinopoiskDev\Http\PersonRequests;
use KinopoiskDev\Filter\MovieSearchFilter;
use KinopoiskDev\Filter\PersonSearchFilter;
use KinopoiskDev\Exceptions\KinopoiskDevException;
use KinopoiskDev\Exceptions\KinopoiskResponseException;
use KinopoiskDev\Exceptions\ValidationException;

// Проверяем наличие токена
if (!getenv('KINOPOISK_TOKEN')) {
    echo "❌ Ошибка: Не найден токен API. Установите переменную окружения KINOPOISK_TOKEN\n";
    exit(1);
}

echo "⚠️  KinopoiskDev - Обработка ошибок\n";
echo "====================================\n\n";

try {
    $movieRequests = new \KinopoiskDev\Http\MovieRequests(useCache: true);
    $personRequests = new \KinopoiskDev\Http\PersonRequests(useCache: true);
    
    // 1. Обработка ошибки 404 - фильм не найден
    echo "🔍 1. Попытка получить несуществующий фильм\n";
    echo "--------------------------------------------\n";
    
    try {
        $nonExistentMovie = $movieRequests->getMovieById(999999999);
        echo "✅ Фильм найден: {$nonExistentMovie->name}\n";
    } catch (KinopoiskResponseException $e) {
        echo "❌ Ошибка API (код {$e->getCode()}): {$e->getMessage()}\n";
        
        switch ($e->getCode()) {
            case 404:
                echo "💡 Фильм с таким ID не найден в базе данных\n";
                break;
            case 401:
                echo "💡 Проверьте правильность API токена\n";
                break;
            case 403:
                echo "💡 Недостаточно прав для доступа к этому ресурсу\n";
                break;
            case 429:
                echo "💡 Превышен лимит запросов, попробуйте позже\n";
                break;
            default:
                echo "💡 Неизвестная ошибка API\n";
        }
    }
    
    echo "\n";

    // 2. Обработка ошибок валидации
    echo "✅ 2. Тестирование валидации параметров\n";
    echo "----------------------------------------\n";
    
    try {
        $invalidFilter = new MovieSearchFilter();
        $invalidFilter->year(-1000) // Некорректный год
                     ->withMinRating(-5, 'kp'); // Некорректный рейтинг
        
        $results = $movieRequests->searchMovies($invalidFilter, 1, 5);
        echo "✅ Поиск выполнен успешно\n";
    } catch (ValidationException $e) {
        echo "❌ Ошибка валидации: {$e->getMessage()}\n";
        
        if ($e->hasErrors()) {
            echo "📋 Детали ошибок:\n";
            foreach ($e->getErrors() as $field => $error) {
                echo "   - {$field}: {$error}\n";
            }
        }
    }
    
    echo "\n";

    // 3. Обработка ошибок с некорректными фильтрами
    echo "🔍 3. Поиск с некорректными параметрами\n";
    echo "----------------------------------------\n";
    
    try {
        $invalidSearchFilter = new PersonSearchFilter();
        $invalidSearchFilter->age(-10, 'gte') // Некорректный возраст
                           ->searchByName(''); // Пустое имя
        
        $results = $personRequests->searchPersons($invalidSearchFilter, 1, 5);
        echo "✅ Поиск персон выполнен успешно\n";
    } catch (ValidationException $e) {
        echo "❌ Ошибка валидации фильтра: {$e->getMessage()}\n";
        
        if ($e->hasErrors()) {
            echo "📋 Ошибки в фильтре:\n";
            foreach ($e->getErrors() as $field => $error) {
                echo "   - {$field}: {$error}\n";
            }
        }
    }
    
    echo "\n";

    // 4. Обработка ошибок с некорректными ID
    echo "🎬 4. Попытка получить фильм с некорректным ID\n";
    echo "----------------------------------------------\n";
    
    $invalidIds = ['abc', -1, 0, 'invalid_id'];
    
    foreach ($invalidIds as $invalidId) {
        try {
            echo "🔍 Попытка получить фильм с ID: {$invalidId}\n";
            $movie = $movieRequests->getMovieById($invalidId);
            echo "✅ Успешно получен фильм: {$movie->name}\n";
        } catch (ValidationException $e) {
            echo "❌ Ошибка валидации: {$e->getMessage()}\n";
        } catch (KinopoiskResponseException $e) {
            echo "❌ Ошибка API (код {$e->getCode()}): {$e->getMessage()}\n";
        } catch (KinopoiskDevException $e) {
            echo "❌ Ошибка библиотеки: {$e->getMessage()}\n";
        }
        echo "\n";
    }
    
    echo "\n";

    // 5. Обработка ошибок с некорректными страницами
    echo "📄 5. Пагинация с некорректными параметрами\n";
    echo "--------------------------------------------\n";
    
    $invalidPages = [-1, 0, 'abc', 999999];
    
    foreach ($invalidPages as $invalidPage) {
        try {
            echo "📄 Попытка получить страницу: {$invalidPage}\n";
            $filter = new MovieSearchFilter();
            $filter->withMinRating(7.0, 'kp');
            
            $results = $movieRequests->searchMovies($filter, $invalidPage, 5);
            echo "✅ Получено результатов: {$results->total}\n";
        } catch (ValidationException $e) {
            echo "❌ Ошибка валидации: {$e->getMessage()}\n";
        } catch (KinopoiskResponseException $e) {
            echo "❌ Ошибка API (код {$e->getCode()}): {$e->getMessage()}\n";
        }
        echo "\n";
    }
    
    echo "\n";

    // 6. Graceful degradation - продолжение работы при ошибках
    echo "🔄 6. Graceful degradation\n";
    echo "---------------------------\n";
    
    $movieIds = [301, 999999999, 326, 999999999, 328]; // Смесь валидных и невалидных ID
    $successfulMovies = [];
    
    foreach ($movieIds as $index => $movieId) {
        try {
            $attemptNumber = $index + 1;
            echo "🔍 Попытка {$attemptNumber}: получение фильма ID {$movieId}\n";
            $movie = $movieRequests->getMovieById($movieId);
            $successfulMovies[] = $movie;
            echo "✅ Успешно: {$movie->name}\n";
        } catch (Exception $e) {
            echo "❌ Ошибка: " . $e->getMessage() . "\n";
            echo "🔄 Продолжаем работу...\n";
        }
    }
    
    echo "\n📊 Результат: успешно получено " . count($successfulMovies) . " из " . count($movieIds) . " фильмов\n";
    
    if (!empty($successfulMovies)) {
        echo "✅ Полученные фильмы:\n";
        foreach ($successfulMovies as $movie) {
            echo "   • {$movie->name} ({$movie->year})\n";
        }
    }
    
    echo "\n";

    // 7. Обработка ошибок с повторными попытками
    echo "🔄 7. Повторные попытки при ошибках\n";
    echo "------------------------------------\n";
    
    $maxRetries = 3;
    $retryDelay = 1; // секунды
    
    for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
        try {
            echo "🔄 Попытка {$attempt} из {$maxRetries}\n";
            
            // Симулируем потенциально проблемный запрос
            $filter = new MovieSearchFilter();
            $filter->withMinRating(9.5, 'kp') // Очень высокий рейтинг
                   ->withMinVotes(1000000, 'kp'); // Очень много голосов
            
            $results = $movieRequests->searchMovies($filter, 1, 5);
            echo "✅ Успешно получено результатов: {$results->total}\n";
            break; // Выходим из цикла при успехе
            
        } catch (KinopoiskResponseException $e) {
            echo "❌ Ошибка API (код {$e->getCode()}): {$e->getMessage()}\n";
            
            if ($attempt < $maxRetries) {
                echo "⏳ Ожидание {$retryDelay} секунд перед повторной попыткой...\n";
                sleep($retryDelay);
                $retryDelay *= 2; // Экспоненциальная задержка
            } else {
                echo "💥 Все попытки исчерпаны. Ошибка: {$e->getMessage()}\n";
            }
        } catch (Exception $e) {
            echo "❌ Неожиданная ошибка: {$e->getMessage()}\n";
            break; // Не повторяем при неожиданных ошибках
        }
    }
    
    echo "\n";

    // 8. Логирование ошибок
    echo "📝 8. Логирование ошибок\n";
    echo "------------------------\n";
    
    $errorLog = [];
    
    function logError($context, $error, $details = []) {
        global $errorLog;
        $errorLog[] = [
            'timestamp' => date('Y-m-d H:i:s'),
            'context' => $context,
            'error' => $error,
            'details' => $details
        ];
    }
    
    // Тестируем различные сценарии ошибок
    $testScenarios = [
        'invalid_movie_id' => function() use ($movieRequests) {
            return $movieRequests->getMovieById(999999999);
        },
        'invalid_filter' => function() use ($movieRequests) {
            $filter = new MovieSearchFilter();
            $filter->year(-1000);
            return $movieRequests->searchMovies($filter, 1, 5);
        },
        'invalid_pagination' => function() use ($movieRequests) {
            $filter = new MovieSearchFilter();
            return $movieRequests->searchMovies($filter, -1, 5);
        }
    ];
    
    foreach ($testScenarios as $scenario => $testFunction) {
        try {
            echo "🧪 Тестирование: {$scenario}\n";
            $result = $testFunction();
            echo "✅ Успешно\n";
        } catch (ValidationException $e) {
            echo "❌ Ошибка валидации: {$e->getMessage()}\n";
            logError($scenario, 'ValidationException', [
                'message' => $e->getMessage(),
                'errors' => $e->hasErrors() ? $e->getErrors() : []
            ]);
        } catch (KinopoiskResponseException $e) {
            echo "❌ Ошибка API: {$e->getMessage()}\n";
            logError($scenario, 'KinopoiskResponseException', [
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ]);
        } catch (Exception $e) {
            echo "❌ Неожиданная ошибка: {$e->getMessage()}\n";
            logError($scenario, 'Exception', [
                'message' => $e->getMessage(),
                'type' => get_class($e)
            ]);
        }
        echo "\n";
    }
    
    // Выводим лог ошибок
    if (!empty($errorLog)) {
        echo "📋 Лог ошибок:\n";
        foreach ($errorLog as $logEntry) {
            echo "   📅 {$logEntry['timestamp']} - {$logEntry['context']}: {$logEntry['error']}\n";
        }
    }
    
    echo "\n";

    // 9. Обработка ошибок с пользовательскими сообщениями
    echo "💬 9. Пользовательские сообщения об ошибках\n";
    echo "--------------------------------------------\n";
    
    function getUserFriendlyError($exception) {
        if ($exception instanceof ValidationException) {
            return "Проверьте правильность введенных данных";
        } elseif ($exception instanceof KinopoiskResponseException) {
            switch ($exception->getCode()) {
                case 401:
                    return "Необходимо проверить API токен";
                case 403:
                    return "Недостаточно прав для выполнения операции";
                case 404:
                    return "Запрашиваемый ресурс не найден";
                case 429:
                    return "Слишком много запросов, попробуйте позже";
                default:
                    return "Произошла ошибка при обращении к API";
            }
        } elseif ($exception instanceof KinopoiskDevException) {
            return "Ошибка в работе библиотеки";
        } else {
            return "Произошла неожиданная ошибка";
        }
    }
    
    try {
        $movie = $movieRequests->getMovieById(999999999);
        echo "✅ Фильм получен успешно\n";
    } catch (Exception $e) {
        $userMessage = getUserFriendlyError($e);
        echo "❌ {$userMessage}\n";
        echo "🔧 Техническая информация: {$e->getMessage()}\n";
    }
    
    echo "\n";

    echo "✅ Все примеры обработки ошибок выполнены успешно!\n";

} catch (KinopoiskDevException $e) {
    echo "❌ Критическая ошибка библиотеки: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "❌ Критическая ошибка: " . $e->getMessage() . "\n";
}

echo "\n🎉 Обработка ошибок завершена!\n"; 