<?php

declare(strict_types=1);

namespace Infrastructure\Actions\ValidatePromocode;

use Domain\Eshop\Offer\OfferListInterface;
use Domain\Eshop\Promocode\PromocodeInterface;
use Infrastructure\Shared\Traits\ErrorTrait;
use Infrastructure\Shared\Traits\SuccessTrait;

class ValidatePromocodeResponse
{
    use ErrorTrait;
    use SuccessTrait;

    private bool $promocodeValidated = false;
    private PromocodeInterface $validatedPromocode;
    private OfferListInterface $validatedOffers;

    public function __construct(){}

    public function setPromocodeValidated(): self
    {
        $this->promocodeValidated = true;
        $this->setSuccess(true);

        return $this;
    }
    
    public function setValidatedOffers(OfferListInterface $validatedOffers): self
    {
        $this->validatedOffers = $validatedOffers;

        return $this;
    }

    public function getValidatedOffers(): OfferListInterface
    {
        return $this->validatedOffers;
    }

    public function setValidatedPromocode(PromocodeInterface $promocodeList): self
    {
        $this->validatedPromocode = $promocodeList;

        return $this;
    }

    public function getValidatedPromocode(): PromocodeInterface
    {
        return $this->validatedPromocode;
    }

    public function hasValidOffers(): bool
    {
        return $this->validatedOffers instanceof OfferListInterface && count($this->validatedOffers->getAll()) > 0;
    }

}
