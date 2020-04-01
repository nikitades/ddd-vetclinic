<?php

namespace App\Application\Patient\DTO;

class GetPatientDTO
{
    public ?string $patientName;
    public ?int $patientId;
    public ?string $ownerName;
    public ?int $ownerId;
}