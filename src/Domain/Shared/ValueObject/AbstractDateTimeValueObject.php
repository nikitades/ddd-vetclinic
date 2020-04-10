<?php

namespace App\Domain\Shared\ValueObject;

use DateTime;
use DateTimeInterface;

class AbstractDateTimeValueObject
{
    protected DateTimeInterface $value;

    public final function __construct(DateTimeInterface $value)
    {
        $this->check($value);
        $this->value = $value;
    }

    public function getValue(): DateTimeInterface
    {
        return $this->value;
    }

    public function equals(self $anotherObject): bool
    {
        return $this->value === $anotherObject->value;
    }

    protected function check(DateTimeInterface $value): void
    {
        //
    }

    /**
     * Creates an instance from a string
     *
     * @param string $str
     * @return static
     */
    public static function fromString(string $str): self
    {
        $date = new DateTime($str);
        return new static($date);
    }

    /**
     * Creates an instance with the current timestamp inside
     *
     * @return static
     */
    public static function now(): self
    {
        return new static(new DateTime());
    }
}
