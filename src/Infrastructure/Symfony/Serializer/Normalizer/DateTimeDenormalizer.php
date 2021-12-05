<?php

namespace Infrastructure\Symfony\Serializer\Normalizer;

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class DateTimeDenormalizer implements DenormalizerInterface
{
    /**
     * @throws DateTimeDenormalizerException
     */
    public function denormalize($data, string $type, string $format = null, array $context = array()) :\DateTime
    {
        try {
            return new \DateTime($data);
        } catch (\Throwable $exception) {
            throw new DateTimeDenormalizerException($exception->getMessage());
        }
    }

    public function supportsDenormalization($data, string $type, ?string $format = null): bool
    {
        if ($format == 'json' && $type === \DateTime::class) {
            return true;
        }

        return false;
    }
}
