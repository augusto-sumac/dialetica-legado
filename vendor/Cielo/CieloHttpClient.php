<?php

require_once(rtrim(dirname(__DIR__), DS) . '/curl.php');

class CieloHttpClient
{
    private $merchant_id;
    private $merchant_key;

    private $sandbox = false;

    private $base_url;

    private $service = '';

    public function __construct(string $merchant_id, string $merchant_key, $sandbox = false)
    {
        $this->merchant_id = $merchant_id;

        $this->merchant_key = $merchant_key;

        $this->sandbox = $sandbox;

        $this->base_url = 'https://api' . ($this->sandbox ? 'sandbox' : '') . '.cieloecommerce.cielo.com.br';
    }

    public function getMerchantId()
    {
        return $this->merchant_id;
    }

    public function getMerchantKey()
    {
        return $this->merchant_key;
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
            ->withHeader("MerchantId: {$this->merchant_id}")
            ->withHeader("MerchantKey: {$this->merchant_key}");
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
