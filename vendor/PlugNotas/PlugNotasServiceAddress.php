<?php

require_once(__DIR__ . '/PlugNotasService.php');

class PlugNotasServiceAddress extends PlugNotasService
{
    public $service = 'cep';

    public function prepareData($data)
    {
        return $data;
    }

    public function find($zip_code)
    {
        try {
            $this->wrap($this->http->get($zip_code));
        } catch (\Exception $e) {
            // ...
        }

        return $this->data();
    }
}
