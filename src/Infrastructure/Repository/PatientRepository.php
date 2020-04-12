<?php

namespace App\Infrastructure\Repository;

use App\Domain\Patient\Entity\Card;
use App\Domain\Patient\Entity\Owner;
use App\Domain\Patient\Entity\Patient;
use App\Infrastructure\IEntityAdapter;
use App\Domain\Patient\Entity\MedicalCase;
use App\Application\Patient\IPatientRepository;
use App\Infrastructure\Framework\Entity\MedicalCase as DBALMedicalCase;
use App\Infrastructure\Framework\Repository\CardRepository as DBALCardRepository;
use App\Infrastructure\Framework\Repository\OwnerRepository as DBALOwnerRepository;
use App\Infrastructure\Framework\Repository\PatientRepository as DBALPatientRepository;
use App\Infrastructure\Framework\Repository\MedicalCaseRepository as DBALMedicalCaseRepository;

/**
 * A patient aggregate root repository.
 * Implements methods one layer higher than DBAL repositories.
 * Naive style. If the owner's given, than it definitely exists. Just plain straight DBAL calls
 */
class PatientRepository implements IPatientRepository
{
    private IEntityAdapter $adapter;
    private DBALPatientRepository $dbalPatientRepository;
    private DBALOwnerRepository $dbalOwnerRepository;
    private DBALMedicalCaseRepository $dbalMedicalCaseRepository;
    private DBALCardRepository $dbalCardRepository;

    public function __construct(
        IEntityAdapter $adapter,
        DBALPatientRepository $dbalPatientRepository,
        DBALOwnerRepository $dbalOwnerRepository,
        DBALMedicalCaseRepository $dbalMedicalCaseRepository,
        DBALCardRepository $dbalCardRepository
    ) {
        $this->adapter = $adapter;
        $this->dbalPatientRepository = $dbalPatientRepository;
        $this->dbalOwnerRepository = $dbalOwnerRepository;
        $this->dbalMedicalCaseRepository = $dbalMedicalCaseRepository;
        $this->dbalCardRepository = $dbalCardRepository;
    }

    public function createPatient(Patient $patient): Patient
    {
        $dbalPatient = $this->adapter->fromDomainPatient($patient);
        $newDbalPatient = $this->dbalPatientRepository->create($dbalPatient);
        return $this->adapter->fromDBALPatient($newDbalPatient);
    }

    public function createOwner(Owner $owner): Owner
    {
        $dbalOwner = $this->adapter->fromDomainOwner($owner);
        $newDbalOwner = $this->dbalOwnerRepository->create($dbalOwner);
        return $this->adapter->fromDBALOwner($newDbalOwner);
    }

    public function addPatientToOwner(Patient $patient, Owner $owner): void
    {
        $patient->setOwner($owner);
        $dbalPatient = $this->adapter->fromDomainPatient($patient);
        $this->dbalPatientRepository->create($dbalPatient);
    }

    public function getPatientByNameAndOwnerName(string $patientName, string $ownerName): ?Patient
    {
        $dbalPatient = $this->dbalPatientRepository->getPatientByNameAndOwnerName($patientName, $ownerName);
        if (empty($dbalPatient)) return null;
        return $this->adapter->fromDBALPatient($dbalPatient);
    }

    public function getPatientByNameAndOwnerId(string $patientName, int $ownerId): ?Patient
    {
        $dbalPatient = $this->dbalPatientRepository->getPatientByNameAndOwnerId($patientName, $ownerId);
        if (empty($dbalPatient)) return null;
        return $this->adapter->fromDBALPatient($dbalPatient);
    }

    public function getPatientById(int $patientId): ?Patient
    {
        $dbalPatient = $this->dbalPatientRepository->find($patientId);
        if (empty($dbalPatient)) return null;
        return $this->adapter->fromDBALPatient($dbalPatient);
    }

    public function getOwnerById(int $ownerId): ?Owner
    {
        $dbalOwner = $this->dbalOwnerRepository->find($ownerId);
        if (empty($dbalOwner)) return null;
        return $this->adapter->fromDBALOwner($dbalOwner);
    }

    /**
     * @return Patient[]
     */
    public function getAllPatients(bool $onTreatment = true, bool $released = true): array
    {
        return array_map(
            fn ($dbalPatient) => $this->adapter->fromDBALPatient($dbalPatient),
            $this->dbalPatientRepository->getAll($onTreatment, $released)
        );
    }

    /**
     * @return Patient[]
     */
    public function getAllPatientsWithName(string $name): array
    {
        return array_map(
            fn ($dbalPatient) => $this->adapter->fromDBALPatient($dbalPatient),
            $this->dbalPatientRepository->getAllWithName($name)
        );
    }

    public function updatePatient(Patient $patient): Patient
    {
        $dbalPatient = $this->adapter->fromDomainPatient($patient);
        $this->dbalPatientRepository->update($dbalPatient);
        return $patient;
    }

    /**
     * Update given cases in the DB
     *
     * @param MedicalCase[] $cases
     * @return MedicalCase[]
     */
    public function updatePatientCases(array $cases): array
    {
        /** @var DBALMedicalCase[] */
        $dbalCases = array_map(
            fn ($case) => $this->adapter->fromDomainMedicalCase($case),
            $cases
        );
        $this->dbalMedicalCaseRepository->updateCases($dbalCases);
        return $cases;
    }

    public function removePatientByNameAndOwnerName(string $patientName, string $ownerName): void
    {
        $patient = $this->dbalPatientRepository->getPatientByNameAndOwnerName($patientName, $ownerName);
        if (empty($patient)) return;
        $this->dbalPatientRepository->remove($patient);
    }

    public function removePatientByNameAndOwnerId(string $patientName, int $ownerId): void
    {
        $patient = $this->dbalPatientRepository->getPatientByNameAndOwnerId($patientName, $ownerId);
        if (empty($patient)) return;
        $this->dbalPatientRepository->remove($patient);
    }

    public function removePatientById(int $patientId): void
    {
        $patient = $this->dbalPatientRepository->find($patientId);
        if (empty($patient)) return;
        $this->dbalPatientRepository->remove($patient);
    }

    public function addCardToPatient(Card $card): void
    {
        $dbalCard = $this->adapter->fromDomainCard($card);
        $this->dbalCardRepository->create($dbalCard);
    }

    public function updateOwner(Owner $owner): Owner
    {
        $dbalOwner = $this->adapter->fromDomainOwner($owner);
        $this->dbalOwnerRepository->update($dbalOwner);
        return $owner;
    }
}
