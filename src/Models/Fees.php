<?php

declare(strict_types=1);

namespace KinopoiskDev\Models;

class Fees {

//Methods
	public function __construct(
		public readonly ?CurrencyValue $world = NULL,
		public readonly ?CurrencyValue $russia = NULL,
		public readonly ?CurrencyValue $usa = NULL,
	) {}

	/**
	 * Create Fees object from array (API response)
	 */
	public static function fromArray(array $data): self {
		return new self(
			world : isset($data['world']) ? CurrencyValue::fromArray($data['world']) : NULL,
			russia: isset($data['russia']) ? CurrencyValue::fromArray($data['russia']) : NULL,
			usa   : isset($data['usa']) ? CurrencyValue::fromArray($data['usa']) : NULL,
		);
	}

	/**
	 * Convert to array
	 */
	public function toArray(): array {
		return [
			'world'  => $this->world?->toArray(),
			'russia' => $this->russia?->toArray(),
			'usa'    => $this->usa?->toArray(),
		];
	}
//Methods

}