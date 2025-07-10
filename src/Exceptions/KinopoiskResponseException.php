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
			
			// Безопасный доступ к свойствам через reflection или приведение типа
			$error = property_exists($response, 'error') ? $response->error : 'Unknown error';
			$message = property_exists($response, 'message') ? $response->message : 'Unknown message';
			$statusCode = property_exists($response, 'statusCode') ? $response->statusCode : 0;

			parent::__construct("{$error}: {$message}", $statusCode, $previous);
		} else {
			parent::__construct('', 0, $previous);
		}

	}

}