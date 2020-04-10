<?php

namespace App\Test\Domain\Shared\ValueObject;

use PHPUnit\Framework\TestCase;
use App\Domain\Patient\ValueObject\PatientId;

class IdTest extends TestCase
{
    public function testFromString(): void
    {
        $id = 543;
        $Pid = PatientID::fromInt($id);

        static::assertInstanceOf(PatientID::class, $Pid);
        static::assertIsInt($Pid->getValue());
        static::assertEquals($id, $Pid->getValue());
    }
}
