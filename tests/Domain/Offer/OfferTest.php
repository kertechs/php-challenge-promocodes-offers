<?php

declare(strict_types=1);

namespace Tests\Domain\Offer;

use Domain\Eshop\Offer\OfferException;
use Domain\Eshop\Offer\OfferList;
use Domain\Eshop\Promocode\Promocode;
use Domain\Eshop\Promocode\PromocodeException;
use Infrastructure\Symfony\Serializer\Normalizer\OfferTypeDenormalizer;
use PHPUnit\Framework\TestCase;
use Domain\Eshop\Offer\Offer;
use Domain\Eshop\Offer\OfferType;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;

class OfferTest extends TestCase
{
    public function setUp(): void
    {
        $encoders = [ new JsonEncoder() ];
        $normalizers = [ new GetSetMethodNormalizer(),
            new ArrayDenormalizer(),
            new OfferTypeDenormalizer(),
        ];
        $serializer = new Serializer($normalizers, $encoders);

        $this->encoders = $encoders;
        $this->normalizers = $normalizers;
        $this->serializer = $serializer;

        $jsonOffer = '{
            "offerType": "GAS",
            "offerName": "EKWAG3000",
            "offerDescription": "Une offre croustillante",
            "validPromoCodeList": [
              "EKWA_WELCOME",
              "GAZZZZZZZZY"
            ]
          }';
        $this->jsonOffer = $jsonOffer;

