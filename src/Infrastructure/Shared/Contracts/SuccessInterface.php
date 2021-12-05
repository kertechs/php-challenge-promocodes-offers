<?php

namespace Infrastructure\Shared\Contracts;

Interface SuccessInterface
{
    public function isSuccess(): bool;
    public function setSuccess(bool $success): self;
}