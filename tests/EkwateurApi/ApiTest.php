<?php

declare(strict_types=1);

namespace Tests\EkwateurApi;

use Domain\Eshop\Offer\Offer;
use Domain\Eshop\Promocode\Promocode;
use Domain\Eshop\Promocode\PromocodeList;
use Infrastructure\Ekwateur\EkwateurApi\EkwateurApiException;
use Infrastructure\Ekwateur\EkwateurApi\EkwateurApiInMemoryConnector;
use Infrastructure\Ekwateur\EkwateurManager\EkwateurManager;
use Infrastructure\Ekwateur\EkwateurManager\EkwateurManagerInterface;
use PHPUnit\Framework\TestCase;

class ApiTest extends TestCase
{
    private ?EkwateurManagerInterface $ekwateurManager = null;

    public function setUp(): void
    {
    }

    /**
     * @param PromocodeList $testArray
     * @param mixed $value
     * @return bool
     */
    private function assertArrayContainsSameObjectWithCodeValue(PromocodeList $testArray, mixed $value): bool
    {
        foreach ($testArray as $testObject) {
            if ($testObject->getCode() === $value) {
                return true;
            }
        }

        return false;
    }

    public function testEkwateurApiCanGetPromocodesListFromAssociatedEndpointWithoutCacheAndShouldReturnAListOfPromocode()
    {
        $this->ekwateurManager = EkwateurManager::getEkwateurManagerHttpInstance(cacheEnabled: false);

        $testPromocodesObjects = $this->ekwateurManager->getPromocodes();
        $this->assertContainsOnlyInstancesOf(Promocode::class, $testPromocodesObjects);
    }

    public function testEkwateurApiCanGetPromocodeListFromAssociatedEndpointWithCacheEnabledAndShouldReturnAListOfPromocode()
    {
        $this->ekwateurManager = EkwateurManager::getEkwateurManagerHttpInstance(cacheEnabled: true);

        $testPromocodesObjects1 = $this->ekwateurManager->getPromocodes();
        $testPromocodesObjects2 = $this->ekwateurManager->getPromocodes();
        $this->assertContainsOnlyInstancesOf(Promocode::class, $testPromocodesObjects1);
        $this->assertContainsOnlyInstancesOf(Promocode::class, $testPromocodesObjects2);
    }

    public function testPromocodeAbcdIsNotInHardcodedList()
    {
        $this->ekwateurManager = EkwateurManager::getEkwateurManagerHttpInstance(cacheEnabled: true);

        $testPromocode = "abcd";
        $testPromocodesObjects = $this->ekwateurManager->getPromocodes();

        $this->assertContainsOnlyInstancesOf(Promocode::class, $testPromocodesObjects);
        $this->assertFalse($this->assertArrayContainsSameObjectWithCodeValue($testPromocodesObjects, $testPromocode));
    }

    public function testPromocodeWoodyIsInHardcodedList()
    {
        $this->ekwateurManager = EkwateurManager::getEkwateurManagerHttpInstance(cacheEnabled: true);

        $testPromocode = "WOODY";
        $testPromocodesObjects = $this->ekwateurManager->getPromocodes();

        $this->assertContainsOnlyInstancesOf(Promocode::class, $testPromocodesObjects);
        $this->assertTrue($this->assertArrayContainsSameObjectWithCodeValue($testPromocodesObjects, $testPromocode));
    }

    public function testEkwateurApiGetOffersListFromAssociatedEndpointWithoutCacheShouldReturnAListOfOffers()
    {
        $this->ekwateurManager = EkwateurManager::getEkwateurManagerHttpInstance(cacheEnabled: false);

        $testOffersObjects = $this->ekwateurManager->getOffers();
        $this->assertContainsOnlyInstancesOf(Offer::class, $testOffersObjects);
    }

    public function testEkwateurApiGetOffersListFromAssociatedEndpointWithCacheEnabledShouldReturnAListOfOffers()
    {
        $this->ekwateurManager = EkwateurManager::getEkwateurManagerHttpInstance(cacheEnabled: true);

        $testOffersObjects1 = $this->ekwateurManager->getOffers();
        $testOffersObjects2 = $this->ekwateurManager->getOffers();
        $this->assertContainsOnlyInstancesOf(Offer::class, $testOffersObjects1);
        $this->assertContainsOnlyInstancesOf(Offer::class, $testOffersObjects2);
    }

    public function testEkwateurApiGetOffersListFromAssociatedEndpointInMemoryShouldReturnAListOfOffers()
    {
        $this->ekwateurManager = new EkwateurManager(new EkwateurApiInMemoryConnector());
        try {
            $testOffersObjects = $this->ekwateurManager->getOffers();
            $this->assertContainsOnlyInstancesOf(Offer::class, $testOffersObjects);
        } catch (EkwateurApiException $e) {
            $this->fail("EkwateurApiConnectorException should not be thrown");
        }
    }
}
