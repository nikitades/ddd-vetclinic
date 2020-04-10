<?php

namespace App\Test\Domain\Patient\ValueObject;

use PHPUnit\Framework\TestCase;
use App\Domain\Patient\ValueObject\PatientSpecies;

class PatientSpeciesTest extends TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testShortName(): void
    {
        new PatientSpecies("ы");
    }

    public function testCorrectSpecies(): void
    {
        $species = "Shiba-Inu";
        $ps = new PatientSpecies($species);
        static::assertInstanceOf(PatientSpecies::class, $ps);
        static::assertIsString($ps->getValue());
        static::assertEquals($species, $ps->getValue());
    }
}