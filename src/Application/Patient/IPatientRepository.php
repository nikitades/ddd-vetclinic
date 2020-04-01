<?php

namespace App\Application\Patient;

use App\Domain\Patient\Entity\Owner;
use App\Domain\Patient\Entity\Patient;

interface IPatientRepository
{
    public function addPatientToOwner(Patient $patient, Owner $owner);

    public function getPatientByNameAndOwnerName(string $patientName, string $ownerName): ?Patient;
    public function getPatientByNameAndOwnerId(string $patientName, int $ownerId): ?Patient;
    public function getPatientById(int $patientId): ?Patient;
    public function getOwnerById(int $ownerId): ?Owner;

    /**
     * @return Patient[]
     */
    public function getAllPatients($onTreatment = true, $released = true): array;

    public function updatePatient(Patient $patient): Patient;

    public function removePatientByNameAndOwnerName(string $patientName, string $ownerName): void;
    public function removePatientByNameAndOwnerId(string $patientName, int $ownerId): void;
    public function removePatientById(int $patientId): void;
}
