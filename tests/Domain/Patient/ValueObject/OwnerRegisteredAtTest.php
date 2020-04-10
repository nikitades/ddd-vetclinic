<?php

namespace App\Test\Domain\Patient\ValueObject;

use App\Domain\Patient\ValueObject\OwnerRegisteredAt;
use DateTime;
use PHPUnit\Framework\TestCase;

class OwnerRegisteredAtTest extends TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testTooEarlyDate(): void
    {
        new OwnerRegisteredAt((new DateTime())->setTimestamp(time() + 86400));
    }

    public function testCorrectDate(): void
    {
        $date = new DateTime();
        $orat = new OwnerRegisteredAt($date);
        static::assertInstanceOf(OwnerRegisteredAt::class, $orat);
        static::assertEquals($date, $orat->getValue());
    }
}