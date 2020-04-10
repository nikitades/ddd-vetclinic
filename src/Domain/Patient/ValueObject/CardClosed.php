<?php

namespace App\Domain\Patient\ValueObject;

class CardClosed
{
    private bool $closed;

    public final function __construct(bool $closed)
    {
        $this->closed = $closed;
    }

    public function getValue(): bool
    {
        return $this->closed;
    }

    public static function closed(): self
    {
        return new static(true);
    }

    public static function unclosed(): self
    {
        return new static(false);
    }
}
