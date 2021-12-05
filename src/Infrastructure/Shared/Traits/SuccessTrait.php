<?php

namespace Infrastructure\Shared\Traits;

Trait SuccessTrait
{
    protected bool $success = false;

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function setSuccess(bool $success): self
    {
        $this->success = $success;

        return $this;
    }
}