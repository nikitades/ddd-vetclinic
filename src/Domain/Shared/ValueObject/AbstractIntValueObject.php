<?php

namespace App\Domain\Shared\ValueObject;

class AbstractIntValueObject
{
    private int $value;

    public function __construct(int $value)
    {
        $this->check($value);
        $this->value = $value;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    protected function check(int $value): void
    {
        //
    }

    public static function fromInt(int $value): self
    {
        return new static($value);
    }
}