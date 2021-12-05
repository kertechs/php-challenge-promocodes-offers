<?php

namespace Infrastructure\Ekwateur\Dto;

use Infrastructure\Shared\Contracts\ErrorInterface;
use Infrastructure\Shared\Traits\ErrorTrait;

class Error implements ErrorInterface
{
    use ErrorTrait;

    public function __construct(private string $message,
                                private string|int $code='',
                                private ?\DateTime $dateTime=null,
                                private ?\Throwable $context=null)
    {
        $this->dateTime = $dateTime ?? new \DateTime();
    }


    public function getErrorMessage(): string
    {
        return $this->message;
    }
}