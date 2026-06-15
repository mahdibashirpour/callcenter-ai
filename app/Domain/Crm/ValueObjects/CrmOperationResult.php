<?php

namespace App\Domain\Crm\ValueObjects;

use App\Domain\Crm\Enums\CrmLogStatus;

readonly class CrmOperationResult
{
    public function __construct(
        public bool $success,
        public ?string $externalId = null,
        public ?array $data = null,
        public ?string $message = null,
        public ?string $error = null,
    ) {}

    public static function success(?string $externalId = null, ?array $data = null, ?string $message = null): self
    {
        return new self(
            success: true,
            externalId: $externalId,
            data: $data,
            message: $message,
        );
    }

    public static function failure(string $error, ?array $data = null): self
    {
        return new self(
            success: false,
            data: $data,
            error: $error,
        );
    }

    public function status(): CrmLogStatus
    {
        return $this->success ? CrmLogStatus::Success : CrmLogStatus::Failed;
    }
}
