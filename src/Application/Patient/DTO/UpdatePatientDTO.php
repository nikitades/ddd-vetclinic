<?php

namespace App\Application\Patient\DTO;

use DateTime;

class UpdatePatientDTO
{
    public string $ownerName;
    
    public int $patientId;
    public string $patientName;
    public string $patientSpecies;
    public DateTime $patientBirthDate;
    public int $ownerId;
}