<?php

declare(strict_types=1);

namespace Tests\Domain\Promocode;

use Domain\Eshop\Promocode\Promocode;
use Domain\Eshop\Promocode\PromocodeException;
use Domain\Eshop\Promocode\PromocodeList;
use Infrastructure\Ekwateur\EkwateurApi\EkwateurApiInMemoryConnector;
use Infrastructure\Ekwateur\EkwateurManager\EkwateurManager;
use Infrastructure\Symfony\Serializer\EkwateurSerializer;
use JetBrains\PhpStorm\Pure;
use PHPUnit\Framework\TestCase;

class PromocodeTest extends TestCase
{
    private EkwateurManager $ekwateurManager;
    private EkwateurSerializer $serializer;

    public function setUp(): void
    {
        $this->ekwateurManager = new EkwateurManager(new EkwateurApiInMemoryConnector());
        $this->serializer = new EkwateurSerializer();
    }

    public function testPromocodeCanBeCreatedFromCodeDiscountvalueAndEnddate(): void
    {
        $testDatas = [
            'code' => 'ABC',
            'discountValue' => 10.5,
            'endDate' => new \DateTime('+1 day'),
        ];
        $promocode = new Promocode($testDatas['code'], $testDatas['discountValue'], $testDatas['endDate']);

        $this->assertEquals($testDatas['code'], $promocode->getCode());
        $this->assertEquals($testDatas['discountValue'], $promocode->getDiscountValue());
        $this->assertEquals($testDatas['endDate'], $promocode->getEndDate());
    }

    public function testPromocodeWithExpireDateInTheFutureIsValid()
    {
        $testPromocode = new Promocode('TEST_VALID', 1.0, new \DateTime('now + 1 second'));
        $isValid = $testPromocode->isValid();
        $this->assertEquals(
            true,
            $isValid,
            'Promocode "'
            . $testPromocode->getCode() . '" expiring on "'
            . $testPromocode->getEndDate()->format('Y-m-d H:i:s')
            . '" is valid'
        );
    }

    public function testPromocodeWithExpireDateInThePastIsNotValid()
    {
        $testPromocode = new Promocode(
            'TEST_INVALID',
            1.0,
            new \DateTime('now - 1 second')
        );

        $isNotValid = $testPromocode->isValid();

        $this->assertEquals(
            false,
            $isNotValid,
            'Promocode "'
            . $testPromocode->getCode() . '" expiring on "'
            . $testPromocode->getEndDate()->format('Y-m-d H:i:s')
            . '" is not valid'
        );
    }

    public function testPromocodeListCanBeCreatedFromArrayOfPromocodes()
    {
        $testPromocodes = [
            new Promocode('TEST_1', 1.0, new \DateTime('now + 1 second')),
            new Promocode('TEST_2', 1.0, new \DateTime('now + 1 second')),
            new Promocode('TEST_3', 1.0, new \DateTime('now + 1 second')),
        ];

        $promocodeList = new PromocodeList($testPromocodes);
        $this->assertInstanceOf(PromocodeList::class, $promocodeList);
        $this->assertCount(3, $promocodeList->getPromocodes());
    }

    public function testPromocodeCanBeAddedToPromocodeList()
    {
        $testPromocodes = [
            new Promocode('TEST_1', 1.0, new \DateTime('now + 1 second')),
            new Promocode('TEST_2', 1.0, new \DateTime('now + 1 second')),
            new Promocode('TEST_3', 1.0, new \DateTime('1977-01-01 00:00:00')),
        ];

        $promocodeList = new PromocodeList($testPromocodes);
        $this->ExceptionThrownWhenPromocodeIsAddedTwice($promocodeList, $testPromocodes[2]);
        $this->assertCount(3, $promocodeList->getPromocodes(), 'PromocodeList should have 3 promocodes');

        try {
            $promocodeList->addPromocode($promocodetest4 = new Promocode(
                code: 'TEST_4',
                discountValue: 1.0,
                endDate: new \DateTime('now + 1 second'),
            ));
        } catch (PromocodeException $e) {
            $this->fail('PromocodeException should not be thrown');
        }

        try {
            $promocodeList->addPromocode($promocodetest4);
        } catch (PromocodeException $e) {
            $this->assertEquals(PromocodeException::ERROR_ADD_PROMOCODE, $e->getMessage());
        }

        $this->assertCount(4, $promocodeList->getPromocodes());
    }

