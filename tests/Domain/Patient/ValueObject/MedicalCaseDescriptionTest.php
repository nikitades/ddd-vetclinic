<?php

namespace App\Test\Domain\Patient\ValueObject;

use App\Domain\Patient\ValueObject\MedicalCaseDescription;
use PHPUnit\Framework\TestCase;

class MedicalCaseDescriptionTest extends TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testEmptyDescription(): void
    {
        new MedicalCaseDescription("");
    }

    public function testCorrectDescription(): void
    {
        $text = "Nu tam eto koroche";
        $mcd = new MedicalCaseDescription($text);
        static::assertInstanceOf(MedicalCaseDescription::class, $mcd);
        static::assertEquals($text, $mcd->getValue());
    }
}