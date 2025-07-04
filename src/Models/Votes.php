<?php

declare(strict_types=1);

namespace KinopoiskDev\Models;

/**
 * Votes model for different voting systems
 */
class Votes {

	public function __construct(
		public readonly ?int $kp = NULL,
		public readonly ?int $imdb = NULL,
		public readonly ?int $tmdb = NULL,
		public readonly ?int $filmCritics = NULL,
		public readonly ?int $russianFilmCritics = NULL,
		public readonly ?int $await = NULL,
	) {}

	/**
	 * Create Votes object from array (API response)
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
	 * Get total vote count from all platforms
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
	 * Get platform with most votes
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
	 * Get all available votes as associative array
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
	 * Check if any votes exist
	 */
	public function hasAnyVotes(): bool {
		return $this->kp !== NULL || $this->imdb !== NULL
		       || $this->tmdb !== NULL
		       || $this->filmCritics !== NULL
		       || $this->russianFilmCritics !== NULL
		       || $this->await !== NULL;
	}

	/**
	 * Format vote count with K/M suffixes
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
	 * Get formatted Kinopoisk votes
	 */
	public function getFormattedKpVotes(): ?string {
		return $this->kp ? $this->formatVoteCount($this->kp) : NULL;
	}

	/**
	 * Get formatted IMDB votes
	 */
	public function getFormattedImdbVotes(): ?string {
		return $this->imdb ? $this->formatVoteCount($this->imdb) : NULL;
	}

	/**
	 * String representation
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