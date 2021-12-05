<?php

namespace Infrastructure\Ekwateur\Dto;

use Domain\Eshop\Offer\OfferListInterface;
use Domain\Eshop\Promocode\PromocodeInterface;

class ValidatedPromocode
{
    private string $promocode='';
    private string $endDate='';
    private float $discountValue=0.0;
    private array $compatibleOfferList=[];
    private array $dto=[];

    public function __construct(private PromocodeInterface $validatedPromocode, private OfferListInterface $validatedOffers)
    {
        $this->promocode = $this->validatedPromocode->getCode();
        $this->endDate = $this->validatedPromocode->getEndDate()->format('Y-m-d');
        $this->discountValue = $this->validatedPromocode->getDiscountValue();
        foreach ($this->validatedOffers->getAll() as $validatedOffer) {
            $this->compatibleOfferList[] = [
                'name' => $validatedOffer->getOfferName(),
                'type' => $validatedOffer->getOfferType()->label(),
            ];
        }

        $this->dto = [
            'promocode' => $this->promocode,
            'endDate' => $this->endDate,
            'discountValue' => $this->discountValue,
            'compatibleOfferList' => $this->compatibleOfferList,
        ];
    }

    public function toJson(): string
    {
        return json_encode($this->dto);
    }

    public function toArray(): array
    {
        return $this->dto;
    }

    public function toObject(): object
    {
        return (object) $this->dto;
    }

}