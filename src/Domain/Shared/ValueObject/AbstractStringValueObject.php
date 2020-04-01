<?php

namespace App\Domain\Shared\ValueObject;

use App\Domain\Shared\Exceptions\WrongValueException;

abstract class AbstractStringValueObject extends AbstractValueObject
{
    private string $value;

    public function __construct(string $value)
    {
        $this->check($value);
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    protected function check(string $value): void
    {
        //
    }

    public static function fromString(string $value): self
    {
        return new static($value);
    }
}
