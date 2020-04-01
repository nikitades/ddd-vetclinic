<?php

namespace App\Test\Domain\Patient\ValueObject;

use App\Domain\Patient\ValueObject\CardClosed;
use PHPUnit\Framework\TestCase;

class CardClosedTest extends TestCase
{
    public function testNewCardClosed()
    {
        $cc = new CardClosed(false);
        static::assertNotNull($cc);
        static::assertInstanceOf(CardClosed::class, $cc);
    }

    public function testClosed()
    {
        $cc = CardClosed::closed();
        static::assertNotNull($cc);
        static::assertInstanceOf(CardClosed::class, $cc);
        static::assertTrue($cc->getValue());
    }

    public function testUnlosed()
    {
        $cc = CardClosed::unclosed();
        static::assertNotNull($cc);
        static::assertInstanceOf(CardClosed::class, $cc);
        static::assertFalse($cc->getValue());
    }
}