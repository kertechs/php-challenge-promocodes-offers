<?php

namespace Domain\Eshop\Offer;

class OfferException extends \Exception
{
    public const ERROR_ADD_OFFER = 'Failed to add the offer to the list.';
    public const ERROR_REMOVE_OFFER = 'Failed to remove the offer from the list.';
    public const ERROR_PROMOCODE_ALREADY_ADDED = 'Impossible to add the designed promocode as it was already previously added.';
    public const ERROR_PROMOCODE_CANT_BE_REMOVED = 'Impossible to remove the designed promocode as it was not found.';

    public function __construct(string $message, int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
