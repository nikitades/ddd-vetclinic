<?php

namespace App\Test\Domain\Patient\ValueObject;

use App\Domain\Patient\ValueObject\OwnerEmail;
use PHPUnit\Framework\TestCase;

class OwnerEmailTest extends TestCase
{
    public function testCorrectEmail(): void
    {
        $oe = new OwnerEmail("haha@benis.com");
        static::assertNotNull($oe);
        static::assertInstanceOf(OwnerEmail::class, $oe);
    }

    /** @expectedException \InvalidArgumentException */
    public function testNoZoneEmail(): void
    {
        $oe = new OwnerEmail("haha@benis");
    }

    /** @expectedException \InvalidArgumentException */
    public function testNoNameEmail(): void
    {
        $oe = new OwnerEmail("benis.com");
    }

    /** @expectedException \InvalidArgumentException */
    public function testTwoAtsEmail(): void
    {
        $oe = new OwnerEmail("ha@ha@benis.com");
    }
}
