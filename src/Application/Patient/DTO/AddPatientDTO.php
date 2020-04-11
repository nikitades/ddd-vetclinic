<?php

namespace App\Application\Patient\DTO;

class AddPatientDTO
{
    public int $ownerId;
    public string $patientName;
    public string $patientSpecies;
    public string $patientBirthDate;

    public function __construct(string $patientName, string $patientSpecies, string $patientBirthDate)
    {
        $this->patientName = $patientName;
        $this->patientSpecies = $patientSpecies;
        $this->patientBirthDate = $patientBirthDate;
    }
}
