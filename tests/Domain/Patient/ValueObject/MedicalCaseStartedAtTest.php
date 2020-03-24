<?php

namespace App\Test\Domain\Patient\ValueObject;

use App\Domain\Patient\ValueObject\MedicalCaseStartedAt;
use DateTime;
use PHPUnit\Framework\TestCase;

class MedicalCaseStartedAtTest extends TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testEarlyStartedAt()
    {
        new MedicalCaseStartedAt((new DateTime())->setTimestamp(time() + 86400));
    }

    public function testCorrectStartedAt()
    {
        $date = new DateTime();
        $mcsa = new MedicalCaseStartedAt($date);
        static::assertInstanceOf(MedicalCaseStartedAt::class, $mcsa);
        static::assertEquals($date, $mcsa->getValue());
    }
}
