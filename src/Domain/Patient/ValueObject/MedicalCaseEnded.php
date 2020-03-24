<?php

namespace App\Domain\Patient\ValueObject;

class MedicalCaseEnded
{
    private bool $ended;

    public function __construct(bool $ended)
    {
        $this->ended = $ended;
    }

    public function getValue(): bool
    {
        return $this->ended;
    }

    public static function ended(): self
    {
        return new static(true);
    }

    public static function notEnded(): self
    {
        return new static(false);
    }
}
