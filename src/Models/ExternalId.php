<?php

declare(strict_types=1);

namespace KinopoiskDev\Models;

/**
 * External ID model for different movie databases
 */
class ExternalId {

	public function __construct(
		public readonly ?string $kpHD = NULL,
		public readonly ?string $imdb = NULL,
		public readonly ?int    $tmdb = NULL,
	) {}

	/**
	 * Create ExternalId object from array (API response)
	 */
	public static function fromArray(array $data): self {
		return new self(
			kpHD: $data['kpHD'] ?? NULL,
			imdb: $data['imdb'] ?? NULL,
			tmdb: isset($data['tmdb']) ? (int) $data['tmdb'] : NULL,
		);
	}

	/**
	 * Convert to array
	 */
	public function toArray(): array {
		return [
			'kpHD' => $this->kpHD,
			'imdb' => $this->imdb,
			'tmdb' => $this->tmdb,
		];
	}

	/**
	 * Get Kinopoisk HD ID
	 */
	public function getKinopoiskHdId(): ?string {
		return $this->kpHD;
	}

	/**
	 * Get IMDB ID
	 */
	public function getImdbId(): ?string {
		return $this->imdb;
	}

	/**
	 * Get TMDB ID
	 */
	public function getTmdbId(): ?int {
		return $this->tmdb;
	}

	/**
	 * Get IMDB URL if ID exists
	 */
	public function getImdbUrl(): ?string {
		return $this->imdb ? "https://www.imdb.com/title/{$this->imdb}/" : NULL;
	}

	/**
	 * Get TMDB URL if ID exists
	 */
	public function getTmdbUrl(): ?string {
		return $this->tmdb ? "https://www.themoviedb.org/movie/{$this->tmdb}"
			: NULL;
	}

	/**
	 * Check if any external ID exists
	 */
	public function hasAnyId(): bool {
		return $this->kpHD !== NULL || $this->imdb !== NULL
		       || $this->tmdb !== NULL;
	}

	/**
	 * Check if IMDB ID exists
	 */
	public function hasImdbId(): bool {
		return $this->imdb !== NULL;
	}

	/**
	 * Check if TMDB ID exists
	 */
	public function hasTmdbId(): bool {
		return $this->tmdb !== NULL;
	}

	/**
	 * Check if Kinopoisk HD ID exists
	 */
	public function hasKinopoiskHdId(): bool {
		return $this->kpHD !== NULL;
	}

	/**
	 * Get all available IDs as associative array
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
	 * String representation
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

}