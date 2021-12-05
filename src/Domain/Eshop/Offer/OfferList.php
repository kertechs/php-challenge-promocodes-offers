<?php

namespace Domain\Eshop\Offer;

final class OfferList implements OfferListInterface, \IteratorAggregate
{
    /**
     * @var OfferInterface[] $offerList
     */
    private array $offerList = [];

    /**
     * @param OfferInterface[] $offerList
     */
    public function __construct(array $offerList = [])
    {
        foreach ($offerList as $offer) {
            try {
                $this->addOffer($offer);
            } catch (OfferException $e) {
                //todo: log
                dump($e->getMessage());
                //skip to next offer
            }
        }
    }

    public function addOffer(OfferInterface $offer): self
    {
        if (!in_array($offer, $this->offerList)) {
            $this->offerList[] = $offer;
        } else {
            throw new OfferException(OfferException::ERROR_ADD_OFFER);
        }

        return $this;
    }

    public function removeOffer(OfferInterface $offer): self
    {
        foreach ($this->offerList as $_idx => $_offer) {
            if ($offer === $_offer) {
                unset($this->offerList[$_idx]);
                $this->offerList = array_values($this->offerList);

                return $this;
            }
        }

        throw new OfferException(OfferException::ERROR_REMOVE_OFFER);
    }

    /**
     * @return OfferInterface[]
     */
    public function getAll(): array
    {
        return $this->offerList;
    }

    /**
     * @return \ArrayIterator<int, OfferInterface>
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->offerList);
    }

    public function count(): int
    {
        return count($this->getAll());
    }
}
