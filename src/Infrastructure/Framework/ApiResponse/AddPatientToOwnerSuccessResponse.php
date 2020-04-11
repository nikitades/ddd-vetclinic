<?php

namespace App\Infrastructure\Framework\ApiResponse;

use App\Domain\Patient\Entity\Owner;
use App\Domain\Patient\Entity\Patient;

class AddPatientToOwnerSuccessResponse extends AbstractApiResponse
{
    use JsonConverting;

    private Patient $patient;
    private Owner $owner;

    public function __construct(Patient $patient, Owner $owner)
    {
        $this->patient = $patient;
        $this->owner = $owner;
    }

    public function getStatusCode(): int
    {
        return 200;
    }

    /** @return mixed */
    public function jsonSerialize()
    {
        return [
            'patient' => $this->json($this->patient),
            'owner' => $this->json($this->owner)
        ];
    }
}