    public function testPromocodeCanBeRemovedFromPromocodeList()
    {
        $testPromocodes = [
            $promocode1 = new Promocode('TEST_1', 1.0, new \DateTime('now + 1 second')),
            $promocode2 = new Promocode('TEST_2', 1.0, new \DateTime('now + 1 second')),
            $promocode3 = new Promocode('TEST_3', 1.0, new \DateTime('now + 1 second')),
        ];

        $promocodeList = new PromocodeList($testPromocodes);
        $this->assertCount(3, $promocodeList->getPromocodes());

        try {
            $promocodeList->removePromocode($promocode2);
        } catch (PromocodeException $e) {
            $this->fail('PromocodeException should not be thrown');
        }

        try {
            $promocodeList->removePromocode($promocode2);
        } catch (PromocodeException $e) {
            $this->assertEquals(PromocodeException::ERROR_REMOVE_PROMOCODE, $e->getMessage());
        }


        $this->assertCount(2, $promocodeList->getPromocodes());
    }

    public function testGetAllMethodReturnsAnArrayOfPromocodes()
    {
        $testPromocodes = [
            new Promocode('TEST_1', 1.0, new \DateTime('now + 1 second')),
            new Promocode('TEST_2', 1.0, new \DateTime('now + 1 second')),
        ];

        $promocodeList = new PromocodeList($testPromocodes);
        $this->assertCount(2, $promocodeList->getPromocodes());

        $promocodes = $promocodeList->getAll();
        $this->assertCount(2, $promocodes);
        $this->assertContainsOnly(Promocode::class, $promocodes);
    }

    public function testEkwateurApiEndpointGetPromocodeListReturnsAListOfPromocode()
    {
        $testPromocodesObjects = $this->ekwateurManager->getPromocodes();
        $this->assertContainsOnlyInstancesOf(Promocode::class, $testPromocodesObjects);
    }

    public function testPromocodeAbcdIsNotInHardcodedList()
    {
        $testPromocode = "abcd";

        $testPromocodesObjects = $this->ekwateurManager->getPromocodes();

        $this->assertContainsOnlyInstancesOf(Promocode::class, $testPromocodesObjects);
        $this->assertFalse($this->assertArrayContainsSameObjectWithCodeValue($testPromocodesObjects, $testPromocode));
    }

    public function testPromocodeWoodyIsInHardcodedList()
    {
        $testPromocode = "WOODY";
        $testPromocodesObjects = $this->ekwateurManager->getPromocodes();

        $this->assertContainsOnlyInstancesOf(Promocode::class, $testPromocodesObjects);
        $this->assertTrue($this->assertArrayContainsSameObjectWithCodeValue($testPromocodesObjects, $testPromocode));
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

    public function testPromocodeCanBeCreatedFromDeserializedJson()
    {
        $testPromocode = new Promocode(
            code: 'TEST_1',
            discountValue: 8.8,
            endDate: new \DateTime('2021-12-31'),
        );

        $jsonData = '{"code":"TEST_1","discountValue":8.8,"endDate":"2021-12-31"}';
        $promocodeFromJson = $this->serializer->deserialize(data: $jsonData, type:Promocode::class, format:'json');

        $this->assertEquals('2021-12-31', $promocodeFromJson->getEndDate()->format('Y-m-d'));
        $this->assertEquals($testPromocode, $promocodeFromJson);
    }

    private function ExceptionThrownWhenPromocodeIsAddedTwice(PromocodeList $promocodeList, Promocode $promocode)
    {
        try {
            $promocodeList->addPromocode($promocode);
        } catch (PromocodeException $e) {
            $this->assertEquals(PromocodeException::ERROR_ADD_PROMOCODE, $e->getMessage());
        }
    }
}
