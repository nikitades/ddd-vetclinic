<?php

namespace App\Test\Application;

use DateTime;
use PHPUnit\Framework\TestCase;
use App\Domain\Patient\Entity\Card;
use App\Domain\Patient\Entity\Owner;
use App\Domain\Patient\Entity\Patient;
use App\Domain\Patient\Entity\MedicalCase;
use App\Domain\Patient\ValueObject\CardId;
use App\Application\Patient\PatientService;
use App\Domain\Patient\ValueObject\OwnerId;
use App\Domain\Patient\ValueObject\OwnerName;
use App\Domain\Patient\ValueObject\PatientId;
use App\Application\Patient\DTO\AddPatientDTO;
use App\Application\Patient\DTO\GetPatientDTO;
use App\Domain\Patient\ValueObject\OwnerPhone;
use App\Application\Patient\IPatientRepository;
use App\Domain\Patient\ValueObject\PatientName;
use App\Domain\Patient\ValueObject\OwnerAddress;
use App\Application\Patient\DTO\RemovePatientDTO;
use App\Application\Patient\DTO\GetAllPatientsDTO;
use App\Application\Patient\DTO\ReleasePatientDTO;
use App\Domain\Patient\ValueObject\PatientSpecies;
use App\Application\Patient\DTO\AddCardToPatientDTO;
use App\Domain\Patient\ValueObject\PatientBirthDate;
use App\Application\Patient\DTO\RemoveCardFromPatientDTO;
use App\Application\Patient\DTO\AddMedicalCaseToPatientDTO;
use App\Application\Patient\DTO\CloseMedicalCaseDTO;
use App\Application\Patient\DTO\RemoveMedicalCaseFromPatientDTO;
use App\Domain\Patient\ValueObject\MedicalCaseId;

class PatientServiceTest extends TestCase
{
    private Owner $owner1;
    private Owner $owner2;
    private Owner $owner3;

    private Patient $patient1;
    private Patient $patient2;
    private Patient $patient3;

    public function __construct()
    {
        parent::__construct();

        $this->owner1 = new Owner(
            new OwnerName("Sam"),
            new OwnerPhone("+79998887777"),
            new OwnerAddress("5th Av.")
        );
        $this->owner1->setId(new OwnerId(403));

        $this->owner2 = new Owner(
            new OwnerName("Keanu"),
            new OwnerPhone("+73334442424"),
            new OwnerAddress("Matrix")
        );
        $this->owner2->setId(new OwnerId(38));

        $this->owner3 = new Owner(
            new OwnerName("Harry"),
            new OwnerPhone("+78889293030"),
            new OwnerAddress("Hogwarts")
        );
        $this->owner3->setId(new OwnerId(707));

        $this->patient1 = new Patient(
            new PatientName("Pegasus"),
            new PatientBirthDate(new DateTime("1984-01-01")),
            new PatientSpecies("Pegasus")
        );
        $this->patient1->setOwner($this->owner1);
        $this->patient1->setId(new PatientId(55));
        $card1 = new Card();
        $card1->setId(new CardId(666));
        $this->patient1->addCard($card1);
        $case1 = new MedicalCase();
        $case1->setId(new MedicalCaseId(8348));
        $this->patient1->getCurrentCard()->addCase($case1);

        $this->patient2 = new Patient(
            new PatientName("Boulevard"),
            new PatientBirthDate(new DateTime("1970-03-20")),
            new PatientSpecies("Horse")
        );
        $this->patient2->setOwner($this->owner2);
        $this->patient2->setId(new PatientId(345));

        $this->patient3 = new Patient(
            new PatientName("Ponce"),
            new PatientBirthDate(new DateTime("1991-09-19")),
            new PatientSpecies("Hyppogryphus")
        );
        $this->patient3->setOwner($this->owner3);
        $this->patient3->setId(new PatientId(999));
    }

