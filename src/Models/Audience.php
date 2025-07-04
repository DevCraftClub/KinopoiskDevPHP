<?php

declare(strict_types=1);

namespace KinopoiskDev\Models;

class Audience {

	public function __construct(
		public readonly ?int $count = null,
		public readonly ?string $country = null,
	) {}

	/**
	 * Create Audience object from array (API response)
	 */
	public static function fromArray(array $data): self {
		return new self(
			count: $data['count'] ?? null,
			country: $data['country'] ?? null,
		);
	}

	/**
	 * Convert to array
	 */
	public function toArray(): array {
		return [
			'count' => $this->count,
			'country' => $this->country,
		];
	}
}