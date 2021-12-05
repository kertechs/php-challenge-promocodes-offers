<?php

namespace Domain\Eshop\Promocode;

use Domain\Eshop\Offer\OfferInterface;
use Domain\Eshop\Offer\OfferListInterface;

interface PromocodeInterface
{
    public function isValid(): bool;
    public function getCode(): string;
    public function getDiscountValue(): float;
    public function getEndDate(): \DateTime;
    public function isValidForOffer(OfferInterface $offer): bool;
    public function getOffers(): OfferListInterface|null;
    public function addOffer(OfferInterface $offer): void;

    //todo: suggest :public function getOffers(): OfferListInterface|null;
    //todo: suggest :public function hasOffers() :bool;
    //todo: suggest : method apply() ?
}
