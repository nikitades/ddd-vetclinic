<?php

namespace App\Domain\Patient\ValueObject;

use DateTime;
use App\Domain\Shared\ValueObject\AbstractDateTimeValueObject;

class MedicalCaseStartedAt extends AbstractDateTimeValueObject
{
    protected function check(DateTime $value): void
    {
        if ($value->getTimestamp() > time()) {
            throw new \InvalidArgumentException("A case start time must be in past");
        }
    }
}
