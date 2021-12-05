<?php

namespace Infrastructure\Actions\ValidatePromocode;

use Infrastructure\Ekwateur\EkwateurManager\EkwateurManagerInterface;

interface ValidatePromocodeInterface
{
    public function __construct(?EkwateurManagerInterface $ekwateurManager=null);
    public function __invoke(ValidatePromocodeRequest $request): ValidatePromocodeResponse;
}