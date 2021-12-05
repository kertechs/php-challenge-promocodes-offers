<?php

declare(strict_types=1);

namespace Domain\Eshop\Offer;

interface OfferListInterface
{
    public function addOffer(OfferInterface $offer): OfferListInterface;
    public function removeOffer(OfferInterface $offer): OfferListInterface;

    /**
     * @return OfferInterface[]
     */
    public function getAll(): array;

    public function count(): int;
}
