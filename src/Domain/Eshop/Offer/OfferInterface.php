<?php

namespace Domain\Eshop\Offer;

use Domain\Eshop\Promocode\PromocodeInterface;

interface OfferInterface
{
    /**
     * @param PromocodeInterface $promocode
     * @return OfferInterface
     */
    public function addPromocode(PromocodeInterface $promocode): OfferInterface;

    /**
     * @param PromocodeInterface $promocode
     * @return OfferInterface
     */
    public function removePromocode(PromocodeInterface $promocode): OfferInterface;

    /**
     * @return array<int, string>
     */
    public function getValidPromocodes(): array;

    public function getOfferName(): string;
    public function getOfferType(): OfferType;
}
