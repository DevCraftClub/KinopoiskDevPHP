<?php

declare(strict_types=1);

namespace KinopoiskDev\Models;

class Fees {

	public function __construct(
		public readonly ?CurrencyValue $world = null,
		public readonly ?CurrencyValue $russia = null,
		public readonly ?CurrencyValue $usa = null,
	) {}

	/**
	 * Create Fees object from array (API response)
	 */
	public static function fromArray(array $data): self {
		return new self(
			world: isset($data['world']) ? CurrencyValue::fromArray($data['world']) : null,
			russia: isset($data['russia']) ? CurrencyValue::fromArray($data['russia']) : null,
			usa: isset($data['usa']) ? CurrencyValue::fromArray($data['usa']) : null,
		);
	}

	/**
	 * Convert to array
	 */
	public function toArray(): array {
		return [
			'world' => $this->world?->toArray(),
			'russia' => $this->russia?->toArray(),
			'usa' => $this->usa?->toArray(),
		];
	}
}