<?php

namespace Infrastructure\Ekwateur\EkwateurManager;

use Domain\Eshop\Offer\OfferListInterface;
use Domain\Eshop\Promocode\PromocodeListInterface;
use Infrastructure\Ekwateur\EkwateurApi\EkwateurApiHttpConnector;
use Infrastructure\Ekwateur\EkwateurApi\EkwateurApiInterface;

class EkwateurManager implements EkwateurManagerInterface
{
    private static ?EkwateurManagerInterface $ekwateurManagerCacheEnabled = null;
    private static ?EkwateurManagerInterface $ekwateurManagerNotCacheEnabled = null;
    private static ?EkwateurManagerInterface $ekwateurManager = null;

    private EkwateurApiInterface $ekwateurConnector;

    /*
     * Set the appropriate connector (InMemory or Http or Database ...)
     * InMemory and Http are the only two connectors currently implemented
     */
    public function __construct(EkwateurApiInterface $ekwateurConnector)
    {
        $this->ekwateurConnector = $ekwateurConnector;
    }

    /*
     * Get the list of promocodes through the defined connector
     */
    public function getPromocodes(): PromocodeListInterface
    {
        return $this->ekwateurConnector->getPromocodes();
    }

    /*
     * Get the list of offers through the defined connector
     */
    public function getOffers(): OfferListInterface
    {
        return $this->ekwateurConnector->getOffers();
    }

    /**
     * @param bool $cacheEnabled
     * @return EkwateurManagerInterface|null
     */
    public static function getEkwateurManagerHttpInstance(bool $cacheEnabled = true): ?EkwateurManagerInterface
    {
        switch ($cacheEnabled) {
            case true:
                if (self::$ekwateurManagerCacheEnabled === null) {
                    self::$ekwateurManagerCacheEnabled = new self(new EkwateurApiHttpConnector(
                        options: ['use_cache' => $cacheEnabled],
                    ));
                }
                self::$ekwateurManager = self::$ekwateurManagerCacheEnabled;
                break;

            case false:
                if (self::$ekwateurManagerNotCacheEnabled === null) {
                    self::$ekwateurManagerNotCacheEnabled = new self(new EkwateurApiHttpConnector(
                        options: ['use_cache' => $cacheEnabled],
                    ));
                }
                self::$ekwateurManager = self::$ekwateurManagerNotCacheEnabled;
                break;
        }

        /**
         * @var EkwateurManagerInterface|null self::$ekwateurManager
         */
        return self::$ekwateurManager;
    }
}
