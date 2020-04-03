<?php

namespace App\Infrastructure\Patient\Repository;

use App\Domain\Patient\Entity\Owner;
use App\Domain\Patient\Entity\Patient;
use App\Application\Patient\IPatientRepository;
use App\Infrastructure\IEntityAdapter;

//TODO: test this? or not?
class PatientRepository implements IPatientRepository
{
    private IEntityAdapter $adapter;

    public function __construct(IEntityAdapter $adapter)
    {
        $this->adapter = $adapter;
    }

    public function addPatientToOwner(Patient $patient, Owner $owner)
    {
        //TOOD: заимплементить метод на основе фреймворковских patientRepo и OwnerRepo
    }

    public function getPatientByNameAndOwnerName(string $patientName, string $ownerName): ?Patient
    {
    }

    public function getPatientByNameAndOwnerId(string $patientName, int $ownerId): ?Patient
    {
    }

    public function getPatientById(int $patientId): ?Patient
    {
    }

    public function getOwnerById(int $ownerId): ?Owner
    {
    }

    /**
     * @return Patient[]
     */
    public function getAllPatients($onTreatment = true, $released = true): array
    {
    }

    //TODO: тут вся работа по сути. нужно все сущности, находящиеся у пациента, итеративно обновить
    public function updatePatient(Patient $patient): Patient
    {
    }

    public function removePatientByNameAndOwnerName(string $patientName, string $ownerName): void
    {
    }

    public function removePatientByNameAndOwnerId(string $patientName, int $ownerId): void
    {
    }

    public function removePatientById(int $patientId): void
    {
    }
}
