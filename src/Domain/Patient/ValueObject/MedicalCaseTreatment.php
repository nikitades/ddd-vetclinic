<?php

namespace App\Domain\Patient\ValueObject;

use Webmozart\Assert\Assert;
use App\Domain\Shared\ValueObject\AbstractStringValueObject;

class MedicalCaseTreatment extends AbstractStringValueObject
{
    protected function check(string $value): void
    {
        Assert::stringNotEmpty($value);
    }
}