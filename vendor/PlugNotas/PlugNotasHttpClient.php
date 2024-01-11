<?php

require_once(rtrim(dirname(__DIR__), DS) . '/curl.php');

class PlugNotasHttpClient
{
    private $api_key;

    private $sandbox = false;

    private $base_url;

    private $service = '';

    public function __construct(string $api_key, $sandbox = false)
    {
        $this->api_key = $api_key;

        $this->sandbox = $sandbox;

        $this->base_url = "https://api." . ($this->sandbox ? 'sandbox.' : '') . 'plugnotas.com.br';
    }

    public function getApiKey()
    {
        return $this->api_key;
    }

    public function getSandbox()
    {
        return $this->sandbox;
    }

    public function setService(string $service)
    {
        $this->service = $service;
        return $this;
    }

    public function setBaseUrl(string $base_url)
    {
        $this->base_url = $base_url;
        return $this;
    }

    public function getUrl($path)
    {
        $trim = fn ($s) => rtrim(ltrim($s, '/'), '/');
        return implode('/', array_filter([
            rtrim($this->base_url, '/'),
            $trim($this->service),
            $trim($path)
        ], fn ($p) => !empty($p)));
    }

    public function curl($path)
    {
        return Curl::to($this->getUrl($path))
            ->withHeader("x-api-key: {$this->api_key}");
    }

    public function request($pathOrData, $data = [])
    {
        if (is_array($pathOrData)) {
            $data = array_merge($pathOrData, $data);
            $pathOrData = '';
        }

        return $this->curl($pathOrData)
            ->withData($data)
            ->asJson(true)
            ->returnResponseArray();
    }

    public function get($pathOrData, array $data = [])
    {
        return $this->request($pathOrData, $data)->get();
    }

    public function post($pathOrData, array $data = [])
    {
        return $this->request($pathOrData, $data)->post();
    }

    public function put($pathOrData, array $data = [])
    {
        return $this->request($pathOrData, $data)->put();
    }

    public function delete($pathOrData, array $data = [])
    {
        return $this->request($pathOrData, $data)->delete();
    }
}
