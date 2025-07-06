<?php

declare(strict_types=1);

namespace KinopoiskDev\Responses;

class ErrorResponseDto {

	public function __construct(
		public readonly int $statusCode,
		public readonly string $message,
		public readonly string $error,
	) {}

	/**
	 * Create from array
	 */
	public static function fromArray(array $data): self {
		return new self(
			statusCode: $data['statusCode'],
			message: $data['message'],
			error: $data['error'],
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