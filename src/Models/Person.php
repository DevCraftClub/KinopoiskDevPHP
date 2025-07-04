<?php

declare(strict_types=1);

namespace KinopoiskDev\Models;

use KinopoiskDev\Enums\PersonProfession;
use KinopoiskDev\Enums\PersonSex;

/**
 * Person model for actors, directors, etc.
 */
class Person {

	public function __construct(
		public readonly int               $id,
		public readonly ?string           $photo = NULL,
		public readonly ?string           $name = NULL,
		public readonly ?string           $enName = NULL,
		public readonly ?string           $description = NULL,
		public readonly ?string           $profession = NULL,
		public readonly ?PersonProfession $enProfession = NULL,
		public readonly ?PersonSex        $sex = NULL,
		public readonly ?int              $growth = NULL,
		public readonly ?string           $birthday = NULL,
		public readonly ?string           $death = NULL,
		public readonly ?int              $age = NULL,
		public readonly array             $birthPlace = [],
		public readonly array             $deathPlace = [],
		public readonly array             $spouses = [],
		public readonly ?int              $countAwards = NULL,
		public readonly array             $facts = [],
		public readonly array             $movies = [],
		public readonly ?string           $updatedAt = NULL,
		public readonly ?string           $createdAt = NULL,
	) {}

	/**
	 * Create Person object from array (API response)
	 */
	public static function fromArray(array $data): self {
		return new self(
			id          : $data['id'],
			photo       : $data['photo'] ?? NULL,
			name        : $data['name'] ?? NULL,
			enName      : $data['enName'] ?? NULL,
			description : $data['description'] ?? NULL,
			profession  : $data['profession'] ?? NULL,
			enProfession: isset($data['enProfession']) ? PersonProfession::tryFrom($data['enProfession']) : NULL,
			sex         : isset($data['sex']) ? PersonSex::tryFrom($data['sex']) : NULL,
			growth      : $data['growth'] ?? NULL,
			birthday    : $data['birthday'] ?? NULL,
			death       : $data['death'] ?? NULL,
			age         : $data['age'] ?? NULL,
			birthPlace  : $data['birthPlace'] ?? [],
			deathPlace  : $data['deathPlace'] ?? [],
			spouses     : $data['spouses'] ?? [],
			countAwards : $data['countAwards'] ?? NULL,
			facts       : $data['facts'] ?? [],
			movies      : $data['movies'] ?? [],
			updatedAt   : $data['updatedAt'] ?? NULL,
			createdAt   : $data['createdAt'] ?? NULL,
		);
	}

	/**
	 * Convert to array
	 */
	public function toArray(): array {
		return [
			'id'           => $this->id,
			'photo'        => $this->photo,
			'name'         => $this->name,
			'enName'       => $this->enName,
			'description'  => $this->description,
			'profession'   => $this->profession,
			'enProfession' => $this->enProfession?->value,
			'sex'          => $this->sex?->value,
			'growth'       => $this->growth,
			'birthday'     => $this->birthday,
			'death'        => $this->death,
			'age'          => $this->age,
			'birthPlace'   => $this->birthPlace,
			'deathPlace'   => $this->deathPlace,
			'spouses'      => $this->spouses,
			'countAwards'  => $this->countAwards,
			'facts'        => $this->facts,
			'movies'       => $this->movies,
			'updatedAt'    => $this->updatedAt,
			'createdAt'    => $this->createdAt,
		];
	}

	/**
	 * Get best available name
	 */
	public function getBestName(): ?string {
		return $this->name ?? $this->enName;
	}

	/**
	 * Get photo URL
	 */
	public function getPhotoUrl(): ?string {
		return $this->photo;
	}

	/**
	 * Get profession in Russian
	 */
	public function getProfessionRu(): ?string {
		return $this->profession;
	}

	/**
	 * Get profession in English
	 */
	public function getProfessionEn(): ?string {
		return $this->enProfession;
	}

	/**
	 * Get role description
	 */
	public function getRoleDescription(): ?string {
		return $this->description;
	}

	/**
	 * Check if person is actor
	 */
	public function isActor(): bool {
		return $this->enProfession === PersonProfession::ACTOR ||
		       $this->profession === 'актеры' ||
		       $this->profession === 'актер';
	}

	/**
	 * Check if person is director
	 */
	public function isDirector(): bool {
		return $this->enProfession === PersonProfession::DIRECTOR ||
		       $this->profession === 'режиссеры' ||
		       $this->profession === 'режиссер';
	}

	/**
	 * Check if person is writer
	 */
	public function isWriter(): bool {
		return $this->enProfession === PersonProfession::WRITER ||
		       $this->profession === 'сценаристы' ||
		       $this->profession === 'сценарист';
	}

	/**
	 * Check if person is producer
	 */
	public function isProducer(): bool {
		return $this->enProfession === PersonProfession::PRODUCER ||
		       $this->profession === 'продюсеры' ||
		       $this->profession === 'продюсер';
	}

	/**
	 * Check if person is composer
	 */
	public function isComposer(): bool {
		return $this->enProfession === PersonProfession::COMPOSER ||
		       $this->profession === 'композиторы' ||
		       $this->profession === 'композитор';
	}

	/**
	 * Get person's role category
	 */
	public function getRoleCategory(): string {
		if ($this->isActor()) {
			return PersonProfession::ACTOR->value;
		}
		if ($this->isDirector()) {
			return PersonProfession::DIRECTOR->value;
		}
		if ($this->isWriter()) {
			return PersonProfession::WRITER->value;
		}
		if ($this->isProducer()) {
			return PersonProfession::PRODUCER->value;
		}
		if ($this->isComposer()) {
			return PersonProfession::COMPOSER->value;
		}

		return PersonProfession::OTHER->value;
	}

	/**
	 * Get formatted name with role
	 */
	public function getFormattedNameWithRole(): string {
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
	public function __toString(): string {
		return $this->getFormattedNameWithRole();
	}

}
