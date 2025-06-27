<?php

namespace App\Core;

class Response
{
    /**
     * @param string                $content    The content of the response.
     * @param int                   $statusCode The HTTP status code of the response.
     * @param array<string, string> $headers    An associative array of headers to be sent with the response.
     */
    public function __construct(
        private string $content,
        private int $statusCode = 200,
        private array $headers = []
    ) {
    }

    /**
     * Returns the content of the response.
     *
     * @return string The content of the response.
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * Returns the HTTP status code of the response.
     *
     * @return int The HTTP status code.
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Returns the headers to be sent with the response.
     *
     * @return array<string, string> An associative array of headers.
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

}
