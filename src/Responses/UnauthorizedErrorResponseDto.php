<?php

declare(strict_types=1);

namespace KinopoiskDev\Responses;

class UnauthorizedErrorResponseDto {

	public function __construct(
		public readonly int $statusCode = 401,
		public readonly string $message = 'В запросе не указан токен!',
		public readonly string $error = 'Unauthorized',
	) {}

	/**
	 * Create from array
	 */
	public static function fromArray(array $data): self {
		return new self(
			statusCode: $data['statusCode'] ?? 401,
			message: $data['message'] ?? 'В запросе не указан токен!',
			error: $data['error'] ?? 'Unauthorized',
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