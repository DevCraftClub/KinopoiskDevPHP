<?php

namespace KinopoiskDev\Models;

class FactInMovie {

	public function __construct(
		public readonly string  $value,
		public readonly ?string $type = NULL,
		public readonly ?bool   $spoiler = NULL,
	) {}

}