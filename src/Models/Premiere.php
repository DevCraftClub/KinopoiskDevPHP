<?php

declare(strict_types=1);

namespace KinopoiskDev\Models;

class Premiere {

	public function __construct(
		public readonly ?string $country = null,
		public readonly ?string $world = null,
		public readonly ?string $russia = null,
		public readonly ?string $digital = null,
		public readonly ?string $cinema = null,
		public readonly ?string $bluray = null,
		public readonly ?string $dvd = null,
	) {}

	/**
	 * Create Premiere object from array (API response)
	 */
	public static function fromArray(array $data): self {
		return new self(
			country: $data['country'] ?? null,
			world: $data['world'] ?? null,
			russia: $data['russia'] ?? null,
			digital: $data['digital'] ?? null,
			cinema: $data['cinema'] ?? null,
			bluray: $data['bluray'] ?? null,
			dvd: $data['dvd'] ?? null,
		);
	}

	/**
	 * Convert to array
	 */
	public function toArray(): array {
		return [
			'country' => $this->country,
			'world' => $this->world,
			'russia' => $this->russia,
			'digital' => $this->digital,
			'cinema' => $this->cinema,
			'bluray' => $this->bluray,
			'dvd' => $this->dvd,
		];
	}
}