<?php

declare(strict_types=1);

namespace KinopoiskDev\Models;

class ShortImage {

	public function __construct(
		public readonly ?string $url = null,
		public readonly ?string $previewUrl = null,
	) {}

	/**
	 * Create ShortImage object from array (API response)
	 */
	public static function fromArray(array $data): self {
		return new self(
			url: $data['url'] ?? null,
			previewUrl: $data['previewUrl'] ?? null,
		);
	}

	/**
	 * Convert to array
	 */
	public function toArray(): array {
		return [
			'url' => $this->url,
			'previewUrl' => $this->previewUrl,
		];
	}
}