<?php

namespace Infrastructure\Symfony\Serializer\Normalizer;

use Domain\Eshop\Offer\OfferType;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class OfferTypeDenormalizer implements DenormalizerInterface
{
    /**
     * @throws OfferTypeDenormalizerException
     */
    public function denormalize($data, string $type, string $format = null, array $context = array()) :OfferType
    {
        try {
            $offerType = null;

            if (is_string($data)) {
                $offerType = OfferType::getValue($data);
            } elseif (is_int($data)) {
                $offerType = OfferType::from($data);
            }

            if (!$offerType instanceof OfferType) {
                throw new \Exception('Unknown data type');
            }

            return $offerType;
        } catch (\Throwable $exception) {
            throw new OfferTypeDenormalizerException($exception->getMessage());
        }
    }

    public function supportsDenormalization($data, string $type, ?string $format = null): bool
    {
        if ($format == 'json' && $type === OfferType::class) {
            return true;
        }
        return false;
    }
}
