<?php

require_once(__DIR__ . '/PlugNotasHttpClient.php');
require_once(__DIR__ . '/PlugNotasServiceInvoices.php');
require_once(__DIR__ . '/PlugNotasServiceAddress.php');

class PlugNotas
{
    private $invoicesService;
    private $addressService;

    /**
     * PlugNotas
     *
     * @param string $api_key
     * @param boolean $sandbox
     */
    public function __construct(string $api_key, $sandbox = false)
    {
        $this->invoicesService =  new PlugNotasServiceInvoices(
            new PlugNotasHttpClient($api_key, $sandbox)
        );

        $this->addressService =  new PlugNotasServiceAddress(
            new PlugNotasHttpClient($api_key, $sandbox)
        );
    }

    /**
     * Invoices Service
     *
     * @return PlugNotasServiceInvoices
     */
    public function invoices()
    {
        return $this->invoicesService;
    }

    /**
     * Address Service
     *
     * @return PlugNotasServiceAddress
     */
    public function address()
    {
        return $this->addressService;
    }
}
