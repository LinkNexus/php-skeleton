<?php

namespace App\Router\Exceptions;

class HttpNotFoundException extends \Exception
{
    /**
     * @param string $message The error message
     * @param int    $code    The error code
     */
    public function __construct(string $message = "Requested ressource was not found", int $code = 404)
    {
        http_response_code($code);
        parent::__construct($message, $code);
    }
}
