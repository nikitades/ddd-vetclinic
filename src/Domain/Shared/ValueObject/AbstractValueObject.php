<?php

namespace App\Domain\Shared\ValueObject;

abstract class AbstractValueObject
{
    public function equals(self $anotherValueObject): bool
    {
        return $this->value === $anotherValueObject->value;
    }
}
