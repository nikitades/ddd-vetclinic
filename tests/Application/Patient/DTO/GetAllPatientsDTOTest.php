<?php

namespace App\Test\Application\Patient\DTO;

use App\Application\Patient\DTO\GetAllPatientsDTO;
use PHPUnit\Framework\TestCase;

class GetAllPatientsDTOTest extends TestCase
{
    public function testOnTreatmentShortcut(): void
    {
        $getAllPatientsDTO = GetAllPatientsDTO::onTreatment();
        static::assertInstanceOf(GetAllPatientsDTO::class, $getAllPatientsDTO);
        static::assertTrue($getAllPatientsDTO->onTreatment);
        static::assertFalse($getAllPatientsDTO->released);
    }

    public function testReleasedShortcut(): void
    {
        $getAllPatientsDTO = GetAllPatientsDTO::released();
        static::assertInstanceOf(GetAllPatientsDTO::class, $getAllPatientsDTO);
        static::assertTrue($getAllPatientsDTO->released);
        static::assertFalse($getAllPatientsDTO->onTreatment);
    }

    public function testGetOnTreatmentProp(): void
    {
        $getAllPatientsDTO = GetAllPatientsDTO::onTreatment();
        static::assertInstanceOf(GetAllPatientsDTO::class, $getAllPatientsDTO);
        $onTreatmentProp = $getAllPatientsDTO->onTreatment;
        static::assertIsBool($onTreatmentProp);
        static::assertTrue($onTreatmentProp);
    }

    public function testGetReleasedProp(): void
    {
        $getAllPatientsDTO = GetAllPatientsDTO::Released();
        static::assertInstanceOf(GetAllPatientsDTO::class, $getAllPatientsDTO);
        $releasedProp = $getAllPatientsDTO->released;
        static::assertIsBool($releasedProp);
        static::assertTrue($releasedProp);
    }
}
