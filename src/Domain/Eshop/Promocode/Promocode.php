<?php

namespace Domain\Eshop\Promocode;

use Domain\Eshop\Offer\OfferException;
use Domain\Eshop\Offer\OfferInterface;
use Domain\Eshop\Offer\OfferList;
use Domain\Eshop\Offer\OfferListInterface;

final class Promocode implements PromocodeInterface
{
    private ?OfferListInterface $offerList = null;

    public function __construct(
        //todo: request constraints to check on code (length, valid chars pool ...)
        private string $code,
        private float $discountValue,
        private \DateTime $endDate,
    ) {}

    /*
    * Promocode is valid when endDate is in the future
    */
    public function isValid(): bool
    {
        return $this->endDate > new \DateTime('now');
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getDiscountValue(): float
    {
        return $this->discountValue;
    }

    //todo: discuss with team+biz the way to ensure timezone validity. Right now relying purely on default settings
    public function getEndDate(): \DateTime
    {
        return $this->endDate;
    }

    public function isValidForOffer(OfferInterface $offer): bool
    {
        return in_array($this->getCode(), $offer->getValidPromocodes());
    }

    public function getOfferList(): OfferListInterface
    {
        if (null === $this->offerList) {
            $this->offerList = new OfferList();
        }

        return $this->offerList;
    }

    public function addOffer(OfferInterface $offer): void
    {
        try {
            $this->getOfferList()->addOffer($offer);
        } catch (OfferException $e) {
            //todo: log exception
        }
    }

    public function getOffers(): OfferListInterface|null
    {
        return $this->getOfferList();
    }
}
