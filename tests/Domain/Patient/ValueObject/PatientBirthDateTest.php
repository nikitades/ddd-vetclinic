<?php

namespace App\Test\Domain\Patient\ValueObject;

use DateTime;
use PHPUnit\Framework\TestCase;
use App\Domain\Patient\Entity\Patient;
use App\Domain\Patient\ValueObject\PatientAge;
use App\Domain\Patient\ValueObject\PatientBirthDate;

class PatientBirthDateTest extends TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNegativeAge()
    {
        $pa = new PatientBirthDate(new DateTime(date("Y-m-d", time() + 86400)));
    }

    public function testDifferentAges()
    {
        static::assertInstanceOf(PatientBirthDate::class, new PatientBirthDate(new DateTime(date("Y-m-d", time()))));
        static::assertInstanceOf(PatientBirthDate::class, new PatientBirthDate(new DateTime(date("Y-m-d", time() - 500000))));
        static::assertInstanceOf(PatientBirthDate::class, new PatientBirthDate(new DateTime(date("Y-m-d", 0))));
    }

    public function testCorrectAge()
    {
        $date = (new DateTime())->setTimestamp(time() - 86400 * 365 * 4); //four years
        $pa = new PatientBirthDate($date);
        static::assertInstanceOf(PatientBirthDate::class, $pa);
        static::assertIsString($pa->getValue()->format("Y-m-d"));
        static::assertEquals($date->format("Y-m-d"), $pa->getDay());
    }
}
