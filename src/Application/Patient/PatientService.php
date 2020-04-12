<?php

namespace App\Application\Patient;

use DateTime;
use App\Domain\Patient\Entity\Card;
use App\Domain\Patient\Entity\Owner;
use App\Domain\Patient\Entity\Patient;
use App\Domain\Patient\Entity\MedicalCase;
use App\Domain\Patient\ValueObject\CardId;
use App\Domain\Patient\ValueObject\OwnerName;
use App\Application\Patient\DTO\AddPatientDTO;
use App\Application\Patient\DTO\GetPatientDTO;
use App\Domain\Patient\ValueObject\OwnerEmail;
use App\Domain\Patient\ValueObject\OwnerPhone;
use App\Application\Patient\DTO\CreateOwnerDTO;
use App\Application\Patient\IPatientRepository;
use App\Domain\Patient\ValueObject\PatientName;
use App\Domain\Patient\ValueObject\OwnerAddress;
use App\Application\Patient\DTO\RemovePatientDTO;
use App\Application\Patient\DTO\UpdatePatientDTO;
use App\Domain\Patient\ValueObject\MedicalCaseId;
use App\Application\Patient\DTO\GetAllPatientsDTO;
use App\Application\Patient\DTO\ReleasePatientDTO;
use App\Domain\Patient\ValueObject\PatientSpecies;
use App\Application\Patient\DTO\AddCardToPatientDTO;
use App\Application\Patient\DTO\CloseMedicalCaseDTO;
use App\Domain\Patient\ValueObject\PatientBirthDate;
use App\Application\Patient\DTO\GetPatientsByNameDTO;
use App\Application\Patient\DTO\RequireNotificationDTO;
use App\Application\Patient\DTO\AttachPatientToOwnerDTO;
use App\Domain\Patient\ValueObject\MedicalCaseTreatment;
use App\Application\Patient\DTO\RemoveCardFromPatientDTO;
use App\Domain\Patient\ValueObject\MedicalCaseDescription;
use App\Application\Patient\DTO\AddMedicalCaseToPatientDTO;
use App\Application\Patient\Exception\OwnerNotFoundException;
use App\Application\Patient\Exception\PatientNotFoundException;
use App\Application\Patient\DTO\RemoveMedicalCaseFromPatientDTO;
use App\Application\Patient\Exception\NoActivePatientCardsFoundException;

class PatientService
{
    private IPatientRepository $patientRepo;

    public function __construct(IPatientRepository $patientRepo)
    {
        $this->patientRepo = $patientRepo;
    }

    public function addPatient(AddPatientDTO $addPatientDTO): Patient
    {
        $patient = new Patient(
            new PatientName($addPatientDTO->patientName),
            new PatientBirthDate(new DateTime($addPatientDTO->patientBirthDate)),
            new PatientSpecies($addPatientDTO->patientSpecies)
        );
        $createdPatient = $this->patientRepo->createPatient($patient);
        return $createdPatient;
    }

    public function getPatient(GetPatientDTO $getPatientDTO): ?Patient
    {
        if (!empty($getPatientDTO->patientName) && !empty($getPatientDTO->ownerName)) {
            return $this->patientRepo->getPatientByNameAndOwnerName($getPatientDTO->patientName, $getPatientDTO->ownerName);
        }
        if (!empty($getPatientDTO->patientName) && !empty($getPatientDTO->ownerId)) {
            return $this->patientRepo->getPatientByNameAndOwnerId($getPatientDTO->patientName, $getPatientDTO->ownerId);
        }
        if (!empty($getPatientDTO->patientId)) {
            return $this->patientRepo->getPatientById($getPatientDTO->patientId);
        }
        throw new \InvalidArgumentException("No valid query parameters given (owner's name, patient's name or patient's id)");
    }

    public function getOwner(int $ownerId): ?Owner
    {
        return $this->patientRepo->getOwnerById($ownerId);
    }

