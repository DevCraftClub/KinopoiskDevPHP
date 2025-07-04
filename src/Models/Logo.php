<?php

declare(strict_types=1);

namespace KinopoiskDev\Models;

class Logo {

	public function __construct(
		public readonly ?string $url = null,
	) {}

	/**
	 * Create Logo object from array (API response)
	 */
	public static function fromArray(array $data): self {
		return new self(
			url: $data['url'] ?? null,
		);
	}

	/**
	 * Convert to array
	 */
	public function toArray(): array {
		return [
			'url' => $this->url,
		];
	}
}