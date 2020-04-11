<?php

namespace App\Domain\Patient\ValueObject;

final class OwnerNotificationRequired
{
    private bool $value;

    public function __construct(bool $value)
    {
        $this->value = $value;    
    }

    public function getValue(): bool
    {
        return $this->value;
    }

    public static function on(): self
    {
        return new static(true);
    }

    public static function off(): self
    {
        return new static(false);
    }
}