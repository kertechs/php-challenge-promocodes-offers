<?php

namespace Domain\Eshop\Promocode;

interface PromocodeListInterface
{
    public function addPromocode(PromocodeInterface $promocode): PromocodeListInterface;
    public function removePromocode(PromocodeInterface $promocode): PromocodeListInterface;

    /**
     * @return PromocodeInterface[]
     */
    public function getAll(): array;

    public function count(): int;
}
