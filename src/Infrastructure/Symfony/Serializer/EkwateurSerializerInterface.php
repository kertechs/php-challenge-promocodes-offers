<?php

namespace Infrastructure\Symfony\Serializer;

interface EkwateurSerializerInterface
{
    public function serialize($data, string $format, array $context = []);

    public function deserialize($data, string $type, string $format, array $context = []);
}
