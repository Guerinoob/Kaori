<?php

namespace App;

class Response {

    private $status_code;

    private $headers;

    private $content;

    public function setStatusCode($status_code)
    {
        $this->status_code = $status_code;
        return $this;
    }

    public function getStatusCode()
    {
        return $this->status_code;
    }

    public function setHeaders($headers)
    {
        $this->headers = $headers;
        return $this;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    public function getContent()
    {
        return $this->content;
    }

}