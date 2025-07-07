<?php

declare(strict_types=1);

namespace KinopoiskDev\Exceptions;

use Exception;

class KinopoiskResponseException extends Exception {

	public function __construct(
		string $rspnsCls = '',
		?Exception $previous = NULL,
	) {
		if (!empty($rspnsCls)) {
			$response = new $rspnsCls();

			parent::__construct("{$response->error}: {$response->message}", $response->statusCode, $previous);
		} else {
			parent::__construct('', 0, $previous);
		}

	}

}