    private function createPatientService(bool $pegasusDeleted = false)
    {
        $patientRepo = $this->createMock(IPatientRepository::class);

        $patientRepo
            ->method("getOwnerById")
            ->willReturnMap([
                [$this->owner1->getId()->getValue(), $this->owner1],
                [$this->owner2->getId()->getValue(), $this->owner2],
                [$this->owner3->getId()->getValue(), $this->owner3],
            ]);


        $patientRepo
            ->method("getAllPatients")
            ->willReturnMap([
                [true, true, $pegasusDeleted ? [$this->patient2, $this->patient3] : [$this->patient1, $this->patient2, $this->patient3]],
                [true, false, [$this->patient2]],
                [false, true, [$this->patient1, $this->patient3]]
            ]);

        $patientRepo
            ->method("getPatientByNameAndOwnerName")
            ->willReturnMap([
                [$this->patient2->getName()->getValue(), $this->owner2->getName()->getValue(), $this->patient2],
                [$this->patient3->getName()->getValue(), $this->owner3->getName()->getValue(), $this->patient3],
                [$this->patient1->getName()->getValue(), $this->owner1->getName()->getValue(), $this->patient1]
            ]);

        $patientRepo
            ->method("getPatientByNameAndOwnerId")
            ->willReturnMap([
                [$this->patient2->getName()->getValue(), $this->patient2->getOwner()->getId()->getValue(), $this->patient2],
                [$this->patient3->getName()->getValue(), $this->patient3->getOwner()->getId()->getValue(), $this->patient3]
            ]);

        $patientRepo
            ->method("getPatientById")
            ->willReturnMap([
                [$this->patient1->getId()->getValue(), $this->patient1],
                [$this->patient2->getId()->getValue(), $this->patient2],
                [$this->patient3->getId()->getValue(), $this->patient3],
            ]);

        return new PatientService($patientRepo);
    }

    private function createAddPatientDTO(int $ownerId, string $dogName, string $dogSpecies, string $dogBirthDate): AddPatientDTO
    {
        return new AddPatientDTO(
            $ownerId,
            $dogName,
            $dogSpecies,
            $dogBirthDate
        );
    }

    public function testGetAllPatients()
    {
        $patientService = $this->createPatientService();
        $getAllPatientsDTO = new GetAllPatientsDTO();
        $allPatients = $patientService->getAllPatients($getAllPatientsDTO);
        static::assertIsArray($allPatients);
        static::assertCount(3, $allPatients);
        static::assertContainsOnly(Patient::class, $allPatients);
    }

    public function testGetAllPatientsOnTreatment()
    {
        $patientService = $this->createPatientService();

        $patient1 = $this->patient1;
        $patient2 = $this->patient2;
        $patient3 = $this->patient3;

        foreach ([$patient1, $patient2, $patient3] as $patient) {
            $addPatientDTO = $this->createAddPatientDTO(
                $patient->getOwner()->getId()->getValue(),
                $patient->getName()->getValue(),
                $patient->getSpecies()->getValue(),
                $patient->getBirthDate()->getValue()->format("Y-m-d")
            );
            $patientService->addPatient($addPatientDTO);
        }

        $getPatientDTO = new GetPatientDTO();
        $getPatientDTO->ownerName = $patient2->getOwner()->getName()->getValue();
        $getPatientDTO->patientName = $patient2->getName()->getValue();
        $unreleasedPatient = $patientService->getPatient($getPatientDTO);
        static::assertNotNull($unreleasedPatient);

        $releasePatientDTO = new ReleasePatientDTO(
            $unreleasedPatient->getId()->getValue()
        );
        $patientService->releasePatient($releasePatientDTO);

        $getAllPatientsDTO = GetAllPatientsDTO::onTreatment();
        $allPatients = $patientService->getAllPatients($getAllPatientsDTO);
        static::assertIsArray($allPatients);
        static::assertCount(1, $allPatients);
        $allPetsNames = array_map(fn ($pet) => $pet->getName()->getValue(), $allPatients);
        static::assertContains($patient2->getName()->getValue(), $allPetsNames);
    }

