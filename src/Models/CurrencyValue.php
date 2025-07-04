<?php

declare(strict_types=1);

namespace KinopoiskDev\Models;

class CurrencyValue {

	public function __construct(
		public readonly ?int $value = null,
		public readonly ?string $currency = null,
	) {}

	/**
	 * Create CurrencyValue object from array (API response)
	 */
	public static function fromArray(array $data): self {
		return new self(
			value: $data['value'] ?? null,
			currency: $data['currency'] ?? null,
		);
	}

	/**
	 * Convert to array
	 */
	public function toArray(): array {
		return [
			'value' => $this->value,
			'currency' => $this->currency,
		];
	}
}