    /**
     * Gets all the patients meeting the criteria
     *
     * @param GetAllPatientsDTO $getAllPatientsDTO
     * @return Patient[]
     */
    public function getAllPatients(GetAllPatientsDTO $getAllPatientsDTO): array
    {
        return $this->patientRepo->getAllPatients($getAllPatientsDTO->onTreatment, $getAllPatientsDTO->released);
    }

    public function getPatientsByName(GetPatientsByNameDTO $getPatientsByNameDTO): array
    {
        return $this->patientRepo->getAllPatientsWithName($getPatientsByNameDTO->patientName);
    }

    public function createOwner(CreateOwnerDTO $createOwnerDTO): Owner
    {
        $owner = new Owner(
            new OwnerName($createOwnerDTO->name),
            new OwnerPhone($createOwnerDTO->phone),
            new OwnerAddress($createOwnerDTO->address),
            new OwnerEmail($createOwnerDTO->email)
        );
        return $this->patientRepo->createOwner($owner);
    }

    public function updatePatient(UpdatePatientDTO $updatePatientDTO): Patient
    {
        $patient = $this->fetchPatient(
            $updatePatientDTO->patientId,
            $updatePatientDTO->patientName,
            $updatePatientDTO->ownerId,
            $updatePatientDTO->ownerName
        );
        if (empty($patient)) {
            throw new PatientNotFoundException(
                (string) ($updatePatientDTO->patientId
                    ?? $updatePatientDTO->patientName
                    ?? $updatePatientDTO->ownerId
                    ?? $updatePatientDTO->ownerName)
            );
        }
        $patient->setName(new PatientName($updatePatientDTO->patientName));
        $patient->setSpecies(new PatientSpecies($updatePatientDTO->patientSpecies));
        $patient->setBirthDate(new PatientBirthDate($updatePatientDTO->patientBirthDate));
        $this->patientRepo->updatePatient($patient);
        return $patient;
    }

    public function releasePatient(ReleasePatientDTO $releasePatientDTO): Patient
    {
        $patient = $this->fetchPatient($releasePatientDTO->patientId, null, null, null);
        $patient->release();
        $cases = array_merge(...(array_map(
            fn ($card) => $card->getCases(),
            $patient->getCards()
        )));
        $this->patientRepo->updatePatientCases($cases);
        return $patient;
    }

    public function removePatient(RemovePatientDTO $removePatientDTO): void
    {
        if (!empty($removePatientDTO->patientName) && !empty($removePatientDTO->ownerName)) {
            $this->patientRepo->removePatientByNameAndOwnerName($removePatientDTO->patientName, $removePatientDTO->ownerName);
            return;
        }
        if (!empty($removePatientDTO->patientName) && !empty($removePatientDTO->ownerId)) {
            $this->patientRepo->removePatientByNameAndOwnerId($removePatientDTO->patientName, $removePatientDTO->ownerId);
            return;
        }
        if (!empty($removePatientDTO->patientId)) {
            $this->patientRepo->removePatientById($removePatientDTO->patientId);
            return;
        }
        throw new \InvalidArgumentException("No valid query parameters given (owner's name, patient's name or patient's id)");
    }

    public function addCardToPatient(AddCardToPatientDTO $addCardToPatientDTO): Patient
    {
        $patient = $this->fetchPatient($addCardToPatientDTO->patientId, null, null, null);
        $card = new Card();
        $patient->addCard($card);
        $this->patientRepo->addCardToPatient($card);
        return $patient;
    }

    public function removeCardFromPatient(RemoveCardFromPatientDTO $removeCardFromPatientDTO): Patient
    {
        $patient = $this->fetchPatient($removeCardFromPatientDTO->patientId, null, null, null);
        $patient->removeCard(new CardId($removeCardFromPatientDTO->cardId));
        return $this->patientRepo->updatePatient($patient);
    }

