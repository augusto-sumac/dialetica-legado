<?php

require_once(__DIR__ . '/AbstractProcessor.php');

class BooleanProcessor extends AbstractProcessor
{
    public function canBeProcessed(): bool
    {
        $loweredValue = strtolower($this->value);

        return in_array($loweredValue, ['true', 'false'], true);
    }

    public function execute()
    {
        return strtolower($this->value) === 'true';
    }
}
