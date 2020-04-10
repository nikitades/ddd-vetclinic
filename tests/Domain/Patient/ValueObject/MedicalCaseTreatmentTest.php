<?php

namespace App\Test\Domain\Patient\ValueObject;

use App\Domain\Patient\ValueObject\MedicalCaseStartedAt;
use App\Domain\Patient\ValueObject\MedicalCaseTreatment;
use PHPUnit\Framework\TestCase;

class MedicalCaseTreatmentTest extends TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testEmptyTreatment(): void
    {
        new MedicalCaseTreatment("");
    }

    public function testCorrectTreatment(): void
    {
        $text = "Vi normalniy";
        $treatment = new MedicalCaseTreatment($text);
        static::assertInstanceOf(MedicalCaseTreatment::class, $treatment);
        static::assertEquals($text, $treatment->getValue());
    }
}