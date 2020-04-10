<?php

namespace App\Test\Domain\Patient\ValueObject;

use DateTime;
use PHPUnit\Framework\TestCase;
use App\Domain\Patient\ValueObject\CardCreatedAt;

class CardCreatedAtTest extends TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNegativeDate(): void
    {
        $cca = new CardCreatedAt(new DateTime(date("Y-m-d H:i:s", time() + 86400)));
    }

    public function testDifferentDates(): void
    {
        static::assertInstanceOf(CardCreatedAt::class, new CardCreatedAt(new DateTime(date("Y-m-d", time()))));
        static::assertInstanceOf(CardCreatedAt::class, new CardCreatedAt(new DateTime(date("Y-m-d", time() - 500000))));
        static::assertInstanceOf(CardCreatedAt::class, new CardCreatedAt(new DateTime(date("Y-m-d", 0))));
    }

    public function testCorrectDate(): void
    {
        $cca = CardCreatedAt::fromString(date("Y-m-d", time() - 86400 * 90));
        static::assertInstanceOf(CardCreatedAt::class, $cca);
        static::assertIsString($cca->getValue()->format("Y-m-d"));
        static::assertGreaterThan(0, $cca->getValue()->getTimestamp());
    }
}
