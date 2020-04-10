<?php

namespace App\Test\Domain\Patient\ValueObject;

use PHPUnit\Framework\TestCase;
use App\Domain\Patient\ValueObject\OwnerPhone;

class OwnerPhoneTest extends TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testShortPhone(): void
    {
        new OwnerPhone("+3");
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testPhoneWithoutStartingPlus(): void
    {
        new OwnerPhone("79993334444");
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testTooLongPhone(): void
    {
        new OwnerPhone("799933344445");
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testPhoneWithLetters(): void
    {
        new OwnerPhone("79993334A44");
    }

    public function testCorrectPhone(): void
    {
        $phone = "+79993334444";
        $op = new OwnerPhone($phone);
        static::assertInstanceOf(OwnerPhone::class, $op);
        static::assertEquals($phone, $op->getValue());
    }
}
