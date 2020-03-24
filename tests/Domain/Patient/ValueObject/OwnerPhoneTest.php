<?php

namespace App\Test\Domain\Patient\ValueObject;

use PHPUnit\Framework\TestCase;
use App\Domain\Patient\ValueObject\OwnerPhone;

class OwnerPhoneTest extends TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testShortPhone()
    {
        new OwnerPhone("+3");
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testPhoneWithoutStartingPlus()
    {
        new OwnerPhone("79993334444");
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testTooLongPhone()
    {
        new OwnerPhone("799933344445");
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testPhoneWithLetters()
    {
        new OwnerPhone("79993334A44");
    }

    public function testCorrectPhone()
    {
        $phone = "+79993334444";
        $op = new OwnerPhone($phone);
        static::assertInstanceOf(OwnerPhone::class, $op);
        static::assertEquals($phone, $op->getValue());
    }
}
