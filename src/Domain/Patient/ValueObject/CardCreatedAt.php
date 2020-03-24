<?php

namespace App\Domain\Patient\ValueObject;

use DateTime;
use App\Domain\Shared\ValueObject\AbstractDateTimeValueObject;

class CardCreatedAt extends AbstractDateTimeValueObject
{
    protected function check(DateTime $value): void
    {
        if ($value->getTimestamp() > time()) {
            throw new \InvalidArgumentException("A card creation date must be in the past");
        }
    }
}