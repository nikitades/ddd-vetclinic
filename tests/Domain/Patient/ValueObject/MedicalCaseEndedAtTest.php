<?php

namespace App\Test\Domain\Patient\ValueObject;

use DateTime;
use PHPUnit\Framework\TestCase;
use App\Domain\Patient\ValueObject\MedicalCaseEndedAt;

class MedicalCaseEndedAtTest extends TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testEarlyEndedAt(): void
    {
        new MedicalCaseEndedAt((new DateTime())->setTimestamp(time() + 86400));
    }

    public function testCorrectEndedAt(): void
    {
        $date = new DateTime();
        $mcea = new MedicalCaseEndedAt($date);
        static::assertInstanceOf(MedicalCaseEndedAt::class, $mcea);
        static::assertEquals($date, $mcea->getValue());
    }
}
