<?php

namespace App\Application\Patient\DTO;

final class GetPatientsByNameDTO
{
    public string $patientName;

    public function __construct(string $patientName)
    {
        $this->patientName = $patientName;
    }
}