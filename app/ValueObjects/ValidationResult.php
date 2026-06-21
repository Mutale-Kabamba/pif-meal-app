<?php

namespace App\ValueObjects;

use App\Models\MealLog;

class ValidationResult
{
    public bool $success;
    public string $message;
    public string $status;
    public ?MealLog $mealLog;
    public array $details;

    public function __construct(
        bool $success,
        string $message,
        string $status,
        ?MealLog $mealLog = null,
        array $details = []
    ) {
        $this->success = $success;
        $this->message = $message;
        $this->status = $status;
        $this->mealLog = $mealLog;
        $this->details = $details;
    }

    public static function approved(string $message, MealLog $mealLog, array $details = []): self
    {
        return new self(true, $message, 'approved', $mealLog, $details);
    }

    public static function duplicate(string $message, array $details = []): self
    {
        return new self(false, $message, 'duplicate', null, $details);
    }

    public static function invalid(string $message, array $details = []): self
    {
        return new self(false, $message, 'invalid', null, $details);
    }

    public static function suspended(string $message, array $details = []): self
    {
        return new self(false, $message, 'suspended', null, $details);
    }

    public static function error(string $message, array $details = []): self
    {
        return new self(false, $message, 'error', null, $details);
    }

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'status' => $this->status,
            'message' => $this->message,
            'details' => $this->details,
        ];
    }
}
