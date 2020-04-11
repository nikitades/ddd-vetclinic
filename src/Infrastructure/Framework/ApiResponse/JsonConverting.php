<?php

namespace App\Infrastructure\Framework\ApiResponse;

use DateTime;
use App\Domain\Patient\Entity\Patient;

trait JsonConverting
{
    /**
     * @param mixed $input 
     * @return mixed 
     * */
    protected function json($input)
    {
        switch (get_class($input)) {
            case Patient::class:
                return $this->getPatient($input);
        }
    }

    /** @return array<mixed> */
    protected function getPatient(Patient $patient)
    {
        return [
            'id' => $patient->getId()->getValue(),
            'name' => $patient->getName()->getValue(),
            'species' => $patient->getSpecies()->getValue(),
            'age' => $patient->getBirthDate()->getValue()->diff((new DateTime()))->format("%y")
        ];
    }
}
