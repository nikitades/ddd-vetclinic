<?php

namespace App\Domain\Shared\ValueObject;

abstract class AbstractStringValueObject
{
    protected string $value;

    public final function __construct(string $value)
    {
        $this->check($value);
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(self $anotherObject): bool
    {
        return $this->value === $anotherObject->value;
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
