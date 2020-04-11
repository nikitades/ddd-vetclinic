<?php

namespace App\Application\Patient;

use App\Domain\Patient\Entity\Card;
use App\Domain\Patient\Entity\Owner;
use App\Domain\Patient\Entity\Patient;
use App\Domain\Patient\Entity\MedicalCase;

interface IPatientRepository
{
    public function addPatientToOwner(Patient $patient, Owner $owner): void;
    public function addCardToPatient(Card $card): void;

    public function getPatientByNameAndOwnerName(string $patientName, string $ownerName): ?Patient;
    public function getPatientByNameAndOwnerId(string $patientName, int $ownerId): ?Patient;
    public function getPatientById(int $patientId): ?Patient;
    public function getOwnerById(int $ownerId): ?Owner;

    /**
     * @return Patient[]
     */
    public function getAllPatients(bool $onTreatment = true, bool $released = true): array;

    public function updatePatient(Patient $patient): Patient;
    public function updateOwner(Owner $owner): Owner;
    
    // /**
    //  * @param Card[] $cards
    //  * @return Card[]
    //  */
    // public function updatePatientCards(array $cards): array;

    /**
     * @param MedicalCase[] $cases
     * @return MedicalCase[]
     */
    public function updatePatientCases(array $cases): array;

    public function removePatientByNameAndOwnerName(string $patientName, string $ownerName): void;
    public function removePatientByNameAndOwnerId(string $patientName, int $ownerId): void;
    public function removePatientById(int $patientId): void;
}
