<?php

require_once(__DIR__ . '/IProcessor.php');

abstract class AbstractProcessor implements IProcessor
{
    /**
     * The value to process
     * @var string
     */
    protected $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }
}
