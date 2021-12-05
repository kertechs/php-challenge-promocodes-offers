<?php

declare(strict_types=1);

namespace Infrastructure\Ekwateur\EkwateurApi;

use Domain\Eshop\Offer\OfferInterface;
use Domain\Eshop\Offer\OfferList;
use Domain\Eshop\Promocode\PromocodeInterface;
use Domain\Eshop\Promocode\PromocodeList;
use Infrastructure\Symfony\Serializer\EkwateurSerializer;
use Infrastructure\Symfony\Serializer\EkwateurSerializerInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\RetryableHttpClient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class EkwateurApiHttpConnector implements EkwateurApiInterface
{
    //Handled endpoints list
    private const ENDPOINT_PROMOCODE_LIST = '/promoCodeList';
    private const ENDPOINT_OFFER_LIST = '/offerList';

    //Error handling
    public const HTTP_ERROR_GET_PROMOCODE_LIST = 'Error while getting promocodes list from Ekwateur API';
    public const HTTP_ERROR_GET_OFFER_LIST = 'Error while getting offers list from Ekwateur API';
    public const API_ERROR_SETTING_CACHE_OPTION = 'Failed to set cache option';

    private RetryableHttpClient $client;
    private ?CacheInterface $cache = null;

    //Options
    private bool $useCache = false;

    /**
     * EkwateurApiHttpConnector constructor.
     * @param string|null $ekwateurApiBaseUrl
     * @param array<string, bool|int|object|string> $options
     * @param EkwateurSerializerInterface|null $serializer
     */
    public function __construct(
        private ?string $ekwateurApiBaseUrl = null,
        private array $options = [],
        private ?EkwateurSerializerInterface $serializer = null,
    ) {
        //dump('HTTP is here');
        $this->ekwateurApiBaseUrl = $this->ekwateurApiBaseUrl ?? $_ENV['EKWATEUR_API_BASE_URL'];
        $this->client = new RetryableHttpClient(HttpClient::createForBaseUri($this->ekwateurApiBaseUrl));
        $this->serializer = $this->serializer ?? new EkwateurSerializer();
        $this->setOptions();
    }

    private function setOptions(): void
    {
        foreach ($this->options as $optionName => $optionValue) {
            $this->setOption($optionName, $optionValue);
        }
    }

    private function setOption(string $optionName, bool|int|object|string $optionValue): void
    {
        switch ($optionName) {
            //todo: improve options. Maybe setTimeout ? ...
            //use dependency injection to set options
            case 'use_cache':
                try {
                    $this->useCache = (bool) $optionValue;
                    $this->cache = new FilesystemAdapter();
                } catch (\Exception $exception) {
                    throw new EkwateurApiException(self::API_ERROR_SETTING_CACHE_OPTION, 0, $exception);
                }
                break;
        }
    }

    /**
     * @return PromocodeList
     * @throws EkwateurApiException
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function getPromocodes(): PromocodeList
    {
        try {
            if ($this->useCache) {
                $promocodes = $this->getPromocodesFromCache();
            } else {
                $promocodes = $this->getPromocodesFromApi();
            }

            /**
             * @var array<int, PromocodeInterface> $promocodesList
             */
            $promocodesList = $this->serializer
                ->deserialize(
                    \json_encode($promocodes),
                    '\Domain\Eshop\Promocode\Promocode[]',
                    'json',
                );

            return new PromocodeList($promocodesList);
        } catch (\Exception $exception) {
            throw new EkwateurApiException(
                self::HTTP_ERROR_GET_PROMOCODE_LIST,
                Response::HTTP_BAD_REQUEST,
                $exception
            );
        }
    }

    public function getOffers(): OfferList
    {
        try {
            if ($this->useCache) {
                $offers = $this->getOffersFromCache();
            } else {
                $offers = $this->getOffersFromApi();
            }

            try {
                /**
                 * @var array<int, OfferInterface> $offersList
                 */
                $offersList = $this->serializer
                    ->deserialize(
                        \json_encode($offers),
                        '\Domain\Eshop\Offer\Offer[]',
                        'json',
                    );
            } catch (\Exception $exception) {
                throw new EkwateurApiException(
                    self::HTTP_ERROR_GET_OFFER_LIST,
                    Response::HTTP_BAD_REQUEST,
                    $exception
                );
            }

            return new OfferList($offersList);
        } catch (\Exception $exception) {
            throw new EkwateurApiException(
                self::HTTP_ERROR_GET_OFFER_LIST,
                Response::HTTP_BAD_REQUEST,
                $exception
            );
        }
    }

    /**
     * @return array<int, string> of promocodes from Http url
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    private function getPromocodesFromApi(): array
    {
        return $this->client
            ->request('GET', self::ENDPOINT_PROMOCODE_LIST)
            ->toArray();
    }

    /**
     * @return array<int, string> of promocodes from Cache if cache exists
     * @throws \Psr\Cache\InvalidArgumentException
     */
    private function getPromocodesFromCache(): array
    {
        if ($this->cache === null) {
            return [];
        }

        $promocodes = $this->cache->get('promocodesList', function (ItemInterface $item) {
            $item->expiresAfter(3600);

            return $this->getPromocodesFromApi();
        });

        /**
         * @var array<int, string> $promocodes
         */
        return $promocodes;
    }

    /**
     * @return array<int, string> of offers from Http url
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    private function getOffersFromApi(): array
    {
        return $this->client
            ->request('GET', self::ENDPOINT_OFFER_LIST)
            ->toArray();
    }

    /**
     * @return array<int, string> of offers from Cache if cache exists
     * @throws \Psr\Cache\InvalidArgumentException
     */
    private function getOffersFromCache(): array
    {
        if ($this->cache === null) {
            return [];
        }

        $offers = $this->cache->get('offersList', function (ItemInterface $item) {
            $item->expiresAfter(3600);

            return $this->getOffersFromApi();
        });

        /**
         * @var array<int, string> $offers
         */
        return $offers;
    }
}
