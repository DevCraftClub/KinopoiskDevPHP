<?php

namespace KinopoiskDev\Models;

class Name {

	public function __construct(
		public readonly string $name,
		public readonly ?string $language = null,
		public readonly ?string $type = null
	) {
	}

	public static function fromArray(array $data): self {
		return new self(
			name: $data['name'],
			language: $data['language'] ?? null,
			type: $data['type'] ?? null
		);
	}

	public function toArray(): array {
		return [
			'name' => $this->name,
			'language' => $this->language,
			'type' => $this->type
		];
	}
}