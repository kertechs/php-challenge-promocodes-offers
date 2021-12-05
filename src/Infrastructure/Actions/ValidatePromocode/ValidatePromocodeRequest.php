<?php

declare(strict_types=1);

namespace Infrastructure\Actions\ValidatePromocode;

class ValidatePromocodeRequest
{
    public function __construct(private string $promocode=''){}

    /**
     * @return string
     */
    public function getPromocode(): string
    {
        return $this->promocode;
    }
}
