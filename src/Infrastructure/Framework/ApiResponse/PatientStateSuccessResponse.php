<?php

namespace App\Infrastructure\Framework\ApiResponse;

use DateTime;
use App\Domain\Patient\Entity\Patient;

final class PatientStateSuccessResponse extends AbstractApiResponse
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

    /**
     * Creates the json output
     *
     * @return mixed
     */
    public function jsonSerialize()
    {
        return [
            'patient' => $this->json($this->patient),
            'released' => $this->patient->isCured()
        ];
    }
}
