<?php

declare(strict_types=1);

namespace KinopoiskDev\Models;

class PersonInMovie {

	public function __construct(
		public readonly int $id,
		public readonly ?string $photo = null,
		public readonly ?string $name = null,
		public readonly ?string $enName = null,
		public readonly ?string $description = null,
		public readonly ?string $profession = null,
		public readonly ?string $enProfession = null,
	) {}

	/**
	 * Create PersonInMovie object from array (API response)
	 */
	public static function fromArray(array $data): self {
		return new self(
			id: $data['id'],
			photo: $data['photo'] ?? null,
			name: $data['name'] ?? null,
			enName: $data['enName'] ?? null,
			description: $data['description'] ?? null,
			profession: $data['profession'] ?? null,
			enProfession: $data['enProfession'] ?? null,
		);
	}

	/**
	 * Convert to array
	 */
	public function toArray(): array {
		return [
			'id' => $this->id,
			'photo' => $this->photo,
			'name' => $this->name,
			'enName' => $this->enName,
			'description' => $this->description,
			'profession' => $this->profession,
			'enProfession' => $this->enProfession,
		];
	}
}