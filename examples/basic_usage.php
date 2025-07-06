<?php

require_once __DIR__ . '/../vendor/autoload.php';

use KinopoiskDev\Kinopoisk;
use KinopoiskDev\Exceptions\KinopoiskDevException;

// API-Token - erhalten Sie diesen von kinopoisk.dev
$apiToken = 'YOUR_API_TOKEN_HERE';

try {
	// Client initialisieren
	$client = new Kinopoisk($apiToken);

	// 1. Einzelnen Film abrufen
	echo "=== Film nach ID abrufen ===\n";
	$movie = $client->getMovieById(5394738); // Шпионская свадьба
	echo "Titel: {$movie->name}\n";
	echo "Jahr: {$movie->year}\n";
	echo "Bewertung: " . ($movie->getKinopoiskRating() ?? 'Keine') . "\n";
	echo "Genres: " . implode(', ', $movie->getGenreNames()) . "\n\n";

	// 2. Zufälligen Film abrufen
	echo "=== Zufälliger Film ===\n";
	$randomMovie = $client->getRandomMovie();
	echo "Titel: {$randomMovie->name}\n";
	echo "Jahr: {$randomMovie->year}\n\n";

	// 3. Filme suchen
	echo "=== Filme suchen (Komödien von 2020) ===\n";
	$searchResults = $client->searchMovies([
		'genres.name' => 'комедия',
		'year' => 2020,
		'rating.kp' => '7-10'
	], 1, 5);

	echo "Gefunden: {$searchResults['total']} Filme\n";
	foreach ($searchResults['docs'] as $movie) {
		echo "- {$movie->name} ({$movie->year}) - Bewertung: " .
		     ($movie->getKinopoiskRating() ?? 'N/A') . "\n";
	}

} catch (KinopoiskDevException $e) {
	echo "Fehler: " . $e->getMessage() . "\n";
	echo "Code: " . $e->getCode() . "\n";
} catch (Exception $e) {
	echo "Unerwarteter Fehler: " . $e->getMessage() . "\n";
}