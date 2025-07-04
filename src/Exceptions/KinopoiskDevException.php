<?php

declare(strict_types=1);

namespace KinopoiskDev\Exceptions;

use Exception;

class KinopoiskDevException extends Exception {

	public function __construct(
		string $message = '',
		int $code = 0,
		?Exception $previous = NULL,
	) {
		parent::__construct($message, $code, $previous);
	}

}