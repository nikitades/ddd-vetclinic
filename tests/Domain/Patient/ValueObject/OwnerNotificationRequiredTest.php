<?php

namespace App\Test\Domain\Patient\ValueObject;

use PHPUnit\Framework\TestCase;
use App\Domain\Patient\ValueObject\OwnerNotificationRequired;

class OwnerNotificationRequiredTest extends TestCase
{
    public function testNewOwnerNotificationRequired(): void
    {
        $onr = new OwnerNotificationRequired(true);
        static::assertInstanceOf(OwnerNotificationRequired::class, $onr);
        static::assertTrue($onr->getValue());
    }

    public function testOwnerNotificationRequiredOn(): void
    {
        $onr = OwnerNotificationRequired::on();
        static::assertInstanceOf(OwnerNotificationRequired::class, $onr);
        static::assertTrue($onr->getValue());
    }

    public function testOwnerNotificationRequiredOff(): void
    {
        $onr = OwnerNotificationRequired::off();
        static::assertInstanceOf(OwnerNotificationRequired::class, $onr);
        static::assertFalse($onr->getValue());
    }
}