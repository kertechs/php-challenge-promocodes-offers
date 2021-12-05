<?php

declare(strict_types=1);

namespace Domain\Eshop\Promocode;

final class PromocodeList implements PromocodeListInterface, \IteratorAggregate
{
    /**
     * @var PromocodeInterface[] $promocodeList
     */
    private $promocodeList = [];

    /**
     * @param array<int, PromocodeInterface> $promocodeList
     */
    public function __construct(array $promocodeList = [])
    {
        foreach ($promocodeList as $promocode) {
            try {
                $this->addPromocode($promocode);
            } catch (\Throwable $e) {
                //todo: log
                dump($e->getMessage());
                //skip to next promocode
            }
        }
    }

    /**
     * @return PromocodeInterface[]
     */
    public function getPromocodes(): array
    {
        return $this->promocodeList;
    }

    /**
     * @return \ArrayIterator<int, PromocodeInterface>
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->promocodeList);
    }

    /**
     * @param PromocodeInterface $promocode
     * @return self
     * @throws PromocodeException
     */
    public function addPromocode(PromocodeInterface $promocode): self
    {
        if (!in_array($promocode, $this->promocodeList)) {
            $this->promocodeList[] = $promocode;
        } else {
            throw new PromocodeException(message: PromocodeException::ERROR_ADD_PROMOCODE);
        }

        return $this;
    }

    /**
     * @param PromocodeInterface $promocode
     * @return $this
     * @throws PromocodeException
     */
    public function removePromocode(PromocodeInterface $promocode): self
    {
        foreach ($this->promocodeList as $_idx => $_promocode) {
            if ($promocode === $_promocode) {
                unset($this->promocodeList[$_idx]);
                $this->promocodeList = array_values($this->promocodeList);

                return $this;
            }
        }

        throw new PromocodeException(message: PromocodeException::ERROR_REMOVE_PROMOCODE);
    }

    /**
     * @return PromocodeInterface[]
     */
    public function getAll(): array
    {
        return $this->promocodeList;
    }

    public function count(): int
    {
        return count($this->getAll());
    }
}
