<?php

namespace App\Test\Application;

use PHPUnit\Framework\TestCase;
use App\Application\PatientService;
use App\Domain\Patient\Entity\Owner;
use App\Domain\Patient\Entity\Patient;
use App\Domain\Patient\ValueObject\OwnerId;
use App\Domain\Patient\ValueObject\OwnerPhone;

class PatientServiceTest extends TestCase
{
    private function createPatientService()
    {
        return new PatientService();
    }

    public function testAddAndGetPatient()
    {
        $patientService = $this->createPatientService();

        $ownerName = "Owen Wilson";
        $dogName = "Marley";
        $dogSpecies = "Dog";
        $dogBirthDate = "2000-06-30";

        $createPatientDTO = new CreatePatientDTO(
            $ownerName,
            [
                $dogName,
                $dogSpecies,
                $dogBirthDate,
            ]
        );

        $patientService->addPatient($createPatientDTO);

        $getPatientDTO = new GetPatientDTO(
            $dogName,
            $ownerName
        );

        $patient = $patientService->getPatient($getPatientDTO);
        static::assertNotNull($patient);
        static::assertInstanceOf(Patient::class, $patient);
        static::assertEquals($dogName, $patient->getName());
        static::assertEquals($dogSpecies, $patient->getSpecies());
        static::assertEquals($dogBirthDate, $patient->getBirthDate()->getValue()->format("Y-m-d"));
    }

    //TODO: test delete patient, test get all patients, test get all patients on treatment, test get all released patients
}
