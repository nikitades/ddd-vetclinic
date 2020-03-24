<?php

namespace App\Domain\Patient\ValueObject;

use DateTime;
use App\Domain\Shared\ValueObject\AbstractDateTimeValueObject;
use InvalidArgumentException;

class PatientBirthDate extends AbstractDateTimeValueObject
{
    protected function check(DateTime $value): void
    {
        if ($value->getTimestamp() > time()) {
            throw new InvalidArgumentException("Patient's birth date must be in the past");
        }
    }

    public function getDay(): string
    {
        return $this->value->format("Y-m-d");
    }
}
