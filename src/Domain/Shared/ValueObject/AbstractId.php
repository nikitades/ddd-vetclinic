<?php

namespace App\Domain\Shared\ValueObject;

class AbstractId
{
    public function __construct(int $value)
    {
        $this->value = $value;
    }

    protected int $value;

    public function getValue(): int
    {
        return $this->value;
    }

    public function equalsTo(AbstractId $id)
    {
        return $id->getValue() === $this->getValue();
    }

    public static function fromInt(int $value): self
    {
        return new static($value);
    }
}
