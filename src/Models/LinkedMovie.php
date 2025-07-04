<?php

declare(strict_types=1);

namespace KinopoiskDev\Models;

use KinopoiskDev\Enums\MovieType;

class LinkedMovie {

	public function __construct(
		public readonly int         $id,
		public readonly ?string     $name = NULL,
		public readonly ?string     $enName = NULL,
		public readonly ?string     $alternativeName = NULL,
		public readonly ?MovieType  $type = NULL,
		public readonly ?ShortImage $poster = NULL,
		public readonly ?Rating     $rating = NULL,
		public readonly ?int        $year = NULL,
	) {}

	/**
	 * Create LinkedMovie object from array (API response)
	 */
	public static function fromArray(array $data): self {
		return new self(
			id             : $data['id'],
			name           : $data['name'] ?? NULL,
			enName         : $data['enName'] ?? NULL,
			alternativeName: $data['alternativeName'] ?? NULL,
			type           : isset($data['type']) ? MovieType::tryFrom($data['type']) : NULL,
			poster         : isset($data['poster']) ? ShortImage::fromArray($data['poster']) : NULL,
			rating         : isset($data['rating']) ? Rating::fromArray($data['rating']) : NULL,
			year           : $data['year'] ?? NULL,
		);
	}

	/**
	 * Convert to array
	 */
	public function toArray(): array {
		return [
			'id'              => $this->id,
			'name'            => $this->name,
			'enName'          => $this->enName,
			'alternativeName' => $this->alternativeName,
			'type'            => $this->type?->value,
			'poster'          => $this->poster?->toArray(),
			'rating'          => $this->rating?->toArray(),
			'year'            => $this->year,
		];
	}

}