        $jsonOffers = '[
          {
            "offerType": "GAS",
            "offerName": "EKWAG2000",
            "offerDescription": "Une offre incroyable",
            "validPromoCodeList": [
              "EKWA_WELCOME",
              "ALL_2000"
            ]
          },
          {
            "offerType": "GAS",
            "offerName": "EKWAG3000",
            "offerDescription": "Une offre croustillante",
            "validPromoCodeList": [
              "EKWA_WELCOME",
              "GAZZZZZZZZY"
            ]
          }
        ]';
        $this->jsonOffers = $jsonOffers;
    }

    public function testOfferCanBeCreatedWithOffertypeNameDescriptionAndValidPromocodeList()
    {
        $offer = new Offer(
            offerType: OfferType::GAS,
            offerName: 'EKWAG3000',
            offerDescription: 'EKWAG3000 description',
        );

        $this->assertEquals('GAS', $offer->getOfferType()->label());
        $this->assertInstanceOf(Offer::class, $offer);
    }

    public function testOfferCanBeCreatedFromJsonStringDeserialized()
    {
        $offer = $this->serializer->deserialize($this->jsonOffer, Offer::class, 'json');

        $this->assertEquals('GAS', $offer->getOfferType()->label());
        $this->assertInstanceOf(Offer::class, $offer);
        $this->assertEquals('EKWAG3000', $offer->getOfferName());
        $this->assertEquals('Une offre croustillante', $offer->getOfferDescription());
        $this->assertIsArray($offer->getValidPromocodes(), 'offer->getValidPromocodes() should return an array');
        $this->assertCount(2, $offer->getValidPromocodes());
    }

    public function testOfferListCanBeCreatedFromJsonArrayDeserialized()
    {
        $offers = $this->serializer->deserialize($this->jsonOffers, '\Domain\Eshop\Offer\Offer[]', 'json');

        $this->assertContainsOnlyInstancesOf(Offer::class, $offers);
        $this->assertCount(2, $offers);

        $offerList = new OfferList($offers);
        $this->assertInstanceOf(OfferList::class, $offerList);
        $this->assertCount(2, $offerList);
    }

    public function testOfferCanBeAddedToOfferList()
    {
        $offer = new Offer(
            offerType: OfferType::GAS,
            offerName: 'EKWAG3000',
            offerDescription: 'EKWAG3000 description',
        );

        try {
            $offerList = new OfferList();
        } catch (OfferException $e) {
            $this->fail('OfferList should not throw an exception');
        }

        try {
            $offerList->addOffer($offer);
        } catch (OfferException $e) {
            $this->fail('OfferList should not throw an exception');
        }

        try {
            $offerList->addOffer($offer);
        } catch (OfferException $e) {
            $this->assertEquals(OfferException::ERROR_ADD_OFFER, $e->getMessage());
        }

        $this->assertInstanceOf(OfferList::class, $offerList);
        $this->assertCount(1, $offerList);
    }

    public function testOfferCanBeRemovedFromOfferList()
    {
        $offer = new Offer(
            offerType: OfferType::GAS,
            offerName: 'EKWAG3000',
            offerDescription: 'EKWAG3000 description',
        );

        $offerList = new OfferList();
        $offerList->addOffer($offer);

        $this->assertInstanceOf(OfferList::class, $offerList);
        $this->assertCount(1, $offerList);

        try {
            $offerList->removeOffer($offer);
        } catch (OfferException $e) {
            $this->fail('OfferList should not throw an exception');
        }

        try {
            $offerList->removeOffer($offer);
        } catch (OfferException $e) {
            $this->assertEquals(OfferException::ERROR_REMOVE_OFFER, $e->getMessage());
        }

        $this->assertCount(0, $offerList);
    }


    public function testGetallMethodReturnsAnArrayOfOffers()
    {
        $offer = new Offer(
            offerType: OfferType::GAS,
            offerName: 'EKWAG3000',
            offerDescription: 'EKWAG3000 description',
        );

        $offerList = new OfferList();
        $offerList->addOffer($offer);

        $this->assertInstanceOf(OfferList::class, $offerList);
        $this->assertCount(1, $offerList);

        $offers = $offerList->getAll();
        $this->assertIsArray($offers);
        $this->assertCount(1, $offers);
        $this->assertContainsOnly(Offer::class, $offers);
    }

    public function testPromocodeCanbeAddedToOffer()
    {
        $offer = new Offer(
            offerType: OfferType::GAS,
            offerName: 'EKWAG3000',
            offerDescription: 'Une offre croustillante',
        );

        $promocode1 = new Promocode('PROMO1', 10, new \DateTime('+1 week'));
        $promocode2 = new Promocode('PROMO2', 10, new \DateTime('+1 day'));

        $offer->addPromocode($promocode1);
        $offer->addPromocode($promocode2);

        $this->assertCount(2, $offer->getValidPromocodes());
    }

    public function testPromocodeCanBeRemovedFromOffer()
    {
        $offer = new Offer(
            offerType: OfferType::GAS,
            offerName: 'EKWAG3000',
            offerDescription: 'Une offre croustillante',
        );

        $promocode1 = new Promocode('PROMO1', 10, new \DateTime('+1 week'));
        $promocode2 = new Promocode('PROMO2', 10, new \DateTime('+1 day'));

        try {
            $offer->addPromocode($promocode1);
            $this->assertCount(1, $offer->getValidPromocodes());
            $this->assertContains('PROMO1', $offer->getValidPromocodes());

            $offer->addPromocode($promocode2);
            $this->assertCount(2, $offer->getValidPromocodes());
            $this->assertContains('PROMO1', $offer->getValidPromocodes());
            $this->assertContains('PROMO2', $offer->getValidPromocodes());
        } catch (OfferException $e) {
            $this->fail('PromocodeException should not be thrown');
        }

        try {
            $offer->addPromocode($promocode1);
        } catch (OfferException $e) {
            $this->assertEquals(OfferException::ERROR_PROMOCODE_ALREADY_ADDED, $e->getMessage());
        }

        try {
            $offer->removePromocode($promocode2);
            $this->assertCount(1, $offer->getValidPromocodes());
            $this->assertNotContains('PROMO2', $offer->getValidPromocodes());
            $this->assertContains('PROMO1', $offer->getValidPromocodes());

            $offer->removePromocode($promocode1);
            $this->assertCount(0, $offer->getValidPromocodes());
            $this->assertNotContains('PROMO2', $offer->getValidPromocodes());
            $this->assertNotContains('PROMO1', $offer->getValidPromocodes());
        } catch (OfferException $e) {
            $this->fail('PromocodeException should not be thrown');
        }

        try {
            $offer->removePromocode($promocode1);
        } catch (OfferException $e) {
            $this->assertEquals(OfferException::ERROR_PROMOCODE_CANT_BE_REMOVED, $e->getMessage());
        }
    }
}
