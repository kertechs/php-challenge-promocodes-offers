<?php

declare(strict_types=1);

namespace Domain\Eshop\Offer;

enum OfferType: int
{
case GAS = 101;
case ELECTRICITY = 102;
case WOOD = 103;

    public function label(): string
    {
        return self::getLabel($this);
    }

    public static function getLabel(self $value): string
    {
        return match ($value) {
            OfferType::GAS => 'GAS',
            OfferType::ELECTRICITY => 'ELECTRICITY',
            OfferType::WOOD => 'WOOD',
        };
    }

    public static function getValue(string $label): OfferType
    {
        return match ($label) {
            'GAS' => OfferType::GAS,
            'ELECTRICITY' => OfferType::ELECTRICITY,
            'WOOD' => OfferType::WOOD,
        };
    }
    }
