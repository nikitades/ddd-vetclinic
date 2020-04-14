<?php

namespace App\Infrastructure\Framework\ApiResponse;

use DateTime;
use App\Domain\Patient\Entity\Owner;
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
            case Owner::class:
                return $this->getOwner($input);
        }
    }

    /** @return array<mixed> */
    protected function getPatient(Patient $patient)
    {
        $patientId = $patient->getId();
        $jsonId = $patientId ? $patientId->getValue() : null;
        return [
            'id' => $jsonId,
            'name' => $patient->getName()->getValue(),
            'species' => $patient->getSpecies()->getValue(),
            'age' => $patient->getBirthDate()->getValue()->diff((new DateTime()))->format("%y")
        ];
    }

    /** @return array<mixed> */
    protected function getOwner(Owner $owner)
    {
        $ownerId = $owner->getId();
        $jsonId = $ownerId ? $ownerId->getValue() : null;
        return [
            'id' => $jsonId,
            'name' => $owner->getName()->getValue(),
            'phone' => $owner->getPhone()->getValue(),
            'address' => $owner->getAddress()->getValue(),
            'email' => $owner->getEmail()->getValue(),
            'notification_required' => $owner->getNotificationRequired()->getValue()
        ];
    }
}
