<?php

namespace App\Application\Patient\DTO;

class AddMedicalCaseToPatientDTO
{
    public int $patientId;
    public string $caseDescription;
    public string $caseTreatment;
}