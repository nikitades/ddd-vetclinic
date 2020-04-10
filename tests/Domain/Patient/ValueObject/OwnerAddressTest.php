<?php

namespace App\Test\Domain\Patient\ValueObject;

use App\Domain\Patient\ValueObject\OwnerAddress;
use PHPUnit\Framework\TestCase;

class OwnerAddressTest extends TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testTooShortAddress(): void
    {
        new OwnerAddress("");
    }

    public function testCorrectAddress(): void
    {
        $addr = "улица Пушкина, панельки панельки";
        $oa = new OwnerAddress($addr);
        static::assertInstanceOf(OwnerAddress::class, $oa);
        static::assertEquals($addr, $oa->getValue());
    }
}
