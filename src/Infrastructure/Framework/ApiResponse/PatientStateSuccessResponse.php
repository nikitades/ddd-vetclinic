<?php

namespace App\Infrastructure\Framework\ApiResponse;

use DateTime;
use App\Domain\Patient\Entity\Patient;

final class PatientStateSuccessResponse extends AbstractApiResponse
{
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
            'patient' => [
                'name' => $this->patient->getName()->getValue(),
                'age' => (new DateTime())->diff($this->patient->getBirthDate()->getValue())->format("%y"),
                'species' => $this->patient->getSpecies()->getValue(),
            ],
            'released' => $this->patient->isCured()
        ];
    }
}
