<?php

namespace Infrastructure\Symfony\Serializer;

use Infrastructure\Symfony\Serializer\Normalizer\DateTimeDenormalizer;
use Infrastructure\Symfony\Serializer\Normalizer\OfferTypeDenormalizer;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class EkwateurSerializer extends Serializer implements EkwateurSerializerInterface
{
    public function __construct(?array $normalizers = null, ?array $encoders = null)
    {
        $normalizers = $normalizers ?? [
            new ArrayDenormalizer(),
            new DateTimeDenormalizer(),
            new GetSetMethodNormalizer(propertyTypeExtractor: new ReflectionExtractor()),
            new OfferTypeDenormalizer(),
            new ObjectNormalizer(propertyTypeExtractor: new ReflectionExtractor()),
        ];

        $encoders = $encoders ?? [new JsonEncoder()];

        parent::__construct($normalizers, $encoders);
    }
}
