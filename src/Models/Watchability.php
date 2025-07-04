<?php

declare(strict_types=1);

namespace KinopoiskDev\Models;

class Watchability {

	public function __construct(
		public readonly array $items = [],
	) {}

	/**
	 * Create Watchability object from array (API response)
	 */
	public static function fromArray(array $data): self {
		$items = [];
		if (isset($data['items']) && is_array($data['items'])) {
			$items = array_map(fn($item) => WatchabilityItem::fromArray($item), $data['items']);
		}

		return new self(
			items: $items,
		);
	}

	/**
	 * Convert to array
	 */
	public function toArray(): array {
		return [
			'items' => array_map(fn($item) => $item->toArray(), $this->items),
		];
	}
}