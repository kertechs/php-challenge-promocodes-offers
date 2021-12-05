<?php

declare(strict_types=1);

namespace Domain\Eshop\Offer;

use Domain\Eshop\Promocode\PromocodeInterface;
use Domain\Eshop\Offer\OfferException;

final class Offer implements OfferInterface
{
    /**
     * @param OfferType $offerType
     * @param string $offerName
     * @param string $offerDescription
     * @param array<int, string> $validPromoCodeList
     */
    public function __construct(
        private OfferType $offerType,
        private string $offerName,
        private string $offerDescription,
        private array $validPromoCodeList = [],
    ) {}

    public function addPromocode(PromocodeInterface $promocode): self
    {
        try {
            if (!in_array($promocode->getCode(), $this->validPromoCodeList)) {
                $this->validPromoCodeList[] = $promocode->getCode();
            } else {
                throw new OfferException(OfferException::ERROR_PROMOCODE_ALREADY_ADDED);
            }
        } catch (OfferException $exception) {
            //todo: implement error/logging handler
            //throw $exception;
        }

        return $this;
    }

    public function removePromocode(PromocodeInterface $promocode): self
    {
        try {
            foreach ($this->validPromoCodeList as $key => $code) {
                if ($code === $promocode->getCode()) {
                    unset($this->validPromoCodeList[$key]);
                    $this->validPromoCodeList = array_values($this->validPromoCodeList);

                    return $this;
                }
            }

            throw new OfferException(OfferException::ERROR_PROMOCODE_CANT_BE_REMOVED);
        } catch (OfferException $exception) {
            //todo: implement error/logging handler
            throw $exception;
        }
    }

    /**
     * @return string[]
     */
    public function getValidPromocodes(): array
    {
        //todo: lookup for business constraints to apply
        return $this->validPromoCodeList;
    }

    public function getOfferType(): OfferType
    {
        return $this->offerType;
    }

    public function getOfferName(): string
    {
        return $this->offerName;
    }

    public function getOfferDescription(): string
    {
        return $this->offerDescription;
    }
}
