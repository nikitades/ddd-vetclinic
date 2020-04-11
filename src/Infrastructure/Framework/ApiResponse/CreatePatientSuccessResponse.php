<?php

namespace App\Infrastructure\Framework\ApiResponse;

use App\Domain\Patient\Entity\Patient;

class CreatePatientSuccessResponse extends AbstractApiResponse
{
    use JsonConverting;

    private Patient $patient;

    public function __construct(Patient $patient)
    {
        $this->patient = $patient;
    }

    public function getStatusCode(): int
    {
        return 200;
    }

    /** @return mixed */
    public function jsonSerialize()
    {
        return [
            'patient' => $this->json($this->patient)
        ];
    }
}
