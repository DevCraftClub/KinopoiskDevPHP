<?php

declare(strict_types=1);

namespace KinopoiskDev\Models;

/**
 * Rating model for different rating systems
 */
class Rating {

	public function __construct(
		public readonly ?float $kp = NULL,
		public readonly ?float $imdb = NULL,
		public readonly ?float $tmdb = NULL,
		public readonly ?float $filmCritics = NULL,
		public readonly ?float $russianFilmCritics = NULL,
		public readonly ?float $await = NULL,
	) {}

	/**
	 * Create Rating object from array (API response)
	 */
	public static function fromArray(array $data): self {
		return new self(
			kp: isset($data['kp']) ? (float) $data['kp'] : NULL,
			imdb: isset($data['imdb']) ? (float) $data['imdb'] : NULL,
			tmdb: isset($data['tmdb']) ? (float) $data['tmdb'] : NULL,
			filmCritics: isset($data['filmCritics'])
				? (float) $data['filmCritics'] : NULL,
			russianFilmCritics: isset($data['russianFilmCritics'])
				? (float) $data['russianFilmCritics'] : NULL,
			await: isset($data['await']) ? (float) $data['await'] : NULL,
		);
	}

	/**
	 * Convert to array
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
	 * Get Kinopoisk rating
	 */
	public function getKinopoiskRating(): ?float {
		return $this->kp;
	}

	/**
	 * Get IMDB rating
	 */
	public function getImdbRating(): ?float {
		return $this->imdb;
	}

	/**
	 * Get TMDB rating
	 */
	public function getTmdbRating(): ?float {
		return $this->tmdb;
	}

	/**
	 * Get Film Critics rating
	 */
	public function getFilmCriticsRating(): ?float {
		return $this->filmCritics;
	}

	/**
	 * Get Russian Film Critics rating
	 */
	public function getRussianFilmCriticsRating(): ?float {
		return $this->russianFilmCritics;
	}

	/**
	 * Get await rating
	 */
	public function getAwaitRating(): ?float {
		return $this->await;
	}

	/**
	 * Get highest available rating
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
	 * Get average rating from available ratings
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
	 * Check if any rating exists
	 */
	public function hasAnyRating(): bool {
		return $this->kp !== NULL || $this->imdb !== NULL
		       || $this->tmdb !== NULL
		       || $this->filmCritics !== NULL
		       || $this->russianFilmCritics !== NULL
		       || $this->await !== NULL;
	}

	/**
	 * Get all available ratings as associative array
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

	/**
	 * String representation
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

}
