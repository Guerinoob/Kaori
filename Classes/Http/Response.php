<?php
/**
 * Response class
 */

namespace App\Http;

/**
 * This class represents a response to a HTTP request. It contains the HTTP status code, the response headers and the response content
 * 
 * @see Request
 */
class Response {
    
    /**
     * The response HTTP status code
     *
     * @var int
     */
    private $status_code;
    
    /**
     * The response headers
     *
     * @var array
     */
    private $headers;
    
    /**
     * The response content
     *
     * @var mixed
     */
    private $content;
    
    /**
     * Sets the response HTTP status code
     *
     * @param  int $status_code The status code
     * @return Response The response instance
     */
    public function setStatusCode($status_code): Response
    {
        $this->status_code = $status_code;
        return $this;
    }
    
    /**
     * Returns the response status code
     *
     * @return int The response status code
     */
    public function getStatusCode(): int
    {
        return $this->status_code;
    }

    /**
     * Sets the response headers
     *
     * @param  int $headers The headers
     * @return Response The response instance
     */
    public function setHeaders($headers): Response
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * Returns the response headers
     *
     * @return int The response headers
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Sets the response content
     *
     * @param  int $content The content
     * @return Response The response instance
     */
    public function setContent($content): Response
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Returns the response content
     *
     * @return mixed The response content
     */
    public function getContent(): mixed
    {
        return $this->content;
    }

}