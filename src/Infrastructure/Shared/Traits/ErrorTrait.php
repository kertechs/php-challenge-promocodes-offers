<?php

namespace Infrastructure\Shared\Traits;

use Infrastructure\Shared\Contracts\ErrorInterface;

Trait ErrorTrait
{
    /**
     * @var ErrorInterface[] $errors
     */
    protected array $errors = [];

    /**
     * @return ErrorInterface[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    public function addError(ErrorInterface $error): void
    {
        $this->errors[] = $error;
    }

    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    public function getLastError(): ErrorInterface|bool
    {
        return end($this->errors);
    }

    public function getErrorCount(): int
    {
        return count($this->errors);
    }

    public function clearErrors(): self
    {
        $this->errors = [];

        return $this;
    }

    public function getMessage(): string
    {
        if ($this->message) {
            return $this->message;
        }

        if ($this->hasErrors()) {
            return $this->getLastError()->getErrorMessage();
        }

        return '';
    }

    public function getCode(): string
    {
        return (string) $this->code;
    }

    public function getDateTime(): ?\DateTime
    {
        return $this->dateTime;
    }
}
