<?php
/**
 * Request class
 */

namespace App\Http;

/**
 * This class represents a HTTP request. It contains different information such as the request url, body, headers etc, and returns a Response object when run
 * 
 * @see Response
 */
class Request {    
    /**
     * The request url
     *
     * @var string
     */
    private $url;
    
    /**
     * The request HTTP method
     *
     * @var mixed
     */
    private $method;
    
    /**
     * The request headers
     *
     * @var mixed
     */
    private $headers;
    
    /**
     * The request data
     *
     * @var mixed
     */
    private $body;
    
    /**
     * The request timeout
     *
     * @var mixed
     */
    private $timeout;

    public function __construct()
    {
        $this->headers = [];
    }
    
    /**
     * Sets the request url
     *
     * @param  string $url The request url
     * @return self The request instance
     */
    public function setUrl($url): Request
    {
        $this->url = $url;
        return $this;
    }
    
    /**
     * Returns the request url
     *
     * @return string The request url
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * Sets the request method
     *
     * @param  string $method The request method
     * @return self The request instance
     */
    public function setMethod($method): Request
    {
        $this->method = $method;
        return $this;
    }

    /**
     * Returns the request method
     *
     * @return string The request method
     */
    public function getMethod(): string
    {
        return $this->method;
    }
    
    /**
     * Adds a single or multiple headers to the request
     *
     * @param  string|array $headers An array of headers or a single header
     * @return self The request instance
     */
    public function addHeaders($headers): Request
    {
        $headers = Http::parseHeaders($headers);

        foreach($headers as $key => $value) {
            if (!isset($this->headers[$key]))
                $this->headers[$key] = $value;
            else
                $this->headers[$key] = array_merge([$this->headers[$key]], [$value]);
        }

        return $this;
    }
    
    /**
     * Returns the request headers
     *
     * @return array A key/value array of HTTP headers
     */
    public function getHeaders() : array
    {
        return $this->headers;
    }

    /**
     * Sets the request body
     *
     * @param  array $body A key/value array of data to pass to the request
     * @return self The request instance
     */
    public function setBody($body): Request
    {
        $this->body = $body;
        return $this;
    }

    /**
     * Returns the request body
     *
     * @return array The request body
     */
    public function getBody(): array
    {
        return $this->body;
    }

    /**
     * Sets the request timeout in seconds
     *
     * @param  int $timeout The request timeout
     * @return self The request instance
     */
    public function setTimeout($timeout): Request
    {
        $this->timeout = $timeout;
        return $this;
    }

    /**
     * Returns the request timeout
     *
     * @return int The request timeout
     */
    public function getTimeout(): int
    {
        return $this->timeout;
    }
    
    /**
     * Runs the HTTP request
     *
     * @return Response The request's response
     */
    public function run(): Response
    {
        $headers = [];

        foreach($this->headers as $key => $value) {
            if(!is_array($value)) {
                $headers[] = $key.': '.$value;
            }
            else {
                foreach($value as $i => $val) {
                    $headers[] = $key.': '.$val;
                }
            }
        }

        $context_options = [
            'http' => [
                'method' => $this->method,
                'header' => array_values($headers),
                'content' => http_build_query($this->body),
                'timeout' => $this->timeout
            ]
        ];

        $context_options = stream_context_create($context_options);

        $result = file_get_contents($this->url, false, $context_options);

        $status = $this->getStatus($http_response_header[0]);

        $response = new Response();

        $response->setStatusCode($status)
                ->setHeaders($http_response_header)
                ->setContent($result);

        return $response;
    }
    
    /**
     * Returns the HTTP status contained in the first response header line
     *
     * @param  mixed $status_line THe first response header line
     * @return int The HTTP status code
     */
    private function getStatus($status_line): int
    {
        preg_match('{HTTP\/\S*\s(\d{3})}', $status_line, $match);
        return $match[1];
    }
}