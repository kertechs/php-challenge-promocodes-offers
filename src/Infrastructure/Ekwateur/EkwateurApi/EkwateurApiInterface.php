<?php

namespace Infrastructure\Ekwateur\EkwateurApi;

use Domain\Eshop\Offer\OfferListInterface;
use Domain\Eshop\Promocode\PromocodeListInterface;

interface EkwateurApiInterface
{
    public function getOffers(): OfferListInterface;
    public function getPromocodes(): PromocodeListInterface;
}
