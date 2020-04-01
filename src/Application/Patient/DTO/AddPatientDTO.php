<?php

namespace App\Application\Patient\DTO;

class AddPatientDTO
{
    public int $ownerId;
    public string $patientName;
    public string $patientSpecies;
    public string $patientBirthDate;

    public function __construct(int $ownerId, string $patientName, string $patientSpecies, string $patientBirthDate)
    {
        $this->ownerId = $ownerId;
        $this->patientName = $patientName;
        $this->patientSpecies = $patientSpecies;
        $this->patientBirthDate = $patientBirthDate;
    }
}
