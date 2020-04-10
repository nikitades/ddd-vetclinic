<?php

namespace App\Domain\Patient\ValueObject;

use DateTime;
use App\Domain\Shared\ValueObject\AbstractDateTimeValueObject;
use DateTimeInterface;

class OwnerRegisteredAt extends AbstractDateTimeValueObject
{
    protected function check(DateTimeInterface $value): void
    {
        if ($value->getTimestamp() > time()) {
            throw new \InvalidArgumentException("Owner's registration date must be in past");
        }
    }
}
