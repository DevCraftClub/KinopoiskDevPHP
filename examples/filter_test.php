<?php

require_once __DIR__ . '/../vendor/autoload.php';

use KinopoiskDev\Types\ImageSearchFilter;
use KinopoiskDev\Types\MovieSearchFilter;
use KinopoiskDev\Types\PersonSearchFilter;
use KinopoiskDev\Types\ReviewSearchFilter;
use KinopoiskDev\Types\SeasonSearchFilter;

// Test ImageSearchFilter
echo "Testing ImageSearchFilter:\n";
$imageFilter = new ImageSearchFilter();
$imageFilter->movieId(123)
    ->type('poster')
    ->name('Test Image')
    ->enName('Test Image EN');
print_r($imageFilter->getFilters());

// Test MovieSearchFilter
echo "\nTesting MovieSearchFilter:\n";
$movieFilter = new MovieSearchFilter();
$movieFilter->searchByName('Test Movie')
    ->searchByDescription('Test Description')
    ->withMinRating(7.5)
    ->withMaxRating(9.0)
    ->withRatingBetween(7.5, 9.0);
print_r($movieFilter->getFilters());

// Test PersonSearchFilter
echo "\nTesting PersonSearchFilter:\n";
$personFilter = new PersonSearchFilter();
$personFilter->searchByName('Test Person')
    ->age(30)
    ->ageRange(25, 35);
print_r($personFilter->getFilters());

// Test ReviewSearchFilter
echo "\nTesting ReviewSearchFilter:\n";
$reviewFilter = new ReviewSearchFilter();
$reviewFilter->movieId(123)
    ->type('Позитивный')
    ->author('Test Author');
print_r($reviewFilter->getFilters());

// Test SeasonSearchFilter
echo "\nTesting SeasonSearchFilter:\n";
$seasonFilter = new SeasonSearchFilter();
$seasonFilter->movieId(123)
    ->number(1)
    ->seasonRange(1, 3);
print_r($seasonFilter->getFilters());

echo "\nAll tests completed successfully!\n";