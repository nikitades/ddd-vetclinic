<?php

namespace App\Application\Patient\DTO;

class RemovePatientDTO
{
    public string $patientName;
    public int $patientId;
    public string $ownerName;
    public int $ownerId;
}