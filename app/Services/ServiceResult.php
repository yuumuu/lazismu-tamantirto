<?php

declare(strict_types=1);

namespace App\Services;

/**
 * Standardize service response objects.
 */
class ServiceResult
{
    public function __construct(
        protected bool $success,
        protected string $message,
        protected mixed $data = null
    ) {}

    public static function success(string $message = 'Success', mixed $data = null): self
    {
        return new self(true, $message, $data);
    }

    public static function error(string $message = 'Error', mixed $data = null): self
    {
        return new self(false, $message, $data);
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getData(): mixed
    {
        return $this->data;
    }
}
