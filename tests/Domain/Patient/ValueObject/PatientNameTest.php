<?php

namespace App\Test\Domain\Patient\ValueObject;

use PHPUnit\Framework\TestCase;
use App\Domain\Patient\ValueObject\PatientName;

class PatientNameTest extends TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testShortName()
    {
        $pn = new PatientName("a");
    }

    public function testCorrectName()
    {
        $name = "Gandalf";
        $pn = new PatientName($name);
        static::assertInstanceOf(PatientName::class, $pn);
        static::assertIsString($pn->getValue());
        static::assertEquals($name, $pn->getValue());
    }
}
