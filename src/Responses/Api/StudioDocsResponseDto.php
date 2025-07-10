<?php

namespace KinopoiskDev\Responses\Api;

use KinopoiskDev\Models\Studio;
use KinopoiskDev\Responses\BaseDocsResponseDto;
use KinopoiskDev\Utils\DataManager;

class StudioDocsResponseDto extends BaseDocsResponseDto {

	/**
	 * @inheritDoc
	 * @throws \KinopoiskDev\Exceptions\KinopoiskDevException
	 */
	public static function fromArray(array $data): static {
		return new self(
			docs : DataManager::parseObjectArray($data, 'docs', Studio::class),
			total: $data['total'] ?? 0,
			limit: $data['limit'] ?? 10,
			page : $data['page'] ?? 1,
			pages: $data['pages'] ?? 0,
		);
	}

}