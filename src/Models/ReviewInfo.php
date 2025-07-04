<?php

declare(strict_types=1);

namespace KinopoiskDev\Models;

class ReviewInfo {

	public function __construct(
		public readonly ?int $count = null,
		public readonly ?int $positiveCount = null,
		public readonly ?string $percentage = null,
	) {}

	/**
	 * Create ReviewInfo object from array (API response)
	 */
	public static function fromArray(array $data): self {
		return new self(
			count: $data['count'] ?? null,
			positiveCount: $data['positiveCount'] ?? null,
			percentage: $data['percentage'] ?? null,
		);
	}

	/**
	 * Convert to array
	 */
	public function toArray(): array {
		return [
			'count' => $this->count,
			'positiveCount' => $this->positiveCount,
			'percentage' => $this->percentage,
		];
	}
}