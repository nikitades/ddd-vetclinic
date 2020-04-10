<?php

namespace App\Domain\Shared\ValueObject;

class AbstractId
{
    public final function __construct(int $value)
    {
        $this->value = $value;
    }

    public int $value;

    public function equals(self $anotherObject): bool
    {
        return $this->value === $anotherObject->value;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public static function fromInt(int $value): self
    {
        return new static($value);
    }
}
