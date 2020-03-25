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

    private function createAddPatientDTO($ownerName, $dogName, $dogSpecies, $dogBirthDate): CreatePatientDTO
    {
        return new CreatePatientDTO(
            $ownerName,
            [
                $dogName,
                $dogSpecies,
                $dogBirthDate,
            ]
        );
    }

    public function testGetAllPatients()
    {
        $patientService = $this->createPatientService();

        $petName = "Pegasus";
        $createPatientDTO = $this->createAddPatientDTO("Hercules", $petName, $petName, "1984-01-01");
        $patientService->addPatient($createPatientDTO);

        $getAllPatientsDTO = new GetAllPatientsDTO();
        $allPatients = $patientService->getAll($getAllPatientsDTO);
        static::assertIsArray($allPatients);
        static::assertCount(1, $allPatients);
        $allPetsNames = array_map(fn ($pet) => $pet->getName()->getValue(), $allPatients);
        static::assertContains($petName, $allPatients);
    }

    public function testGetAllPatientsOnTreatment()
    {
        $patientService = $this->createPatientService();

        $patient1 = [
            'ownerName' => 'Hercules',
            'name' => 'Pegasus',
            'species' => 'Pegasus',
            'birthDate' => '1984-01-01'
        ];

        $patient2 = [
            'ownerName' => 'Marc Aurelius',
            'name' => 'Boulevard',
            'species' => 'Horse',
            'birthDate' => '1970-03-20'
        ];

        $patient3 = [
            'ownerName' => 'Harry Potter',
            'name' => 'Ponce',
            'species' => 'Hyppogryphus',
            'birthDate' => '1991-09-19'
        ];

        foreach ([$patient1, $patient2, $patient3] as $patient) {
            $addPatientDTO = $this->createAddPatientDTO($patient['ownerName'], $patient['name'], $patient['species'], $patient['birthDate']);
            $patientService->addPatient($addPatientDTO);
        }

        $releasePatientDTO = new ReleasePatientDTO(
            $patient2['ownerName'],
            $patient2['name']
        );
        $patientService->releasePatient($releasePatientDTO);

        $getAllPatientsDTO = GetAllPatientsDTO::onTreatment();
        $allPatients = $patientService->getAll($getAllPatientsDTO);
        static::assertIsArray($allPatients);
        static::assertCount(1, $allPatients);
        $allPetsNames = array_map(fn ($pet) => $pet->getName()->getValue(), $allPatients);
        static::assertContains($patient2['name'], $allPatients);
    }

    public function testGetAllReleasedPatients()
    {
        $patientService = $this->createPatientService();

        $patient1 = [
            'ownerName' => 'Hercules',
            'name' => 'Pegasus',
            'species' => 'Pegasus',
            'birthDate' => '1984-01-01'
        ];

        $patient2 = [
            'ownerName' => 'Marc Aurelius',
            'name' => 'Boulevard',
            'species' => 'Horse',
            'birthDate' => '1970-03-20'
        ];

        $patient3 = [
            'ownerName' => 'Harry Potter',
            'name' => 'Ponce',
            'species' => 'Hyppogryphus',
            'birthDate' => '1991-09-19'
        ];

        foreach ([$patient1, $patient2, $patient3] as $patient) {
            $addPatientDTO = $this->createAddPatientDTO($patient['ownerName'], $patient['name'], $patient['species'], $patient['birthDate']);
            $patientService->addPatient($addPatientDTO);
        }

        $releasePatientDTO = new ReleasePatientDTO(
            $patient2['ownerName'],
            $patient2['name']
        );
        $patientService->releasePatient($releasePatientDTO);

        $getAllPatientsDTO = GetAllPatientsDTO::released();
        $allPatients = $patientService->getAll($getAllPatientsDTO);
        static::assertIsArray($allPatients);
        static::assertCount(2, $allPatients);
        $allPetsNames = array_map(fn ($pet) => $pet->getName()->getValue(), $allPatients);
        static::assertContains($patient1['name'], $allPatients);
        static::assertContains($patient3['name'], $allPatients);
    }

    public function testAddAndGetPatient()
    {
        $patientService = $this->createPatientService();

        $ownerName = "Owen Wilson";
        $dogName = "Marley";
        $dogSpecies = "Dog";
        $dogBirthDate = "2000-06-30";

        $createPatientDTO = $this->createAddPatientDTO($ownerName, $dogName, $dogSpecies, $dogBirthDate);
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

    public function testDeletePatient()
    {
        $patientService = $this->createPatientService();

        $ownerName = "Owen Wilson";
        $dogName = "Marley";
        $dogSpecies = "Dog";
        $dogBirthDate = "2000-06-30";

        $addPatientDTO = new AddPatientDTO(
            $ownerName,
            [
                $dogName,
                $dogSpecies,
                $dogBirthDate,
            ]
        );
        $patientService->addPatient($addPatientDTO);

        $getAllPatientsDTO = new GetAllPatientsDTO();
        $allPatients = $patientService->getAll($getAllPatientsDTO);
        $allPatientsNames = array_map(fn ($patient) => $patient->getName()->getValue(), $allPatients);
        static::assertContains($dogName, $allPatientsNames);

        $removePatientDto = new RemovePatientDTO(
            $ownerName,
            $dogName
        );
        $patientService->removePatient($removePatientDto);

        $getAllPatientsDTO = new GetAllPatientsDTO();
        $allPatients = $patientService->getAll($getAllPatientsDTO);
        $allPatientsNames = array_map(fn ($patient) => $patient->getName()->getValue(), $allPatients);
        static::assertNotContains($dogName, $allPatientsNames);
    }

    //TODO: implement the service!
}
