<?php

class CieloService
{
    public $http;

    private $response = [];

    public function __construct(CieloHttpClient $http)
    {
        $http->setService($this->service);
        $this->http = $http;
    }

    public function prepareData($data)
    {
        return $data;
    }

    public function data()
    {
        return array_get($this->response, 'content');
    }

    public function status()
    {
        return array_get($this->response, 'status');
    }

    public function error()
    {
        if ($this->status() < 400) return false;

        return array_get($this->response, 'content.error.message');
    }

    public function wrap($response)
    {
        $this->response = $response;

        if ($error = $this->error()) {
            throw new \Exception($error);
        }

        return $this;
    }
}
