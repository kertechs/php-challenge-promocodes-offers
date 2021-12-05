<?php

namespace Infrastructure\Ekwateur\EkwateurManager;

use Domain\Eshop\Offer\OfferListInterface;
use Domain\Eshop\Promocode\PromocodeListInterface;

interface EkwateurManagerInterface
{
    public function getPromocodes(): PromocodeListInterface;
    public function getOffers(): OfferListInterface;
}
