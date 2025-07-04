<?php

declare(strict_types=1);

namespace KinopoiskDev\Models;

class SeasonInfo {

	public function __construct(
		public readonly ?int $number = null,
		public readonly ?int $episodesCount = null,
	) {}

	/**
	 * Create SeasonInfo object from array (API response)
	 */
	public static function fromArray(array $data): self {
		return new self(
			number: $data['number'] ?? null,
			episodesCount: $data['episodesCount'] ?? null,
		);
	}

	/**
	 * Convert to array
	 */
	public function toArray(): array {
		return [
			'number' => $this->number,
			'episodesCount' => $this->episodesCount,
		];
	}
}