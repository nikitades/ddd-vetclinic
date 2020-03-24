<?php

namespace App\Domain\Patient\ValueObject;

use Webmozart\Assert\Assert;
use App\Domain\Shared\ValueObject\AbstractStringValueObject;

/**
 * Let's assume it's a russian phone number
 */
class OwnerPhone extends AbstractStringValueObject
{
    protected function check(string $value): void
    {
        Assert::string($value);
        Assert::stringNotEmpty($value);
        Assert::startsWith($value, "+");
        Assert::length($value, 12);
        Assert::digits(substr($value, 1));
    }
}
