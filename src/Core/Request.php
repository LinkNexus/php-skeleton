<?php

namespace App\Core;

/**
 * Class Request
 *
 * Represents an HTTP request, providing methods to access the request URI and method.
 */
class Request
{
    /**
     * Returns the current URI of the current request.
     *
     * @return string The current URI of the request, excluding the query string.
     */
    public function getPathUri(): string
    {
        return parse_url($_SERVER['REQUEST_URI'] ?? "/", PHP_URL_PATH);
    }


    /**
     * Returns the current HTTP method of the request.
     *
     * @return string The HTTP method of the request, in lowercase.
     */
    public function getMethod(): string
    {
        return strtolower($_SERVER['REQUEST_METHOD']) ?? 'get';
    }

}
