<?php

namespace App\Test\Domain\Patient\ValueObject;

use PHPUnit\Framework\TestCase;
use App\Domain\Patient\ValueObject\MedicalCaseEnded;
use App\Domain\Patient\ValueObject\MedicalCaseEndedAt;

class MedicalCaseEndedTest extends TestCase
{
    public function testNewMedicalCaseEnded()
    {
        $mce = new MedicalCaseEnded(true);
        static::assertInstanceOf(MedicalCaseEnded::class, $mce);
        static::assertEquals(true, $mce->getValue());
    }

    public function testStaticEnded()
    {
        $mce = MedicalCaseEnded::ended();
        static::assertInstanceOf(MedicalCaseEnded::class, $mce);
        static::assertEquals(true, $mce->getValue());
    }

    public function testStaticNotEnded()
    {
        $mce = MedicalCaseEnded::notEnded();
        static::assertInstanceOf(MedicalCaseEnded::class, $mce);
        static::assertEquals(false, $mce->getValue());
    }
}
