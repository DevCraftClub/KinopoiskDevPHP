<?php

declare(strict_types=1);

namespace KinopoiskDev\Models;

class VideoTypes {

	public function __construct(
		public readonly ?array $trailers = null,
	) {}

	/**
	 * Create VideoTypes object from array (API response)
	 */
	public static function fromArray(array $data): self {
		$trailers = null;
		if (isset($data['trailers']) && is_array($data['trailers'])) {
			$trailers = array_map(fn($trailer) => Video::fromArray($trailer), $data['trailers']);
		}

		return new self(
			trailers: $trailers,
		);
	}

	/**
	 * Convert to array
	 */
	public function toArray(): array {
		$trailers = null;
		if ($this->trailers !== null) {
			$trailers = array_map(fn($trailer) => $trailer->toArray(), $this->trailers);
		}

		return [
			'trailers' => $trailers,
		];
	}
}