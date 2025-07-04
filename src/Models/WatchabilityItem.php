<?php

declare(strict_types=1);

namespace KinopoiskDev\Models;

class WatchabilityItem {

	public function __construct(
		public readonly ?string $name = null,
		public readonly Logo $logo,
		public readonly string $url,
	) {}

	/**
	 * Create WatchabilityItem object from array (API response)
	 */
	public static function fromArray(array $data): self {
		return new self(
			name: $data['name'] ?? null,
			logo: Logo::fromArray($data['logo']),
			url: $data['url'],
		);
	}

	/**
	 * Convert to array
	 */
	public function toArray(): array {
		return [
			'name' => $this->name,
			'logo' => $this->logo->toArray(),
			'url' => $this->url,
		];
	}
}