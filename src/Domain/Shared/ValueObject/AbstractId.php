<?php

namespace App\Domain\Shared\ValueObject;

class AbstractId extends AbstractValueObject
{
    public function __construct(int $value)
    {
        $this->value = $value;
    }

    public int $value;

    public function getValue(): int
    {
        return $this->value;
    }

    public static function fromInt(int $value): self
    {
        return new static($value);
    }
}