    public function testGetAllReleasedPatients()
    {
        $patientService = $this->createPatientService();

        $patient1 = $this->patient1;
        $patient2 = $this->patient2;
        $patient3 = $this->patient3;

        foreach ([$patient1, $patient2, $patient3] as $patient) {
            $addPatientDTO = $this->createAddPatientDTO(
                $patient->getOwner()->getId()->getValue(),
                $patient->getName()->getValue(),
                $patient->getSpecies()->getValue(),
                $patient->getBirthDate()->getValue()->format("Y-m-d")
            );
            $patientService->addPatient($addPatientDTO);
        }

        $getPatientDTO = new GetPatientDTO();
        $getPatientDTO->ownerName = $patient2->getOwner()->getName()->getValue();
        $getPatientDTO->patientName = $patient2->getName()->getValue();
        $unreleasedPatient = $patientService->getPatient($getPatientDTO);
        static::assertNotNull($unreleasedPatient);

        $releasePatientDTO = new ReleasePatientDTO(
            $unreleasedPatient->getId()->getValue()
        );
        $patientService->releasePatient($releasePatientDTO);

        $getAllPatientsDTO = GetAllPatientsDTO::released();
        $allPatients = $patientService->getAllPatients($getAllPatientsDTO);
        static::assertIsArray($allPatients);
        static::assertCount(2, $allPatients);
        $allPetsNames = array_map(fn ($pet) => $pet->getName()->getValue(), $allPatients);
        static::assertContains($patient1->getName()->getValue(), $allPetsNames);
        static::assertContains($patient3->getName()->getValue(), $allPetsNames);
    }

    public function testAddAndGetPatient()
    {
        $patientService = $this->createPatientService();

        $owner = $this->owner3;
        $dogName = $this->patient3->getName()->getValue();
        $dogSpecies = $this->patient3->getSpecies()->getValue();
        $dogBirthDate = $this->patient3->getBirthDate()->getValue()->format("Y-m-d");

        $addPatientDTO = $this->createAddPatientDTO($owner->getId()->getValue(), $dogName, $dogSpecies, $dogBirthDate);
        $patientService->addPatient($addPatientDTO);

        $getPatientDTO = new GetPatientDTO();
        $getPatientDTO->patientName = $dogName;
        $getPatientDTO->ownerId = $owner->getId()->getValue();

        $patient = $patientService->getPatient($getPatientDTO);
        static::assertNotNull($patient);
        static::assertInstanceOf(Patient::class, $patient);
        static::assertEquals($dogName, $patient->getName()->getValue());
        static::assertEquals($dogSpecies, $patient->getSpecies()->getValue());
        static::assertEquals($dogBirthDate, $patient->getBirthDate()->getValue()->format("Y-m-d"));
    }

    public function testDeletePatient()
    {
        $patientService = $this->createPatientService();

        $owner = $this->owner1;
        $dogName = $this->patient1->getName()->getValue();
        $dogSpecies = $this->patient1->getSpecies()->getValue();
        $dogBirthDate = $this->patient1->getBirthDate()->getValue()->format("Y-m-d");

        $addPatientDTO = $this->createAddPatientDTO($owner->getId()->getValue(), $dogName, $dogSpecies, $dogBirthDate);
        $patientService->addPatient($addPatientDTO);

        $getAllPatientsDTO = new GetAllPatientsDTO();
        $allPatients = $patientService->getAllPatients($getAllPatientsDTO);
        $allPatientsNames = array_map(fn ($patient) => $patient->getName()->getValue(), $allPatients);
        static::assertContains($dogName, $allPatientsNames);

        $removePatientDto = new RemovePatientDTO();
        $removePatientDto->ownerName = $owner->getName()->getValue();
        $removePatientDto->patientName = $dogName;
        $patientService->removePatient($removePatientDto);

        $pegasusDeleted = true;
        $patientService = $this->createPatientService($pegasusDeleted);

        $getAllPatientsDTO = new GetAllPatientsDTO();
        $allPatients = $patientService->getAllPatients($getAllPatientsDTO);
        $allPatientsNames = array_map(fn ($patient) => $patient->getName()->getValue(), $allPatients);
        static::assertNotContains($dogName, $allPatientsNames);
    }

    public function testAddCardToPatient()
    {
        $patientService = $this->createPatientService();
        $addCardToPatientDTO = new AddCardToPatientDTO();
        $addCardToPatientDTO->patientId = $this->patient2->getId()->getValue();
        $patientService->addCardToPatient($addCardToPatientDTO);
        $getPatientDTO = new GetPatientDTO();
        $getPatientDTO->patientId = $this->patient2->getId()->getValue();
        $patient = $patientService->getPatient($getPatientDTO);
        static::assertInstanceOf(Patient::class, $patient);
        $card = $patient->getCurrentCard();
        static::assertNotNull($card);
        static::assertInstanceOf(Card::class, $card);
    }