    public function addMedicalCaseToPatient(AddMedicalCaseToPatientDTO $addMedicalCaseToPatientDTO): Patient
    {
        $patient = $this->fetchPatient($addMedicalCaseToPatientDTO->patientId, null, null, null);
        $case = new MedicalCase();
        $case->setDescription(new MedicalCaseDescription($addMedicalCaseToPatientDTO->caseDescription));
        $case->setTreatment(new MedicalCaseTreatment($addMedicalCaseToPatientDTO->caseTreatment));
        $card = $patient->getCurrentCard();
        if (empty($card)) {
            throw new NoActivePatientCardsFoundException();
        }
        $card->addCase($case);
        return $this->patientRepo->updatePatient($patient);
    }

    public function removeMedicalCaseFromPatient(RemoveMedicalCaseFromPatientDTO $removeMedicalCaseFromPatientDTO): Patient
    {
        $patient = $this->fetchPatient($removeMedicalCaseFromPatientDTO->patientId, null, null, null);
        $card = $patient->getCurrentCard();
        if (empty($card)) {
            throw new NoActivePatientCardsFoundException();
        }
        $card->removeCase(new MedicalCaseId($removeMedicalCaseFromPatientDTO->medicalCaseId));
        return $this->patientRepo->updatePatient($patient);
    }

    public function closePatientsMedicalCase(CloseMedicalCaseDTO $closeMedicalCaseDTO): Patient
    {
        $patient = $this->fetchPatient($closeMedicalCaseDTO->patientId, null, null, null);
        $card = $patient->getCurrentCard();
        if (empty($card)) {
            throw new NoActivePatientCardsFoundException();
        }
        /**
         * @var MedicalCase[]
         */
        $cases = $card->getCases();
        foreach ($cases as $case) {
            if ($case->getId()->getValue() == $closeMedicalCaseDTO->medicalCaseId) {
                $case->end();
            }
        }
        return $this->patientRepo->updatePatient($patient);
    }

    public function requireNotification(RequireNotificationDTO $requireNotificationDTO): Owner
    {
        $patient = $this->fetchPatient(
            $requireNotificationDTO->patientId ?? null,
            $requireNotificationDTO->patientName ?? null,
            $requireNotificationDTO->ownerId ?? null,
            $requireNotificationDTO->ownerName ?? null
        );
        $owner = $patient->getOwner();
        if (empty($owner)) {
            throw new OwnerNotFoundException("of patient " . $patient->getId()->getValue());
        }
        $owner->enableNotification();
        $this->patientRepo->updateOwner($owner);
        return $owner;
    }

    /**
     * Attaches a patient to owner
     *
     * @param AttachPatientToOwnerDTO $attachPatientToOwnerDTO
     * @return array<mixed>
     */
    public function attachPatientToOwner(AttachPatientToOwnerDTO $attachPatientToOwnerDTO): array
    {
        $patient = $this->fetchPatient($attachPatientToOwnerDTO->patientId, null, null, null);
        $owner = $this->getOwner($attachPatientToOwnerDTO->ownerId);
        if (empty($owner)) {
            throw new OwnerNotFoundException((string) $attachPatientToOwnerDTO->ownerId);
        }

        $patient->setOwner($owner);
        $this->patientRepo->updatePatient($patient);

        return [$patient, $owner];
    }

    protected function fetchPatient(?int $patientId, ?string $patientName, ?int $ownerId, ?string $ownerName): Patient
    {
        $getPatientDTO = new GetPatientDTO();
        $getPatientDTO->patientId = $patientId;
        $getPatientDTO->patientName = $patientName;
        $getPatientDTO->ownerId = $ownerId;
        $getPatientDTO->ownerName = $ownerName;
        $patient = $this->getPatient($getPatientDTO);
        if (empty($patient)) {
            throw new PatientNotFoundException(
                (string) ($getPatientDTO->patientId
                    ?? $getPatientDTO->patientName
                    ?? $getPatientDTO->ownerId
                    ?? $getPatientDTO->ownerName)
            );
        }
        return $patient;
    }
}
