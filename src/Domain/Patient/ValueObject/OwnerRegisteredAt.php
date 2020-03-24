<?php

namespace App\Domain\Patient\ValueObject;

use DateTime;
use App\Domain\Shared\ValueObject\AbstractDateTimeValueObject;

class OwnerRegisteredAt extends AbstractDateTimeValueObject
{
    protected function check(DateTime $value): void
    {
        if ($value->getTimestamp() > time()) {
            throw new \InvalidArgumentException("Owner's registration date must be in past");
        }
    }
}