    public function testRemoveCardFromPatient()
    {
        $patientService = $this->createPatientService();

        $getPatientDTO = new GetPatientDTO();
        $getPatientDTO->patientId = $this->patient1->getId()->getValue();
        $patient = $patientService->getPatient($getPatientDTO);
        static::assertInstanceOf(Patient::class, $patient);
        $card = $patient->getCurrentCard();
        static::assertNotNull($card);
        static::assertInstanceOf(Card::class, $card);

        $removeCardFromPatientDTO = new RemoveCardFromPatientDTO();
        $removeCardFromPatientDTO->patientId = $patient->getId()->getValue();
        $removeCardFromPatientDTO->cardId = $card->getId()->getValue();

        $patientService->removeCardFromPatient($removeCardFromPatientDTO);
        
        $patient = $patientService->getPatient($getPatientDTO);
        static::assertNull($patient->getCurrentCard());
    }

    public function testAddMedicalCaseToPatient()
    {
        $patientService = $this->createPatientService();

        $getPatientDTO = new GetPatientDTO();
        $getPatientDTO->patientId = $this->patient1->getId()->getValue();
        $patient = $patientService->getPatient($getPatientDTO);
        static::assertCount(1, $patient->getCurrentCard()->getCases());

        $caseDesc = "Haha what a chonk";
        $caseTreatment = "Make him go joggin twice a day";

        $addMedicalCaseToPatientDTO = new AddMedicalCaseToPatientDTO();
        $addMedicalCaseToPatientDTO->patientId = $this->patient1->getId()->getValue();
        $addMedicalCaseToPatientDTO->caseDescription = $caseDesc;
        $addMedicalCaseToPatientDTO->caseTreatment = $caseTreatment;
        $patientService->addMedicalCaseToPatient($addMedicalCaseToPatientDTO);

        $patient = $patientService->getPatient($getPatientDTO);
        static::assertCount(2, $patient->getCurrentCard()->getCases());
    }

    public function testRemoveMedicalCaseFromPatient()
    {
        $patientService = $this->createPatientService();

        $getPatientDTO = new GetPatientDTO();
        $getPatientDTO->patientId = $this->patient1->getId()->getValue();
        $patient = $patientService->getPatient($getPatientDTO);
        static::assertCount(1, $patient->getCurrentCard()->getCases());
        /** @var MedicalCase */
        $case = $patient->getCurrentCard()->getCases()[0];

        $removeMedicalCaseFromPatientDTO = new RemoveMedicalCaseFromPatientDTO();
        $removeMedicalCaseFromPatientDTO->patientId = $this->patient1->getId()->getValue();
        $removeMedicalCaseFromPatientDTO->medicalCaseId = $case->getId()->getValue();
        $patientService->removeMedicalCaseFromPatient($removeMedicalCaseFromPatientDTO);

        $patient = $patientService->getPatient($getPatientDTO);
        static::assertEmpty($patient->getCurrentCard()->getCases());
    }

    public function closePatientsMedicalCase()
    {
        $patientService = $this->createPatientService();

        $getPatientDTO = new GetPatientDTO();
        $getPatientDTO->patientId = $this->patient1->getId()->getValue();
        $patient = $patientService->getPatient($getPatientDTO);
        static::assertCount(1, $patient->getCurrentCard()->getCases());
        /** @var MedicalCase */
        $case = $patient->getCurrentCard()->getCases()[0];
        static::assertFalse($case->isEnded());

        $closeMedicalCaseDTO = new CloseMedicalCaseDTO();
        $closeMedicalCaseDTO->patientId = $this->patient1->getId()->getValue();
        $closeMedicalCaseDTO->medicalCaseId = $case->getId()->getValue();

        $patientService->closePatientsMedicalCase($closeMedicalCaseDTO);

        $patient = $patientService->getPatient($getPatientDTO);
        /** @var MedicalCase[] */
        $cases = $patient->getCurrentCard()->getCases();
        static::assertCount(1, $cases);
        /** @var MedicalCase */
        $case = $patient->getCurrentCard()->getCases()[0];
        static::assertTrue($case->isEnded());
    }
}
