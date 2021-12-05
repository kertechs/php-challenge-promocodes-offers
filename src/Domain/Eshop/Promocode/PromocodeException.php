<?php

namespace Domain\Eshop\Promocode;

class PromocodeException extends \Exception
{
    public const ERROR_ADD_PROMOCODE = 'Failed to add the promocode to the list.';
    public const ERROR_REMOVE_PROMOCODE = 'Failed to remove the promocode from the list.';

    public function __construct(string $message, int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
