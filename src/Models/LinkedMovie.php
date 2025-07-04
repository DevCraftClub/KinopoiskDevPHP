<?php

declare(strict_types=1);

namespace KinopoiskDev\Models;

class LinkedMovie {

	public function __construct(
		public readonly int $id,
		public readonly ?string $name = null,
		public readonly ?string $enName = null,
		public readonly ?string $alternativeName = null,
		public readonly ?string $type = null,
		public readonly ?ShortImage $poster = null,
		public readonly ?Rating $rating = null,
		public readonly ?int $year = null,
	) {}

	/**
	 * Create LinkedMovie object from array (API response)
	 */
	public static function fromArray(array $data): self {
		return new self(
			id: $data['id'],
			name: $data['name'] ?? null,
			enName: $data['enName'] ?? null,
			alternativeName: $data['alternativeName'] ?? null,
			type: $data['type'] ?? null,
			poster: isset($data['poster']) ? ShortImage::fromArray($data['poster']) : null,
			rating: isset($data['rating']) ? Rating::fromArray($data['rating']) : null,
			year: $data['year'] ?? null,
		);
	}

	/**
	 * Convert to array
	 */
	public function toArray(): array {
		return [
			'id' => $this->id,
			'name' => $this->name,
			'enName' => $this->enName,
			'alternativeName' => $this->alternativeName,
			'type' => $this->type,
			'poster' => $this->poster?->toArray(),
			'rating' => $this->rating?->toArray(),
			'year' => $this->year,
		];
	}
}