<?php

namespace KinopoiskDev\Models;

interface BaseModel {

	public static function fromArray(array $data): self;
	public function toArray(): array;
}