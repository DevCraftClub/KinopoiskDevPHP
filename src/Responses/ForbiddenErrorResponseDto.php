<?php

declare(strict_types=1);

namespace KinopoiskDev\Responses;

class ForbiddenErrorResponseDto {

	public function __construct(
		public readonly int $statusCode = 403,
		public readonly string $message = 'Превышен дневной лимит!',
		public readonly string $error = 'Forbidden',
	) {}

	/**
	 * Create from array
	 */
	public static function fromArray(array $data): self {
		return new self(
			statusCode: $data['statusCode'] ?? 403,
			message: $data['message'] ?? 'Превышен дневной лимит!',
			error: $data['error'] ?? 'Forbidden',
		);
	}

	/**
	 * Convert to array
	 */
	public function toArray(): array {
		return [
			'statusCode' => $this->statusCode,
			'message' => $this->message,
			'error' => $this->error,
		];
	}
}