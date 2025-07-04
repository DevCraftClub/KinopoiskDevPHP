<?php

namespace KinopoiskDev\Models;

class FactInMovie {

	public function __construct(
		public readonly string $value,
		public readonly ?string $type = null,
		public readonly ?bool $spoiler = null,
	) {

	}
}