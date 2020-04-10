<?php

namespace App\Domain\Patient\ValueObject;

use DateTime;
use App\Domain\Shared\ValueObject\AbstractDateTimeValueObject;
use DateTimeInterface;

class CardCreatedAt extends AbstractDateTimeValueObject
{
    protected function check(DateTimeInterface $value): void
    {
        if ($value->getTimestamp() > time()) {
            throw new \InvalidArgumentException("A card creation date must be in the past");
        }
    }
}