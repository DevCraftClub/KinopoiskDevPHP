<?php

declare(strict_types=1);

namespace KinopoiskDev\Models;

class YearRange {

	public function __construct(
		public readonly ?int $start = null,
		public readonly ?int $end = null,
	) {}

	/**
	 * Create YearRange object from array (API response)
	 */
	public static function fromArray(array $data): self {
		return new self(
			start: $data['start'] ?? null,
			end: $data['end'] ?? null,
		);
	}

	/**
	 * Convert to array
	 */
	public function toArray(): array {
		return [
			'start' => $this->start,
			'end' => $this->end,
		];
	}
}