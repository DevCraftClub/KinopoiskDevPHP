<?php

declare(strict_types=1);

namespace KinopoiskDev\Models;

class Video {

	public function __construct(
		public readonly ?string $url = null,
		public readonly ?string $name = null,
		public readonly ?string $site = null,
		public readonly ?int $size = null,
		public readonly ?string $type = null,
	) {}

	/**
	 * Create Video object from array (API response)
	 */
	public static function fromArray(array $data): self {
		return new self(
			url: $data['url'] ?? null,
			name: $data['name'] ?? null,
			site: $data['site'] ?? null,
			size: $data['size'] ?? null,
			type: $data['type'] ?? null,
		);
	}

	/**
	 * Convert to array
	 */
	public function toArray(): array {
		return [
			'url' => $this->url,
			'name' => $this->name,
			'site' => $this->site,
			'size' => $this->size,
			'type' => $this->type,
		];
	}
}