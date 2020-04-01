<?php

namespace App\Application\Patient\DTO;

class GetAllPatientsDTO
{
    public bool $onTreatment;
    public bool $released;

    public function __construct(bool $onTreatment = true, bool $released = true)
    {
        if (!$onTreatment && !$released) {
            throw new \InvalidArgumentException("No patients requested");
        }
        $this->onTreatment = $onTreatment;
        $this->released = $released;
    }

    public static function onTreatment(): self
    {
        return new static(true, false);
    }

    public static function released(): self
    {
        return new static(false, true);
    }
}
