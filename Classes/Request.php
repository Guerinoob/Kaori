<?php

namespace App;

class Request {
    private $url;

    private $method;

    private $headers;

    private $body;

    private $timeout;

    public function __construct()
    {
        $this->headers = [];
    }

    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    public function getUrl($url)
    {
        return $this->url;
    }

    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function addHeaders($headers) {
        $headers = Http::parseHeaders($headers);

        foreach($headers as $key => $value) {
            if (!isset($this->headers[$key]))
                $this->headers[$key] = $value;
            else
                $this->headers[$key] = array_merge([$this->headers[$key]], [$value]);
        }
    }

    public function getHeaders() 
    {
        return $this->headers;
    }

    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
        return $this;
    }

    public function getTimeout()
    {
        return $this->timeout;
    }

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

    private function getStatus($status_line)
    {
        preg_match('{HTTP\/\S*\s(\d{3})}', $status_line, $match);
        return $match[1];
    }
}