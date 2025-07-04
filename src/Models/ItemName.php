<?php

declare(strict_types=1);

namespace KinopoiskDev\Models;

class ItemName {

	public function __construct(
		public readonly string $name,
	) {}

	/**
	 * Create ItemName object from array (API response)
	 */
	public static function fromArray(array $data): self {
		return new self(
			name: $data['name'],
		);
	}

	/**
	 * Convert to array
	 */
	public function toArray(): array {
		return [
			'name' => $this->name,
		];
	}
}