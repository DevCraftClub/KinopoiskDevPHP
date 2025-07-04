<?php

declare(strict_types=1);

namespace KinopoiskDev\Models;

class NetworkItem {

	public function __construct(
		public readonly ?string $name = null,
		public readonly ?Logo $logo = null,
	) {}

	/**
	 * Create NetworkItemV1_4 object from array (API response)
	 */
	public static function fromArray(array $data): self {
		return new self(
			name: $data['name'] ?? null,
			logo: isset($data['logo']) ? Logo::fromArray($data['logo']) : null,
		);
	}

	/**
	 * Convert to array
	 */
	public function toArray(): array {
		return [
			'name' => $this->name,
			'logo' => $this->logo?->toArray(),
		];
	}
}