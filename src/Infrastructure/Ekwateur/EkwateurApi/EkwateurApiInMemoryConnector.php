<?php

declare(strict_types=1);

namespace Infrastructure\Ekwateur\EkwateurApi;

use Domain\Eshop\Offer\OfferInterface;
use Domain\Eshop\Offer\OfferList;
use Domain\Eshop\Promocode\PromocodeInterface;
use Domain\Eshop\Promocode\PromocodeList;
use Infrastructure\Symfony\Serializer\EkwateurSerializer;
use Infrastructure\Symfony\Serializer\EkwateurSerializerInterface;
use Infrastructure\Symfony\Serializer\Normalizer\OfferTypeDenormalizer;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class EkwateurApiInMemoryConnector implements EkwateurApiInterface
{
    private const FIXTURES_DIRECTORY = __DIR__ . '/../../../../tests/Fixtures/';
    private const JSON_STATIC_OFFER_LIST_FILE = 'staticOfferList.json';
    private const JSON_STATIC_PROMOCODE_LIST_FILE = 'staticPromocodeList.json';

    public const INMEMORY_ERROR_GET_PROMOCODE_LIST = 'Error while getting promocodes list from static list';
    public const INMEMORY_ERROR_GET_OFFER_LIST = 'Error while getting offers list from static list';

    private array $staticPromocodeList;
    /**
     * @var \stdClass[]|array $staticOfferList
     */
    private array $staticOfferList;

    public function __construct(private ?EkwateurSerializerInterface $serializer = null)
    {
        //dump('InMemory is here');
        $this->serializer = $this->serializer ?? new EkwateurSerializer();

        $json = file_get_contents(self::FIXTURES_DIRECTORY . '/' . self::JSON_STATIC_OFFER_LIST_FILE);
        $json = $json ? $json : '';
        /**
         * @var \stdClass[]|array $staticOfferList
         */
        $staticOfferList = \json_decode($json, false);
        $this->staticOfferList = $staticOfferList ? $staticOfferList : [];

        $json = file_get_contents(self::FIXTURES_DIRECTORY . '/' . self::JSON_STATIC_PROMOCODE_LIST_FILE);
        $json = $json ? $json : '';
        /**
         * @var \stdClass[]|array $staticPromocodeList
         */
        $staticPromocodeList = \json_decode($json, false);
        $this->staticPromocodeList = $staticPromocodeList ? $staticPromocodeList : [];
    }

    /**
     * @throws EkwateurApiException
     */
    public function getPromocodes(): PromocodeList
    {
        $datas = $this->staticPromocodeList;
        try {
            /**
             * @var array<int, PromocodeInterface> $promocodes
             */
            $promocodes = $this->serializer
                ->deserialize(
                    \json_encode($datas),
                    '\Domain\Eshop\Promocode\Promocode[]',
                    'json',
                );
            return new PromocodeList($promocodes);
        } catch (\Exception $exception) {
            throw new EkwateurApiException(message: self::INMEMORY_ERROR_GET_PROMOCODE_LIST, previous: $exception);
        }
    }

    public function getOffers(): OfferList
    {
        $datas = $this->staticOfferList;
        try {
            $offers = $this->serializer
                ->deserialize(
                    \json_encode($datas),
                    '\Domain\Eshop\Offer\Offer[]',
                    'json',
                );

            /**
             * @var array<int, OfferInterface> $offers
             */
            return new OfferList($offers);
        } catch (\Exception $exception) {
            throw new EkwateurApiException(message: self::INMEMORY_ERROR_GET_OFFER_LIST, previous: $exception);
        }
    }
}
