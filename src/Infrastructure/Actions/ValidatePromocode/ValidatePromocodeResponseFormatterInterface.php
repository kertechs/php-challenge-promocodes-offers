<?php

namespace Infrastructure\Actions\ValidatePromocode;

interface ValidatePromocodeResponseFormatterInterface
{
    public function __construct(?ValidatePromocodeResponse $response=null);
    public function format(): self;
    public function output(): self;
    public function __invoke(ValidatePromocodeResponse $response): void;
}