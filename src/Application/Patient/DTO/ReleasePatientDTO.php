<?php

namespace App\Application\Patient\DTO;

class ReleasePatientDTO
{
    public int $patientId;

    public function __construct(int $patientId)
    {
        $this->patientId = $patientId;
    }
}