<?php

declare(strict_types=1);

namespace KinopoiskDev\Models;

class Networks {

	public function __construct(
		public readonly ?array $items = null,
	) {}

	/**
	 * Create Networks object from array (API response)
	 */
	public static function fromArray(array $data): self {
		$items = null;
		if (isset($data['items']) && is_array($data['items'])) {
			$items = array_map(fn($item) => NetworkItem::fromArray($item), $data['items']);
		}

		return new self(
			items: $items,
		);
	}

	/**
	 * Convert to array
	 */
	public function toArray(): array {
		$items = null;
		if ($this->items !== null) {
			$items = array_map(fn($item) => $item->toArray(), $this->items);
		}

		return [
			'items' => $items,
		];
	}
}