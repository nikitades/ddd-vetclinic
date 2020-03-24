<?php

namespace App\Domain\Patient\ValueObject;

use DateTime;
use App\Domain\Shared\ValueObject\AbstractDateTimeValueObject;

class MedicalCaseEndedAt extends AbstractDateTimeValueObject
{
    protected function check(DateTime $value): void
    {
        if ($value->getTimestamp() > time()) {
            throw new \InvalidArgumentException("A case end time must be in past");
        }
    }
}
