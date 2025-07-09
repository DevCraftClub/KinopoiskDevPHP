<?php

namespace KinopoiskDev\Models;

readonly class Keyword implements BaseModel {

	public function __construct(
		public int $id,
		public ?string $title = NULL,
		public array $movies = [],
		public string $updatedAt,
		public string $createdAt,
	) {}

	public static function fromArray(array $data): BaseModel {
		// TODO: Implement fromArray() method.
	}

	public function toArray(): array {
		// TODO: Implement toArray() method.
	}

}