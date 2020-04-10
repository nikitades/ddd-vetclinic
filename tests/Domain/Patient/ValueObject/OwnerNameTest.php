<?php

namespace App\Test\Domain\Patient\ValueObject;

use PHPUnit\Framework\TestCase;
use App\Domain\Patient\ValueObject\OwnerName;

class OwnerNameTest extends TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testEmptyName(): void
    {
        new OwnerName("");
    }

    public function testCorrectName(): void
    {
        $name = "Евграф";
        $ownerName = new OwnerName($name);
        static::assertInstanceOf(OwnerName::class, $ownerName);
        static::assertEquals($name, $ownerName->getValue());
    }
}
