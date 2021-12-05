<?php

namespace Infrastructure\Shared\Contracts;

interface ErrorInterface
{
    public function __construct(string $message, int $code = 0, ?\DateTime $dateTime=null, ?\Throwable $previous = null);
    public function addError(ErrorInterface $error): void;

    /**
     * @return ErrorInterface[]
     */
    public function getErrors(): array;

    public function hasErrors(): bool;

    public function clearErrors(): self;

    public function getErrorCount(): int;

    public function getErrorMessage(): string;

    public function getLastError(): ErrorInterface|bool;
}