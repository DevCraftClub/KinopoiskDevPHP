<?php

/**
 * Работа с изображениями и медиа контентом в KinopoiskDev
 * 
 * Этот пример демонстрирует:
 * - Поиск изображений фильмов
 * - Работа с различными типами изображений
 * - Поиск изображений персон
 * - Фильтрация по типам изображений
 * - Получение постеров и кадров
 */

require_once __DIR__ . '/../vendor/autoload.php';

use KinopoiskDev\Http\ImageRequests;
use KinopoiskDev\Http\MovieRequests;
use KinopoiskDev\Http\PersonRequests;
use KinopoiskDev\Filter\ImageSearchFilter;
use KinopoiskDev\Enums\ImageType;
use KinopoiskDev\Exceptions\KinopoiskDevException;

// Проверяем наличие токена
if (!getenv('KINOPOISK_TOKEN')) {
    echo "❌ Ошибка: Не найден токен API. Установите переменную окружения KINOPOISK_TOKEN\n";
    exit(1);
}

echo "🖼️  KinopoiskDev - Работа с изображениями и медиа\n";
echo "==================================================\n\n";

try {
    $imageRequests = new \KinopoiskDev\Http\ImageRequests(useCache: true);
    $movieRequests = new \KinopoiskDev\Http\MovieRequests(useCache: true);
    $personRequests = new \KinopoiskDev\Http\PersonRequests(useCache: true);
    
    // 1. Поиск постеров фильма
    echo "🎬 1. Постеры фильма 'Матрица'\n";
    echo "--------------------------------\n";
    
    $matrixId = 301; // ID фильма "Матрица"
    $posterFilter = new ImageSearchFilter();
    $posterFilter->movieId($matrixId)
                 ->type(ImageType::POSTER);
    
    $posters = $imageRequests->searchImages($posterFilter, 1, 5);
    
    echo "📊 Найдено постеров: {$posters->total}\n\n";
    
    foreach ($posters->docs as $index => $image) {
        echo sprintf("%d. %s\n", 
            $index + 1, 
            $image->name ?? 'Без названия'
        );
        echo "   📏 Размер: {$image->width}x{$image->height}\n";
        echo "   🔗 URL: {$image->url}\n";
        echo "   📝 Описание: " . ($image->description ?? 'Нет описания') . "\n\n";
    }
    
    echo "\n";

    // 2. Поиск кадров из фильма
    echo "🎥 2. Кадры из фильма 'Матрица'\n";
    echo "--------------------------------\n";
    
    $frameFilter = new ImageSearchFilter();
    $frameFilter->movieId($matrixId)
                ->type(ImageType::STILL);
    
    $frames = $imageRequests->searchImages($frameFilter, 1, 5);
    
    echo "📊 Найдено кадров: {$frames->total}\n\n";
    
    foreach ($frames->docs as $index => $image) {
        echo sprintf("%d. %s\n", 
            $index + 1, 
            $image->name ?? 'Без названия'
        );
        echo "   📏 Размер: {$image->width}x{$image->height}\n";
        echo "   🔗 URL: {$image->url}\n\n";
    }
    
    echo "\n";

    // 3. Поиск изображений персоны
    echo "👤 3. Фотографии актера\n";
    echo "------------------------\n";
    
    $personId = 1; // ID персоны (можно изменить)
    $personImageFilter = new ImageSearchFilter();
    $personImageFilter->personId($personId);
    
    $personImages = $imageRequests->searchImages($personImageFilter, 1, 5);
    
    echo "📊 Найдено изображений персоны: {$personImages->total}\n\n";
    
    foreach ($personImages->docs as $index => $image) {
        echo sprintf("%d. %s\n", 
            $index + 1, 
            $image->name ?? 'Без названия'
        );
        echo "   📏 Размер: {$image->width}x{$image->height}\n";
        echo "   🔗 URL: {$image->url}\n\n";
    }
    
    echo "\n";

    // 4. Поиск изображений по типу
    echo "🎨 4. Различные типы изображений\n";
    echo "--------------------------------\n";
    
    $imageTypes = [
        ImageType::POSTER => 'Постеры',
        ImageType::STILL => 'Кадры',
        ImageType::SCREENSHOT => 'Скриншоты',
        ImageType::SHOOTING => 'Съемки',
        ImageType::WALLPAPER => 'Обои',
        ImageType::COVER => 'Обложки',
        ImageType::FAN_ART => 'Фан-арт'
    ];
    
    foreach ($imageTypes as $type => $typeName) {
        echo "🎨 {$typeName}:\n";
        
        $typeFilter = new ImageSearchFilter();
        $typeFilter->type($type)
                   ->limit(3);
        
        $typeImages = $imageRequests->searchImages($typeFilter, 1, 3);
        
        echo "   📊 Найдено: {$typeImages->total}\n";
        
        foreach (array_slice($typeImages->docs, 0, 2) as $image) {
            echo "   • {$image->name} ({$image->width}x{$image->height})\n";
        }
        echo "\n";
    }
    
    echo "\n";

    // 5. Поиск изображений по размеру
    echo "📏 5. Изображения высокого разрешения\n";
    echo "------------------------------------\n";
    
    $hdFilter = new ImageSearchFilter();
    $hdFilter->type(ImageType::POSTER)
             ->limit(5);
    
    $hdImages = $imageRequests->searchImages($hdFilter, 1, 5);
    
    echo "📊 Найдено постеров: {$hdImages->total}\n\n";
    
    foreach ($hdImages->docs as $index => $image) {
        $resolution = "{$image->width}x{$image->height}";
        $aspectRatio = round($image->width / $image->height, 2);
        
        echo sprintf("%d. %s\n", 
            $index + 1, 
            $image->name ?? 'Без названия'
        );
        echo "   📏 Разрешение: {$resolution} (соотношение: {$aspectRatio})\n";
        echo "   🔗 URL: {$image->url}\n\n";
    }
    
    echo "\n";

    // 6. Поиск изображений для конкретного фильма
    echo "🎬 6. Все изображения фильма\n";
    echo "-----------------------------\n";
    
    $movieImagesFilter = new ImageSearchFilter();
    $movieImagesFilter->movieId($matrixId)
                      ->limit(10);
    
    $movieImages = $imageRequests->searchImages($movieImagesFilter, 1, 10);
    
    echo "📊 Найдено изображений фильма: {$movieImages->total}\n\n";
    
    // Группируем по типам
    $imagesByType = [];
    foreach ($movieImages->docs as $image) {
        $type = $image->type ?? 'unknown';
        if (!isset($imagesByType[$type])) {
            $imagesByType[$type] = [];
        }
        $imagesByType[$type][] = $image;
    }
    
    foreach ($imagesByType as $type => $images) {
        echo "🎨 {$type}: " . count($images) . " изображений\n";
        foreach (array_slice($images, 0, 2) as $image) {
            echo "   • {$image->name} ({$image->width}x{$image->height})\n";
        }
        echo "\n";
    }
    
    echo "\n";

    // 7. Поиск изображений с фильтрацией
    echo "🔍 7. Поиск изображений с фильтрацией\n";
    echo "------------------------------------\n";
    
    $filteredImageFilter = new ImageSearchFilter();
    $filteredImageFilter->type(ImageType::POSTER)
                        ->limit(5);
    
    $filteredImages = $imageRequests->searchImages($filteredImageFilter, 1, 5);
    
    echo "📊 Найдено постеров: {$filteredImages->total}\n\n";
    
    foreach ($filteredImages->docs as $index => $image) {
        echo sprintf("%d. %s\n", 
            $index + 1, 
            $image->name ?? 'Без названия'
        );
        
        if (!empty($image->description)) {
            echo "   📝 " . substr($image->description, 0, 100) . "...\n";
        }
        
        echo "   📏 {$image->width}x{$image->height}\n";
        echo "   🔗 {$image->url}\n\n";
    }
    
    echo "\n";

    // 8. Работа с медиа контентом фильма
    echo "🎬 8. Медиа контент фильма\n";
    echo "---------------------------\n";
    
    $movie = $movieRequests->getMovieById($matrixId);
    
    echo "🎬 Фильм: {$movie->name}\n";
    
    // Постеры
    if (!empty($movie->poster)) {
        echo "🎨 Постер: {$movie->poster->url}\n";
    }
    
    // Логотипы
    if (!empty($movie->logo)) {
        echo "🏷️  Логотип: {$movie->logo->url}\n";
    }
    
    // Видео
    if (!empty($movie->videos)) {
        echo "🎥 Видео: " . count($movie->videos) . " файлов\n";
        foreach (array_slice($movie->videos, 0, 3) as $video) {
            echo "   • {$video->name} ({$video->url})\n";
        }
    }
    
    // Изображения
    if (!empty($movie->images)) {
        echo "🖼️  Изображения: " . count($movie->images) . " файлов\n";
        foreach (array_slice($movie->images, 0, 3) as $image) {
            echo "   • {$image->name} ({$image->url})\n";
        }
    }
    
    echo "\n";

    echo "✅ Все примеры работы с изображениями и медиа выполнены успешно!\n";

} catch (KinopoiskDevException $e) {
    echo "❌ Ошибка: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "❌ Неожиданная ошибка: " . $e->getMessage() . "\n";
}

echo "\n🎉 Работа с изображениями завершена!\n"; 