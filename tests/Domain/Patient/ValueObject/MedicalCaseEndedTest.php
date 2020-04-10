<?php

namespace App\Test\Domain\Patient\ValueObject;

use PHPUnit\Framework\TestCase;
use App\Domain\Patient\ValueObject\MedicalCaseEnded;
use App\Domain\Patient\ValueObject\MedicalCaseEndedAt;

class MedicalCaseEndedTest extends TestCase
{
    public function testNewMedicalCaseEnded(): void
    {
        $mce = new MedicalCaseEnded(true);
        static::assertInstanceOf(MedicalCaseEnded::class, $mce);
        static::assertEquals(true, $mce->getValue());
    }

    public function testStaticEnded(): void
    {
        $mce = MedicalCaseEnded::ended();
        static::assertInstanceOf(MedicalCaseEnded::class, $mce);
        static::assertEquals(true, $mce->getValue());
    }

    public function testStaticNotEnded(): void
    {
        $mce = MedicalCaseEnded::notEnded();
        static::assertInstanceOf(MedicalCaseEnded::class, $mce);
        static::assertEquals(false, $mce->getValue());
    }
}
