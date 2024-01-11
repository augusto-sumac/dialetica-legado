<?php

require_once(__DIR__ . '/CieloHttpClient.php');
require_once(__DIR__ . '/CieloServicePayments.php');

class Cielo
{
    private $paymentsService;

    /**
     * Cielo
     *
     * @param string $merchant_id
     * @param string $merchant_key
     * @param boolean $sandbox
     */
    public function __construct(string $merchant_id, string $merchant_key, $sandbox = false)
    {
        $this->paymentsService =  new CieloServicePayments(
            new CieloHttpClient($merchant_id, $merchant_key, $sandbox)
        );
    }

    /**
     * Payments Service
     *
     * @return CieloServicePayments
     */
    public function payments()
    {
        return $this->paymentsService;
    }
}
