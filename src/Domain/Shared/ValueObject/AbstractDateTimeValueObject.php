<?php

namespace App\Domain\Shared\ValueObject;

use DateTime;

class AbstractDateTimeValueObject
{
    protected DateTime $value;

    public function __construct(DateTime $value)
    {
        $this->check($value);
        $this->value = $value;
    }

    public function getValue(): DateTime
    {
        return $this->value;
    }

    protected function check(DateTime $value): void
    {
        //
    }

    public static function fromString(string $str): self
    {
        $date = new DateTime($str);
        return new static($date);
    }

    public static function now(): self
    {
        return new static(new DateTime());
    }
}
