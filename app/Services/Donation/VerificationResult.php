<?php

declare(strict_types=1);

namespace App\Services\Donation;

/**
 * Value object for verification results.
 */
readonly class VerificationResult
{
    private function __construct(
        public bool $success,
        public string $message,
    ) {}

    public static function success(string $message): self
    {
        return new self(true, $message);
    }

    public static function cannotVerify(string $message): self
    {
        return new self(false, $message);
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
