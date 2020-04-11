<?php

namespace App\Application\Patient\DTO;

use App\Domain\Patient\Entity\Patient;

class RequireNotificationDTO
{
    public ?int $ownerId;
    public ?int $patientId;
    public ?string $ownerName;
    public ?string $patientName;
}
