<?php

namespace App\Test\Application\Patient;

use DateTime;
use InvalidArgumentException;
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
use App\Domain\Patient\ValueObject\OwnerEmail;
use App\Domain\Patient\ValueObject\OwnerPhone;
use App\Application\Patient\DTO\CreateOwnerDTO;
use App\Application\Patient\IPatientRepository;
use App\Domain\Patient\ValueObject\PatientName;
use App\Domain\Patient\ValueObject\OwnerAddress;
use App\Application\Patient\DTO\CreatePatientDTO;
use App\Application\Patient\DTO\RemovePatientDTO;
use App\Domain\Patient\ValueObject\MedicalCaseId;
use App\Application\Patient\DTO\GetAllPatientsDTO;
use App\Application\Patient\DTO\ReleasePatientDTO;
use App\Domain\Patient\ValueObject\PatientSpecies;
use App\Application\Patient\DTO\AddCardToPatientDTO;
use App\Application\Patient\DTO\CloseMedicalCaseDTO;
use App\Domain\Patient\ValueObject\PatientBirthDate;
use App\Application\Patient\DTO\RequireNotificationDTO;
use App\Application\Patient\DTO\AttachPatientToOwnerDTO;
use App\Domain\Patient\ValueObject\MedicalCaseTreatment;
use App\Application\Patient\DTO\RemoveCardFromPatientDTO;
use App\Domain\Patient\ValueObject\MedicalCaseDescription;
use App\Application\Patient\DTO\AddMedicalCaseToPatientDTO;
use App\Application\Patient\DTO\RemoveMedicalCaseFromPatientDTO;

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
            new OwnerAddress("5th Av."),
            new OwnerEmail("egor@letov.su")
        );
        $this->owner1->setId(new OwnerId(403));

        $this->owner2 = new Owner(
            new OwnerName("Keanu"),
            new OwnerPhone("+73334442424"),
            new OwnerAddress("Matrix"),
            new OwnerEmail("neo@matrix.com")
        );
        $this->owner2->setId(new OwnerId(38));

        $this->owner3 = new Owner(
            new OwnerName("Harry"),
            new OwnerPhone("+78889293030"),
            new OwnerAddress("Hogwarts"),
            new OwnerEmail("potter@griffindor.hogwarts.wiz")
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
        $case = new MedicalCase();
        $case->setId(new MedicalCaseId(4392));
        $case->setDescription(new MedicalCaseDescription("hoho"));
        $case->setTreatment(new MedicalCaseTreatment("huhu"));
        $card1->addCase($case);
        $this->patient1->addCard($card1);

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

    private function createPatientService(bool $pegasusDeleted = false): PatientService
    {
        if (
            is_null($this->patient1->getOwner())
            || is_null($this->patient2->getOwner())
            || is_null($this->patient3->getOwner())
        ) throw new InvalidArgumentException("A patient is expected to have the owner");

        /** @var mixed */
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

        $patientRepo
            ->method("createPatient")
            ->willReturn($this->patient1);

        $patientRepo
            ->method("createOwner")
            ->willReturn($this->patient2->getOwner());

        return new PatientService($patientRepo);
    }

    private function createAddPatientDTO(string $dogName, string $dogSpecies, string $dogBirthDate): AddPatientDTO
    {
        return new AddPatientDTO(
            $dogName,
            $dogSpecies,
            $dogBirthDate
        );
    }

    public function testGetAllPatients(): void
    {
        $patientService = $this->createPatientService();
        $getAllPatientsDTO = new GetAllPatientsDTO();
        $allPatients = $patientService->getAllPatients($getAllPatientsDTO);
        static::assertIsArray($allPatients);
        static::assertCount(3, $allPatients);
        static::assertContainsOnly(Patient::class, $allPatients);
    }

    public function testGetAllPatientsOnTreatment(): void
    {
        $patientService = $this->createPatientService();

        $patient1 = $this->patient1;
        $patient2 = $this->patient2;
        $patient3 = $this->patient3;

        if (
            is_null($patient1->getOwner())
            || is_null($patient2->getOwner())
            || is_null($patient3->getOwner())
        ) throw new InvalidArgumentException("A patient is expected to have the owner");

        foreach ([$patient1, $patient2, $patient3] as $patient) {
            if (is_null($patient->getOwner())) throw new InvalidArgumentException("A patient is expected to have the owner here");
            $addPatientDTO = $this->createAddPatientDTO(
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
        if (empty($unreleasedPatient)) return;

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

    public function testGetAllReleasedPatients(): void
    {
        $patientService = $this->createPatientService();

        $patient1 = $this->patient1;
        $patient2 = $this->patient2;
        if (is_null($patient2->getOwner())) throw new InvalidArgumentException("A patient is expected to have the owner here");
        $patient3 = $this->patient3;

        foreach ([$patient1, $patient2, $patient3] as $patient) {
            if (is_null($patient->getOwner())) throw new InvalidArgumentException("A patient is expected to have the owner here");
            $addPatientDTO = $this->createAddPatientDTO(
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
        if (empty($unreleasedPatient)) return;

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

    public function testAddAndGetPatient(): void
    {
        $patientService = $this->createPatientService();

        $owner = $this->owner3;
        $dogName = $this->patient3->getName()->getValue();
        $dogSpecies = $this->patient3->getSpecies()->getValue();
        $dogBirthDate = $this->patient3->getBirthDate()->getValue()->format("Y-m-d");

        $addPatientDTO = $this->createAddPatientDTO($dogName, $dogSpecies, $dogBirthDate);
        $patientService->addPatient($addPatientDTO);

        $getPatientDTO = new GetPatientDTO();
        $getPatientDTO->patientName = $dogName;
        $getPatientDTO->ownerId = $owner->getId()->getValue();

        $patient = $patientService->getPatient($getPatientDTO);
        static::assertNotNull($patient);
        if (empty($patient)) return;
        static::assertInstanceOf(Patient::class, $patient);
        static::assertEquals($dogName, $patient->getName()->getValue());
        static::assertEquals($dogSpecies, $patient->getSpecies()->getValue());
        static::assertEquals($dogBirthDate, $patient->getBirthDate()->getValue()->format("Y-m-d"));
    }

    public function testDeletePatient(): void
    {
        $patientService = $this->createPatientService();

        $owner = $this->owner1;
        $dogName = $this->patient1->getName()->getValue();
        $dogSpecies = $this->patient1->getSpecies()->getValue();
        $dogBirthDate = $this->patient1->getBirthDate()->getValue()->format("Y-m-d");

        $addPatientDTO = $this->createAddPatientDTO($dogName, $dogSpecies, $dogBirthDate);
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

    public function testAddCardToPatient(): void
    {
        $patientService = $this->createPatientService();
        $addCardToPatientDTO = new AddCardToPatientDTO();
        $addCardToPatientDTO->patientId = $this->patient2->getId()->getValue();
        $patientService->addCardToPatient($addCardToPatientDTO);
        $getPatientDTO = new GetPatientDTO();
        $getPatientDTO->patientId = $this->patient2->getId()->getValue();
        $patient = $patientService->getPatient($getPatientDTO);
        static::assertNotNull($patient);
        static::assertInstanceOf(Patient::class, $patient);
        if (empty($patient)) return;
        $card = $patient->getCurrentCard();
        static::assertNotNull($card);
        static::assertInstanceOf(Card::class, $card);
    }

    public function testRemoveCardFromPatient(): void
    {
        $patientService = $this->createPatientService();

        $getPatientDTO = new GetPatientDTO();
        $getPatientDTO->patientId = $this->patient1->getId()->getValue();
        $patient = $patientService->getPatient($getPatientDTO);
        static::assertInstanceOf(Patient::class, $patient);
        static::assertNotNull($patient);
        if (empty($patient)) return;
        $card = $patient->getCurrentCard();
        static::assertNotNull($card);
        static::assertInstanceOf(Card::class, $card);
        if (empty($card)) return;

        $removeCardFromPatientDTO = new RemoveCardFromPatientDTO();
        $removeCardFromPatientDTO->patientId = $patient->getId()->getValue();
        $removeCardFromPatientDTO->cardId = $card->getId()->getValue();

        $patientService->removeCardFromPatient($removeCardFromPatientDTO);

        $patient = $patientService->getPatient($getPatientDTO);
        static::assertNotNull($patient);
        static::assertInstanceOf(Patient::class, $patient);
        if (empty($patient)) return;
        static::assertNull($patient->getCurrentCard());
    }

    public function testAddMedicalCaseToPatient(): void
    {
        $patientService = $this->createPatientService();

        $getPatientDTO = new GetPatientDTO();
        $getPatientDTO->patientId = $this->patient1->getId()->getValue();
        $patient = $patientService->getPatient($getPatientDTO);
        static::assertNotNull($patient);
        static::assertInstanceOf(Patient::class, $patient);
        if (empty($patient)) return;
        $card = $patient->getCurrentCard();
        static::assertNotNull($card);
        static::assertInstanceOf(Card::class, $card);
        if (empty($card)) return;
        static::assertCount(1, $card->getCases());

        $caseDesc = "Haha what a chonk";
        $caseTreatment = "Make him go joggin twice a day";

        $addMedicalCaseToPatientDTO = new AddMedicalCaseToPatientDTO();
        $addMedicalCaseToPatientDTO->patientId = $this->patient1->getId()->getValue();
        $addMedicalCaseToPatientDTO->caseDescription = $caseDesc;
        $addMedicalCaseToPatientDTO->caseTreatment = $caseTreatment;
        $patientService->addMedicalCaseToPatient($addMedicalCaseToPatientDTO);

        $patient = $patientService->getPatient($getPatientDTO);
        static::assertNotNull($patient);
        static::assertInstanceOf(Patient::class, $patient);
        if (empty($patient)) return;
        $card = $patient->getCurrentCard();
        static::assertNotNull($card);
        static::assertInstanceOf(Card::class, $card);
        if (empty($card)) return;
        static::assertCount(2, $card->getCases());
    }

    public function testRemoveMedicalCaseFromPatient(): void
    {
        $patientService = $this->createPatientService();

        $getPatientDTO = new GetPatientDTO();
        $getPatientDTO->patientId = $this->patient1->getId()->getValue();
        $patient = $patientService->getPatient($getPatientDTO);
        static::assertNotNull($patient);
        static::assertInstanceOf(Patient::class, $patient);
        if (empty($patient)) return;
        $card = $patient->getCurrentCard();
        static::assertNotNull($card);
        static::assertInstanceOf(Card::class, $card);
        if (empty($card)) return;
        static::assertCount(1, $card->getCases());
        /** @var MedicalCase */
        $case = $card->getCases()[0];

        $removeMedicalCaseFromPatientDTO = new RemoveMedicalCaseFromPatientDTO();
        $removeMedicalCaseFromPatientDTO->patientId = $this->patient1->getId()->getValue();
        $removeMedicalCaseFromPatientDTO->medicalCaseId = $case->getId()->getValue();
        $patientService->removeMedicalCaseFromPatient($removeMedicalCaseFromPatientDTO);

        $patient = $patientService->getPatient($getPatientDTO);
        static::assertNotNull($patient);
        static::assertInstanceOf(Patient::class, $patient);
        if (empty($patient)) return;
        $card = $patient->getCurrentCard();
        static::assertNotNull($card);
        static::assertInstanceOf(Card::class, $card);
        if (empty($card)) return;
        static::assertEmpty($card->getCases());
    }

    public function testClosePatientsMedicalCase(): void
    {
        $patientService = $this->createPatientService();

        $getPatientDTO = new GetPatientDTO();
        $getPatientDTO->patientId = $this->patient1->getId()->getValue();
        $patient = $patientService->getPatient($getPatientDTO);
        static::assertNotNull($patient);
        static::assertInstanceOf(Patient::class, $patient);
        if (empty($patient)) return;
        $card = $patient->getCurrentCard();
        static::assertNotNull($card);
        static::assertInstanceOf(Card::class, $card);
        if (empty($card)) return;
        static::assertCount(1, $card->getCases());
        /** @var MedicalCase */
        $case = $card->getCases()[0];
        $ended = $case->isEnded();
        static::assertFalse($ended->getValue());

        $closeMedicalCaseDTO = new CloseMedicalCaseDTO();
        $closeMedicalCaseDTO->patientId = $this->patient1->getId()->getValue();
        $closeMedicalCaseDTO->medicalCaseId = $case->getId()->getValue();

        $patientService->closePatientsMedicalCase($closeMedicalCaseDTO);

        $patient = $patientService->getPatient($getPatientDTO);
        static::assertNotNull($patient);
        static::assertInstanceOf(Patient::class, $patient);
        if (empty($patient)) return;
        $card = $patient->getCurrentCard();
        static::assertNotNull($card);
        static::assertInstanceOf(Card::class, $card);
        if (empty($card)) return;
        /** @var MedicalCase[] */
        $cases = $card->getCases();
        static::assertCount(1, $cases);
        /** @var MedicalCase */
        $case = $card->getCases()[0];
        $ended = $case->isEnded();
        if (empty($ended)) return;
        static::assertTrue($ended->getValue());
    }

    public function testRequireNotification(): void
    {
        $patientService = $this->createPatientService();

        $requireNotificationDTO = new RequireNotificationDTO();
        $owner = $this->patient1->getOwner();
        if (empty($owner)) return;
        static::assertFalse($owner->getNotificationRequired()->getValue());
        $requireNotificationDTO->ownerName = $owner->getName()->getValue();
        $requireNotificationDTO->patientName = $this->patient1->getName()->getValue();

        $patientService->requireNotification($requireNotificationDTO);

        static::assertTrue($owner->getNotificationRequired()->getValue());
    }

    public function testCreatePatient(): void
    {
        $patientService = $this->createPatientService();

        $patient = $this->patient1;
        $addPatientDTO = new AddPatientDTO(
            $patient->getName()->getValue(),
            $patient->getSpecies()->getValue(),
            $patient->getBirthDate()->getValue()->format("Y-m-d H:i:s")
        );

        $patient = $patientService->addPatient($addPatientDTO);
        static::assertNotNull($patient);
        static::assertInstanceOf(Patient::class, $patient);

        $getPatientDTO = new GetPatientDTO();
        $getPatientDTO->patientId = $patient->getId()->getValue();
        $newPatient = $patientService->getPatient($getPatientDTO);
        static::assertNotNull($newPatient);
        static::assertInstanceOf(Patient::class, $newPatient);
        if (empty($newPatient)) return;

        static::assertEquals($patient->getName()->getValue(), $newPatient->getName()->getValue());
    }

    public function testCreateOwner(): void
    {
        $patientService = $this->createPatientService();

        $owner = $this->patient2->getOwner();
        static::assertNotNull($owner);
        static::assertInstanceOf(Owner::class, $owner);
        if (empty($owner)) return;

        $createOwnerDTO = new CreateOwnerDTO();
        $createOwnerDTO->name = $owner->getName()->getValue();
        $createOwnerDTO->email = $owner->getEmail()->getValue();
        $createOwnerDTO->address = $owner->getAddress()->getValue();
        $createOwnerDTO->phone = $owner->getPhone()->getValue();

        $createdOwner = $patientService->createOwner($createOwnerDTO);
        static::assertNotNull($createdOwner);
        static::assertInstanceOf(Owner::class, $createdOwner);
        static::assertTrue($owner->getName()->equals($createdOwner->getName()));
    }

    public function testAttachPatientToOwner(): void
    {
        $patientService = $this->createPatientService();

        $patient = $this->patient3;
        $owner = $patient->getOwner();
        if (empty($owner)) return;

        $attachPatientToOwnerDTO = new AttachPatientToOwnerDTO();
        $attachPatientToOwnerDTO->patientId = $patient->getId()->getValue();
        $attachPatientToOwnerDTO->ownerId = $owner->getId()->getValue();

        /** array<mixed> */
        $result = $patientService->attachPatientToOwner($attachPatientToOwnerDTO);
        /** @var Patient */
        $attachedPatient = $result[0];
        /** @var Owner */
        $attachedOwner = $result[1];

        static::assertNotNull($attachedPatient);
        static::assertInstanceOf(Patient::class, $attachedPatient);
        static::assertTrue($attachedPatient->getId()->equals($patient->getId()));

        static::assertNotNull($attachedOwner);
        static::assertInstanceOf(Owner::class, $attachedOwner);
        static::assertTrue($attachedOwner->getId()->equals($owner->getId()));
    }

    /**
     * @expectedException \App\Application\Patient\Exception\OwnerNotFoundException
     */
    public function testAttachPatientToOwnerWithNotExistingOwner(): void
    {
        $patientService = $this->createPatientService();

        $patient = $this->patient3;

        $attachPatientToOwnerDTO = new AttachPatientToOwnerDTO();
        $attachPatientToOwnerDTO->patientId = $patient->getId()->getValue();
        $attachPatientToOwnerDTO->ownerId = 234;

        $patientService->attachPatientToOwner($attachPatientToOwnerDTO);
    }

    /**
     * @expectedException \App\Application\Patient\Exception\PatientNotFoundException
     */
    public function testAttachPatientToOwnerWithNotExistingPatient(): void
    {
        $patientService = $this->createPatientService();

        $patient = $this->patient3;
        $owner = $patient->getOwner();
        if (empty($owner)) return;

        $attachPatientToOwnerDTO = new AttachPatientToOwnerDTO();
        $attachPatientToOwnerDTO->patientId = 564;
        $attachPatientToOwnerDTO->ownerId = $owner->getId()->getValue();

        $patientService->attachPatientToOwner($attachPatientToOwnerDTO);
    }
}
