<?php

declare(strict_types=1);

namespace KinopoiskDev\Models;

/**
 * Person model for actors, directors, etc.
 */
class Person
{
	public function __construct(
		public readonly int $id,
		public readonly ?string $photo = null,
		public readonly ?string $name = null,
		public readonly ?string $enName = null,
		public readonly ?string $description = null,
		public readonly ?string $profession = null,
		public readonly ?string $enProfession = null,
		public readonly ?string $sex = null,
		public readonly ?int $growth = null,
		public readonly ?string $birthday = null,
		public readonly ?string $death = null,
		public readonly ?int $age = null,
		public readonly array $birthPlace = [],
		public readonly array $deathPlace = [],
		public readonly array $spouses = [],
		public readonly ?int $countAwards = null,
		public readonly array $facts = [],
		public readonly array $movies = [],
		public readonly ?string $updatedAt = null,
		public readonly ?string $createdAt = null,
	) {}

	/**
	 * Create Person object from array (API response)
	 */
	public static function fromArray(array $data): self
	{
		return new self(
			id: $data['id'],
			photo: $data['photo'] ?? null,
			name: $data['name'] ?? null,
			enName: $data['enName'] ?? null,
			description: $data['description'] ?? null,
			profession: $data['profession'] ?? null,
			enProfession: $data['enProfession'] ?? null,
			sex: $data['sex'] ?? null,
			growth: $data['growth'] ?? null,
			birthday: $data['birthday'] ?? null,
			death: $data['death'] ?? null,
			age: $data['age'] ?? null,
			birthPlace: $data['birthPlace'] ?? [],
			deathPlace: $data['deathPlace'] ?? [],
			spouses: $data['spouses'] ?? [],
			countAwards: $data['countAwards'] ?? null,
			facts: $data['facts'] ?? [],
			movies: $data['movies'] ?? [],
			updatedAt: $data['updatedAt'] ?? null,
			createdAt: $data['createdAt'] ?? null,
		);
	}

	/**
	 * Convert to array
	 */
	public function toArray(): array
	{
		return [
			'id' => $this->id,
			'photo' => $this->photo,
			'name' => $this->name,
			'enName' => $this->enName,
			'description' => $this->description,
			'profession' => $this->profession,
			'enProfession' => $this->enProfession,
			'sex' => $this->sex,
			'growth' => $this->growth,
			'birthday' => $this->birthday,
			'death' => $this->death,
			'age' => $this->age,
			'birthPlace' => $this->birthPlace,
			'deathPlace' => $this->deathPlace,
			'spouses' => $this->spouses,
			'countAwards' => $this->countAwards,
			'facts' => $this->facts,
			'movies' => $this->movies,
			'updatedAt' => $this->updatedAt,
			'createdAt' => $this->createdAt,
		];
	}

	/**
	 * Get best available name
	 */
	public function getBestName(): ?string
	{
		return $this->name ?? $this->enName;
	}

	/**
	 * Get photo URL
	 */
	public function getPhotoUrl(): ?string
	{
		return $this->photo;
	}

	/**
	 * Get profession in Russian
	 */
	public function getProfessionRu(): ?string
	{
		return $this->profession;
	}

	/**
	 * Get profession in English
	 */
	public function getProfessionEn(): ?string
	{
		return $this->enProfession;
	}

	/**
	 * Get role description
	 */
	public function getRoleDescription(): ?string
	{
		return $this->description;
	}

	/**
	 * Check if person is actor
	 */
	public function isActor(): bool
	{
		return $this->enProfession === 'actor' ||
		       $this->profession === 'актеры' ||
		       $this->profession === 'актер';
	}

	/**
	 * Check if person is director
	 */
	public function isDirector(): bool
	{
		return $this->enProfession === 'director' ||
		       $this->profession === 'режиссеры' ||
		       $this->profession === 'режиссер';
	}

	/**
	 * Check if person is writer
	 */
	public function isWriter(): bool
	{
		return $this->enProfession === 'writer' ||
		       $this->profession === 'сценаристы' ||
		       $this->profession === 'сценарист';
	}

	/**
	 * Check if person is producer
	 */
	public function isProducer(): bool
	{
		return $this->enProfession === 'producer' ||
		       $this->profession === 'продюсеры' ||
		       $this->profession === 'продюсер';
	}

	/**
	 * Check if person is composer
	 */
	public function isComposer(): bool
	{
		return $this->enProfession === 'composer' ||
		       $this->profession === 'композиторы' ||
		       $this->profession === 'композитор';
	}

	/**
	 * Get person's role category
	 */
	public function getRoleCategory(): string
	{
		if ($this->isActor()) return 'actor';
		if ($this->isDirector()) return 'director';
		if ($this->isWriter()) return 'writer';
		if ($this->isProducer()) return 'producer';
		if ($this->isComposer()) return 'composer';

		return 'other';
	}

	/**
	 * Get formatted name with role
	 */
	public function getFormattedNameWithRole(): string
	{
		$name = $this->getBestName() ?? "Person #{$this->id}";
		$role = $this->description;

		if ($role) {
			return "{$name} ({$role})";
		}

		return $name;
	}

	/**
	 * String representation
	 */
	public function __toString(): string
	{
		return $this->getFormattedNameWithRole();
	}
}
