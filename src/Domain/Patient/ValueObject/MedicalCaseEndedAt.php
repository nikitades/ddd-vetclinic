<?php

namespace App\Domain\Patient\ValueObject;

use DateTime;
use App\Domain\Shared\ValueObject\AbstractDateTimeValueObject;
use DateTimeInterface;

class MedicalCaseEndedAt extends AbstractDateTimeValueObject
{
    protected function check(DateTimeInterface $value): void
    {
        if ($value->getTimestamp() > time()) {
            throw new \InvalidArgumentException("A case end time must be in past");
        }
    }
}
