<?php

namespace App\Domain\Patient\ValueObject;

use Webmozart\Assert\Assert;
use App\Domain\Shared\ValueObject\AbstractStringValueObject;

class PatientName extends AbstractStringValueObject
{
    protected function check(string $value): void
    {
        Assert::minLength($value, 2, "The patient's name must be at least %2\$s symbols long");
    }
}
